# Scrollstage – Konzept und Aufbau

Version: 1.10 · Stand: 30.09.2026 · Plugin-Version: 2.4.1

## 1. Zweck

Ein Medium füllt den Bildschirm und bleibt stehen, während der Text des
aktuellen Schritts darüber hinwegscrollt. Erreicht der nächste Schritt die
Bildschirmmitte, wechselt das Medium dahinter. Der Effekt ist als „Spiegel-
Scrollytelling" bekannt.

Das Plugin ist für die Veröffentlichung im WordPress-Verzeichnis gedacht.
Oberfläche und Dokumentation im Plugin sind deshalb englisch, diese
Projektdoku ist deutsch.

## 2. Verzeichnisse

```
~/dev/scrollstage/
├── plugin/                    ausgeliefertes Plugin
│   ├── scrollstage.php        Header, Konstanten, Textdomain, Modul-Loader
│   ├── readme.txt             für das WordPress-Verzeichnis
│   ├── uninstall.php          derzeit ohne Daten zu löschen
│   ├── includes/
│   │   └── blocks.php         Kategorie, Registrierung, Klassen der Story,
│   │                          Bühnen-Markup
│   ├── src/                   Quellen für wp-scripts
│   │   ├── story/             block.json, index/edit/save, render.php,
│   │   │   │                  style.scss, editor.scss, view.js
│   │   │   ├── view/          Module des Frontend-Scripts: selectors, media,
│   │   │   │                  after, pull
│   │   │   └── style/         SCSS-Teildateien: layout, stage, stage-limited,
│   │   │                      steps, after, effects
│   │   ├── step/              ebenso, ohne view.js
│   │   └── after/             Nachspann, ohne view.js
│   ├── build/                 Ergebnis von "npm run build", nicht im Repo
│   ├── languages/scrollstage.pot
│   └── .distignore            Ausschlüsse für ZIP und SVN-Export
├── assets/                    WP.org-Assets: Screenshots, Icon, Banner (nicht im Paket)
├── build.sh                   Release-ZIP erzeugen
├── docs/                      diese Doku, banner.svg, Quellvideo der Aufnahme
├── stubs/                     zusätzliche Stubs für PHPStan (derzeit leer)
├── composer.json, phpcs.xml.dist, phpstan.neon.dist, phpstan-bootstrap.php
└── package.json
```

Namensregeln: Slug und Text-Domain `scrollstage`, Blöcke `scrollstage/story`,
`scrollstage/step` und `scrollstage/after`, Funktionen `jgor_st_`, Konstanten `JGOR_ST_`,
CSS-Klassen `jgor-st-`.

Seit 2.4.1 sind die Quellen der Story in Module geteilt. `view.js` und
`style.scss` bleiben die Einstiegspunkte, damit `block.json` und die Dateien
im Build gleich heißen:

| Datei | Inhalt |
| --- | --- |
| `view/selectors.js` | gemeinsame Selektoren |
| `view/media.js` | Medienwechsel per `IntersectionObserver` |
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

`story` ist der Rahmen, `step` das einzelne Kapitel, `after` der optionale
Nachspann hinter dem letzten Schritt. Alle rendern
serverseitig (`render.php`), gespeichert werden nur die Kindblöcke. Dadurch
wirken Änderungen am Markup sofort, ohne Beiträge neu zu speichern.

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

### Nachspann `after`

Ohne eigene Attribute; nimmt beliebige Blöcke auf, dazu Farben, Innenabstand
und Schrift über die Block-Supports. Erlaubt nur als Kind von `story`. Der
Editor zeigt ihn dort, wo er eingefügt wurde, im Frontend steht er immer hinter
dem letzten Schritt. Ein Innenabstand oben (`clamp(1.5rem, 4vh, 2.5rem)`) hält
die erste Überschrift von der Bühnenkante fern; ein am Block gesetzter Abstand
hat Vorrang.

## 4. Wie der Effekt entsteht

1. `story/render.php` liest die Medien der Kind-Schritte aus
   `$block->parsed_block['innerBlocks']` und baut daraus eine Bühne. Jeder
   Schritt bekommt genau ein Bühnenelement, auch ein Schritt ohne Medium
   (Klasse `is-empty`).
2. Bühne und Schrittspalte liegen in derselben Grid-Zelle. Die Bühne ist
   `position: sticky` und bildschirmhoch, die Schritte liegen mit `z-index`
   darüber.
3. `view.js` beobachtet die Schritte mit einem `IntersectionObserver`
   (`rootMargin: -45% 0px -45%`, also ein schmales Band in der Mitte) und
   schaltet das zugehörige Bühnenelement aktiv. Schritte ohne Medium laufen
   rückwärts bis zum letzten vorhandenen.
4. Ohne JavaScript bleibt das erste Medium sichtbar
   (`:not(.is-enhanced) .jgor-st-stage__item:first-child`).

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
* `prefers-reduced-motion` schaltet die Überblendung ab und lässt Videos
  stehen.
* Der Editor weist darauf hin, wenn einem Bild der Alternativtext fehlt.
* Voreingestellt ist heller Text auf abgedunkeltem Medium; eine am Block
  gewählte Textfarbe hat Vorrang.

## 7. Medien vorbereiten

| Modus | Seitenverhältnis | Auflösung |
| --- | --- | --- |
| Ausschnitt füllen | beliebig, Fokuspunkt je Schritt setzen | Bildschirmhöhe × Seitenverhältnis, praktisch etwa 2560 px Breite |
| Ganzes Medium zeigen | für alle Schritte gleich wählen | Breite der Bühne genügt |

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
   (`Stable tag`), `package.json` und allen drei `block.json` gleichziehen.
3. Vier Screenshots als `assets/screenshot-1..4.png` außerhalb des Plugins,
   Motiv 1 als animiertes PNG aus `docs/scrollstage-hero.mp4`; Icon und
   Banner ebenfalls animiert, aus `assets/icon.svg` und `docs/banner.svg`
   im Browser gerendert (kleine Größen aus den großen skalieren).
4. `./build.sh`: prüft die Versionsnummern, baut die Blöcke, exportiert
   `plugin/` ohne die Einträge aus `plugin/.distignore` (`src/`, `.po`,
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
| Animationsbibliothek | keine | IntersectionObserver genügt, rund 1 KB statt 70 KB |
| Editor-Vorschau | vereinfacht, ohne klebende Bühne | der Editor hat einen eigenen Scroll-Container |
| Nachspann ohne Bühnenbegrenzung | erlaubt, folgt der Story wie normaler Inhalt | beim Umschalten der Begrenzung geht nichts verloren |
| Nur ein Nachspann je Story | nicht erzwungen, mehrere werden nacheinander ausgegeben | die Sperre bräuchte `@wordpress/data` als zusätzliche Abhängigkeit |

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
