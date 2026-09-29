=== Scrollstage ===
Contributors: jgorres
Tags: scrollytelling, storytelling, scroll, sticky, blocks
Requires at least: 6.7
Tested up to: 7.1
Requires PHP: 8.1
Stable tag: 2.3.2
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
* How a medium fills the screen: crop to fill, or show the whole medium
* With a stage limited to the medium: pull the content after the story up below the stage, so it shows from the first step on
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

= Why is there empty space below the stage? =

With "Limit the stage to the medium" the stage is lower than the screen, and content after the story only follows the last step. Switch on "Pull up the following content" in the story block: the content after it then sits right below the stage while the steps play and scrolls on once the story ends. If the content belongs to the story itself, put it into a "Scrollstage Afterword" block inside the story instead.

= Does the text stay readable on a bright image? =

The story dims its media; you can set how much. Each step can also carry its own background and text color through the usual block settings.

== Screenshots ==

1. A story in the front end: the media stay in place and change while the text boxes scroll across them (animated).
2. A stage limited to the medium, with the content after the story pulled up below it.
3. The story block in the editor with its settings.
4. A single step in the editor with its medium and its text.

== Changelog ==

= 2.3.2 =
* First public release.
