# ClubOrganisation - Joomla 5/6 Komponente

**Version:** 1.2.0  
**Lizenz:** GPLv3 (siehe LICENSE Datei)

---

## 📋 Übersicht

Vollständige Joomla-Komponente zur Verwaltung von Vereinsmitgliedschaften mit:

✅ **Personen-Verwaltung** - Mitgliederdaten mit Foto und Entry/Exit Year  
✅ **Mitgliedschafts-Verwaltung** - Zeitraum-basiert mit Überschneidungsprüfung  
✅ **Bankdaten-Verwaltung** - AES-256 verschlüsselt  
✅ **Migration Tool** - Import aus Clubmanagement  
✅ **DSGVO-konform** - Automatische Anonymisierung  
✅ **Mehrsprachig** - Deutsch & Englisch vollständig  
✅ **ACL-Integration** - Vollständige Berechtigungsverwaltung  
✅ **Joomla 5/6 kompatibel** - Moderne Architektur  

Inspiriert wurde das Projekt von https://github.com/momo10216/clubmgnt. Da dort aber keinerlei Aktivitäten mehr zu verzeichnen waren, wurde eine neue Entwicklung gestartet.

---

## ✨ Features

### Administrator-Bereich (7 Views)

#### Personen
- Liste aller Personen mit Filter (Name, Mitgliedsnummer, Aktiv-Status)
- Anlegen, Bearbeiten, Löschen, Batch-Operationen
- Foto-Upload mit Vorschau
- Verknüpfung mit Joomla-Benutzer
- Entry Year / Exit Year (automatisch aus Mitgliedschaften)
- Automatische Zeitstempel

#### Mitgliedschaften
- Liste mit Filter (Person, Typ, Zeitraum)
- Zeitraum-Überschneidungsprüfung
- Kategorisierung nach Typ
- Mehrere Mitgliedschaften pro Person möglich
- Maximal eine aktive Mitgliedschaft gleichzeitig

#### Bankverbindungen
- Verschlüsselte Speicherung (AES-256-CBC mit Sodium)
- Session-basierter Zugriff (Schlüssel nie in Datenbank)
- IBAN, BIC, Kontoinhaber
- Pro Mitgliedschaft eine Bankverbindung

#### Stammdaten
- **Anreden** pflegen (Herr, Frau, Divers)
- **Mitgliedschaftstypen** pflegen:
  - Einzelmitglied
  - Einzelmitglied (reduziert)
  - Familienmitglied (zahlend)
  - Familienmitglied

#### Migration Clubmanagement
- Import aus alter Clubmanagement-Komponente
- Mapping von Feldern
- Automatische Username→User-ID Konvertierung
- Validierung und Fehlerprotokoll
- Transaction-Safe (Rollback bei Fehlern)

#### DSGVO Cleanup
- Automatische Anonymisierung nach konfigurierbarer Frist (1-20 Jahre)
- Zeigt Personen mit beendeten Mitgliedschaften
- Prüft auf aktive Mitgliedschaften (Schutz vor falscher Anonymisierung)
- Filtert bereits anonymisierte Personen
- Löscht vollständig alle Bankverbindungen
- Transaction-Safe mit Audit-Trail
- DSGVO Artikel 17 konform

### Frontend-Bereich (4 Views)

#### Aktive Mitglieder
- Übersicht aller aktiven Vereinsmitglieder
- Konfigurierbare Spaltenanzeige
- Sortierung (Nachname, Vorname, Stadt, PLZ, etc.)
- Pagination mit konfigurierbarem Limit
- Entry Year / Exit Year Anzeige

#### Eintritte/Austritte
- Jahres-basierte Übersicht
- Umschaltbar zwischen Eintritten und Austritten
- Zeigt erste/letzte Mitgliedschaft
- Entry Year / Exit Year basiert
- Konfigurierbare Spalten und Sortierung
- Pagination funktionsfähig

#### Meine Mitgliedschaften
- Liste aller Mitgliedschaften des eingeloggten Benutzers
- Chronologische Darstellung
- Aktiv/Beendet-Status

#### Mein Profil
- Persönliche Daten des Mitglieds
- Verknüpfte Mitgliedschaften
- Kontaktdaten

---

## 📦 Datenbankstruktur

### Tabellen

1. **#__cluborganisation_persons** - Personen
   - Stammdaten, Foto, Entry/Exit Year, Active Flag
   
2. **#__cluborganisation_memberships** - Mitgliedschaften
   - Zeitraum (begin, end), Typ, Person-Verknüpfung
   
3. **#__cluborganisation_membershipbanks** - Bankverbindungen
   - Verschlüsselt: accountname, iban, bic
   
4. **#__cluborganisation_salutations** - Anreden
   - Stammdaten: Herr, Frau, Divers
   
5. **#__cluborganisation_membershiptypes** - Mitgliedschaftstypen
   - Kategorisierung und Beitragsklassen

### Besonderheiten

- **Keine Foreign Keys** zu Joomla-Kerntabellen (verhindert Installationsprobleme)
- **IF NOT EXISTS** bei INSERTs (keine Duplikate bei Updates)
- **Deinstallation löscht KEINE Tabellen** (Datenschutz, manuelle Bereinigung möglich)
- **Subqueries** für Entry/Exit Year (MIN(begin), MAX(end))
- **Active Flag** semantisch genutzt (0 = anonymisiert)

---

## 🔒 Sicherheit

### Verschlüsselung
- **Methode:** AES-256-CBC (Sodium)
- **Verschlüsselte Felder:** accountname, iban, bic
- **Schlüsselverwaltung:** Session-basiert (nie in DB gespeichert)
- **Zugriff:** Verschlüsselungsschlüssel muss vor jedem Zugriff eingegeben werden
- **Entschlüsselung:** Nur für autorisierte Benutzer

### Validierung
- E-Mail-Format Validierung
- Eindeutige Mitgliedsnummern
- Zeitraum-Überschneidungsprüfung
- SQL-Injection-Schutz (Prepared Statements)
- XSS-Schutz (Output Escaping)
- CSRF-Schutz (Joomla Tokens)

### ACL (Access Control List)
- Komponenten-Level Berechtigungen
- Standard Joomla-Aktionen: create, edit, delete, edit.state, edit.own
- Konfigurierbar über Joomla Berechtigungssystem
- View-spezifische Zugriffskontrollen

### DSGVO-Compliance
- Recht auf Vergessenwerden (Artikel 17)
- Anonymisierung statt Löschung (Statistiken bleiben)
- Konfigurierbare Aufbewahrungsfristen
- Audit-Trail für Anonymisierungen
- Schutz vor versehentlicher Anonymisierung aktiver Mitglieder

---

## 🌍 Internationalisierung

Vollständig übersetzt:
- **Deutsch** (de-DE) ✅ 150+ Konstanten
- **Englisch** (en-GB) ✅ 150+ Konstanten

Sprachdateien:
- Admin: `/admin/language/[lang]/[lang].com_cluborganisation.ini`
- Admin System: `/admin/language/[lang]/[lang].com_cluborganisation.sys.ini`
- Site: `/site/language/[lang]/[lang].com_cluborganisation.ini`
- Site System: `/site/language/[lang]/[lang].com_cluborganisation.sys.ini`

Alle Texte ausgelagert:
- Menüpunkt-Beschreibungen
- Formulare und Labels
- Fehlermeldungen
- Hilfe-Texte

---

## 📋 Technische Details

### Namespace
```
CSOSCD\Component\ClubOrganisation\[Administrator|Site]\[Type]
```

### Architektur
- **MVC-Pattern** (Model-View-Controller)
- **Service Provider** (Dependency Injection)
- **PSR-12** Code-Stil
- **Type Hints** (PHP 8.1+)
- **PHPDoc** Vollständig dokumentiert
- **Transaction-Safe** kritische Operationen

### Komponenten-Übersicht

**Backend (7 Views):**
- Persons (Liste + Edit)
- Memberships (Liste + Edit)
- Membershipbanks (Liste + Edit)
- Salutations (Liste + Edit)
- Membershiptypes (Liste + Edit)
- Migration
- DSGVO Cleanup

**Frontend (4 Views):**
- Active Members (Liste)
- Membership List (Liste)
- Member Movements (Eintritte/Austritte)
- My Profile (Einzelansicht)

**Helper & Utilities:**
- EncryptionHelper (AES-256 Verschlüsselung)
- YearrangeField (Custom Field Type)

### Dateien-Statistik

| Kategorie | Anzahl | Details |
|-----------|--------|---------|
| **Kern-Komponenten** | 6 | Manifest, ACL, Config, SQL, Provider |
| **PHP-Klassen** | 50+ | Controller, Models, Views, Tables, Helpers |
| **Templates** | 15+ | Admin + Site Templates |
| **Formulare** | 12+ | Edit-Forms, Filter-Forms |
| **Sprachdateien** | 8 | DE/EN, .ini/.sys.ini |
| **Dokumentation** | 10+ | Projekt-Docs, Fix-Docs |

---

## 🛠️ Installation & Entwicklung

### Voraussetzungen
- PHP 8.1 oder höher
- MySQL 5.7+ / MariaDB 10.3+
- Joomla 5.x oder 6.x
- Sodium Extension (für Verschlüsselung)

### Installation

1. **Build erstellen:**
   ```bash
   cd /opt/dev/cluborganisation
   ./auto_install.sh
   ```

2. **In Joomla installieren:**
   ```
   Backend → System → Install → Extensions
   → ZIP hochladen: build/cluborganisation_site_components_v1.0.0.zip
   ```

3. **Konfigurieren:**
   ```
   Backend → Components → ClubOrganisation → Options
   → DSGVO Jahre-Schwelle einstellen
   → Berechtigungen konfigurieren
   ```

4. **Stammdaten prüfen:**
   - Anreden (Herr, Frau, Divers)
   - Mitgliedschaftstypen

### Entwicklung & Erweiterung

**Neue Felder hinzufügen:**
1. SQL Update-Script erstellen
2. Table-Klasse anpassen (`getTableColumns()`)
3. Form XML erweitern
4. Template updaten
5. Sprachdateien aktualisieren

**Neue Views hinzufügen:**
1. View-Klasse erstellen (`src/View/[Name]/HtmlView.php`)
2. Model erstellen (`src/Model/[Name]Model.php`)
3. Controller erstellen (`src/Controller/[Name]Controller.php`)
4. Template erstellen (`tmpl/[name]/default.php`)
5. Menu Item Type registrieren (`.sys.ini`)
6. Sprachdateien aktualisieren

**Best Practices:**
- Immer `populateState()` für ListModels implementieren
- Form-Elemente für Pagination-Templates
- Aktive Mitgliedschaften mit Subqueries prüfen
- Transaction-Safety bei kritischen Operationen
- Menu Item Types in `.sys.ini`, nicht `.ini`

---

## ⚙️ Konfiguration

### Nach der Installation

1. **Komponenten-Optionen:**
   ```
   Backend → ClubOrganisation → Options
   → DSGVO: Jahre bis Cleanup (Standard: 3)
   → Berechtigungen für Benutzergruppen
   ```

2. **Stammdaten prüfen:**
   - Anreden (sollten angelegt sein)
   - Mitgliedschaftstypen (sollten angelegt sein)

3. **Verschlüsselung einrichten:**
   - Verschlüsselungsschlüssel generieren
   - In Session speichern (automatisch)
   - Vor jedem Zugriff auf Bankdaten eingeben

### Menu Items erstellen

**Frontend-Menüpunkte:**
```
Menus → Main Menu → New Menu Item
→ Menu Item Type auswählen:
  - Aktive Mitglieder
  - Eintritte/Austritte
  - Mein Profil
  - Meine Mitgliedschaften
```

**Konfigurierbare Optionen:**
- Spaltenanzeige (welche Felder zeigen)
- Sortierung (Primär/Sekundär)
- Anzahl pro Seite (Display Num)
- Bewegungstyp (Eintritte/Austritte)
- Jahr (für Bewegungen)

---

## 🔄 Updates

Bei Updates:

1. **Backup erstellen:**
   ```bash
   mysqldump -u root -p joomla_db > backup_$(date +%Y%m%d).sql
   ```

2. **Neue Version installieren:**
   - Backend → System → Install → Extensions
   - ZIP hochladen (überschreibt alte Version)

3. **Update-SQL-Scripts:**
   - Werden automatisch ausgeführt
   - Prüfen: Backend → System → Database

4. **Cache leeren:**
   ```
   Backend → System → Clear Cache → Alle auswählen → Delete
   
   Terminal:
   rm -rf /var/www/html/cache/*
   rm -rf /var/www/html/administrator/cache/*
   sudo systemctl reload php8.1-fpm
   ```

5. **Verifikation:**
   - Alle Views prüfen
   - Pagination testen
   - DSGVO Cleanup testen

---

## 📚 Dokumentation

Detaillierte Dokumentation im `/doc` Verzeichnis:

- **PROJECT_STRUCTURE.md** - Detaillierte Dateistruktur
- **PROJEKTDOKUMENTATION.md** - Technische Dokumentation
- **UEBERSICHT.md** - Feature-Übersicht

Zusätzliche Dokumentation im Repository:
- Fix-Dokumentationen (10+ Dateien)
- Code-Patterns und Best Practices
- Installations-Anleitungen
- Troubleshooting-Guides

---

## 🐛 Bekannte Probleme & Lösungen

### Problem: Pagination funktioniert nicht
**Lösung:** Models brauchen `populateState()` und Templates `<form>` Element.
→ Siehe `FIX_SITE_PAGINATION_LIMIT.md`

### Problem: State-Error in Views
**Lösung:** `populateState()` Methode im Model implementieren.
→ Siehe `FIX_DSGVO_STATE_ERROR.md`

### Problem: Menu zeigt Konstanten statt Text
**Lösung:** Konstanten in `.sys.ini` statt `.ini` Dateien.
→ Siehe `FIX_MENU_LANGUAGE_SYS_INI.md`

### Problem: Personen mit aktiven Mitgliedschaften in Exits
**Lösung:** Subquery für aktive Mitgliedschaften (COUNT WHERE end IS NULL = 0).
→ Siehe `FIX_PAGINATION_AND_ACTIVE_EXITS.md`

---

## 📄 Lizenz

GNU General Public License version 3 or later

Siehe LICENSE Datei im Root-Verzeichnis für Details.

---

## 🤝 Unterstützung

Wenn dir das Projekt gefällt:

[![ko-fi](https://ko-fi.com/img/githubbutton_sm.svg)](https://ko-fi.com/O5O21U13R9)

---

## ✨ Status & Features

| Feature | Status |
|---------|--------|
| **Joomla Kompatibilität** | |
| Joomla 5 kompatibel | ✅ |
| Joomla 6 kompatibel | ✅ |
| PHP 8.1+ | ✅ |
| **Backend (7 Views)** | |
| Personen-Verwaltung | ✅ |
| Mitgliedschafts-Verwaltung | ✅ |
| Bankdaten (verschlüsselt) | ✅ |
| Stammdaten (Anreden, Typen) | ✅ |
| Migration Tool | ✅ |
| DSGVO Cleanup | ✅ |
| **Frontend (4 Views)** | |
| Aktive Mitglieder | ✅ |
| Eintritte/Austritte | ✅ |
| Mein Profil | ✅ |
| Meine Mitgliedschaften | ✅ |
| **Features** | |
| Entry/Exit Year | ✅ |
| Pagination funktionsfähig | ✅ |
| Konfigurierbare Spalten | ✅ |
| Mehrsprachig (DE/EN) | ✅ |
| ACL-Integration | ✅ |
| DSGVO-konform | ✅ |
| Transaction-Safe | ✅ |
| Vollständig dokumentiert | ✅ |

---

## 🎯 Roadmap

### Geplante Features

- [ ] Automatische DSGVO-Anonymisierung (Cronjob)
- [ ] E-Mail-Benachrichtigungen
- [ ] PDF-Export (Mitgliederlisten)
- [ ] Excel-Import/Export
- [ ] Statistik-Dashboard
- [ ] Geburtstagsliste
- [ ] Beitrags-Verwaltung
- [ ] Rechnungserstellung

### In Planung

- [ ] REST-API
- [ ] Mobile App Integration
- [ ] Erweiterte Suchfilter
- [ ] Bulk-Operationen
- [ ] Erweiterte ACL-Rollen

---

**Viel Erfolg mit ClubOrganisation! 🎉**

**Version:** 1.2.0  
**Stand:** Februar 2026  
**Produktionsbereit:** ✅
