# Scrollstage – Erweiterungen

Version: 1.7 · Stand: 30.09.2026 · Plugin-Version: 2.7.2

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
| 3 | Container-Block „Scrollstage Row" (Reihe) | 2.7.0 | 3–5 Tage | erledigt |

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

Nachtrag 2.6.2: zwei weitere Effekte auf Wunsch. `rotate` dreht den Kasten
wie einen Propeller herein (zwei Umdrehungen, dabei von Maßstab 0,2 auf volle
Größe, beim Verschwinden in derselben Richtung weiter), `dissolve` setzt ihn
aus einem Punktraster zusammen und löst ihn wieder darin auf (eine erste
Fassung mit Unschärfe war vom Einblenden kaum zu unterscheiden). Die
Testseite hat dafür drei weitere Schritte (jetzt acht). Beide in Firefox 146
und in Chromium nachgemessen. Dabei fiel auf, dass der Propeller flackerte
oder ausblieb, wenn das Scrollen an der Kante des sichtbaren Bandes anhielt:
Der Observer sah den gedrehten und verkleinerten Kasten. Er beobachtet jetzt
ein leeres Element am Platz des Kastens.

### Schritt 3: Scrollstage-Reihe (2.7.0)

Neuer Container-Block `scrollstage/row` („Scrollstage Row"), nur innerhalb
einer Story erlaubt. Er nimmt Schritte auf und spielt sie waagerecht ab;
Schritte vor und nach der Reihe laufen weiter senkrecht. Damit ist auch der
Wechsel zwischen senkrechtem und waagerechtem Ablauf in einer Story möglich.
Die Mechanik steht in `konzept.md`, Abschnitte 3 und 4.

Umgesetzt in vier Teilschritten, je ein Commit:

| Teilschritt | Inhalt |
| --- | --- |
| 3a | Block, Einfügeregeln, Medien der Reihen-Schritte auf der Bühne; die Reihe läuft noch senkrecht |
| 3b | waagerechter Ablauf mit Scroll-Timeline, Medienwechsel über eine Mittellinie |
| 3c | stufenweiser Ablauf ohne Scroll-Timelines, untereinander bei zu hohem Text, Fokus und Anker |
| 3d | Version 2.7.0, readme, `.pot`, Doku |

Prototyp vom 30.09.2026: `docs/prototyp-reihe.html`, eine einzelne Datei ohne
Plugin, aufrufbar unter `https://scrollstage-pro.local/prototyp-reihe.html`
(Symlink im DocRoot der Pro-Site). Er zeigt die drei vorgesehenen Abläufe der
Reihe:

| Modus | Wann | Verhalten |
| --- | --- | --- |
| stufenlos | Browser mit Scroll-Timelines | Spur folgt dem Scrollweg, nur CSS (benannte View-Timeline, Bereich `contain`) |
| stufenweise | alle anderen, auch Firefox | Sentinels und `IntersectionObserver` setzen den Index, die Spur gleitet in 600 ms dorthin |
| untereinander | reduzierte Bewegung, oder ein Text passt nicht auf den Bildschirm | Schritte wie gewöhnliche Schritte |

`?modus=stufen` und `?modus=stapel` erzwingen den zweiten und dritten Modus.
Der Medienwechsel nutzt für alle Schritte ein kleines Feld in der
Bildschirmmitte (`rootMargin: -45%` an allen vier Seiten). Gemessen in
Chromium (alle drei Modi) und in Firefox 146 (stufenweise). Der Prototyp ist
am 30.09.2026 abgenommen, das stufenweise Gleiten in Firefox damit auch.

Der Block weicht in diesen Punkten vom Prototyp ab:

* Der Scrollweg je Schritt folgt `minStepHeight` der Story statt fest einer
  Bildschirmhöhe; beim Standardwert 100 ist das dasselbe.
* Der Bereich der Animation ist `exit-crossing` statt `contain`, damit der
  Abstand von oben und die begrenzte Bühne stimmen.
* Der Medienwechsel läuft über eine Mittellinie je Schritt statt über ein
  Feld in der Fenstermitte, das bei einer Story neben einer Seitenleiste
  versagt.
* Im stufenweisen Ablauf wird der Index gezählt (Sentinels oberhalb der
  Bildschirmoberkante) statt aus dem Sentinel in der Bildschirmmitte gelesen;
  das stimmt auch nach Sprüngen.
* Das waagerechte Layout setzt das Script (`is-sideways`), auch in Browsern
  mit Scroll-Timelines: Ohne Script wäre ein zu hoher Text abgeschnitten.
* „Der Text passt" misst den Textkasten samt Abständen gegen die Bühne statt
  gegen 85 % der Fensterhöhe.
* Neu: Tastaturfokus und Anker holen einen Schritt in den Ausschnitt;
  Schreibrichtung von rechts nach links.
* Schritte in einer Reihe haben keinen Texteffekt.

Nachweis: Testseite `/scrollstage-row/` auf Pro (Seite 109: zwei Schritte,
eine Reihe mit drei Schritten, ein Schritt; Story mit `slide`). Chromium 154
stufenlos und Firefox 146 stufenweise gemessen: Lage der Spur, Index,
Medienfolge 1 bis 6 auf- und abwärts und nach Sprüngen, zu hoher Text,
reduzierte Bewegung, Tab und Anker. Abstand von oben, Schritthöhe 60,
begrenzte Bühne (nur Chromium), Schreibrichtung und 390 px Breite sind nur
zur Laufzeit im Browser umgestellt, nicht als gespeicherte Seiten. Der Editor
ist in 3a geprüft (Einfügeregeln, Hinweistext, alle Blöcke gültig). Vergleich
Basis gegen Pro für die drei gemeinsamen Seiten nach jedem Teilschritt gleich.

Nicht geprüft: Safari, WordPress 6.7, eine Story in einem Schritt, eine Reihe
zusammen mit Nachspann oder hochgezogenem Inhalt.

## 3. Testsites

Zwei lokale Sites mit demselben Inhalt (Datenbank-Kopie von
`plugintest.local` vom 30.09.2026, Twenty Twenty-Five), auf beiden ist nur
Scrollstage aktiv:

| Site | Scrollstage | Zweck |
| --- | --- | --- |
| `https://scrollstage-basis.local` | 2.4.0 aus `scrollstage-2.4.0.zip`, feste Kopie | Vergleichsstand |
| `https://scrollstage-pro.local` | Symlink auf `~/dev/jgorres-im-WP-Repository/scrollstage/plugin` | Entwicklungsstand |

Testseiten auf beiden: `/scrollstage-hero/`, `/scrollstage-standard-width/`
(begrenzte Bühne, Hochziehen) und `/scrollytelling-test/`. Jede hat fünf
Schritte, einer davon mit Video. Nur auf Pro liegen zusätzlich
`/scrollstage-typography/` für Schriftformatierung und Textkasten,
`/scrollstage-text-effects/` für die Texteffekte und `/scrollstage-row/` für
die Reihe.

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
| 1.5 | 30.09.2026 | Effekte `rotate` und `dissolve` (Plugin 2.6.2); Prototyp für die Reihe |
| 1.6 | 30.09.2026 | Schritt 3 erledigt (Plugin 2.7.0): Block „Scrollstage Row", Abweichungen vom Prototyp, Testseite auf Pro |
| 1.7 | 30.09.2026 | Pfad des Projekts nach dem Umzug nach `~/dev/jgorres-im-WP-Repository/scrollstage` |
