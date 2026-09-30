# Scrollstage – Erweiterungen

Version: 1.4 · Stand: 30.09.2026 · Plugin-Version: 2.6.1

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

Entschieden am 30.09.2026: Die Erweiterungen kommen in dasselbe Plugin. Ein
getrenntes Pro-Plugin gibt es nicht; das Basisplugin braucht deshalb keine
Hooks oder Ereignisse als öffentliche Schnittstelle.

## 2. Schritte

| Schritt | Inhalt | Plugin-Version | Aufwand | Stand |
| --- | --- | --- | --- | --- |
| 0 | Modularisierung ohne Verhaltensänderung | 2.4.1 | 1 Tag | erledigt |
| 1 | Schriftformatierung und Textkasten über Block-Supports | 2.5.0 | ½ Tag | erledigt |
| 2 | Effekte für den Textkasten | 2.6.0 | 1–2 Tage | erledigt |
| 3 | Container-Block „Scrollstage-Reihe" | 2.7.0 | 3–5 Tage | offen |

Der Aufwand ist geschätzt.

### Schritt 0: Modularisierung (2.4.1)

Vorbereitung, damit die folgenden Schritte nicht in einer einzigen Datei
landen. Das Verhalten im Frontend und im Editor ist unverändert.

* `src/story/view.js` ist auf `src/story/view/` verteilt: Medienwechsel
  (`media.js`), Anheften des Nachspanns (`after.js`), Hochziehen des
  folgenden Inhalts (`pull.js`), gemeinsame Selektoren (`selectors.js`).
* `src/story/style.scss` ist in Teildateien unter `src/story/style/` zerlegt.
* `jgor_st_story_classes()` baut die Klassen der Story. Die Funktion liegt in
  `includes/blocks.php`, weil `render.php` je Block neu eingebunden wird.
* Effekte werden einheitlich als Klasse `is-effect-<name>` ausgegeben;
  `has-fade` heißt jetzt `is-effect-fade`, die ungenutzte Klasse `no-fade`
  entfällt.

Nachweis: Vergleich von Basis gegen Pro (Abschnitt 3) nach jedem Teilschritt,
je 42 Screenshots pixelgleich und 84 Zustände gleich, beim letzten Teilschritt
bis auf den umbenannten Klassennamen. Nicht im Browser geprüft sind der harte
Wechsel (`transition` = none) und eine Story mit Nachspann-Block, weil keine
Testseite sie nutzt.

### Schritt 1: Schriftformatierung und Textkasten (2.5.0)

Block-Supports in `src/step/block.json`, keine eigenen Bedienelemente, keine
eigenen Attribute.

* Typografie am Schritt: Schrift, Schnitt, Zeichenabstand, Schreibweise,
  Dekoration und Textausrichtung, zusätzlich zu Größe und Zeilenhöhe.
* Schatten als neue Einstellung.
* Entschieden am 30.09.2026: Der Textkasten ist ein echter Kasten.
  Hintergrund, Rahmen, Eckenradius, Schatten und Innenabstand wirken auf den
  Textkasten statt auf den ganzen Schritt. Dafür kamen
  `jgor_st_step_box_attributes()` in `includes/blocks.php` und
  `src/step/text-box.js` dazu; Einzelheiten in `konzept.md`, Abschnitt 3.

Nachweis: Testseite `/scrollstage-typography/` auf Pro (Seite 96, sechs
Schritte: drei zur Schrift, drei zum Kasten), Werte im Frontend und im Editor
gemessen; Vergleich Basis gegen Pro für die drei gemeinsamen Seiten gleich.
Geprüft nur unter WordPress 7.1.2, nicht unter der Mindestversion 6.7.

### Schritt 2: Effekte für den Textkasten (2.6.0)

* Attribut `textEffect` an der Story (`none`, `fade`, `slide`, `zoom`), je
  Schritt überschreibbar; der Wert der Story kommt als Block-Kontext an.
* Umsetzung mit CSS `animation-timeline: view()` in
  `src/step/style/_text-effects.scss`.
* Fallback für Browser ohne Scroll-Timelines: `src/story/view/text.js` setzt
  per `IntersectionObserver` `is-visible`, der Effekt läuft als gewöhnlicher
  Übergang.
* Bei `prefers-reduced-motion` sind die Effekte aus.

Browserstand am 30.09.2026 laut MDN: Chrome ab 115 und Safari ab 26 haben
Scroll-Timelines, Firefox nur als Vorschau. Im Firefox-Release 156 und in
ESR 140 läuft der Fallback.

Nachweis: Testseite `/scrollstage-text-effects/` auf Pro (Seite 104, fünf
Schritte: wie Story, `fade`, `zoom`, `none`, wie Story mit Kasten). In
Chromium gemessen: halb hereingescrollt Deckkraft 0,5 und halber Versatz,
mittig voll sichtbar, halb hinausgescrollt wieder 0,5. Fallback in Chromium
mit abgeschalteter Erkennung geprüft (Klassen, Endzustände), reduzierte
Bewegung auf beiden Wegen ohne Effekt, Auswahl im Editor an Story und Schritt.
Vergleich Basis gegen Pro für die drei gemeinsamen Seiten gleich.

Nachtrag 2.6.1: `zoom` war kaum wahrnehmbar. In einem echten Firefox 146
nachgemessen, lag der Maßstab bei halber Deckkraft schon bei 0,96. Jetzt
beginnt der Effekt bei 0,7, und der Kasten ist nach dem halben Weg voll
deckend. `fade` war ebenfalls zu schwach und läuft jetzt länger
(Fallback 1500 ms, mit Scroll-Timeline bis `cover 40%`). Der Fallback ist
damit auch in Firefox selbst geprüft (`fade`, `slide`, `zoom`).

### Schritt 3: Scrollstage-Reihe (2.7.0)

Neuer Container-Block, nur innerhalb einer Story erlaubt. Er nimmt Schritte
auf und spielt sie waagerecht ab:

* Die Reihe wird per Scroll-Animation seitlich verschoben.
* Der Observer misst innerhalb der Reihe waagerecht.
* Schritte vor und nach der Reihe laufen weiter senkrecht. Damit ist auch der
  Wechsel zwischen senkrechtem und waagerechtem Ablauf in einer Story möglich.

## 3. Testsites

Zwei lokale Sites mit demselben Inhalt (Datenbank-Kopie von
`plugintest.local` vom 30.09.2026, Twenty Twenty-Five), auf beiden ist nur
Scrollstage aktiv:

| Site | Scrollstage | Zweck |
| --- | --- | --- |
| `https://scrollstage-basis.local` | 2.4.0 aus `scrollstage-2.4.0.zip`, feste Kopie | Vergleichsstand |
| `https://scrollstage-pro.local` | Symlink auf `~/dev/scrollstage/plugin` | Entwicklungsstand |

Testseiten auf beiden: `/scrollstage-hero/`, `/scrollstage-standard-width/`
(begrenzte Bühne, Hochziehen) und `/scrollytelling-test/`. Jede hat fünf
Schritte, einer davon mit Video. Nur auf Pro liegen zusätzlich
`/scrollstage-typography/` für Schriftformatierung und Textkasten und
`/scrollstage-text-effects/` für die Texteffekte.

Vergleichslauf: je Seite 14 Scrollpositionen im Abstand von 400 px bei
1280×800. Screenshots mit reduzierter Bewegung, damit das Video steht; die
Zustände (Klassen, Inline-Variablen, aktives Medium, Video läuft oder steht,
Lage von Bühne und Nachspann, geladene Bilddatei) zusätzlich mit normaler
Bewegung. Weichen Screenshots ab, den Lauf zuerst wiederholen: Beim ersten
Aufruf einer frischen Site wichen zwölf Bilder als feines Rauschen ab, beim
zweiten mit demselben Build keines.

`plugintest.local` bleibt unverändert und zeigt über seinen Symlink ebenfalls
den Entwicklungsstand.

## 4. Regeln

* Nie `transform` im Scroll-Handler setzen: Das flattert in Firefox
  (Plugin 2.3.0). Festhalten per `position: sticky`, Bewegung nur über
  Scroll-Animationen des Browsers.
* Jede Stufe ist ein eigener Versionsstand: Konzept nachziehen, Prüflauf
  (`composer check`, `npm run lint:js`, `npm run lint:css`), Vergleich Basis
  gegen Pro, Commit.
* Für neue Funktionen bekommt `scrollstage-pro.local` eigene Testseiten; die
  drei gemeinsamen Seiten bleiben auf beiden Sites gleich.
* Die Testseiten auf `plugintest.local` bleiben, wie sie sind.

## Änderungen

| Version | Datum | Änderung |
| --- | --- | --- |
| 1.0 | 30.09.2026 | Erste Fassung: Plan für vier Erweiterungen in den Schritten 0 bis 3 |
| 1.1 | 30.09.2026 | Entscheidung für ein Plugin, Schritt 0 erledigt (Plugin 2.4.1), Testsites Basis und Pro |
| 1.2 | 30.09.2026 | Schritt 1 erledigt (Plugin 2.5.0): Schriftformatierung, Textkasten als echter Kasten, Testseite auf Pro |
| 1.3 | 30.09.2026 | Schritt 2 erledigt (Plugin 2.6.0): Texteffekte, Browserstand der Scroll-Timelines, Testseite auf Pro |
| 1.4 | 30.09.2026 | Nachtrag zu Schritt 2 (Plugin 2.6.1): `zoom` sichtbar gemacht, `fade` verlängert, Fallback in Firefox nachgemessen |
