# Changelog – ClubOrganisation

Alle wichtigen Änderungen an diesem Projekt werden in dieser Datei dokumentiert.  
Format orientiert sich an [Keep a Changelog](https://keepachangelog.com/).

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
