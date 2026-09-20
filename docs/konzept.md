# Scrollstage – Konzept und Aufbau

Version: 1.0 · Stand: 20.09.2026 · Plugin-Version: 2.0.1

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
│   │   └── step/              ebenso, ohne view.js
│   ├── build/                 Ergebnis von "npm run build", nicht im Repo
│   ├── templates/fullwidth.php
│   └── languages/scrollstage.pot
├── docs/                      diese Doku
├── stubs/generatepress.php    für PHPStan
├── composer.json, phpcs.xml.dist, phpstan.neon.dist, phpstan-bootstrap.php
└── package.json
```

Namensregeln: Slug und Text-Domain `scrollstage`, Blöcke `scrollstage/story`
und `scrollstage/step`, Funktionen `jgor_st_`, Konstanten `JGOR_ST_`,
CSS-Klassen `jgor-st-`.

## 3. Aufbau der Blöcke

`story` ist der Rahmen, `step` das einzelne Kapitel. Beide rendern
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
   (`Stable tag`), `package.json` und beiden `block.json` gleichziehen.
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

## Änderungen

| Version | Datum | Änderung |
| --- | --- | --- |
| 1.0 | 20.09.2026 | Erste Fassung zum Plugin-Stand 2.0.1 |
