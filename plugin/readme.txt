=== Scrollstage ===
Contributors: jgorres
Tags: scrollytelling, storytelling, scroll, sticky, blocks
Requires at least: 6.7
Tested up to: 7.1
Requires PHP: 8.1
Stable tag: 2.0.1
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Full-screen media that stay in place while text boxes scroll across them, built from two blocks in the editor.

== Description ==

Scrollstage turns a page into a scroll-driven story. One medium fills the screen and stays in place while the text of the current step scrolls across it. As soon as the next step reaches the middle of the screen, the medium behind it changes.

The story is built from two blocks:

* **Scrollstage Story** — the frame. It holds the layout of the text boxes, the dimming of the media and the length of a step.
* **Scrollstage Step** — one step of the story. It carries its own image or video plus any blocks you want as text.

= What you can set =

* Position of the text boxes: left, centre or right, horizontally and vertically
* Width of the text boxes in percent of the available column
* How a medium fills the screen: crop to fill, or show the whole medium
* Dimming of the media so that text stays readable
* Offset from the top, for sites with a fixed menu
* Height of a step, which decides how long its medium stays in place
* Cross-fade or instant change between media
* Focal point per medium, so the right part survives the crop

= Built to behave =

* **No JavaScript library.** The front end script is about one kilobyte and uses the browser's own IntersectionObserver.
* **Readable without JavaScript.** The first medium stays visible and the text follows underneath, so the page never breaks.
* **Respects reduced motion.** No cross-fade and no playing video when the visitor asked for less motion.
* **Screen readers get one medium at a time.** All media of a story live in the document at once; only the visible one is exposed.
* **Sharp images.** The plugin calculates the sizes attribute from the aspect ratio of the image, because a screen-high medium needs a wider file than the screen itself.

= GeneratePress =

The plugin ships an optional page template, "Scrollstage: full width", which drops the sidebars while header, footer and the usual content width stay untouched. It builds on the hooks of GeneratePress and only appears when that theme is active. The blocks themselves work with any theme.

== Installation ==

1. Install the plugin through Plugins → Add New, or upload the folder to `/wp-content/plugins/`.
2. Activate it.
3. Add the block "Scrollstage Story" to a page and pick a medium for every step.
4. For a story that touches the edges of the screen, set the story block to full width.

== Frequently Asked Questions ==

= Which image sizes should I use? =

For "fill the frame" the aspect ratio does not matter, but the resolution does: a screen-high medium needs roughly screen height times aspect ratio in width, so about 2560 pixels for a 16:9 image. For "show the whole medium" it is worth giving every medium of a story the same aspect ratio, otherwise the free margins change from step to step.

= Can I use videos? =

Yes, images and videos from the media library. A video runs muted and only while its own step is visible, and it stays paused when the visitor asked for reduced motion.

= What happens to a step without a medium? =

Its text scrolls across the medium of the previous step. That is useful for a closing note on the last image.

= Does the text stay readable on a bright image? =

The story dims its media; you can set how much. Each step can also carry its own background and text colour through the usual block settings.

== Screenshots ==

1. A story in the front end: the medium fills the screen, the text box scrolls across it.
2. The story block in the editor with its settings.
3. A single step with its medium and its text.

== Changelog ==

= 2.0.1 =
* Tested with WordPress 7.1.

= 2.0.0 =
* First public release.
