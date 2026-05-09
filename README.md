# ClubOrganisation – Joomla 5/6 Komponente

**Version:** 2.3.0
**Lizenz:** GPLv3 (siehe LICENSE)

---

## Übersicht

Vollständige Joomla-Komponente zur Verwaltung von Vereinsmitgliedschaften.

✅ **Personen-Verwaltung** – Mitgliederdaten, Foto, Joomla-User-Verknüpfung  
✅ **Mitgliedschafts-Verwaltung** – Zeitraum-basiert mit Überschneidungsprüfung  
✅ **Bankdaten-Verwaltung** – AES-256 verschlüsselt, Session-basierter Schlüssel  
✅ **REST-API** – JSON-Export für externe Systeme  
✅ **Statistik** – Grafische & tabellarische Mitgliederauswertungen (neu in 2.1.0)  
✅ **DSGVO-konform** – Automatische Anonymisierung nach konfigurierbarer Frist  
✅ **Migration Tool** – Import aus Clubmanagement  
✅ **BwPostman-Sync** – Newsletter-Synchronisation  
✅ **Mehrsprachig** – Deutsch & Englisch  
✅ **ACL-Integration** – Standard Joomla-Berechtigungssystem  
✅ **Joomla 5/6 kompatibel** – Moderne Namespace-Architektur  

---

## Features

### Backend (10 Views)

#### Personen
- Vollständige Stammdaten: Anrede, Name, Geburtsname, Adresse, Kontakt
- Mitgliedsnummer (eindeutig), Geburtsdatum, Sterbedatum
- Foto-Upload, Joomla-User-Verknüpfung
- Automatische Joomla-User-Erstellung mit E-Mail-Versand der Zugangsdaten
- Filter nach Name, Mitgliedsnummer, Aktiv-Status

#### Mitgliedschaften
- Zeitraum-basiert (Begin/End), maximal eine aktive gleichzeitig
- Typ-Kategorisierung (Einzelmitglied, Familie, etc.)
- Überschneidungsprüfung beim Speichern
- **Abhängige Mitgliedschaftstypen** (neu in 2.3.0): beitragsfreie Typen (z. B. Familienmitglied) können einem zahlenden übergeordneten Typ zugeordnet werden; Enddaten werden automatisch kaskadiert

#### Bankverbindungen
- AES-256-CBC Verschlüsselung (Kontoinhaber, IBAN, BIC)
- Schlüssel ausschließlich in PHP-Session, nie in Datenbank
- Entsperr-Maske vor Zugriff auf die Liste
- Canary-Mechanismus für deterministische Schlüsselvalidierung
- Key Rotation mit automatischer Neu-Verschlüsselung aller Datensätze
- Schreibgeschützte Detailansicht, IBAN-Maskierung in der Liste

#### Mitgliedschaftsgebühren & Beitragsübersicht
- Zeitbasierte Gebühren pro Mitgliedschaftstyp
- Jahres-Beitragsübersicht mit automatischen Summen

#### BwPostman-Synchronisation
- 3-stufiger Prozess: Aktive sync, Inaktive archivieren, Mailinglisten
- Konfigurierbare Zuordnung und Gender-Mapping

#### Stammdaten
- Anreden und Mitgliedschaftstypen pflegen und erweitern

#### Migration & DSGVO
- Import aus alter Clubmanagement-Komponente
- Anonymisierung nach DSGVO Artikel 17 (konfigurierbare Frist)

### Frontend (5 Views)

- **Aktive Mitglieder** – öffentliche Liste mit konfigurierbaren Spalten und Sortierung
- **Eintritte/Austritte** – jahresbasierte Übersicht, umschaltbar
- **Mitgliedschaftsgebühren** – öffentliche Beitragsübersicht
- **Mein Profil** – eigene Stammdaten (nur eingeloggte Nutzer)
- **Meine Mitgliedschaften** – Mitgliedschafts-Historie (nur eingeloggte Nutzer)

### REST-API (neu in 2.0.0)

```
GET /api/index.php/v1/cluborganisation/members
```

Authentifizierung über `X-Joomla-Token` Header. Steuerbar über Parameter:
- `active_memberships` (0/1) – nur aktive Mitgliedschaften
- `include_banks` (0/1) – Bankdaten einschließen (erfordert `encryption_key`)
- `active_banks` (0/1) – nur aktive Bankverbindungen

---

## Sicherheit

- **Verschlüsselung:** AES-256-CBC; Schlüssel nie persistiert
- **Canary-Validierung:** deterministisch, kein Raten möglich
- **XSS-Schutz:** Output Escaping überall
- **SQL-Injection-Schutz:** Prepared Statements
- **CSRF-Schutz:** Joomla Token
- **ACL:** Vollständige Joomla-Berechtigungsintegration

---

## Installation

### Voraussetzungen

- Joomla 5.x oder 6.x
- PHP 8.1+
- MySQL 8.0+ / MariaDB 10.4+

### Komponente installieren

1. Backend → System → Installieren → Erweiterungen
2. ZIP-Datei `com_cluborganisation_v2.0.0.zip` hochladen
3. Warten bis Erfolgsmeldung erscheint

### Webservices-Plugin installieren (für REST-API)

1. ZIP-Datei `plg_webservices_cluborganisation_v2.0.0.zip` hochladen
2. Backend → System → Plugins → Typ: Webservices
3. Plugin „ClubOrganisation - Webservices" aktivieren

### Erstkonfiguration

```
Backend → Komponenten → ClubOrganisation → Optionen
→ Allgemein: DSGVO-Frist in Jahren (Standard: 3)
→ Berechtigungen: ACL für Benutzergruppen konfigurieren
```

**Stammdaten prüfen:** Anreden und Mitgliedschaftstypen sollten vorhanden sein (werden bei Installation angelegt).

**API-Token erstellen:** Backend → Benutzer → eigenes Profil → API-Token generieren.

---

## Updates

```bash
# 1. Backup
mysqldump -u root -p joomladb > backup_$(date +%Y%m%d).sql

# 2. Neue Version über Joomla installieren (überschreibt automatisch)
# Backend → System → Installieren → Erweiterungen → ZIP hochladen

# 3. Cache leeren
rm -rf /var/www/html/cache/* /var/www/html/administrator/cache/*
```

---

## Lizenz

GNU General Public License version 3 or later – siehe `LICENSE`.

---

## Unterstützung

[![ko-fi](https://ko-fi.com/img/githubbutton_sm.svg)](https://ko-fi.com/O5O21U13R9)

---

## Feature-Status

| Feature | Status |
|---|---|
| Joomla 5 / 6 | ✅ |
| PHP 8.1+ | ✅ |
| Personen-Verwaltung | ✅ |
| Mitgliedschafts-Verwaltung | ✅ |
| Bankdaten (verschlüsselt) | ✅ |
| REST-API | ✅ |
| BwPostman-Sync | ✅ |
| Mitgliedschaftsgebühren | ✅ |
| Migration Tool | ✅ |
| DSGVO Cleanup | ✅ |
| Frontend: 5 Views | ✅ |
| Mehrsprachig (DE/EN) | ✅ |
| ACL-Integration | ✅ |
| Transaction-Safe | ✅ |

---

**Version:** 2.3.0 · **Stand:** März 2026
