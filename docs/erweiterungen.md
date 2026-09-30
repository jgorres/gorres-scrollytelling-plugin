# Scrollstage – Erweiterungen

Version: 1.0 · Stand: 30.09.2026 · Plugin-Version: 2.4.0

## 1. Ausgangspunkt

Grundlage ist das Basisplugin 2.4.0 (Git-Tag `basisplugin-2.4.0`, siehe
`konzept.md`, Abschnitt 10). Auf diesen Stand lässt sich jederzeit per
`git checkout basisplugin-2.4.0` zurückgehen. Die Einreichung von 2.4.0 im
WordPress-Verzeichnis läuft unabhängig von den Erweiterungen.

Gewünscht sind vier Erweiterungen:

1. Schriftformatierung im Textfeld eines Schritts.
2. Effekte beim Erscheinen und Verschwinden des Textkastens.
3. Horizontale Scrollline.
4. Wechsel zwischen senkrechtem und waagerechtem Ablauf in einer Story.

Die Punkte 3 und 4 deckt ein gemeinsamer Block ab (Schritt 3).

## 2. Schritte

| Schritt | Inhalt | Plugin-Version | Aufwand |
| --- | --- | --- | --- |
| 0 | Modularisierung ohne Verhaltensänderung | 2.4.1 | 1 Tag |
| 1 | Schriftformatierung über Block-Supports | 2.5.0 | ½ Tag |
| 2 | Effekte für den Textkasten | 2.6.0 | 1–2 Tage |
| 3 | Container-Block „Scrollstage-Reihe" | 2.7.0 | 3–5 Tage |

Der Aufwand ist geschätzt.

### Schritt 0: Modularisierung (2.4.1)

Vorbereitung, damit die folgenden Schritte nicht in einer einzigen Datei
landen. Das Verhalten im Frontend und im Editor bleibt unverändert.

* `src/story/view.js` wird auf `src/story/view/` verteilt: Medienwechsel,
  Anheften des Nachspanns, Hochziehen des folgenden Inhalts.
* `src/story/style.scss` wird in Teildateien zerlegt.
* `story/render.php` bekommt eine Funktion, die die Klassen der Story baut.
* Effekte werden einheitlich als Klasse `is-effect-<name>` ausgegeben.

Nachweis: Screenshot-Vergleich der Testseiten auf `plugintest.local` vor und
nach dem Umbau.

### Schritt 1: Schriftformatierung (2.5.0)

Nur Block-Supports in `src/step/block.json`: `typography`, `spacing`,
`border`, `shadow`. Keine eigenen Bedienelemente, keine eigenen Attribute.

### Schritt 2: Effekte für den Textkasten (2.6.0)

* Attribut `textEffect` an der Story, je Schritt überschreibbar.
* Umsetzung mit CSS `animation-timeline: view()`.
* Fallback für Browser ohne Scroll-Timelines: `IntersectionObserver` setzt
  `is-visible`, der Effekt läuft als gewöhnlicher Übergang.
* Bei `prefers-reduced-motion` sind die Effekte aus.

Vor Beginn den Browserstand der Scroll-Timelines prüfen (Firefox lief bisher
nur mit Flag, siehe `konzept.md`, Abschnitt 4).

### Schritt 3: Scrollstage-Reihe (2.7.0)

Neuer Container-Block, nur innerhalb einer Story erlaubt. Er nimmt Schritte
auf und spielt sie waagerecht ab:

* Die Reihe wird per Scroll-Animation seitlich verschoben.
* Der Observer misst innerhalb der Reihe waagerecht.
* Schritte vor und nach der Reihe laufen weiter senkrecht. Damit ist auch der
  Wechsel zwischen senkrechtem und waagerechtem Ablauf in einer Story möglich.

## 3. Regeln

* Nie `transform` im Scroll-Handler setzen: Das flattert in Firefox
  (Plugin 2.3.0). Festhalten per `position: sticky`, Bewegung nur über
  Scroll-Animationen des Browsers.
* Jede Stufe ist ein eigener Versionsstand: Konzept nachziehen, Prüflauf
  (`composer check`, `npm run lint:js`, `npm run lint:css`), Test auf
  `plugintest.local`, Commit.
* Die Testseiten auf `plugintest.local` bleiben, wie sie sind.

## Änderungen

| Version | Datum | Änderung |
| --- | --- | --- |
| 1.0 | 30.09.2026 | Erste Fassung: Plan für vier Erweiterungen in den Schritten 0 bis 3 |
