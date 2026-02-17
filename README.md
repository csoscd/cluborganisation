# ClubOrganisation - Joomla 5/6 Komponente

**Version:** 1.8.0  
**Lizenz:** GPLv3 (siehe LICENSE Datei)

---

## 📋 Übersicht

Vollständige Joomla-Komponente zur Verwaltung von Vereinsmitgliedschaften mit:

✅ **Personen-Verwaltung** - Mitgliederdaten mit Foto und Entry/Exit Year  
✅ **Mitgliedschafts-Verwaltung** - Zeitraum-basiert mit Überschneidungsprüfung  
✅ **Beitrags-Verwaltung** - Zeitbasierte Gebühren pro Membershiptype  
✅ **Bankdaten-Verwaltung** - AES-256 verschlüsselt  
✅ **Migration Tool** - Import aus Clubmanagement  
✅ **DSGVO-konform** - Automatische Anonymisierung  
✅ **Mehrsprachig** - Deutsch & Englisch vollständig  
✅ **ACL-Integration** - Vollständige Berechtigungsverwaltung  
✅ **Joomla 5/6 kompatibel** - Moderne Architektur  

Inspiriert wurde das Projekt von https://github.com/momo10216/clubmgnt. Da dort aber keinerlei Aktivitäten mehr zu verzeichnen waren, wurde eine neue Entwicklung gestartet.

---

## ✨ Features

### Administrator-Bereich (10 Views)

#### BwPostman Synchronisation ⭐ NEU in 1.8.0
- 3-stufiger Synchronisationsprozess mit BwPostman Newsletter-Komponente
- **Aktive Mitglieder**: Automatisches Anlegen/Aktualisieren in BwPostman
  - Neue Subscriber werden erstellt
  - Archivierte/Inaktive werden reaktiviert
  - Mailinglist-Verbindungen werden hergestellt
- **Inaktive Mitglieder**: Archivierung in BwPostman
  - Mitglieder ohne aktive Mitgliedschaft werden markiert
  - Kein Newsletter-Versand mehr an inaktive Mitglieder
- **Mailinglist-Auswahl**: Flexible Zuordnung zu verschiedenen Listen
- **Gender-Mapping**: Konfigurierbare Zuordnung Anrede → Geschlecht
- Intelligent Matching über Mitgliedsnummer
- Transaction-Safe mit vollständigem Rollback

#### Personen
- Liste aller Personen mit Filter (Name, Mitgliedsnummer, Aktiv-Status)
- Anlegen, Bearbeiten, Löschen, Batch-Operationen
- Foto-Upload mit Vorschau
- Verknüpfung mit Joomla-Benutzer
- **Automatische Joomla-User Erstellung** mit konfigurierbarer Benutzergruppe
- **E-Mail-Versand von Zugangsdaten** (optional, konfigurierbar)
- Entry Year / Exit Year (automatisch aus Mitgliedschaften)
- Automatische Zeitstempel

#### Mitgliedschaften
- Liste mit Filter (Person, Typ, Zeitraum)
- Zeitraum-Überschneidungsprüfung
- Kategorisierung nach Typ
- Mehrere Mitgliedschaften pro Person möglich
- Maximal eine aktive Mitgliedschaft gleichzeitig

#### Mitgliedschaftsgebühren ⭐ NEU in 1.7.0
- Verwaltung von Beiträgen pro Mitgliedschaftstyp
- Zeitbasierte Gültigkeit (begin-Datum)
- Historische Gebühren und zukünftige Änderungen
- Beliebig viele Fees pro Membershiptype
- Decimal(10,2) für präzise Cent-Beträge

#### Beitragsübersicht ⭐ NEU in 1.7.0
- Automatische Berechnung für aktuelles Jahr
- Automatische Berechnung für kommendes Jahr
- Gruppierung nach Membershiptype
- Summen: Anzahl × Beitrag
- Gesamtsummen pro Jahr

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

### Frontend-Bereich (5 Views)

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

#### Mitgliedschaftsgebühren ⭐ NEU in 1.7.0
- Öffentliche Darstellung aktueller Fees
- Zeigt zukünftige Gebührenänderungen
- Gruppiert nach Membershiptype
- Übersichtliche Tabelle mit Gültig-ab-Datum
- Konfigurierbar über Menü-Item

#### Meine Mitgliedschaften
- Liste aller Mitgliedschaften des eingeloggten Benutzers
- Chronologische Darstellung
- Aktiv/Beendet-Status

#### Mein Profil
- Persönliche Daten des Mitglieds
- Verknüpfte Mitgliedschaften
- Kontaktdaten

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

## ⚙️ Konfiguration

### Nach der Installation

1. **Komponenten-Optionen:**
   ```
   Backend → ClubOrganisation → Options
   → DSGVO: Jahre bis Cleanup (Standard: 3)
   → Berechtigungen für Benutzergruppen
   → Joomla-User Einstellungen
   ```

2. **Joomla-User Einstellungen konfigurieren:**
   - **Absender E-Mail-Adresse**: E-Mail für Zugangsdaten-Versand
   - **E-Mail-Text**: Template mit Platzhaltern [FIRSTNAME], [LASTNAME], [USERNAME], [PASSWORD]
   - **Passwortzurücksetzung fordern**: Default Ja/Nein
   - **Benutzerstatus**: Default Freigegeben/Gesperrt

3. **Stammdaten prüfen:**
   - Anreden (sollten angelegt sein)
   - Mitgliedschaftstypen (sollten angelegt sein)

4. **Verschlüsselung einrichten:**
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
| **Backend (10 Views)** | |
| Personen-Verwaltung | ✅ |
| Mitgliedschafts-Verwaltung | ✅ |
| Mitgliedschaftsgebühren | ✅ |
| Beitragsübersicht | ✅ |
| BwPostman Synchronisation | ✅ |
| Bankdaten (verschlüsselt) | ✅ |
| Stammdaten (Anreden, Typen) | ✅ |
| Migration Tool | ✅ |
| DSGVO Cleanup | ✅ |
| **Frontend (5 Views)** | |
| Aktive Mitglieder | ✅ |
| Eintritte/Austritte | ✅ |
| Mitgliedschaftsgebühren | ✅ |
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
- [x] Beitrags-Verwaltung ✅ **Fertig in v1.7.0**
- [ ] Rechnungserstellung

### In Planung

- [ ] REST-API
- [ ] Mobile App Integration
- [ ] Erweiterte Suchfilter
- [ ] Bulk-Operationen
- [ ] Erweiterte ACL-Rollen

---

**Viel Erfolg mit ClubOrganisation! 🎉**

**Version:** 1.8.0  
**Stand:** Februar 2026  
**Produktionsbereit:** ✅
