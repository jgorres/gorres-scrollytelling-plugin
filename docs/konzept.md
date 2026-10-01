# Scrollstage – Konzept und Aufbau

Version: 1.27 · Stand: 01.10.2026 · Plugin-Version: 2.9.0

## 1. Zweck

Ein Medium füllt den Bildschirm und bleibt stehen, während der Text des
aktuellen Schritts darüber hinwegscrollt. Erreicht der nächste Schritt die
Bildschirmmitte, wechselt das Medium dahinter. Der Effekt ist als „Spiegel-
Scrollytelling" bekannt.

Das Plugin ist für die Veröffentlichung im WordPress-Verzeichnis gedacht.
Oberfläche und Dokumentation im Plugin sind deshalb englisch, diese
Projektdoku ist deutsch.

## 2. Verzeichnisse

Das Projekt liegt unter `~/dev/jgorres-im-WP-Repository/`, wie alles, was ein
Plugin im WordPress-Verzeichnis ist oder werden soll. Im selben Ordner liegen
die Release-ZIPs und die Übersetzungsdateien für GlotPress.

```
~/dev/jgorres-im-WP-Repository/scrollstage/
├── plugin/                    ausgeliefertes Plugin
│   ├── scrollstage.php        Header, Konstanten, Textdomain, Modul-Loader
│   ├── readme.txt             für das WordPress-Verzeichnis
│   ├── uninstall.php          derzeit ohne Daten zu löschen
│   ├── includes/
│   │   ├── blocks.php         Kategorie, Registrierung, Klassen der Story,
│   │   │                      Schritte sammeln, Kontext der Reihe,
│   │   │                      Textkasten des Schritts, Bühnen-Markup
│   │   └── admin.php          Spendenlink in der Plugin-Liste
│   ├── src/                   Quellen für wp-scripts
│   │   ├── story/             block.json, index/edit/save, render.php,
│   │   │   │                  style.scss, editor.scss, view.js
│   │   │   ├── view/          Module des Frontend-Scripts: selectors, support,
│   │   │   │                  media, text, row, after, pull
│   │   │   └── style/         SCSS-Teildateien: layout, stage, stage-limited,
│   │   │                      steps, after, effects
│   │   ├── step/              ebenso, ohne view.js; dazu text-box.js
│   │   │   │                  (Textkasten im Editor)
│   │   │   └── style/         SCSS-Teildatei: text-effects
│   │   ├── row/               Reihe, ohne view.js (ihr Script ist
│   │   │                      story/view/row.js)
│   │   └── after/             Nachspann, ohne view.js
│   ├── build/                 Ergebnis von "npm run build", nicht im Repo
│   └── languages/scrollstage.pot
├── assets/                    WP.org-Assets: Screenshots, Icon, Banner (nicht im Paket)
├── build.sh                   Release-ZIP erzeugen
├── .distignore                Ausschlüsse aus plugin/ für ZIP und SVN-Export
├── docs/                      diese Doku, banner.svg, Quellvideo der Aufnahme
├── playground/                Onlinehilfe als Bundle für den WordPress
│                              Playground, siehe Abschnitt 12
├── stubs/                     zusätzliche Stubs für PHPStan (derzeit leer)
├── composer.json, phpcs.xml.dist, phpstan.neon.dist, phpstan-bootstrap.php
└── package.json
```

Namensregeln: Slug und Text-Domain `scrollstage`, Blöcke `scrollstage/story`,
`scrollstage/step`, `scrollstage/row` und `scrollstage/after`, Funktionen
`jgor_st_`, Konstanten `JGOR_ST_`, CSS-Klassen `jgor-st-`.

Seit 2.4.1 sind die Quellen der Story in Module geteilt. `view.js` und
`style.scss` bleiben die Einstiegspunkte, damit `block.json` und die Dateien
im Build gleich heißen:

| Datei | Inhalt |
| --- | --- |
| `view/selectors.js` | gemeinsame Selektoren |
| `view/support.js` | Erkennung von Scroll-Timelines |
| `view/media.js` | Medienwechsel per `IntersectionObserver` |
| `view/text.js` | Texteffekte für Browser ohne Scroll-Timelines |
| `view/row.js` | Ablauf der Reihen: nebeneinander, stufenweise, Fokus und Anker |
| `view/after.js` | Nachspann unter der begrenzten Bühne anheften |
| `view/pull.js` | folgenden Inhalt hochziehen |
| `style/_layout.scss` | Variablen, Grid, Abstand unter der Story |
| `style/_stage.scss` | klebende Bühne, Abdunkelung, Medien |
| `style/_stage-limited.scss` | auf das Medium begrenzte Bühne |
| `style/_steps.scss` | Schrittspalte |
| `style/_after.scss` | Nachspann, Anheften, hochgezogener Inhalt |
| `style/_effects.scss` | Effekte und `prefers-reduced-motion` |

Die Reihenfolge der `@use`-Zeilen in `style.scss` ist die Reihenfolge der
Regeln im kompilierten CSS.

## 3. Aufbau der Blöcke

`story` ist der Rahmen, `step` das einzelne Kapitel, `row` eine optionale
Reihe von Schritten, die waagerecht abläuft, `after` der optionale Nachspann
hinter dem letzten Schritt. Alle rendern serverseitig (`render.php`),
gespeichert werden nur die Kindblöcke. Dadurch wirken Änderungen am Markup
sofort, ohne Beiträge neu zu speichern.

### Attribute von `story`

| Attribut | Werte | Standard | Wirkung |
| --- | --- | --- | --- |
| `textPosition` | left, center, right | center | waagerechte Lage der Textkästen |
| `textWidth` | 20–100 | 45 | Breite der Textkästen in Prozent |
| `stepAlign` | start, center, end | center | senkrechte Lage im Schritt |
| `mediaFit` | cover, contain | cover | Ausschnitt füllen oder ganzes Medium |
| `limitStage` | true/false | false | Bühne auf das Medium begrenzen (nur bei contain) |
| `pullContent` | true/false | false | Inhalt nach der Story unter die begrenzte Bühne ziehen (nur mit `limitStage`) |
| `overlayOpacity` | 0–90 | 35 | Abdunkelung der Medien in Prozent |
| `stickyOffset` | 0–200 | 0 | Abstand von oben in Pixeln |
| `minStepHeight` | 40–200 | 100 | Höhe eines Schritts in Prozent der Bildschirmhöhe |
| `transition` | fade, none | fade | Überblenden oder harter Wechsel |
| `textEffect` | none, fade, slide, zoom, rotate, dissolve | none | Effekt, mit dem die Textkästen erscheinen und verschwinden |

### Klassen von `story`

`jgor_st_story_classes()` in `includes/blocks.php` baut alle Klassen des
Rahmens an einer Stelle; `story/render.php` übergibt nur den geprüften
Zustand.

| Klasse | Bedeutung | gesetzt von |
| --- | --- | --- |
| `is-text-left`, `-center`, `-right` | `textPosition` | PHP |
| `is-align-start`, `-center`, `-end` | `stepAlign` | PHP |
| `is-fit-cover`, `-contain` | `mediaFit` | PHP |
| `is-effect-<name>` | aktiver Effekt, derzeit nur `is-effect-fade` (`transition` = fade) | PHP |
| `is-stage-limited` | begrenzte Bühne mit bekanntem Seitenverhältnis | PHP |
| `has-pull-content` | Inhalt nach der Story hochziehen | PHP |
| `has-after` | Nachspann oder hochgezogener Inhalt vorhanden | PHP, Script |
| `is-enhanced` | Script steuert den Medienwechsel | Script |
| `is-after-pinned` | Nachspann klebt unter der Bühne | Script |

Bis 2.4.0 hieß `is-effect-fade` noch `has-fade`; beim harten Wechsel gab es
zusätzlich die ungenutzte Klasse `no-fade`.

### Attribute von `step`

| Attribut | Wirkung |
| --- | --- |
| `mediaId`, `mediaUrl`, `mediaType` | gewähltes Medium aus der Mediathek |
| `mediaAlt` | Alternativtext; leer übernimmt den Text der Mediathek |
| `focalPoint` | Punkt, der beim Zuschneiden sichtbar bleibt |
| `portraitId`, `portraitUrl` | zweites Bild für Bildschirme im Hochformat; nur zusammen mit einem Bild als Medium |
| `portraitFocalPoint` | Fokuspunkt des Hochformat-Bilds |
| `textEffect` | eigener Effekt des Textkastens; leer übernimmt den der Story, `none` schaltet ihn ab |
| `minHeight` | eigene Höhe des Schritts in Prozent der Bildschirmhöhe (20 bis 200); 0 übernimmt `minStepHeight` der Story |

#### Bild für Hochformat (`portraitId`, `portraitUrl`)

Seit 2.9.0. Ein Querformat, das einen hochkant gehaltenen Bildschirm füllt,
zeigt nur seinen mittleren Teil (bei 3:2 auf einem Handy etwa ein Drittel der
Breite). Ein Schritt kann deshalb ein zweites Bild tragen.
`jgor_st_render_stage_item()` gibt beide in einem `picture`-Element aus:

```html
<picture class="jgor-st-stage__picture">
	<source media="(orientation: portrait)" srcset="…" sizes="…" />
	<img class="jgor-st-stage__media" … />
</picture>
```

Der Browser lädt nur das passende Bild und wechselt beim Drehen des Geräts
von selbst; das Script ist nicht beteiligt. Kriterium ist die Ausrichtung des
Fensters, nicht seine Breite: Ein Tablet im Hochformat bekommt das
Hochformat-Bild, ein Handy im Querformat das Querformat.

- `picture` hat `display: contents`, damit das Bild weiter die Bühne füllt.
- Die Figur trägt `has-portrait`; der eigene Fokuspunkt kommt als
  `--jgor-st-portrait-focal-x/-y` und gilt per Media Query nur im Hochformat.
- `srcset` und `sizes` der Quelle stammen aus der Mediathek
  (`jgor_st_portrait_source()`); fehlt der Anhang, wird `portraitUrl`
  unverändert ausgegeben. Der Alternativtext ist der des Schritts.
- Nur Bilder: Ein Video als Medium bekommt keine Quelle, der Editor bietet den
  Bereich „Portrait screens" dann nicht an und leert die Attribute.
- Begrenzte Bühne: `story/render.php` berechnet für das Hochformat ein eigenes
  Verhältnis (je Schritt das Hochformat-Bild, sonst das Medium) und gibt es
  als `--jgor-st-stage-ratio-portrait` mit der Klasse `has-portrait-ratio`
  aus. Das Stylesheet ersetzt damit im Hochformat `--jgor-st-stage-ratio` an
  Bühne und Schritten.
- Der Editor zeigt im Schritt weiter das Medium; das Hochformat-Bild ist im
  Fokuspunkt-Wähler des Bereichs zu sehen.

#### Eigene Schritthöhe (`minHeight`)

Seit 2.8.0. Die Story setzt die Schritthöhe als `--jgor-st-step-min` an ihrem
Wrapper. Ein Schritt mit eigener Höhe setzt dieselbe Variable an seinem
eigenen Wrapper (`step/render.php`, geprüft von `jgor_st_step_min_height()`
in `includes/blocks.php`); Stylesheet und Scripte bleiben unverändert.

Zweck: mehrere Textkästen vor demselben Medium. Der erste Schritt trägt das
Medium, die folgenden haben keines und eine geringere Höhe. Das Medium bleibt
stehen (Abschnitt 4), die Kästen folgen dicht aufeinander, und jeder behält
seinen eigenen Kastenstil und Texteffekt. Ein Kasten, der höher ist als der
Wert, dehnt den Schritt (`min-height`).

In einer Reihe zählt der Wert nicht (Kontext `scrollstage/inRow`): Der
Scrollweg der Reihe ist Anzahl der Schritte mal Schritthöhe der Story. Der
Editor zeigt die Höhe nicht, dort sind alle Schritte gleich hoch; die
Einstellung liegt im Bereich „Height" des Schritts.

### Block-Einstellungen von `step`

Der Schritt ist so breit wie die Story und so hoch wie der Bildschirm, der
Textkasten darin (`.jgor-st-step__content`) nur so groß wie sein Text. Seit
2.5.0 sind die Einstellungen des Blocks deshalb aufgeteilt:

| Einstellung | wirkt auf |
| --- | --- |
| Typografie: Schrift, Größe, Schnitt, Zeilenhöhe, Zeichenabstand, Schreibweise, Dekoration, Ausrichtung | Schritt, vererbt sich auf den Text |
| Textfarbe, Außenabstand | Schritt |
| Hintergrund, Rahmen, Eckenradius, Schatten, Innenabstand | Textkasten |

1. `block.json` nimmt Hintergrund, Rahmen, Schatten und Innenabstand per
   `__experimentalSkipSerialization` vom Block-Wrapper.
2. Im Frontend baut `jgor_st_step_box_attributes()` (`includes/blocks.php`)
   aus denselben Attributen Klassen und Inline-Style für den Textkasten, mit
   der Style Engine und je Einstellung so, wie es die Block-Supports des Core
   tun.
3. Im Editor macht `step/text-box.js` dasselbe mit den Helfern, die auch der
   Button-Block des Core benutzt. Sie sind als experimentell markiert; fehlt
   einer, entfällt nur dieser Teil der Vorschau.
4. Ein Kasten mit Hintergrund bekommt `padding: clamp(1rem, 3vw, 2rem)`; ein
   am Block gesetzter Innenabstand hat Vorrang.
5. Weiße Schrift gilt nur, wenn keine Textfarbe gewählt ist und der Kasten
   keinen Hintergrund hat. Mit Hintergrund ohne Textfarbe gilt die Textfarbe
   des Themes.

Die Typografie-Schlüssel heißen in `block.json` noch `__experimental…`, weil
WordPress 7.1 sie in seinen eigenen Blöcken so führt. Setzt ein Theme Werte
direkt an Überschriften (Twenty Twenty-Five: Schriftstärke, Zeichenabstand),
haben diese Vorrang vor den Werten des Schritts.

Klassische Themes (GeneratePress) laden im Editor `classic.min.css` des Core.
Es zentriert jeden Block mit automatischen Rändern und einer Höchstbreite. In
der Story, im Editor eine Flex-Spalte, schrumpften Schritte, Reihen und
Nachspann dadurch auf die Breite ihres Inhalts, sichtbar bei einer Story in
voller Breite. Seit 2.8.1 heben `story/editor.scss` und `row/editor.scss`
Ränder und Höchstbreite für die Kinder von Story und Reihe auf. Block-Themes
laden dieses Stylesheet nicht.

Das Editor-Stylesheet der Blöcke hängt an der `version` aus `block.json`.
Ohne Versionssprung liefert der Browser nach einer CSS-Änderung das alte
Stylesheet aus dem Cache; im Frontend stehen die Styles inline.

### Reihe `row`

Seit 2.7.0. Ohne eigene Attribute; nimmt nur Schritte auf und ist nur als Kind
von `story` erlaubt, `step` hat dafür `story` und `row` als Elternblöcke. Der
Editor zeigt die Schritte der Reihe untereinander in einem gestrichelten
Rahmen mit Hinweistext; das waagerechte Gleiten gibt es nur im Frontend.

`row/render.php` gibt `.jgor-st-row > .jgor-st-row__viewport >
.jgor-st-row__track` aus und die Zahl der Schritte als `--jgor-st-row-count`.
Die Medien der Schritte stehen wie alle anderen auf der Bühne der Story:
`jgor_st_collect_steps()` sammelt die Schritte der Story und ihrer Reihen als
flache Liste in Dokumentreihenfolge.

Schritte in einer Reihe bekommen keinen Texteffekt, denn die Effekte gehören
zu einem Kasten, der von unten kommt und oben geht. Der Filter
`render_block_context` (`jgor_st_row_context()`) setzt dafür den Kontext
`scrollstage/inRow`, weil ein Schritt beim Rendern seinen Elternblock nicht
sieht.

| Klasse an `.jgor-st-row` | Bedeutung | gesetzt von |
| --- | --- | --- |
| `is-sideways` | Schritte liegen nebeneinander, der Ausschnitt klebt | Script |
| `is-stepped` | zusätzlich: Browser ohne Scroll-Timelines, die Spur gleitet stufenweise | Script |

### Nachspann `after`

Ohne eigene Attribute; nimmt beliebige Blöcke auf, dazu Farben, Innenabstand
und Schrift über die Block-Supports. Erlaubt nur als Kind von `story`. Der
Editor zeigt ihn dort, wo er eingefügt wurde, im Frontend steht er immer hinter
dem letzten Schritt. Ein Innenabstand oben (`clamp(1.5rem, 4vh, 2.5rem)`) hält
die erste Überschrift von der Bühnenkante fern; ein am Block gesetzter Abstand
hat Vorrang.

## 4. Wie der Effekt entsteht

1. `story/render.php` liest die Medien der Schritte aus
   `$block->parsed_block['innerBlocks']`, auch die der Schritte in Reihen
   (`jgor_st_collect_steps()`), und baut daraus eine Bühne. Jeder Schritt
   bekommt genau ein Bühnenelement, auch ein Schritt ohne Medium (Klasse
   `is-empty`).
2. Bühne und Schrittspalte liegen in derselben Grid-Zelle. Die Bühne ist
   `position: sticky` und bildschirmhoch, die Schritte liegen mit `z-index`
   darüber.
3. `view.js` beobachtet die Schritte mit einem `IntersectionObserver`
   (`rootMargin: -45% 0px -45%`, also ein schmales Band in der Mitte) und
   schaltet das zugehörige Bühnenelement aktiv. Schritte ohne Medium laufen
   rückwärts bis zum letzten vorhandenen. Gezählt werden nur die Schritte der
   eigenen Story und ihrer Reihen (`STEP_SELECTOR` mit `:scope >`), nicht die
   einer Story, die in einem Schritt liegt.
4. Ohne JavaScript bleibt das erste Medium sichtbar
   (`:not(.is-enhanced) .jgor-st-stage__item:first-child`).

### Effekte der Textkästen (`textEffect`)

Ein Textkasten kann beim Erscheinen einblenden (`fade`), sich von unten
hereinschieben (`slide`), sich vergrößern (`zoom`), sich hereindrehen
(`rotate`) oder sich aus einem Punktraster zusammensetzen (`dissolve`) und
verschwindet oben auf demselben Weg.

1. Die Story reicht ihr `textEffect` als Block-Kontext
   (`scrollstage/textEffect`) an die Schritte weiter.
   `jgor_st_resolve_text_effect()` wählt den eigenen Wert des Schritts, sonst
   den der Story; `step/render.php` setzt daraus
   `has-text-effect is-text-effect-<name>` am Schritt.
2. Browser mit Scroll-Timelines (Chrome ab 115, Safari ab 26) koppeln den
   Effekt rein in CSS an den Scrollweg (`step/style/_text-effects.scss`): zwei
   Animationen auf `animation-timeline: view()`, eine über den Bereich
   `entry`, eine über `exit`. Die zweite füllt nur vorwärts, sonst überdeckte
   ihr erstes Keyframe die erste. Der Abstand von oben (`--jgor-st-offset`)
   geht als Einzug in die Timeline ein.
3. Alle anderen Browser bekommen den Effekt über `view/text.js`: Ein
   `IntersectionObserver` beobachtet den Platz der Textkästen in einem Band,
   das oben und unten 15 % des Fensters auslässt, und setzt am Schritt
   `is-visible`. Beobachtet wird nicht der Kasten selbst, sondern ein leeres
   Element an seinem Platz im Layout (`.jgor-st-step__ghost`, per
   `ResizeObserver` nachgeführt): Der Observer sieht einen Kasten so, wie er
   gezeichnet wird, und ein Kasten, der verborgen schrumpft oder sich dreht,
   verließe und beträte das Band durch seine eigene Bewegung. Blieb das
   Scrollen an der Bandkante stehen, flackerte der Propeller deshalb oder
   erschien gar nicht.
   Ein zweiter beobachtet den Bereich oberhalb des Bandes und setzt `is-past`
   (Kasten ist oben hinaus); das Band allein genügt dafür nicht, weil ein
   Sprung, etwa zu einem Anker, einen Kasten von oben nach unten trägt, ohne
   dass er das Band berührt. Die Story bekommt
   `is-text-enhanced`, erst nach dem ersten Bericht des Observers, damit ein
   beim Laden sichtbarer Kasten nicht flackert. Der Effekt läuft dann einmal
   als Übergang von 600 ms, nicht an den Scrollweg gekoppelt. Im
   Scroll-Handler wird nichts bewegt.
4. `is-text-enhanced` schaltet die CSS-Animationen ab, beide Wege schließen
   sich also aus.
5. Ohne Skript in einem Browser ohne Scroll-Timelines und bei
   `prefers-reduced-motion` gibt es keinen Effekt, der Text ist einfach da.
6. `zoom` beginnt bei Maßstab 0,7 und ist nach der Hälfte des Weges voll
   deckend (Weg 2: Keyframe bei 50 %, Weg 3: Deckkraft 400 ms, Maßstab
   800 ms; beim Verschwinden wartet die Deckkraft die erste Hälfte ab). Über
   den ganzen Weg eingeblendet wäre der Kasten fast schon in voller Größe,
   bevor man ihn sieht; so war es in 2.6.0 mit Startwert 0,92.
7. `fade` läuft länger als die anderen Effekte, weil ein Kasten, der schon
   beim Hereinkommen deckend ist, wie gar kein Effekt wirkt. Weg 2: Einblenden
   von `entry 0%` bis `cover 40%`, Ausblenden von `cover 60%` bis
   `exit 100%`. Weg 3: 1500 ms mit `ease-in-out` statt 600 ms mit `ease`.
8. `rotate` (seit 2.6.2) dreht den Kasten wie einen Propeller herein: zwei
   volle Umdrehungen um die Mitte, dabei wächst er von Maßstab 0,2 auf volle
   Größe; beim Verschwinden dreht er in derselben Richtung weiter und
   schrumpft. Start und Ziel nennen dieselben Funktionen (`rotate()` und
   `scale()`), sonst würde der Browser die Umdrehungen auf den kürzesten Weg
   kürzen. Weg 3: 1200 ms auslaufend beim Erscheinen, 1000 ms anlaufend beim
   Verschwinden, Deckkraft je 300 ms am äußeren Ende.
9. `dissolve` (seit 2.6.2) zeigt den Kasten durch ein Punktraster: eine Maske
   aus gekachelten Kreisen (Zelle 4 px), deren Radius von 0 auf 4 px wächst,
   bis die Punkte zur Fläche verschmelzen; beim Verschwinden schrumpfen sie.
   Der Radius ist die registrierte Variable `--jgor-st-dots` (`@property`,
   Firefox ab 128), nur so lässt er sich animieren. Ihr Startwert deckt die
   ganze Zelle, ohne Effekt oder ohne `@property` ist der Kasten also einfach
   vollständig. Weg 2 läuft über denselben langen Bereich wie `fade`, Weg 3
   über 2000 ms mit gleichmäßigem Tempo; eine kurze Ein- und Ausblendung von
   200 ms fängt Browser ab, die den Radius nicht animieren können. Eine erste
   Fassung mit Unschärfe war vom Einblenden kaum zu unterscheiden.

Stand 30.09.2026: Firefox hat Scroll-Timelines nur als Vorschau, im Release
156 und in ESR 140 läuft also Weg 3. Geprüft ist Weg 3 in Chromium mit
abgeschalteter Erkennung und in einem echten Firefox 146. Der Editor zeigt
nur die Auswahl, keine Vorschau des Effekts.

### Reihe: waagerechter Ablauf

Eine Reihe läuft auf einem von drei Wegen ab. `view/row.js` entscheidet je
Reihe und setzt die Klassen, `row/style.scss` enthält die Regeln.

| Modus | Wann | Verhalten |
| --- | --- | --- |
| stufenlos | Browser mit Scroll-Timelines | Spur folgt dem Scrollweg, Bewegung nur in CSS |
| stufenweise | alle anderen, derzeit Firefox | Script meldet den Index, die Spur gleitet in 600 ms dorthin |
| untereinander | reduzierte Bewegung, ein Text passt nicht in den Ausschnitt, oder ohne Script | Schritte wie gewöhnliche Schritte |

1. Mit `is-sideways` klebt `.jgor-st-row__viewport` an derselben Stelle und in
   derselben Größe wie die Bühne (`top: --jgor-st-offset`, bei begrenzter
   Bühne deren Seitenverhältnis) und schneidet mit `overflow: clip` ab. Die
   Spur ist ein Flex-Container, jeder Schritt so breit und hoch wie der
   Ausschnitt.
2. Unter dem Ausschnitt hält `::after` den Scrollweg frei: (Anzahl − 1) ×
   `--jgor-st-step-min`. Jeder weitere Schritt braucht also so viel Scrollweg,
   wie ein Schritt der Story hoch ist. Beim Standardwert 100 ist die Reihe
   genauso hoch wie untereinander, ein Wechsel des Modus verschiebt dann
   nichts.
3. Stufenlos: Die Reihe trägt eine benannte View-Timeline
   (`view-timeline: --jgor-st-row block`, Einzug `--jgor-st-offset`), die Spur
   läuft darauf von `exit-crossing 0%` (der Ausschnitt beginnt zu kleben) bis
   `exit-crossing <Scrollweg>`. Der Bereich `contain` aus dem Prototyp stimmt
   nur, wenn der Ausschnitt bildschirmhoch ist.
4. Stufenweise: Je Schritt ab dem zweiten liegt ein Sentinel
   (`.jgor-st-row__sentinel`) dort, wo die Bildschirmoberkante steht, wenn der
   halbe Weg zu diesem Schritt gescrollt ist. Ein `IntersectionObserver` über
   dem Bereich oberhalb der Oberkante zählt die Sentinels, die dort angekommen
   sind; die Zahl ist der Index (`--jgor-st-row-index`). Zählen stimmt auch
   nach Sprüngen, die mehrere Sentinels auf einmal über die Kante tragen.
   Bewegt wird nur per CSS-Übergang, nichts im Scroll-Handler.
5. Untereinander bleibt eine Reihe, wenn ein Textkasten samt Innen- und
   Außenabstand seines Schritts höher als die Bühne ist (der Ausschnitt
   schnitte ihn sonst ab), bei `prefers-reduced-motion` und solange das Script
   nicht gelaufen ist. Gemessen wird gegen die Bühne, das Ergebnis hängt also
   nicht vom aktuellen Modus ab; ein `ResizeObserver` prüft nach
   Größenänderungen neu.
6. Medienwechsel: Für Schritte einer Reihe beobachtet `view/media.js` nicht
   den Schritt, sondern eine Mittellinie darin (`.jgor-st-row__marker`). Der
   Ausschnitt schneidet sie ab, solange weniger als die Hälfte des Schritts
   darin liegt; so zählt immer nur ein Schritt der Reihe. Das Feld in der
   Fenstermitte aus dem Prototyp versagt, wenn die Story nicht mittig im
   Fenster steht. Untereinander ist die Linie so hoch wie der Schritt und
   vertritt ihn.
7. Tastaturfokus (`:focus-visible`) und Anker (`hashchange`, `load`) auf einen
   Schritt außerhalb des Ausschnitts: Das Script scrollt zur Position dieses
   Schritts. Der Browser kann das nicht selbst, weil die Lage des Schritts vom
   Scrollweg abhängt. Ein Mausklick verschiebt nichts. Ein zweiter Klick auf
   denselben Anker-Link landet am Anfang der Reihe, weil der Browser dafür
   kein Ereignis meldet.
8. Schreibrichtung von rechts nach links: `:dir(rtl)` kehrt die Richtung der
   Spur um (`--jgor-st-row-direction`).

Geprüft in Chromium 154 (stufenlos) und in einem echten Firefox 146
(stufenweise), nicht in Safari.

### Nachspann unter der begrenzten Bühne

Bei begrenzter Bühne ist die Bühne niedriger als der Bildschirm. Ohne
Nachspann bliebe darunter während aller Schritte weißer Raum, denn Inhalt nach
dem Block kommt erst nach dem letzten Schritt.

1. Der Filter `render_block_scrollstage/after` (Priorität `PHP_INT_MAX`)
   nimmt das fertige Markup aus dem Inhalt und legt es je Blockinstanz in
   einer `WeakMap` ab (`jgor_st_after_store()`). `story/render.php` holt es
   über `$block->inner_blocks` ab und setzt es als `.jgor-st-story__after`
   hinter die Schrittspalte. Weil der Block selbst leer rendert, würde WordPress
   ab 6.9 sein Stylesheet wieder entfernen; `enqueue_empty_block_content_assets`
   verhindert das.
2. Ohne Skript und ohne Bühnenbegrenzung liegt der Nachspann in Grid-Zeile 2,
   also wie gewöhnlicher Inhalt nach der Story.
3. Mit Begrenzung misst `view.js` per `ResizeObserver` die Höhen von Bühne und
   Nachspann (`--jgor-st-stage-h`, `--jgor-st-after-h`) und setzt
   `is-after-pinned`. Der Nachspann rückt in Zeile 1, klebt mit
   `top: Abstand + Bühnenhöhe` direkt unter der Bühne und liegt mit
   `z-index: 2` über den Schritten.
4. Bühne (`margin-bottom`) und Schrittspalte (`padding-bottom`) wachsen um
   die Höhe des Nachspanns. So lösen sich Bühne und Nachspann im selben Moment,
   und die Standzeit der Medien bleibt so lang wie ohne Nachspann.
5. Der Nachspann bekommt die erste deckende Hintergrundfarbe ab der Story
   aufwärts (`--jgor-st-after-bg`), sonst schienen die weißen Schritttexte
   durch. Eine eigene Hintergrundfarbe am Block hat Vorrang.

Der Nachspann ist nur für Inhalt gedacht, der zur Geschichte gehört.

### Folgenden Inhalt hochziehen (`pullContent`)

Für gewöhnlichen Seiteninhalt nach der Story, der schon unter dem ersten Bild
erscheinen soll.

1. `view.js` nimmt beim Laden die Geschwister nach der Story bis zur nächsten
   Story (`followingContent()`) und hängt sie einmal in `.jgor-st-follow`
   innerhalb von `.jgor-st-story__after` um (hinter einen vorhandenen
   Nachspann). Danach gilt der Mechanismus des Nachspanns: Der Browser hält
   den Inhalt per `position: sticky` unter der Bühne fest.
2. Die Reihenfolge im Dokument bleibt gleich, der Inhalt folgt weiter den
   Schritten. Ohne Skript oder ohne `ResizeObserver` bleibt er, wo er ist.
3. `.jgor-st-follow` hat oben denselben Abstand wie der Nachspann; das letzte
   Element verliert den unteren Außenabstand, wie es Themes für das letzte
   Element des Inhalts tun (GeneratePress: `.entry-content>p:last-child`).
4. Nur innerhalb des Elternelements: Liegt die Story in einer Gruppe, wird nur
   der Inhalt dieser Gruppe hochgezogen. Theme-Regeln der Form
   `.entry-content > …` greifen für den umgehängten Inhalt nicht mehr.

Verworfen: Verschieben per `transform` bei jedem Scroll-Frame. Der Browser
scrollt auf einem eigenen Thread, die Verschiebung hinkt einen Frame
hinterher, der Text flattert (Plugin 2.3.0). Scroll-gesteuerte
CSS-Animationen wären ruckelfrei, laufen in Firefox aber nicht ohne Flag.

### Abstand unter der Story

Jede Story ohne Nachspann hat `margin-bottom: clamp(1.5rem, 4vh, 2.5rem)`, weil
das letzte Medium mit harter Kante endet. Ein am Block gesetzter Außenabstand
hat Vorrang (Inline-Style). Beim Hochziehen gilt der Abstand ebenfalls.

Die Textbreite hängt an einer Container-Query auf der Schrittspalte, nicht an
einer Media-Query: Die Spalte ist je nach Theme-Layout und Bühnenbegrenzung
viel schmaler als das Fenster.

## 5. Themes

Das Plugin enthält keinen Theme-Code. Volle Breite liefert das Theme über
`alignfull`, die Blöcke setzen nur ihre eigenen Klassen und Variablen. Eine
Seitenvorlage für GeneratePress (2.0.0 bis 2.3.2) wurde in 2.4.0 entfernt,
weil sie nur ein Theme betraf und Plugin Check die Theme-Hooks bemängelte.

## 6. Barrierefreiheit

* Nur das sichtbare Bühnenelement steht im Accessibility-Baum; die übrigen
  tragen `aria-hidden`, serverseitig gesetzt und vom Skript mitgeführt.
* `prefers-reduced-motion` schaltet die Überblendung und die Texteffekte ab,
  lässt Videos stehen und spielt Reihen untereinander ab.
* In einer Reihe holt der Tastaturfokus den Schritt, in dem er landet, in den
  Ausschnitt.
* Der Editor weist darauf hin, wenn einem Bild der Alternativtext fehlt.
* Voreingestellt ist heller Text auf abgedunkeltem Medium; eine am Block
  gewählte Textfarbe hat Vorrang. Ein Textkasten mit eigenem Hintergrund
  nimmt die Textfarbe des Themes.

## 7. Medien vorbereiten

| Modus | Seitenverhältnis | Auflösung |
| --- | --- | --- |
| Ausschnitt füllen | beliebig, Fokuspunkt je Schritt setzen | Bildschirmhöhe × Seitenverhältnis, praktisch etwa 2560 px Breite |
| Ganzes Medium zeigen | für alle Schritte gleich wählen | Breite der Bühne genügt |
| Bild für Hochformat (je Schritt, optional) | hochkant, etwa 9:16 bis 9:20 | Bildschirmhöhe, praktisch etwa 1440 × 2560 px |

Das `sizes`-Attribut wird aus dem Seitenverhältnis berechnet
(`max(100vw, <ratio>vh)`), weil ein bildschirmhohes Querformat eine breitere
Datei braucht als das Fenster. Ohne das lädt der Browser zu kleine Dateien.

## 8. Werkzeuge

```bash
npm install            # einmalig
npm run build          # Blöcke bauen (Pflicht vor Deploy und Release)
npm run start          # Entwicklungsmodus
npm run lint:js        # ESLint, mit -- --fix auch korrigieren
npm run lint:css       # Stylelint
composer check         # PHPCS (WPCS) und PHPStan Level 6
wp i18n make-pot plugin plugin/languages/scrollstage.pot \
  --domain=scrollstage --exclude=build,node_modules --package-name="Scrollstage"
```

`plugin/build/` liegt nicht im Repo. Vor jedem Paket oder Deploy muss
`npm run build` gelaufen sein.

## 9. Weg ins WordPress-Verzeichnis

1. `npm run build`, danach alle Prüfwerkzeuge grün.
2. `.pot` neu erzeugen, Version in Header, Konstante, `readme.txt`
   (`Stable tag`), `package.json` und allen vier `block.json` gleichziehen.
3. Vier Screenshots als `assets/screenshot-1..4.png` außerhalb des Plugins,
   Motiv 1 als animiertes PNG aus `docs/scrollstage-hero.mp4`; Icon und
   Banner ebenfalls animiert, aus `assets/icon.svg` und `docs/banner.svg`
   im Browser gerendert (kleine Größen aus den großen skalieren).
4. `./build.sh`: prüft die Versionsnummern, baut die Blöcke, exportiert
   `plugin/` ohne die Einträge aus `.distignore` (`src/`, `.po`,
   `.mo`, `.json`, `.l10n.php`) und schreibt
   `~/dev/jgorres-im-WP-Repository/scrollstage-<version>.zip`.
5. Plugin Check laufen lassen, dann einreichen. Erwartung: keine Fehler,
   keine Warnungen.
6. Nach Freischaltung SVN: `trunk` plus `tags/<version>`, Assets nach
   `assets/`.

Übersetzungen laufen über translate.wordpress.org; im Paket liegt nur die
`.pot`.

## 10. Basisplugin

Stand 2.4.0 (Commit b652234, Git-Tag `basisplugin-2.4.0`) ist das
**Basisplugin**: drei Blöcke, senkrechtes Scrollen, Überblenden, Nachspann,
Hochziehen. Es funktioniert und dient bei allen Erweiterungen als
Startpunkt, auf den sich per `git checkout basisplugin-2.4.0` zurückgehen
lässt.

Die Erweiterungen und die beiden Testsites dazu stehen in `erweiterungen.md`.

## 11. Entschiedene Fragen

| Frage | Entscheidung | Grund |
| --- | --- | --- |
| Name | Scrollstage | „Scrollytelling" ist im Verzeichnis vergeben und zu generisch |
| Sprache der Oberfläche | Englisch | translate.wordpress.org übersetzt von en_US |
| Themes | nur Blöcke, kein Theme-Code | volle Breite kommt aus dem Theme, kein Sonderfall für ein einzelnes Theme |
| Animationsbibliothek | keine | IntersectionObserver genügt, knapp 7 KB (komprimiert 2,2 KB) statt 70 KB |
| Editor-Vorschau | vereinfacht, ohne klebende Bühne | der Editor hat einen eigenen Scroll-Container |
| Nachspann ohne Bühnenbegrenzung | erlaubt, folgt der Story wie normaler Inhalt | beim Umschalten der Begrenzung geht nichts verloren |
| Nur ein Nachspann je Story | nicht erzwungen, mehrere werden nacheinander ausgegeben | die Sperre bräuchte `@wordpress/data` als zusätzliche Abhängigkeit |
| Reihe ohne Script | untereinander, auch in Browsern mit Scroll-Timelines | nur das Script erkennt einen Text, der nicht in den Ausschnitt passt und abgeschnitten würde |
| Reihe in Firefox | stufenweises Gleiten | Scroll-Timelines fehlen dort, und `transform` im Scroll-Handler flattert; am Prototyp abgenommen |
| Texteffekte in einer Reihe | keine | die Effekte setzen einen Kasten voraus, der von unten kommt und oben geht |

## 12. Onlinehilfe im WordPress Playground

Die Hilfe zum Plugin ist eine eigene WordPress-Site, die im WordPress
Playground läuft: Der Leser sieht die Blöcke live und kann sie, als
Administrator angemeldet, im Editor ausprobieren. Jeder Aufruf baut die Site
im Browser neu auf; Änderungen eines Besuchers gehen mit dem Schließen des
Tabs verloren.

### Vorlagen-Site

Gepflegt wird die Hilfe auf der lokalen Site `https://scrollstage.local`
(WordPress aktuell, PHP 8.4, Admin `jgorres`):

- Theme Twenty Twenty-Five aktiv, GeneratePress installiert und inaktiv.
- Plugins aktiv: Plugin Check, Simple Page Ordering, Polylang (freie Version),
  Scrollstage aus dem Release-ZIP (kein Symlink auf das Repo).
- Polylang: Englisch ist Standardsprache ohne Präfix, Deutsch liegt unter
  `/de/`. Die Option „Startseiten-URL enthält den Sprachcode" ist gesetzt,
  damit die deutsche Startseite `/de/` heißt.
- 13 Seiten je Sprache, paarweise als Übersetzungen verknüpft: Startseite,
  Installation, Blocks (Story, Step, Row, Afterword), Settings (Media,
  Typography and text box, Text effects, Step height), FAQ.
- Deutsche Texte in der du-Form. Blocknamen und Schalter bleiben englisch,
  solange die Oberfläche des Plugins nicht übersetzt ist.

Besonderheiten der Site:

| Teil | Umsetzung |
| --- | --- |
| Navigation | Seitenliste plus Block `polylang/navigation-language-switcher`. Die freie Polylang-Version übersetzt `wp_navigation` nicht; die Seitenliste filtert selbst je Sprache. Die Reihenfolge kommt aus dem Seitenbaum (Simple Page Ordering) |
| Kopf | angepasster Template-Teil `header`: Website-Logo (Banner aus der Mediathek, 560 px) statt Site-Titel, Navigation in eigener Zeile darunter. Der Logo-Link folgt der Sprache |
| Fuß | angepasster Template-Teil `footer`: ohne Logo, ohne die Platzhalter Blog, Events, Shop und Themes, mit dem Text „4050 / 2 = Twenty Twenty-Five" |
| Startseite | eigene Vorlage `home-menu-below` (wie `page-no-title`, ohne Kopf), nur den beiden Startseiten zugewiesen; beginnt mit einer Beispiel-Story aus fünf Schritten ohne Abdunklung. Erster Schritt ist das animierte Banner (APNG) ohne Text, eingebunden nur über `mediaUrl` (`mediaId` 0): mit `mediaId` gäbe das Plugin ein `srcset` aus, und die verkleinerten Fassungen sind Standbilder. Die Überschrift des zweiten Schritts ist die `h1`. Direkt hinter der Story steht das Menü als Gruppe im Seiteninhalt (ohne Logo, Position „sticky", Hintergrund `base`): es bleibt am oberen Rand hängen, sobald es ihn erreicht, und verschwindet wieder, wenn man in die Story zurückscrollt |
| FAQ | Accordion-Block des Core (`core/accordion`), sieben Einträge |
| Medien | Beispiel-Story mit dem Testvideo am Ende der Seite |

Medien: vier Fotos aus dem WordPress Photo Directory (CC0, auf 2560 px
verkleinert) und das selbst erzeugte Testvideo. Quelle und Fotograf stehen in
der Bildbeschreibung der Mediathek. Bilder von Picsum/Unsplash werden bewusst
nicht verwendet, weil deren Lizenz bei WordPress.org nicht als GPL-kompatibel
gilt.

### Bundle

`playground/` ist ein Blueprint-Bundle: `blueprint.json` in der Wurzel, alle
weiteren Dateien werden daraus als `bundled`-Ressourcen gelesen.

| Datei | Inhalt |
| --- | --- |
| `blueprint.json` | Schritte für den Playground |
| `import.php` | baut die Site im Playground aus `content.json` auf |
| `export.php` | liest die Vorlagen-Site aus (läuft lokal per `wp eval-file`) |
| `build.sh` | ruft den Export auf und kopiert das aktuelle Release-ZIP |
| `content.json` | erzeugt: Sprachen, Polylang-Einstellungen, Optionen, Beiträge |
| `uploads.zip` | erzeugt: Inhalt von `wp-content/uploads` |
| `scrollstage.zip` | erzeugt: Kopie von `scrollstage-<version>.zip` |

Nach jeder Änderung an der Vorlagen-Site:

```
playground/build.sh
```

Das Script erwartet das Release-ZIP der Version aus dem Plugin-Header; fehlt
es, zuerst `./build.sh` im Repo ausführen.

Der Weg der Inhalte ist JSON plus Import-Script, nicht WXR und nicht SQL: WXR
vergibt neue IDs und überträgt die Sprachzuordnung von Polylang nicht
zuverlässig, ein Dump aus MariaDB müsste im Playground erst für SQLite
übersetzt werden.

`export.php` schreibt Seiten, Beiträge, Navigation, synchronisierte Vorlagen,
angepasste Templates und Template-Teile sowie Anhänge mit ID, Sprache,
Übersetzungspartnern, Elternseite, Reihenfolge, Meta-Feldern und den Begriffen
`wp_theme` und `wp_template_part_area`. Die Adresse der Site wird durch den
Platzhalter `{{JGOR_ST_HELP_SITE_URL}}` ersetzt.

`import.php` läuft nur unter WP-CLI und nur mit dem Argument `confirm-wipe`,
weil es zuerst alle vorhandenen Inhalte dieser Beitragstypen löscht. Es legt
die Sprachen an, lädt fehlende Sprachpakete nach und fügt die Beiträge mit
ihren ursprünglichen IDs ein (`import_id`), damit ID-Verweise in
Block-Attributen (Medien der Schritte, Website-Logo) stimmen.

### Schritte des Blueprints

1. Anmelden; Twenty Twenty-Five aktivieren, GeneratePress installieren.
2. Plugin Check, Simple Page Ordering und Polylang von WordPress.org,
   Scrollstage aus `scrollstage.zip`.
3. Akismet und Hello Dolly löschen, falls vorhanden. Das geschieht per
   `runPHP` mit `delete_plugins()`: `wp plugin delete` als `wp-cli`-Schritt
   bricht im Playground ab, weil WP-CLI dafür einen Unterprozess startet.
4. Willkommens-Dialog des Editors abschalten (`updateUserMeta`,
   `wp_persisted_preferences`).
5. `uploads.zip` entpacken, `import.php` und `content.json` ablegen, Import
   per `wp eval-file`.
6. `wp rewrite flush` als eigener Schritt. Im Import-Lauf kennt Polylang die
   Sprachen noch nicht; dort gebaute Regeln hätten kein `/de/`, und alle
   deutschen Seiten lieferten 404. `import.php` verwirft die Regeln deshalb
   nur.
7. Hilfsdateien wieder entfernen.

### Prüfen

Ohne Server, Ergebnis in ein Verzeichnis schreiben und in der SQLite-Datei
`wp-content/database/.ht.sqlite` nachsehen:

```
npx @wp-playground/cli@latest run-blueprint --blueprint=./playground/ \
  --blueprint-may-read-adjacent-files --mount-before-install=<verzeichnis>:/wordpress
```

Im Browser (endet nicht von selbst, Adresse `http://127.0.0.1:9400`):

```
npx @wp-playground/cli@latest server --blueprint=./playground/ \
  --blueprint-may-read-adjacent-files
```

Der Server baut die Site nur beim Start. Nach `playground/build.sh` muss er
neu gestartet werden. Das CLI verlangt laut Paket Node 24, lief am 01.10.2026
aber mit Node 20.

### Offen

- Ablage: öffentliches GitHub-Repo, aus dem der Playground das Bundle über
  `?blueprint-url=…` lädt. Eigener Webspace scheidet wegen des Traffics aus.
- Nach der Freischaltung bei WordPress.org: Scrollstage im Blueprint per Slug
  statt aus dem ZIP, zusätzlich `assets/blueprints/blueprint.json` im SVN für
  den Knopf „Live Preview".
- `uploads.zip` ist rund 13 MB groß, gut die Hälfte davon das Video. Das
  verlängert jeden Start der Hilfe.

## Änderungen

| Version | Datum | Änderung |
| --- | --- | --- |
| 1.0 | 20.09.2026 | Erste Fassung zum Plugin-Stand 2.0.1 |
| 1.1 | 20.09.2026 | Hinweis auf fehlendes GeneratePress ergänzt (Plugin 2.1.0) |
| 1.2 | 20.09.2026 | Begrenzte Bühne übernimmt das Seitenverhältnis des Mediums (Plugin 2.1.1) |
| 1.3 | 21.09.2026 | Nachspann-Block `after`, klebt unter der begrenzten Bühne (Plugin 2.2.0) |
| 1.4 | 21.09.2026 | Folgenden Inhalt hochziehen, Abstand unter der Story (Plugin 2.3.0) |
| 1.5 | 21.09.2026 | Hochziehen per Sticky statt Scroll-Transform, kein Flattern mehr (Plugin 2.3.1) |
| 1.6 | 29.09.2026 | Admin-Hinweis auf fehlendes GeneratePress entfernt (Plugin 2.3.2) |
| 1.7 | 29.09.2026 | build.sh, .distignore, Assets und animierte Screenshots beschrieben |
| 1.8 | 29.09.2026 | Seitenvorlage für GeneratePress entfernt (Plugin 2.4.0) |
| 1.9 | 30.09.2026 | Abschnitt Basisplugin (Tag basisplugin-2.4.0) |
| 1.10 | 30.09.2026 | Quellen der Story in Module geteilt (`view/`, `style/`), Klassen von `story` mit `is-effect-fade`, Verweis auf erweiterungen.md (Plugin 2.4.1) |
| 1.11 | 30.09.2026 | Block-Einstellungen von `step`: Typografie am Schritt, Hintergrund, Rahmen, Schatten und Innenabstand am Textkasten (Plugin 2.5.0) |
| 1.12 | 30.09.2026 | Effekte der Textkästen: `textEffect` an Story und Schritt, Scroll-Timelines mit Rückfall auf `view/text.js` (Plugin 2.6.0) |
| 1.13 | 30.09.2026 | `zoom` sichtbar gemacht: Start bei 0,7, Deckkraft nach dem halben Weg; `fade` verlängert; Fallback in Firefox 146 nachgemessen (Plugin 2.6.1) |
| 1.14 | 30.09.2026 | Effekte `rotate` und `dissolve` (Plugin 2.6.2) |
| 1.15 | 30.09.2026 | Block `row`: Reihe von Schritten, die waagerecht abläuft; `view/row.js`, `view/support.js`, Medienwechsel über Mittellinie (Plugin 2.7.0) |
| 1.16 | 30.09.2026 | readme: Beschreibung mit Erklärung von Scrollytelling, Blöcke in beliebiger Zahl, Donate link (Plugin 2.7.1); am Plugin selbst nichts geändert |
| 1.17 | 30.09.2026 | `includes/admin.php`: Spendenlink (Ko-fi) in der Zeile des Plugins in der Plugin-Liste über `plugin_row_meta` (Plugin 2.7.2) |
| 1.18 | 30.09.2026 | `.distignore` liegt in der Wurzel des Repos statt in `plugin/`: Plugin Check meldete die versteckte Datei auf den Testsites, die den Ordner per Symlink laden. Paket unverändert |
| 1.19 | 30.09.2026 | Projekt von `~/dev/scrollstage` nach `~/dev/jgorres-im-WP-Repository/scrollstage` umgezogen; Symlinks der Testsites nachgezogen |
| 1.20 | 01.10.2026 | Attribut `minHeight` am Schritt: eigene Schritthöhe, damit mehrere Textkästen nacheinander vor demselben Medium durchlaufen (Plugin 2.8.0) |
| 1.21 | 01.10.2026 | Editor mit klassischem Theme: Schritte und Reihen füllen die Story wieder, statt auf ihren Inhalt zu schrumpfen (Plugin 2.8.1) |
| 1.22 | 01.10.2026 | readme: Description nennt mehrere Textkästen vor einem Medium (Plugin 2.8.2); am Plugin selbst nichts geändert |
| 1.23 | 01.10.2026 | „Tested up to" aus dem Plugin-Header entfernt: Die automatische Prüfung bei der Einreichung (WordPress.org) lehnt die Zeile dort ab (`plugin_header_tested_up_to_not_allowed`), sie steht nur noch in `readme.txt`; der lokale Plugin Check 2.1.0 meldet das nicht (Plugin 2.8.3) |
| 1.24 | 01.10.2026 | Abschnitt 12: Onlinehilfe im WordPress Playground (Vorlagen-Site `scrollstage.local`, Bundle `playground/`); am Plugin selbst nichts geändert |
| 1.25 | 01.10.2026 | Video startet erst, wenn die Bühne im Viewport ist, und hält an, sobald die Story den Bildschirm verlässt: zweiter IntersectionObserver auf der Bühne in `view/media.js`; vorher lief das Video des ersten Schritts ab dem Laden der Seite und das letzte aktive Video nach der Story weiter (Plugin 2.8.4) |
| 1.26 | 01.10.2026 | Abschnitt 12, Startseiten der Onlinehilfe: animiertes Banner als erster Schritt, Vorlage `home-menu-below` ohne Kopf, Menü sticky unter der Story, Abdunklung aus; Bundle neu exportiert (Plugin 2.8.4); am Plugin selbst nichts geändert |
| 1.27 | 01.10.2026 | Bild für Hochformat je Schritt (`portraitId`, `portraitUrl`, `portraitFocalPoint`): Ausgabe als `picture` mit `source media="(orientation: portrait)"`, eigener Fokuspunkt, eigenes Verhältnis der begrenzten Bühne (Plugin 2.9.0) |
