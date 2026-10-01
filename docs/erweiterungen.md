# Scrollstage – Erweiterungen

Version: 1.15 · Stand: 01.10.2026 · Plugin-Version: 2.11.0

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
| 4 | Eigene Schritthöhe je Schritt (mehrere Textkästen vor einem Medium) | 2.8.0 | ½ Tag | erledigt |

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

### Schritt 4: Eigene Schritthöhe (2.8.0)

Wunsch vom 01.10.2026: innerhalb eines Schritts mehrere Textkästen
nacheinander durchscrollen, bevor das nächste Medium kommt.

Ein Schritt ohne Medium lässt das Medium des vorigen Schritts stehen und ist
damit bereits ein weiterer Textkasten vor demselben Medium, mit eigenem
Kastenstil und eigenem Texteffekt. Es fehlte nur, dass er enger auf den
vorigen folgen kann. Dafür hat der Schritt das Attribut `minHeight` (20 bis
200 Prozent der Bildschirmhöhe, 0 = Wert der Story) mit Schalter und Regler
im Bereich „Height". Mechanik in `konzept.md`, Abschnitt 3.

Entschieden am 01.10.2026 gegen einen eigenen Block „Textkasten" im Schritt:
Kastenstil, Effekte und Geister-Elemente hätten vom Schritt auf den neuen
Block umziehen müssen, samt Migration bestehender Inhalte, bei gleichem
Ergebnis im Frontend.

In einer Reihe wirkt der Wert nicht.

Nachweis: Testseite `/scrollstage/scrollstage-step-height/` auf Pro (Seite
136, sechs Schritte: Medium, zwei ohne Medium mit 40 %, Medium, einer ohne
Medium mit 20 %, Medium mit 150 %; Story mit `slide`). Bei 1280×800 gemessen:
Schritthöhen 800, 320, 320, 800, 235 (Text höher als 20 %) und 1200 px;
Medienfolge 1, 4, 6 auf- und abwärts, die Kästen 2 und 3 laufen vor Medium 1
durch. Chromium 154 und Edge 154 mit Scroll-Timelines (Deckkraft der Kästen
folgt dem Scrollweg), Firefox 146 mit dem Fallback der Texteffekte
(`is-visible`, `is-past` je Kasten). Editor: alle Blöcke gültig,
Schalter und Regler setzen 0, 50 und den gewählten Wert. Per `do_blocks()`:
in einer Reihe kein eigener Wert, 5 wird 20, 999 wird 200, Text wird 0.

Statt des Bildvergleichs Basis gegen Pro (Pro lief mit GeneratePress, Basis
mit Twenty Twenty-Five): Ausgabe der Seiten 86, 33, 12, 96, 104 und 109 und
alle Stylesheets und Frontend-Scripte des Builds mit 2.7.2 und 2.8.0
verglichen, byte-gleich.

Nicht geprüft: Safari, WordPress 6.7, eigene Höhe zusammen mit begrenzter
Bühne oder Nachspann.

Nachtrag 2.8.1: Bei der Prüfung in Edge fiel auf, dass im Editor die Schritte
einer Story in voller Breite unterschiedlich breit waren (Seite 109: 545, 348
und 528 px, die Reihe 332 px, bei einer 985 px breiten Story). Das betrifft
klassische Themes und alle Browser; Ursache und Korrektur stehen in
`konzept.md`, Abschnitt 3. Aufgefallen ist es erst jetzt, weil die Pro-Site
bei den früheren Editor-Prüfungen mit Twenty Twenty-Five lief.

Nachweis: Editor der Seiten 109, 136 und 33 mit GeneratePress und mit Twenty
Twenty-Five (`?wp_theme_preview=twentytwentyfive`) in Chromium 154, Firefox
146 und Edge 154: Schritte, Reihe und Schritte der Reihe sind je Seite gleich
breit und füllen die Story (Story abzüglich 34 px Rahmen und Innenabstand),
alle Blöcke gültig, keine Script-Fehler. Geändert haben sich nur die
Editor-Stylesheets von Story und Reihe; Frontend-Stylesheets und -Scripte
des Builds sind byte-gleich mit 2.8.0.

### Schritt 5: Bild für Hochformat (2.9.0)

Anlass vom 01.10.2026: Das animierte Banner (3:2) als erster Schritt der
Onlinehilfe verliert auf einem hochkant gehaltenen Handy links und rechts den
Text, weil die Bühne das Medium füllend einpasst.

Ein Schritt kann ein zweites Bild für Bildschirme im Hochformat tragen
(`portraitId`, `portraitUrl`, `portraitFocalPoint`), Bereich „Portrait
screens" in den Einstellungen des Schritts. Mechanik in `konzept.md`,
Abschnitt 3.

Erwogen und verworfen: das Banner mittig neu anordnen (auf dem Desktop bliebe
eine schmale Spalte), die ganze Story auf „Ganzes Medium zeigen" stellen
(Ränder an allen Fotos) und eine Einpassung je Schritt (das Banner bliebe auf
dem Handy ein schmaler Streifen).

Grenzen: nur Bilder, kein zweites Video. Ein animiertes PNG oder GIF aus der
Mediathek steht in den verkleinerten Größen von WordPress still; soll die
Animation überall laufen, muss der Schritt nur die Adresse der Originaldatei
tragen (`mediaId` bzw. `portraitId` 0).

Nachweis: `npm run lint:js`, `npm run lint:css` und `composer check` sauber.
Prüfung im Browser und im Editor steht aus.

### Schritt 6: Eigene Position und angehefteter Textkasten (2.10.0)

Anlass vom 01.10.2026: Der Scroll-Hinweis der Onlinehilfe („Scroll and follow
the story!") soll als eigenes Bild über dem Banner stehen bleiben, mittig,
während die übrigen Textkästen der Story links stehen und durchlaufen.

Zwei Einstellungen im Bereich „Position" des Schritts: eigene Position des
Textkastens (`textPosition`, `stepAlign`, Vorgabe „Same as story") und „Keep
the text box in place" (`pinText`). Mechanik in `konzept.md`, Abschnitt 3.

Erwogen und verworfen: den Hinweis mit Bordmitteln von WordPress als
„sticky"-Gruppe über die ganze Story zu legen. Er stünde dann auch über allen
Fotos, wäre den Textkästen im Weg und hinge an einem negativen Außenabstand in
Höhe des Bilds.

Grenzen: Die Strecke, die ein Kasten stehen bleibt, kommt allein aus der
Schritthöhe; bei 100 % bleibt nichts stehen. Kein Texteffekt für angeheftete
Kästen, keine Wirkung in einer Reihe. Ein Kasten, der höher ist als der
Bildschirm, zeigt seinen unteren Teil erst, wenn der Schritt endet.

Nachweis: `npm run lint:js`, `npm run lint:css` und `composer check` sauber.
Prüfung in den drei Browsern und im Editor steht aus.

### Schritt 7: Medium scrollt mit (2.11.0)

Anlass vom 01.10.2026: Auf der Startseite der Onlinehilfe soll das Banner wie
gewöhnlicher Inhalt nach oben wegscrollen, während der Scroll-Hinweis stehen
bleibt und dahinter das erste Foto zum Vorschein kommt. Mit 2.10.0 war es
umgekehrt: Das Banner stand als Medium auf der Bühne, der Hinweis wanderte.

Einstellung „Let the image scroll along" im Bereich „Medium" des Schritts
(`mediaScroll`). Mechanik in `konzept.md`, Abschnitt 3.

Erwogen und verworfen: das Banner als gewöhnlichen Bild-Block über die Story
zu setzen. Das Foto dahinter fehlte dann, und der Hinweis ließe sich nur kurz
festhalten.

Nebenwirkung: Eine Story, die mit Schritten ohne Medium beginnt, zeigt jetzt
von Anfang an das erste folgende Medium statt einer leeren Bühne.

Nachweis: `npm run lint:js`, `npm run lint:css` und `composer check` sauber.
Prüfung in den drei Browsern und im Editor steht aus.

### Prüfung unter WordPress 6.7 (Mindestversion)

Am 01.10.2026 mit Plugin 2.8.1 nachgeholt, auf der Wegwerf-Site
`https://scrollstage-wp67.local`: WordPress 6.7 (de_DE, automatische
Core-Updates abgeschaltet), PHP 8.4, Twenty Twenty-Five 1.0, Scrollstage aus
`scrollstage-2.8.1.zip` installiert, die sieben Testseiten der Pro-Site mit
neu importierten Medien.

* Frontend, alle sieben Seiten in Chromium 154, Firefox 146 und Edge 154:
  Klassen und Variablen von Story, Schritten und Textkästen, Medienfolge
  auf- und abwärts, Video nur in seinem Schritt, Reihe (stufenlos, in
  Firefox stufenweise), begrenzte Bühne mit Hochziehen, eigene Schritthöhe.
  Gegen die Pro-Site (WordPress 7.1.2) in Chromium verglichen: gleich bis
  auf ein Semikolon am Ende der Inline-Styles, das WordPress 6.7 anders
  setzt, und Maße, die vom Theme abhängen. Keine Fehler in der Konsole.
* Editor, fünf Seiten in denselben drei Browsern: alle Blöcke gültig,
  Vorschau der Typografie und des Textkastens (Hintergrund, Rahmen,
  Eckenradius, Schatten, Innenabstand) wie unter 7.1.2, die experimentellen
  Helfer aus `step/text-box.js` sind also vorhanden. Bereiche „Text boxes",
  „Media", „Motion", „Medium" und „Height" da, Schalter und Regler der
  Schritthöhe arbeiten. Mit GeneratePress als Vorschau sind die Schritte
  gleich breit. Nur die Namen der Core-Bereiche im Reiter „Stile" heißen
  anders als unter 7.1.2.
* Kein Eintrag in `wp-content/debug.log` (`WP_DEBUG_LOG` an).

Offen bleibt Safari.

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
`/scrollstage-text-effects/` für die Texteffekte, `/scrollstage-row/` für
die Reihe und `/scrollstage-step-height/` für die eigene Schritthöhe.

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
* Jede Änderung am Frontend wird in drei Browsern geprüft: Chromium, Firefox
  und Edge (seit 01.10.2026; Edge läuft lokal als Flatpak
  `com.microsoft.Edge`).
  Nachgeholt am 01.10.2026 in Edge 154 für die älteren Testseiten: Reihe
  stufenlos (Spur von 0 bis −2560 px, solange der Ausschnitt klebt,
  Medienfolge 1 bis 6 auf- und abwärts), Texteffekte `fade`, `slide`,
  `zoom`, `rotate` und `dissolve` folgen dem Scrollweg, keine Fehler in der
  Konsole. Ebenfalls in Edge 154, mit Chromium 154 als Gegenprobe im selben
  Lauf: begrenzte Bühne mit Hochziehen (`/scrollstage-standard-width/`:
  Bühne 1120×630 px klebt oben, der hochgezogene Inhalt klebt bei 630 px
  darunter und geht mit ihr, Medienfolge 1 bis 5, Video läuft nur in seinem
  Schritt; Scrollverlauf in beiden Browsern gleich) und der Editor (Seiten
  33, 136, 109: alle Blöcke gültig, Schalter für begrenzte Bühne, Hochziehen
  und eigene Schritthöhe, neue Story mit Schritt, Reihe und Nachspann
  einfügbar, Vorschau im Canvas; Werte in beiden Browsern gleich bis auf die
  Breite des Scrollbalkens). Der hochgezogene Inhalt ist in Edge 51 px höher
  als in Chromium, vermutlich wegen anderer Schriften im Flatpak.
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
| 1.8 | 01.10.2026 | Schritt 4 (Plugin 2.8.0): eigene Schritthöhe je Schritt, Entscheidung gegen einen Kasten-Block; Edge als dritter Prüfbrowser |
| 1.9 | 01.10.2026 | Reihe und Texteffekte in Edge 154 nachgeprüft |
| 1.10 | 01.10.2026 | Begrenzte Bühne, Hochziehen und Editor in Edge 154 nachgeprüft |
| 1.11 | 01.10.2026 | Nachtrag 2.8.1: Breite der Schritte im Editor klassischer Themes |
| 1.12 | 01.10.2026 | Prüfung unter WordPress 6.7 auf `scrollstage-wp67.local` |
| 1.13 | 01.10.2026 | Schritt 5: Bild für Hochformat (2.9.0), Prüfung im Browser offen |
| 1.14 | 01.10.2026 | Schritt 6: eigene Position und angehefteter Textkasten (2.10.0), Prüfung im Browser offen |
| 1.15 | 01.10.2026 | Schritt 7: Medium scrollt mit (2.11.0), Prüfung im Browser offen |
