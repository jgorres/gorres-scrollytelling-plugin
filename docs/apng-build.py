#!/usr/bin/env python3
"""
Shrink an animated PNG (APNG) made from screen video.

Usage: apng-build.py <input.png> <output.png> [--threshold N] [--step N] [--fps N]

The source is a screen recording of a scrollytelling story: a static medium
per scene, a text box and the mouse pointer moving across it. Video
compression adds noise to every frame, which would turn each frame into a
full-frame update. Steps:

  1. Decode all frames of the input APNG, optionally keep every n-th frame.
  2. Split into scenes at hard cuts (more than half of the pixels change).
  3. Per scene, compute a noise-free background as the per-pixel temporal
     median of the scene. A pixel of a frame is taken from the frame only
     when it differs from the background by more than --threshold (text,
     pointer, scrolling content); everything else is the background.
     The first two frames of a scene are replaced by the background when
     they are cross-fade frames (more than 30 % differing pixels).
  4. Quantise all frames to one global palette of 255 colours with ffmpeg
     (palettegen/paletteuse, ordered Bayer dithering, so identical pixels get
     identical palette indices). Index 255 is reserved for transparency.
  5. Write the APNG: every frame contains only the rectangle that changed;
     unchanged pixels inside it are transparent and blended "over".

Requirements: Python 3, Pillow, numpy, ffmpeg in PATH.

Author: Jörn Gorres
"""

import argparse
import glob
import io
import os
import struct
import subprocess
import sys
import tempfile
import zlib

import numpy as np
from PIL import Image, ImageSequence

TRANSPARENT = 255
CUT_FRACTION = 0.5
FADE_FRACTION = 0.3
LEAD_FRAMES = 2


def chunk(chunk_type: bytes, data: bytes) -> bytes:
    """Return a complete PNG chunk (length, type, data, CRC)."""
    body = chunk_type + data
    return struct.pack(">I", len(data)) + body + struct.pack(">I", zlib.crc32(body) & 0xFFFFFFFF)


def png_idat(img: Image.Image) -> bytes:
    """Encode a palette image with Pillow and return its concatenated IDAT payload."""
    buf = io.BytesIO()
    img.save(buf, "PNG", optimize=True)
    data = buf.getvalue()
    out = b""
    pos = 8
    while pos < len(data):
        (length,) = struct.unpack(">I", data[pos : pos + 4])
        if data[pos + 4 : pos + 8] == b"IDAT":
            out += data[pos + 8 : pos + 8 + length]
        pos += 12 + length
    return out


def load_frames(path: str, step: int) -> list[np.ndarray]:
    """Load every step-th frame of an APNG as int16 RGB array."""
    frames = [np.array(f.convert("RGB"), dtype=np.int16) for f in ImageSequence.Iterator(Image.open(path))]
    if not frames:
        sys.exit(f"{path}: no frames found")
    return frames[::step]


def scenes(frames: list[np.ndarray]) -> list[tuple[int, int]]:
    """Split the frame list at hard cuts; return (start, end) index pairs."""
    bounds = [0]
    for i in range(1, len(frames)):
        changed = (np.abs(frames[i] - frames[i - 1]).max(axis=2) > 24).mean()
        if changed > CUT_FRACTION:
            bounds.append(i)
    bounds.append(len(frames))
    return list(zip(bounds[:-1], bounds[1:]))


def stabilize(frames: list[np.ndarray], threshold: int) -> list[Image.Image]:
    """Replace video noise by the per-scene median background."""
    out = []
    for start, end in scenes(frames):
        background = np.median(np.stack(frames[start:end]), axis=0).astype(np.int16)
        for j, cur in enumerate(frames[start:end]):
            mask = np.abs(cur - background).max(axis=2) > threshold
            if j < LEAD_FRAMES and mask.mean() > FADE_FRACTION:
                out.append(background)
            else:
                out.append(np.where(mask[..., None], cur, background))
    return [Image.fromarray(a.astype(np.uint8), "RGB") for a in out]


def quantize(frames: list[Image.Image], workdir: str) -> tuple[list[np.ndarray], list[int]]:
    """Quantise all frames to one 255-colour palette with ffmpeg; return index arrays and palette."""
    for i, frame in enumerate(frames):
        frame.save(os.path.join(workdir, f"in{i:04d}.png"))
    pattern = os.path.join(workdir, "in%04d.png")
    palette = os.path.join(workdir, "palette.png")
    subprocess.run(
        ["ffmpeg", "-v", "error", "-y", "-i", pattern, "-vf",
         "palettegen=max_colors=255:stats_mode=full", palette],
        check=True,
    )
    subprocess.run(
        ["ffmpeg", "-v", "error", "-y", "-i", pattern, "-i", palette, "-lavfi",
         "paletteuse=dither=bayer:bayer_scale=5", os.path.join(workdir, "q%04d.png")],
        check=True,
    )
    quantized = [Image.open(p) for p in sorted(glob.glob(os.path.join(workdir, "q*.png")))]
    if len(quantized) != len(frames) or any(q.mode != "P" for q in quantized):
        sys.exit("ffmpeg did not return a palette image per frame")
    pal = quantized[0].getpalette()[: TRANSPARENT * 3] + [0, 0, 0]
    arrays = [np.array(q) for q in quantized]
    if max(int(a.max()) for a in arrays) >= TRANSPARENT:
        sys.exit("palette index 255 is in use; cannot reserve it for transparency")
    return arrays, pal


def assemble(frames: list[np.ndarray], pal: list[int], delay_ms: int, out_path: str) -> None:
    """Write the APNG with changed rectangles only."""
    height, width = frames[0].shape
    seq = 0
    body = b""
    for i, cur in enumerate(frames):
        if i == 0:
            x0, y0, x1, y1 = 0, 0, width, height
            sub = cur
        else:
            diff = cur != frames[i - 1]
            if not diff.any():
                x0, y0, x1, y1 = 0, 0, 1, 1
                sub = np.full((1, 1), TRANSPARENT, dtype=np.uint8)
            else:
                ys, xs = np.where(diff)
                y0, y1, x0, x1 = ys.min(), ys.max() + 1, xs.min(), xs.max() + 1
                sub = cur[y0:y1, x0:x1].copy()
                sub[~diff[y0:y1, x0:x1]] = TRANSPARENT
        img = Image.fromarray(sub, "P")
        img.putpalette(pal)
        img.info["transparency"] = TRANSPARENT
        data = png_idat(img)
        # fcTL: sequence, width, height, x, y, delay_num, delay_den, dispose_op (none), blend_op (over)
        body += chunk(b"fcTL", struct.pack(">IIIIIHHBB", seq, x1 - x0, y1 - y0, x0, y0, delay_ms, 1000, 0, 1))
        seq += 1
        if i == 0:
            body += chunk(b"IDAT", data)
        else:
            body += chunk(b"fdAT", struct.pack(">I", seq) + data)
            seq += 1
    head = b"\x89PNG\r\n\x1a\n"
    head += chunk(b"IHDR", struct.pack(">IIBBBBB", width, height, 8, 3, 0, 0, 0))
    head += chunk(b"PLTE", bytes(pal))
    head += chunk(b"tRNS", b"\xff" * TRANSPARENT + b"\x00")
    head += chunk(b"acTL", struct.pack(">II", len(frames), 0))
    with open(out_path, "wb") as fh:
        fh.write(head + body + chunk(b"IEND", b""))


def main() -> None:
    """Parse arguments and run the pipeline."""
    parser = argparse.ArgumentParser(description="Shrink an animated PNG made from screen video.")
    parser.add_argument("input", help="source APNG")
    parser.add_argument("output", help="target APNG")
    parser.add_argument("--threshold", type=int, default=24, help="difference to the background that counts as content (default 24)")
    parser.add_argument("--step", type=int, default=1, help="keep every n-th frame (default 1)")
    parser.add_argument("--fps", type=float, default=10, help="output frame rate (default 10)")
    args = parser.parse_args()
    if args.threshold < 0 or args.step < 1 or args.fps <= 0:
        sys.exit("invalid arguments")

    frames = stabilize(load_frames(args.input, args.step), args.threshold)
    with tempfile.TemporaryDirectory() as workdir:
        arrays, pal = quantize(frames, workdir)
    assemble(arrays, pal, round(1000 / args.fps), args.output)
    print(f"{args.output}: {len(arrays)} frames, {os.path.getsize(args.output) // 1024} KB")


if __name__ == "__main__":
    main()
