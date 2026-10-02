=== Gorres Scrollytelling ===
Contributors: jgorres
Donate link: https://ko-fi.com/joerngorres/
Tags: scrollytelling, storytelling, scroll, sticky, blocks
Requires at least: 6.7
Tested up to: 7.1
Requires PHP: 8.1
Stable tag: 2.12.0
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Full-screen media that stay in place while text boxes scroll across them, built from blocks in the editor.

== Description ==

Gorres Scrollytelling turns a web page into a story driven by scrolling: scrollytelling.

Scrollytelling (from "scrolling" and "storytelling") is a way of telling stories on the web in which scrolling itself moves the story forward. Instead of merely moving the page down, every scroll step triggers a change: graphics build up, maps zoom, images change, charts animate to match the text.

With Gorres Scrollytelling, one medium fills the screen and stays in place while the text of the current step scrolls across it. As soon as the next step reaches the middle of the screen, the medium behind it changes.

A story is put together from four kinds of blocks, and it can hold as many of them as it needs:

* **Scrollytelling Story** — the frame. It holds the layout of the text boxes, the dimming of the media and the length of a step.
* **Scrollytelling Step** — one step of the story. It carries its own image or video plus any blocks you want as text. A story takes any number of steps. A step without a medium keeps the medium of the step before, so several text boxes can scroll across the same image or video one after the other.
* **Scrollytelling Row** — optional group of steps inside a story that pass sideways: the screen stays in place and the steps move through it while the visitor keeps scrolling down. Steps before and after the row scroll vertically as usual.
* **Scrollytelling Afterword** — optional content that follows the last step. Below a stage that is limited to the medium it shows up right under the stage from the first step on, instead of leaving that space empty.

= What you can set =

* Position of the text boxes: left, center or right, horizontally and vertically, for the whole story or per step
* A text box that stays in place on the screen for the length of its step, for a hint or a headline on top of a medium
* Width of the text boxes in percent of the available column
* Look of a text box per step: background, border, rounded corners, shadow and padding
* Typography per step: font family, size, weight and style, line height, letter spacing, letter case, decoration and text alignment
* How the text boxes appear and disappear: fade, slide, zoom, rotate or dissolve, for the whole story or per step
* Direction: steps scroll up the screen, steps inside a row pass sideways, and a story can mix both
* How a medium fills the screen: crop to fill, or show the whole medium
* With a stage limited to the medium: pull the content after the story up below the stage, so it shows from the first step on
* Dimming of the media so that text stays readable
* Offset from the top, for sites with a fixed menu
* Height of a step, which decides how long its medium stays in place, for the whole story or per step
* Several text boxes in front of one medium: steps without a medium of their own, as close together as you like through their own height
* Cross-fade or instant change between media
* Focal point per medium, so the right part survives the crop
* An image that scrolls away with its step instead of staying in place, for an opening screen that lifts like a curtain
* A second image per step for portrait screens, so a landscape image does not lose its sides on a phone held upright

= Built to behave =

* **No JavaScript library.** The front end script is about seven kilobytes, a little over two when compressed, and uses the browser's own IntersectionObserver.
* **Readable without JavaScript.** The first medium stays visible and the text follows underneath, so the page never breaks. The steps of a row then simply follow each other.
* **Respects reduced motion.** No cross-fade, no text effect, no sideways movement and no playing video when the visitor asked for less motion.
* **Works with the keyboard.** A link or a field in a step of a row that is not on screen yet is brought into view when it receives the focus, and so is a step that a link points to.
* **Screen readers get one medium at a time.** All media of a story live in the document at once; only the visible one is exposed.
* **Sharp images.** The plugin calculates the sizes attribute from the aspect ratio of the image, because a screen-high medium needs a wider file than the screen itself.

== Installation ==

1. Install the plugin through Plugins → Add New, or upload the folder to `/wp-content/plugins/`.
2. Activate it.
3. Add the block "Scrollytelling Story" to a page and pick a medium for every step.
4. For a story that touches the edges of the screen, set the story block to full width.

== Frequently Asked Questions ==

= Which image sizes should I use? =

For "fill the frame" the aspect ratio does not matter, but the resolution does: a screen-high medium needs roughly screen height times aspect ratio in width, so about 2560 pixels for a 16:9 image. For "show the whole medium" it is worth giving every medium of a story the same aspect ratio, otherwise the free margins change from step to step.

= My image loses its sides on a phone. What can I do? =

A landscape image that fills a screen held upright only shows its middle part. Set the focal point of the step to keep the important part in view, or give the step a second image for portrait screens: open "Portrait screens" in the settings of the step and choose an image in portrait format. It replaces the image of the step whenever the screen is taller than wide, has its own focal point and shares the alternative text. Only images can be replaced this way, not videos.

= Can I use videos? =

Yes, images and videos from the media library. A video runs muted and only while its own step is visible, and it stays paused when the visitor asked for reduced motion.

= What happens to a step without a medium? =

Its text scrolls across the medium of the previous step. That is useful for a closing note on the last image.

= Can several text boxes scroll across the same medium? =

Yes. Give the first step the medium and add further steps without one: their text boxes scroll across the medium of the first step, one after the other. To bring the boxes closer together, open "Height" in the settings of such a step, switch off "Use the step height of the story" and choose a lower height. Each of these steps keeps its own look and its own text effect. Inside a row the height of a single step has no effect.

= Can a story open with a title image that scrolls away? =

Yes. Give the first step the title image and switch on "Let the image scroll along" under "Medium". The image fills the screen when the page loads and scrolls away with the step, while the medium of the next step already shows behind it. An image that is taller than the screen at full width keeps its height, and the step becomes as tall as the image; give the step a height of its own under "Height" and the image fills exactly that. Combined with a text box that is kept in place, a hint or a headline stays on screen while the title image leaves. Only images can scroll along, not videos, and inside a row the setting has no effect.

= Can a text box stay in place instead of scrolling by? =

Yes. Open "Position" in the settings of the step and switch on "Keep the text box in place". The text box then stays where it is on the screen while the visitor scrolls through the step, and leaves at the top when the step ends. How long it stays depends on the height of the step: give the step a height above 100 percent under "Height", 150 percent keeps the box for half a screen of scrolling. In the same place a step can put its text box somewhere else than the rest of the story. A text box kept in place has no text effect, and inside a row the setting has no effect.

= Why is there empty space below the stage? =

With "Limit the stage to the medium" the stage is lower than the screen, and content after the story only follows the last step. Switch on "Pull up the following content" in the story block: the content after it then sits right below the stage while the steps play and scrolls on once the story ends. If the content belongs to the story itself, put it into a "Scrollytelling Afterword" block inside the story instead.

= How does a row behave in different browsers? =

Browsers with scroll timelines (Chrome, Edge, Safari from version 26) move the row along with the scroll position. All others, Firefox at the time of writing, glide from one step to the next. If the text of a step is too tall for the screen, or the visitor asked for reduced motion, the steps of the row follow each other vertically like ordinary steps. Text effects do not apply inside a row.

= Does the text stay readable on a bright image? =

The story dims its media; you can set how much. Each step can also give its text box a background, a border and a shadow and set its own text color through the usual block settings.

== Screenshots ==

1. A story in the front end: the media stay in place and change while the text boxes scroll across them (animated).
2. A stage limited to the medium, with the content after the story pulled up below it.
3. The story block in the editor with its settings.
4. A single step in the editor with its medium and its text.

== Source code ==

The files in `build/` are compiled and minified. Their human-readable sources ship with the plugin in the folder `src/`, one subfolder per block: `src/story/`, `src/step/`, `src/row/` and `src/after/`.

The build tool is [@wordpress/scripts](https://www.npmjs.com/package/@wordpress/scripts) (webpack). To regenerate `build/` from `src/`, run these commands in the plugin folder, with Node.js and npm installed:

1. `npm install --save-dev @wordpress/scripts@35`
2. `npx wp-scripts build --webpack-src-dir=src --output-path=build`

The plugin uses no third-party libraries.

== Changelog ==

= 2.12.0 =
* Changed: the plugin is now called Gorres Scrollytelling. The blocks are named Scrollytelling Story, Scrollytelling Step, Scrollytelling Row and Scrollytelling Afterword.
* Changed: the blocks use the namespace `gorres-scrollytelling`, and the text domain is `gorres-scrollytelling`.

= 2.11.0 =
* New: the image of a step can scroll along with the step instead of staying in place on the stage.
* Changed: a story that starts with steps without a medium on the stage shows the first medium that follows from the beginning, instead of an empty stage.
* Changed: the plugin header no longer names a plugin URI.

= 2.10.0 =
* New: a step can place its text box differently from the rest of the story, horizontally and vertically.
* New: a step can keep its text box in place on the screen while the visitor scrolls through it.

= 2.9.0 =
* New: a step can carry a second image for portrait screens. It replaces the image of the step whenever the screen is taller than wide, so a landscape image does not lose its sides on a phone held upright.
* New: the package contains the sources of the compiled block files in `src/`; the readme describes how to build them.

= 2.8.4 =
* Fixed: the video of the first step started as soon as the page had loaded, even when the story was further down the page, and a video kept running after the story had left the screen. A video now only runs while its story is on screen.

= 2.8.3 =
* Plugin header: "Tested up to" is declared in the readme only, as the plugin directory requires. No change in behavior.

= 2.8.2 =
* Readme: the description now mentions several text boxes in front of one medium. No change in behavior.

= 2.8.1 =
* Fixed: in the editor of classic themes, steps and rows inside a full-width story shrank to the width of their content. They fill the story again.

= 2.8.0 =
* New: a step can have a height of its own instead of the step height of the story. Lower steps without a medium let several text boxes scroll across the same medium one after the other.

= 2.7.2 =
* New: donation link in the row of the plugin in the plugin list.

= 2.7.1 =
* Readme: description reworked, donate link added. No change in behavior.

= 2.7.0 =
* New: block "Scrollytelling Row". Put steps into a row inside a story and they pass sideways while the visitor keeps scrolling down; steps before and after the row scroll vertically as before.
* Browsers with scroll timelines move the row with the scroll position; all others glide from step to step. A row whose text does not fit the screen, and every row for visitors who prefer reduced motion, plays vertically.
* Keyboard focus and links to an anchor bring a step of a row into view.

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
