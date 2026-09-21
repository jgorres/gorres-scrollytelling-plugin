# Scrollstage – Konzept und Aufbau

Version: 1.3 · Stand: 21.09.2026 · Plugin-Version: 2.2.0

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
│   │   ├── blocks.php         Kategorie, Registrierung, Bühnen-Markup
│   │   └── templates.php      Seitenvorlage für GeneratePress
│   ├── src/                   Quellen für wp-scripts
│   │   ├── story/             block.json, index/edit/save, render.php,
│   │   │                      style.scss, editor.scss, view.js
│   │   ├── step/              ebenso, ohne view.js
│   │   └── after/             Nachspann, ohne view.js
│   ├── build/                 Ergebnis von "npm run build", nicht im Repo
│   ├── templates/fullwidth.php
│   └── languages/scrollstage.pot
├── docs/                      diese Doku
├── stubs/generatepress.php    für PHPStan
├── composer.json, phpcs.xml.dist, phpstan.neon.dist, phpstan-bootstrap.php
└── package.json
```

Namensregeln: Slug und Text-Domain `scrollstage`, Blöcke `scrollstage/story`,
`scrollstage/step` und `scrollstage/after`, Funktionen `jgor_st_`, Konstanten `JGOR_ST_`,
CSS-Klassen `jgor-st-`.

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
| `overlayOpacity` | 0–90 | 35 | Abdunkelung der Medien in Prozent |
| `stickyOffset` | 0–200 | 0 | Abstand von oben in Pixeln |
| `minStepHeight` | 40–200 | 100 | Höhe eines Schritts in Prozent der Bildschirmhöhe |
| `transition` | fade, none | fade | Überblenden oder harter Wechsel |

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

Die Textbreite hängt an einer Container-Query auf der Schrittspalte, nicht an
einer Media-Query: Die Spalte ist je nach Theme-Layout und Bühnenbegrenzung
viel schmaler als das Fenster.

## 5. Seitenvorlage (GeneratePress)

`Scrollstage: full width` erscheint nur, wenn GeneratePress läuft
(`GENERATE_VERSION`). Sie folgt dem Aufbau von dessen `page.php`, setzt das
Sidebar-Layout per `generate_sidebar_layout` auf `no-sidebar` und umschließt
den Inhalt mit `.entry-content`. Daran hängt die alignfull-Regel des Themes:
Ein Block auf „Volle Breite" bricht randlos aus, gewöhnlicher Text behält
Breite und Abstände des Themes.

Bewusst nicht enthalten: ein Umschalten des Inhalts-Containers auf volle
Breite. Das hatte zur Folge, dass auch Fließtext neben der Story am Rand
klebte.

Fehlt GeneratePress, weist das Plugin auf den Bildschirmen „Plugins" und
„Themes" einmal darauf hin – je nach Lage mit Link zum Aktivieren oder zum
Installieren. Die Aktivierung wird nie blockiert, denn die Blöcke selbst
arbeiten mit jedem Theme.

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
3. Drei Screenshots als `assets/screenshot-1..3.png` außerhalb des Plugins.
4. Paket ohne `node_modules`, `src`, Konfigurationsdateien schnüren.
5. Plugin Check laufen lassen, dann einreichen. Drei Warnungen bleiben und
   sind beabsichtigt: Die Seitenvorlage löst die `generate_*`-Hooks des
   Themes aus, damit GeneratePress-Elements darin arbeiten. Fehler meldet
   der Check keine.
6. Nach Freischaltung SVN: `trunk` plus `tags/<version>`, Assets nach
   `assets/`.

Übersetzungen laufen über translate.wordpress.org; im Paket liegt nur die
`.pot`.

## 10. Entschiedene Fragen

| Frage | Entscheidung | Grund |
| --- | --- | --- |
| Name | Scrollstage | „Scrollytelling" ist im Verzeichnis vergeben und zu generisch |
| Sprache der Oberfläche | Englisch | translate.wordpress.org übersetzt von en_US |
| Fremde Themes | nur Blöcke, Vorlage bleibt GeneratePress | kein Code für Fälle, die niemand nutzt |
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
