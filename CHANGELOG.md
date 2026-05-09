# Changelog – ClubOrganisation

Alle wichtigen Änderungen an diesem Projekt werden in dieser Datei dokumentiert.
Format orientiert sich an [Keep a Changelog](https://keepachangelog.com/).

---

## [2.3.0] – 2026-03-09

### Neu: Abhängige Mitgliedschaftstypen

Ermöglicht die Konfiguration von Mitgliedschaftstypen, die von einem anderen Typ abhängen (z. B. beitragsfreie Familienmitglieder, die von einem zahlenden Familienmitglied abhängen).

#### Mitgliedschaftstypen (Stammdaten)

- Neues Feld **Abhängiger Typ** (Ja/Nein) pro Mitgliedschaftstyp
- Neues Feld **Hängt ab von Typ**: Auswahl des übergeordneten Typs (nur bei „Abhängiger Typ = Ja" sichtbar via `showon`)
- Nur nicht-abhängige Typen können als übergeordneter Typ gewählt werden (keine Typ-Ketten)
- Übersichtsliste zeigt neue Spalte „Abhängiger Typ" mit Badge und Bezeichnung des übergeordneten Typs

#### Mitgliedschaftsformular

- Neues Feld **Übergeordnete Mitgliedschaft**: dynamisches Dropdown, das alle aktiven Mitgliedschaften des übergeordneten Typs (beliebige Person) anzeigt
- Feld erscheint nur bei Auswahl eines abhängigen Typs (JavaScript + AJAX)
- AJAX-Endpunkt: `task=membership.getParentMemberships&type_id=X&format=json`
- Bei Bearbeitung einer bestehenden abhängigen Mitgliedschaft wird die gespeicherte Auswahl wiederhergestellt
- Warnhinweis (gelbe Alert-Box) wenn der gewählte Typ abhängige Typen hat:
  - Bei bekannter Anzahl: konkreter Zähler der verknüpften Mitgliedschaften
  - Erläuterung beider Kaskaden-Richtungen (Setzen und Entfernen des Enddatums)

#### Kaskadierung des Enddatums

Wird eine übergeordnete Mitgliedschaft mit einem Enddatum gespeichert oder das Enddatum geändert, werden alle abhängigen Mitgliedschaften automatisch aktualisiert:

| Situation | Verhalten |
|---|---|
| Enddatum gesetzt/geändert | Abhängige ohne Enddatum oder mit späterem Enddatum werden mitgesetzt |
| Abhängige mit früherem Enddatum | Werden **nicht** verändert |
| Enddatum entfernt | Abhängige mit **gleichem** Enddatum werden ebenfalls auf unbefristet gesetzt |
| Abhängige mit anderem (früherem) Enddatum | Werden **nicht** verändert |

#### Validierung

- Pflichtfeld: Bei abhängigem Typ muss eine übergeordnete Mitgliedschaft ausgewählt sein
- Typprüfung: Die gewählte übergeordnete Mitgliedschaft muss vom konfigurierten übergeordneten Typ sein

#### Datenbankänderungen

| Tabelle | Neue Spalte | Beschreibung |
|---|---|---|
| `#__cluborganisation_membershiptypes` | `is_dependent` TINYINT(1) | Abhängiger Typ (0/1) |
| `#__cluborganisation_membershiptypes` | `depends_on_type` INT UNSIGNED | FK auf übergeordneten Typ |
| `#__cluborganisation_memberships` | `depends_on_membership_id` INT UNSIGNED | FK auf übergeordnete Mitgliedschaft |

#### Neue/geänderte Dateien

| Datei | Änderung |
|---|---|
| `2.3.0.sql` | Neu: Datenbank-Update-Script |
| `mysql_install.sql` | Neue Spalten in beiden Tabellen |
| `cluborganisation.xml` | Version 2.3.0, Datum März 2026 |
| `membershiptype.xml` | 2 neue Formularfelder |
| `membershiptype_edit.php` | Neue Felder gerendert |
| `MembershiptypesModel.php` | JOIN auf übergeordneten Typ, `parent_type_title` |
| `membershiptypes_default.php` | Neue Spalte in der Liste |
| `membership.xml` | Neues Feld `depends_on_membership_id` |
| `MembershipController.php` | AJAX-Task `getParentMemberships` |
| `MembershipTable.php` | Validierung `checkDependentType()` |
| `MembershipModel.php` | Cascade-Logik in `save()`, zwei private Hilfsmethoden |
| `MembershipHtmlView.php` | Typ-Abhängigkeitsinfo + Zähler abhängiger Mitgliedschaften |
| `membership_edit.php` | JS-Logik, Warnhinweis-Box, Feld-Rendering |
| `de-DE.com_cluborganisation.ini` | Neue Sprachkonstanten |
| `en-GB.com_cluborganisation.ini` | Neue Sprachkonstanten |

---

## [2.2.0] – 2026-02-24 (Ergänzung)

### Erweitert: Datacheck

#### Neue Prüfung: Aktive Personen ohne laufende Mitgliedschaft
- Listet alle Personen, die `active = 1` sind, aber keine Mitgliedschaft mit `end IS NULL` haben
- Jede Zeile enthält neben dem Edit-Link einen **Deaktivieren**-Button
- Bestätigungs-Abfrage per `onsubmit`-Dialog vor dem Ausführen
- Nach dem Deaktivieren verbleibt man auf der Datacheck-Seite (Redirect zurück)
- Badge rot hervorgehoben (Unterschied zu anderen Kategorien, die orange sind)
- Neuer Controller `DatacheckController` mit Methode `deactivatePerson()`:
  - CSRF-Schutz via `Session::checkToken()`
  - Setzt `active = 0` und aktualisiert `modified`-Zeitstempel
  - Task: `datacheck.deactivatePerson`

### Korrigiert: Statistik-Filter

`active`-Filter aus allen statistischen Snapshot-Methoden entfernt, da der Admin-Flag
`active` keine historische Aussage trifft. Betrifft:

| Methode | Auswirkung |
|---|---|
| `countMembersAtDate()` | Monats-/Jahresentwicklung – deaktivierte Personen korrekt enthalten |
| `countMembersByTypeAtDate()` | Mitgliederstruktur nach Typ |
| `countMembersByAgeAtDate()` | Altersstruktur |
| `getAverageAgeAtDate()` | Durchschnittsalter |

`getMemberJoinsPerYear()` und `getMemberLeavesPerYear()` hatten keinen Persons-Join → bereits korrekt.

Nur Gegenwarts-/Jubiläums-Abfragen (`getMemberAnniversaries`, `getActivePersonsWithoutActiveMembership` etc.) behalten ihren Kontext-Filter.

### Neue Dateien
- `DatacheckController.php`

### Geänderte Dateien
- `DatacheckModel.php`: `getActivePersonsWithoutActiveMembership()`
- `DatacheckHtmlView.php`: neues Property `$activeNoActiveMembership`
- `datacheck_default.php`: neue Gruppe mit Deaktivieren-Button, `co_datacheck_table()` um `$showDeactivate` erweitert
- `StatisticsModel.php`: `active`-Filter aus 4 Methoden entfernt
- `auto_install.sh`: `DatacheckController.php` integriert
- Neue Sprachkonstanten in `de-DE` und `en-GB`

---

## [2.2.0] – 2026-02-24

### Neu: Dashboard

Neuer Einstiegspunkt der Komponente (ersetzt die Personenliste als Standard-View).

**KPI-Kacheln (immer sichtbar):**
- Aktive Personen
- Aktive Mitgliedschaften
- Neue Mitglieder im laufenden Monat
- Mitgliedschaften, die in den nächsten 60 Tagen enden (rot wenn > 0)
- Offene Datenlücken aus dem Datacheck (nur sichtbar wenn > 0, dann rot)

**Sections:**
- Installierte Komponentenversion als Badge
- Update-Hinweis wenn Joomla-Update-Manager ein Update für com_cluborganisation kennt (mit Direktlink zum Update-Manager)
- Erweiterungs-Status: Birthday Module und Webservices API Plugin mit Installiert-Badge oder Download-Link
- Datacheck-Übersicht mit Zählern je Kategorie und Direktlink zum Datacheck
- Jubiläen im laufenden Monat (nur sichtbar wenn vorhanden)

**Neue Dateien:**
- `DashboardModel.php` – Kennzahlen, Erweiterungsstatus, Update-Check via `#__updates`
- `DashboardHtmlView.php` – Namespace `...View\Dashboard`
- `dashboard_default.php` – Template mit KPI-Grid, Cards, CSS

### Neu: Strukturiertes Admin-Menü

Das Untermenü ist in 6 Gruppen gegliedert (Trenner ohne `link`-Attribut):

| Gruppe | Einträge |
|---|---|
| *(oben)* | Dashboard |
| Mitglieder | Personen, Mitgliedschaften, Bankverbindungen |
| Finanzen | Beitragsübersicht, Beitragssätze |
| Auswertungen | Statistik, Datacheck |
| Kommunikation | BwPostman-Sync |
| Stammdaten | Mitgliedschaftsarten, Anreden |
| System | Migration, DSGVO-Bereinigung |

### Technisches
- `DisplayController.php`: `$default_view = 'dashboard'`
- `cluborganisation.xml`: Version 2.2.0, neues `<submenu>` mit Gruppentrennern
- `auto_install.sh`: Dashboard-View, -Model, -Tmpl integriert; Version 2.2.0
- 26 neue Sprachkonstanten in `de-DE` und `en-GB`

---

## [2.1.0] – 2026-02-23

### Neu: Erweiterte Statistiken

#### Fluktuation (Eintritte / Austritte / Netto)
- Liniendiagramm **Eintritte & Austritte pro Jahr** seit Startjahr (orange = Eintritte, blau = Austritte)
- Balkendiagramm **Netto-Veränderung pro Jahr**: Balken in #f29838 (positiv) bzw. #132d6a (negativ)
- Eintrittsjahr = YEAR(MIN(begin)) aller Mitgliedschaften der Person
- Austrittsjahr = YEAR(MAX(end)) sofern keine end=NULL-Mitgliedschaft existiert

#### Jubiläen
- Tabelle **Jubiläen aktuelles Jahr**: aktive Mitglieder mit 5/10/20/25/40-jährigem Jubiläum
- Tabelle **Vorschau nächstes Jahr**: Mitglieder mit Jubiläum im Folgejahr; nur wenn im nächsten Jahr noch eine aktive Mitgliedschaft besteht (end IS NULL oder end ≥ 1.1. des nächsten Jahres)
- Sortierung: Eintrittsjahr aufsteigend, dann Nachname/Vorname

### Neu: Datacheck (Datenvollständigkeit)

Neuer Menüpunkt **Datacheck** mit vier Prüflisten für aktive Mitglieder:
- **Fehlende Geburtsdaten** (NULL, 0000-00-00 oder 1970-01-01)
- **Fehlende E-Mail-Adresse**
- **Fehlende Telefonnummer**
- **Kein Joomla-Benutzer verknüpft** (user_id NULL oder 0)

Jede Zeile enthält einen Direkt-Link zur Person-Bearbeitungsmaske (`view=person&layout=edit`).
Badge zeigt Anzahl Betroffener oder „Vollständig" wenn keine Lücken.

#### Neue Dateien
- `DatacheckModel.php` – 4 Queries mit Subquery auf end=NULL-Mitgliedschaften
- `DatacheckHtmlView.php` – Namespace `...View\Datacheck`
- `datacheck_default.php` – Template mit CSS-Cards, Badges und Edit-Links

### Technisches
- `StatisticsModel.php`: neue Methoden `getMemberJoinsPerYear()`, `getMemberLeavesPerYear()`, `getMemberAnniversaries()`
- `StatisticsHtmlView.php`: Chart-Daten für Fluktuation und Jubiläen
- `statistics_default.php`: Zeilen 5 (Fluktuation) und 6 (Jubiläen) ergänzt
- `auto_install.sh`: Datacheck-View, -Model und -Tmpl integriert
- Neue Sprachkonstanten in `de-DE` und `en-GB`

---

## [2.1.0] – 2026-02-23

### Neu: Statistik-Seite im Admin-Backend

Neuer Menüpunkt **Statistik** im Verwaltungsbereich der Komponente.

#### Inhalte der Statistikseite

- **Mitgliederentwicklung letztes Jahr** – Liniendiagramm, jeweils letzter Tag des Monats
- **Mitgliederentwicklung aktuelles Jahr** – Liniendiagramm (#f29838), letzter Tag des Monats; zukünftige Monate werden ausgeblendet
- **Mitgliederentwicklung seit Jahr X** – Balkendiagramm (#f29838) mit Jahreswerten (Stichtag 31.12.), Startjahr konfigurierbar
- **Mitgliederstruktur** – Tabelle mit aktuelles Jahr / Vorjahr / Differenz je Mitgliedschaftsart
- **Mitgliederstruktur Vergleich** – Gruppierts Balkendiagramm (aktuelles Jahr: #f29838, Vorjahr: #132d6a)
- **Altersstruktur** – Tabelle nach Altersgruppen (< 18, 18–29, 30–49, 50–65, > 65)
- **Mitgliedschaftsdauer** – Tabelle nach Dauer (≤1 J., 1–5 J., 6–10 J., 11–15 J., 16–20 J., > 20 J.)

#### Neue Konfigurationsoption

Neuer Abschnitt **Reporting** in der Komponentenkonfiguration mit dem Feld *Startjahr der Statistik* (`statistics_start_year`, Standard: 2020).

#### Nachträgliche Korrekturen (2.1.0)

- **Diagramme leer (CSP):** Umgestellt auf `$doc->addScriptDeclaration()` via `buildInitScript()` – Joomla 4.2+/5.x fügt dabei automatisch das CSP-Nonce-Attribut ein. `statistics.js` entfällt. Chart-Daten weiterhin via `addScriptOptions()` → `<script type="application/json">` (kein JS, kein Nonce).
- **Mitgliedschaftsdauer falsche Zählung:** Stichtags-aktive Mitgliedschaften als Basis lieferte falschen Wert (z. B. 16 statt 9 für ≤1 Jahr). Neue Logik: (1) Nur Personen mit mind. einer `end IS NULL`-Mitgliedschaft. (2) Frühestes `begin` über **alle** Mitgliedschaften der Person. (3) Jahresdifferenz = `YEAR(Stichtag) – YEAR(frühestes begin)`. PHP-seitiges Grouping via `loadDurationData()`.
- **Mitgliedschaftsdauer Durchschnitt:** Neue Methode `getAverageDurationAtDate()`, neue View-Properties `$avgDurationCurrent`/`$avgDurationPrev`, neue `<tfoot>`-Zeile im Template.
- **Altersstruktur Durchschnitt:** Ø Durchschnittsalter als `<tfoot>`-Zeile (MySQL `TIMESTAMPDIFF`).
- **Sprachkonstanten:** `COM_CLUBORGANISATION_STATS_DUR_AVG` und `COM_CLUBORGANISATION_STATS_AGE_AVG` in `de-DE` und `en-GB` ergänzt.

#### Technisches

- `StatisticsModel.php` – Datenbanklogik (Dauer, Ø Alter, Ø Dauer)
- `StatisticsHtmlView.php` – Chart-Init via `buildInitScript()` + `addScriptDeclaration()`
- `statistics_default.php` – Durchschnittzeilen in beiden Tabellen
- Sprachkonstanten in `de-DE.com_cluborganisation.ini` und `en-GB.com_cluborganisation.ini`
- Menüeintrag in `cluborganisation.xml`
- Erweiterung `config.xml` um Fieldset `reporting`

---

## [2.0.0] – 2026-02-20

### Neu: REST-API Export

Joomla-konforme REST-API für den maschinell lesbaren Export von Mitgliederdaten über HTTP.

#### Endpunkt

```
GET /api/index.php/v1/cluborganisation/members
```

#### Authentifizierung

Joomla API-Token im HTTP-Header:
```
X-Joomla-Token: <Token aus Benutzerprofil>
```
Erforderliche Berechtigung: `core.manage` auf `com_cluborganisation`.

#### Query-Parameter

| Parameter | Typ | Default | Beschreibung |
|---|---|---|---|
| `active_memberships` | 0/1 | 1 | Nur aktive Mitgliedschaften (begin ≤ heute, end IS NULL oder ≥ heute) |
| `include_banks` | 0/1 | 0 | Bankverbindungen in den Export einschließen |
| `active_banks` | 0/1 | 1 | Nur Bankverbindungen mit begin ≤ heute (nur bei `include_banks=1`) |
| `encryption_key` | string | – | Pflichtfeld wenn `include_banks=1`; wird gegen gespeicherten Canary-Wert geprüft |

Personen ohne passende Mitgliedschaft werden bei `active_memberships=1` vollständig aus der Antwort ausgeschlossen.

#### Beispiele

```bash
# Alle aktiven Mitglieder (Standard)
curl -H "X-Joomla-Token: <TOKEN>" \
     https://example.com/api/index.php/v1/cluborganisation/members

# Inkl. beendeter Mitgliedschaften
curl -H "X-Joomla-Token: <TOKEN>" \
     "https://example.com/api/index.php/v1/cluborganisation/members?active_memberships=0"

# Mit Bankdaten (Schlüssel erforderlich)
curl -H "X-Joomla-Token: <TOKEN>" \
     "https://example.com/api/index.php/v1/cluborganisation/members?include_banks=1&encryption_key=MeinSchluessel"

# Bankdaten: auch historische
curl -H "X-Joomla-Token: <TOKEN>" \
     "https://example.com/api/index.php/v1/cluborganisation/members?include_banks=1&active_banks=0&encryption_key=MeinSchluessel"
```

#### Antwortformat

```json
{
  "success": true,
  "exported": "2026-02-20T14:30:00+01:00",
  "options": {
    "active_memberships": true,
    "include_banks": true,
    "active_banks": true
  },
  "count": 42,
  "members": [
    {
      "id": 1,
      "member_no": "2024-001",
      "salutation": "Herr",
      "firstname": "Max",
      "lastname": "Mustermann",
      "address": "Musterstraße 1",
      "zip": "12345",
      "city": "Musterstadt",
      "country": "Deutschland",
      "telephone": "",
      "mobile": "0171/1234567",
      "email": "max@example.com",
      "birthday": "1980-06-15",
      "deceased": null,
      "active": true,
      "memberships": [
        {
          "id": 5,
          "type": "Vollmitglied",
          "begin": "2024-01-01",
          "end": null,
          "comment": null,
          "banks": [
            {
              "id": 3,
              "accountname": "Max Mustermann",
              "iban": "DE89370400440532013000",
              "bic": "COBADEFFXXX",
              "begin": "2024-01-01"
            }
          ]
        }
      ]
    }
  ]
}
```

#### Fehler-Antworten

| HTTP-Code | Ursache |
|---|---|
| 401 | Nicht authentifiziert oder `encryption_key` fehlt/falsch |
| 403 | Fehlende Berechtigung (`core.manage`) |
| 500 | Interner Fehler |

#### Neue Dateien (Komponente)

| Quelldatei | Installationsziel |
|---|---|
| `api_provider.php` | `api/services/provider.php` |
| `ApiClubOrganisationComponent.php` | `api/src/Extension/ClubOrganisationComponent.php` |
| `ApiMembersController.php` | `api/src/Controller/MembersController.php` |
| `ApiExportModel.php` | `api/src/Model/ExportModel.php` |

#### Zusätzlich erforderlich: Webservices-Plugin

Das Plugin `plg_webservices_cluborganisation` muss separat installiert und aktiviert werden. Es registriert die API-Route bei Joomla's API-Router (`onBeforeApiRoute`). Ohne dieses Plugin antwortet Joomla mit 404.

Plugin-Dateien:
- `plg_webservices_cluborganisation_root.php` → `cluborganisation.php`
- `plg_webservices_cluborganisation.xml` → Manifest

#### Geänderte Dateien

- `cluborganisation.xml`: Version 2.0.0, `<api>`-Block mit Verweis auf `api/`-Verzeichnis hinzugefügt

---

## [1.9.0] – 2026-02-20

### Neu: Schlüsselverwaltung für Bankdaten

#### Entsperr-Maske
- Bankdaten-Übersicht erfordert Eingabe des Verschlüsselungsschlüssels
- Schlüssel wird ausschließlich in der PHP-Session gehalten (nie in Datenbank oder Konfiguration)
- Sperren-Button entfernt den Schlüssel sofort aus der Session

#### Canary-Mechanismus (deterministsche Schlüsselvalidierung)
- Beim Speichern des ersten Bankdatensatzes wird die Konstante `CLUBORG_KEY_CHECK_v1` mit dem aktiven Schlüssel verschlüsselt und als Parameter `encryption_canary` in `#__extensions` gespeichert
- `EncryptionHelper::verifyKey($key)` entschlüsselt den Canary und vergleicht ihn mit der Konstante – vollständig deterministisch, keine Heuristik
- Key Rotation aktualisiert den Canary nach erfolgreicher Neu-Verschlüsselung aller Datensätze

#### Key Rotation
- Modal-Dialog (eigenes JS-Overlay, kein Bootstrap-Abhängigkeit) auf der Bankdaten-Übersichtsseite
- Alle Bankdatensätze werden mit dem neuen Schlüssel neu verschlüsselt
- Canary wird abschließend aktualisiert

#### Anzeigen-Funktion (schreibgeschützte Detailansicht)
- Neuer Task `membershipbank.view` → `layout=view`
- Zeigt entschlüsselte Bankdaten als Nur-Lese-Ansicht
- Schaltflächen: Zurück zur Liste, Bearbeiten

#### Datumsvalidierung gegen Mitgliedschaft
- Beginn der Bankverbindung darf nicht vor Mitgliedschaftsbeginn liegen
- Beginn darf nicht nach Mitgliedschaftsende liegen
- Neue Bankeinträge nur für aktive oder zukünftige Mitgliedschaften erlaubt

#### Kontoinhaber-Vorbelegung
- JavaScript befüllt das Feld Kontoinhaber bei neuen Einträgen automatisch mit Vor-/Nachname der verknüpften Person

#### Dropdown-Filterung
- Mitgliedschafts-Dropdown im Bankdaten-Formular zeigt nur aktive/zukünftige Mitgliedschaften (`WHERE end IS NULL OR end >= CURDATE()`)

#### Weitere Verbesserungen
- IBAN wird in der Übersichtsliste maskiert angezeigt
- Personenname (Nachname, Vorname, Mitgliedsnummer) in der Bankdaten-Liste sichtbar

**Neue Dateien:** `membershipbanks_unlock.php`, `membershipbank_view.php`  
**Neue Methoden:** `EncryptionHelper::saveCanary()`, `verifyKey()`, `getStoredCanary()`

---

## [1.8.0] – 2026-02

### Neu: BwPostman-Synchronisation
- 3-stufiger Synchronisationsprozess mit der BwPostman Newsletter-Komponente
- Automatisches Anlegen/Reaktivieren aktiver Mitglieder als Subscriber
- Archivierung inaktiver Mitglieder in BwPostman
- Konfigurierbare Mailinglist-Zuordnung und Gender-Mapping
- Matching über Mitgliedsnummer; Transaction-Safe

---

## [1.7.0] – 2026-01

### Neu: Mitgliedschaftsgebühren & Beitragsübersicht
- Verwaltung von Beiträgen pro Mitgliedschaftstyp (zeitbasiert mit begin-Datum)
- Frontend-View: öffentliche Darstellung aktueller und zukünftiger Gebühren
- Backend-View: automatische Jahres-Beitragsübersicht mit Summen

---

## [1.1.0] – 2026-01

### Neu
- Frontend-Views: Aktive Mitglieder, Eintritte/Austritte, Mein Profil, Mitgliedschaftsliste
- Konfigurierbare Spaltenanzeige über Menu Item Parameter
- DSGVO Cleanup Tool mit konfigurierbarer Aufbewahrungsfrist
- Migration Tool für Import aus Clubmanagement-Komponente

---

## [1.0.0] – 2025-12

### Erstveröffentlichung
- Personen-Verwaltung mit vollständigen Stammdaten
- Mitgliedschafts-Verwaltung (zeitraum-basiert, Überschneidungsprüfung)
- Bankdaten-Verwaltung (AES-256-CBC verschlüsselt)
- Stammdaten: Anreden, Mitgliedschaftstypen
- ACL-Integration
- Mehrsprachig: Deutsch & Englisch
- Joomla 5.x / 6.x kompatibel
