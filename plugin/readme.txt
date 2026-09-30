=== Scrollstage ===
Contributors: jgorres
Tags: scrollytelling, storytelling, scroll, sticky, blocks
Requires at least: 6.7
Tested up to: 7.1
Requires PHP: 8.1
Stable tag: 2.6.2
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Full-screen media that stay in place while text boxes scroll across them, built from blocks in the editor.

== Description ==

Scrollstage turns a page into a scroll-driven story. One medium fills the screen and stays in place while the text of the current step scrolls across it. As soon as the next step reaches the middle of the screen, the medium behind it changes.

The story is built from two blocks, plus an optional third:

* **Scrollstage Story** — the frame. It holds the layout of the text boxes, the dimming of the media and the length of a step.
* **Scrollstage Step** — one step of the story. It carries its own image or video plus any blocks you want as text.
* **Scrollstage Afterword** — optional content that follows the last step. Below a stage that is limited to the medium it shows up right under the stage from the first step on, instead of leaving that space empty.

= What you can set =

* Position of the text boxes: left, center or right, horizontally and vertically
* Width of the text boxes in percent of the available column
* Look of a text box per step: background, border, rounded corners, shadow and padding
* Typography per step: font family, size, weight and style, line height, letter spacing, letter case, decoration and text alignment
* How the text boxes appear and disappear: fade, slide, zoom, rotate or dissolve, for the whole story or per step
* How a medium fills the screen: crop to fill, or show the whole medium
* With a stage limited to the medium: pull the content after the story up below the stage, so it shows from the first step on
* Dimming of the media so that text stays readable
* Offset from the top, for sites with a fixed menu
* Height of a step, which decides how long its medium stays in place
* Cross-fade or instant change between media
* Focal point per medium, so the right part survives the crop

= Built to behave =

* **No JavaScript library.** The front end script is about four kilobytes, under one and a half when compressed, and uses the browser's own IntersectionObserver.
* **Readable without JavaScript.** The first medium stays visible and the text follows underneath, so the page never breaks.
* **Respects reduced motion.** No cross-fade, no text effect and no playing video when the visitor asked for less motion.
* **Screen readers get one medium at a time.** All media of a story live in the document at once; only the visible one is exposed.
* **Sharp images.** The plugin calculates the sizes attribute from the aspect ratio of the image, because a screen-high medium needs a wider file than the screen itself.

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

= Why is there empty space below the stage? =

With "Limit the stage to the medium" the stage is lower than the screen, and content after the story only follows the last step. Switch on "Pull up the following content" in the story block: the content after it then sits right below the stage while the steps play and scrolls on once the story ends. If the content belongs to the story itself, put it into a "Scrollstage Afterword" block inside the story instead.

= Does the text stay readable on a bright image? =

The story dims its media; you can set how much. Each step can also give its text box a background, a border and a shadow and set its own text color through the usual block settings.

== Screenshots ==

1. A story in the front end: the media stay in place and change while the text boxes scroll across them (animated).
2. A stage limited to the medium, with the content after the story pulled up below it.
3. The story block in the editor with its settings.
4. A single step in the editor with its medium and its text.

== Changelog ==

= 2.6.2 =
* New: two more text effects. "Rotate in" spins the text box into place like a propeller, "Dissolve" builds it up from a raster of growing dots and takes it apart again.

= 2.6.1 =
* Fixed: the zoom effect was hardly visible. The text box now starts smaller and is fully opaque after half of the way, so the change in size shows.
* Changed: the fade effect runs longer, so it is still visible while the text box scrolls into view.

= 2.6.0 =
* New: text effects. The text boxes can fade, slide or zoom in when they enter the screen and leave the same way. Set the effect for the whole story or per step.
* Browsers with scroll timelines tie the effect to the scroll position; all others run it once as a transition. Visitors who prefer reduced motion see no effect.

= 2.5.0 =
* New: typography settings for a step: font family, weight and style, letter spacing, letter case, decoration and text alignment.
* New: shadow for the text box of a step.
* Changed: background, border and padding of a step now apply to its text box instead of the whole step. A box with a background gets a default padding and, unless a text color is set, the text color of the theme.

= 2.4.1 =
* Internal: front end script and styles of the story block are split into modules. No change in behavior.
* Changed: the class that switches on the cross-fade is now `is-effect-fade` (was `has-fade`); the unused class `no-fade` is gone.

= 2.4.0 =
* First public release.
