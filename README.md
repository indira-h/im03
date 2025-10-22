# Virus Tracker HS25 IM 03
**Ramon Künzle & Indira Hagmann**
**24C1** 

## Kurzbeschreibung
Der Virus Tracker ist eine Website, die zeigt, wie viele Viren im Abwasser der Stadt Basel gefunden wurden.  
Die Daten kommen von einer öffentlichen Quelle und werden automatisch in einer Datenbank gespeichert.  
Man kann auswählen, ob man die letzten 7, 14 oder 30 Tage sehen will.  
Eine farbige Anzeige zeigt, ob das Risiko niedrig, mittel oder hoch ist.  
So kann man einfach sehen, wie sich die Viruslage verändert.

## Technische Umsetzung
- Frontend: HTML / CSS / JavaScript
- Backend: PHP / MySQL
- Hosting: Infomaniak  

**ETL-Prozess**  
1. **Extract:** `fetch_api.php` ruft offene Abwasserdaten von Basel ab.  
2. **Transform:** Es wird berechnet, ob das Risiko niedrig, mittel oder hoch ist. (Geringes Risiko bis 0.5 Billionen Viren / Mässiges Risiko bis 1.5 Billionen Viren / Hohes Risiko ab 1.5 Billionen Viren) 
3. **Load:** Speicherung in der Datenbanktabelle `virus_data`.  
4. **Unload:** `data.php` liefert Daten für 7, 14 oder 30 Tage ans Frontend.  

**Frontend-Funktionen:**  
- Dynamischer Zeitraumwechsel (7 / 14 / 30 Tage)  
- Automatische Trendlinie (lineare Regression)  
- Farblich codiertes Risiko-Level (Grün, Gelb, Rot)  
- Sanfte Animationen, responsives Design  

## Learnings
- Verständnis des ETL-Prozesses (Daten holen, umwandeln, speichern, anzeigen)  
- Nutzung von Chart.js zur interaktiven Visualisierung  
- Arbeiten mit Datenbanken SQL
- Organisation eines Projekts über GitHub und Infomaniak 
- Bedeutung von sauberer Datenstruktur und Fehlerbehandlung  

## Schwierigkeiten

- **Unterschiedliche Zeiträume (7 / 14 / 30 Tage):**  
  Anfangs wurden immer nur 7 Tage angezeigt, egal welchen Button ich klickte.  
  Ich dachte zuerst, der Fehler liege im JavaScript, bis ich merkte,  
  dass meine Datenbank erst wenige Datensätze enthielt, 
  die 30-Tage-Ansicht konnte deshalb schlicht noch nicht genug Daten anzeigen.

- **Layout und Achsenbeschriftung:**  
  Es war schwierig, die Beschriftung „Anzahl Viren im Abwasser“ und „Tage“  
  richtig zu positionieren, sie war anfangs zu weit vom Diagramm entfernt.  
  Durch Tests mit CSS-Positionierung (`absolute`, `transform`) fand ich die richtige Balance.

- **Trendlinie (rote Linie):**  
  Die Trendlinie berechnete sich zuerst falsch oder wurde gar nicht angezeigt.  
  Nach vielen Versuchen fand ich mit Hilfe von W3Schools eine Lösung, wie man sie als zweiten Dataset darstellt.  
  Es war lehrreich, die mathematische Formel zu verstehen und richtig einzubauen.

- **Farbliche Risikoanzeige:**  
  Das Risiko-Level wurde anfangs nicht richtig aktualisiert.  
  Ich lernte, DOM-Elemente gezielt per `id` anzusprechen und sanft zu animieren,  
  um visuelles Feedback zu geben.

- **Verständnis der Datenquellen:**  
  Die Werte aus der API (`7_tagemedian_of_e_n1_n2_pro_tag_100_000_pers`)  
  waren anfangs schwer zu interpretieren. Ich musste mich mit den Einheiten  
  und den 7-Tage-Medianwerten vertraut machen, um sinnvolle Werte zu setzen.

- **Datenbank-Synchronisierung:**  
  Mehrfaches Importieren führte zu doppelten Einträgen.  
  Ich lernte, über `ON DUPLICATE KEY UPDATE` zu arbeiten,  
  um Daten konsistent zu halten.

## Einsatz von KI / Hilfsmitteln

Ich habe KI (ChatGPT) bewusst genutzt um Verständnisfragen eingesetzt,z. B.:  
- *„Warum zeigt Chart.js meine Trendlinie nicht an?“*  
- *„Wie erkenne ich, ob meine API-Daten korrekt geladen wurden?“*  
- *„Warum werden Layoutänderungen im CSS (z. B. Abstände oder Schriftgrössen) manchmal nicht übernommen?“*  
- *„Wie kann ich verhindern, dass GitHub mein Passwort speichert?“*  

KI half mir, technische Konzepte besser zu verstehen und Schritt für Schritt eigene Lösungen zu entwickeln.  
Die Umsetzung, Tests und Anpassungen habe ich selbst vorgenommen.  

Ich habe auch W3Schools und Stack Overflow für gezielte Problemstellungen genutzt.  
Dadurch konnte ich mein Verständnis für Zusammenhänge zwischen Frontend, Backend und Datenbank stark vertiefen.

## Links
- **GitHub Repo:** https://github.com/indira-h/im03
- **Live Website:** https://im03.ch
- **Figma:** https://www.figma.com/proto/SKPmuadJJJQgtOVCeVmcUz/Virus-Tracker
- **API:** https://www.freepublicapis.com/virenmonitoring-api

## Fazit
Dieses Projekt war für uns beide eine  Herausforderung, aber auch eine wertvolle Erfahrung.  
Am Anfang war vieles neu, vor allem, wie man Daten speichert und dann auf einer Webseite sichtbar macht.  
Mit der Zeit haben wir verstanden, wie die einzelnen Teile zusammenarbeiten.  
Wir mussten oft ausprobieren, testen und Fehler suchen.
Auch die rote Trendlinie hat uns viele Nerven gekostet, bis wir mit Hilfe von W3Schools endlich eine funktionierende Lösung fanden.  
Durch diese Schwierigkeiten haben wir viel gelernt vor allem Geduld, und das man villeicht mal die Arbeit auf die Seite legt un am nächsten Tag das ganze nochmals genau anschaut.  
Besonders schön war zu sehen, wie wir immer ein Stück näher an unser Ziel näherkamen.

