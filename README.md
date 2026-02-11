# ClubOrganisation - Joomla 5/6 Komponente

**Autor:** csoscd  
**Version:** 1.0.0  
**Lizenz:** GPLv3

---

## 📋 Übersicht

Vollständige Joomla-Komponente zur Verwaltung von Vereinsmitgliedschaften mit:

✅ **Personen-Verwaltung** - Mitgliederdaten mit Foto  
✅ **Mitgliedschafts-Verwaltung** - Zeitraum-basiert mit Überschneidungsprüfung  
✅ **Bankdaten-Verwaltung** - AES-256 verschlüsselt  
✅ **Mehrsprachig** - Deutsch & Englisch  
✅ **ACL-Integration** - Vollständige Berechtigungsverwaltung  
✅ **Joomla 5/6 kompatibel** - Moderne Architektur  

Inspiriert wurde das Projekt von https://github.com/momo10216/clubmgnt. Da dort aber keinerlei Aktivitäten mehr zu verzeichnen waren, habe ich mich für eine neue Entwicklung entschieden.

---

## ✨ Features

### Administrator-Bereich

#### Personen
- Liste aller Personen mit Filter (Name, Mitgliedsnummer, Aktiv-Status)
- Anlegen, Bearbeiten, Löschen
- Foto-Upload
- Verknüpfung mit Joomla-Benutzer
- Automatische Zeitstempel

#### Mitgliedschaften
- Liste mit Filter (Person, Typ, Zeitraum)
- Zeitraum-Überschneidungsprüfung
- Kategorisierung
- Mehrere Mitgliedschaften pro Person (maximal eine aktiv)

#### Bankverbindungen
- Verschlüsselte Speicherung (AES-256-CBC)
- Session-basierter Zugriff
- IBAN, BIC, Kontoinhaber

#### Stammdaten
- Anreden pflegen
- Mitgliedschaftstypen pflegen:
  - Einzelmitglied
  - Einzelmitglied (reduziert)
  - Familienmitglied (zahlend)
  - Familienmitglied

### Frontend-Bereich
- Aktive Mitglieder-Übersicht
- Mitgliedschaftslisten (neu/beendet pro Jahr)
- Persönliches Profil für angemeldete Benutzer

---

## 📦 Datenbankstruktur

### Tabellen

1. **cluborganisation_persons** - Personen
2. **cluborganisation_memberships** - Mitgliedschaften
3. **cluborganisation_membershipbanks** - Bankverbindungen (verschlüsselt)
4. **cluborganisation_salutations** - Anreden
5. **cluborganisation_membershiptypes** - Mitgliedschaftstypen

### Besonderheiten

- **Keine Foreign Keys** zu Joomla-Kerntabellen (verhindert Installationsprobleme)
- **IF NOT EXISTS** bei INSERTs (keine Duplikate)
- **Deinstallation löscht KEINE Tabellen** (Datenschutz)

---

## 🔒 Sicherheit

### Verschlüsselung
- **Methode:** AES-256-CBC
- **Verschlüsselte Felder:** accountname, iban, bic
- **Schlüsselverwaltung:** Session-basiert (nie in DB)
- **Zugriff:** Schlüssel muss vor jedem Zugriff eingegeben werden

### Validierung
- E-Mail-Format
- Eindeutige Mitgliedsnummern
- Zeitraum-Überschneidungsprüfung
- SQL-Injection-Schutz (Prepared Statements)
- XSS-Schutz (Output Escaping)
- CSRF-Schutz (Joomla Tokens)

### ACL (Access Control List)
- Komponenten-Level Berechtigungen
- Standard Joomla-Aktionen: create, edit, delete, edit.state, edit.own
- Konfigurierbar über Joomla Berechtigungssystem

---

## 🌍 Internationalisierung

Vollständig übersetzt:
- **Deutsch** (de-DE) ✅
- **Englisch** (en-GB) ✅

Alle Texte in Sprachdateien:
- Admin: `/admin/language/[lang]/[lang].com_cluborganisation.ini`
- Admin System: `/admin/language/[lang]/[lang].com_cluborganisation.sys.ini`
- Site: `/site/language/[lang]/[lang].com_cluborganisation.ini`

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

### Dateien-Übersicht

**Kern-Komponenten:**
- 1 Manifest (cluborganisation.xml)
- 1 ACL-Konfiguration (access.xml)
- 1 Komponenten-Konfiguration (config.xml)
- 2 SQL-Dateien (install, uninstall)
- 1 Service Provider (provider.php)

**PHP-Klassen:**
- 1 Extension-Klasse
- 7 Controller (Display, Persons, Person, Memberships, Salutations, Membershiptypes, Membershipbanks)
- 6 Models (Persons, Person, Memberships, Salutations, Membershiptypes, Membershipbanks)
- 5 Table-Klassen
- 6 Views
- 1 Helper (Encryption)

**Templates & Forms:**
- 6 Templates (persons, person, memberships, salutations, membershiptypes, membershipbanks)
- 4 Formulare (person, membership, membershipbank, filter_persons)

**Sprachen:**
- 6 Sprachdateien (3x Deutsch, 3x Englisch)

---

## 🛠️ Entwicklung

### Voraussetzungen
- PHP 8.1+
- MySQL 5.7+ / MariaDB 10.3+
- Joomla 5.x oder 6.x

### Erweiterung

**Neue Felder hinzufügen:**
1. SQL Update-Script erstellen
2. Table-Klasse anpassen
3. Form XML erweitern
4. Template updaten

**Neue Views hinzufügen:**
1. View-Klasse erstellen (HtmlView.php)
2. Model erstellen (ListModel/AdminModel)
3. Controller erstellen
4. Template erstellen (default.php/edit.php)
5. Sprachdateien aktualisieren

---

## ⚙️ Konfiguration

Nach der Installation:

1. **Komponenten** → **ClubOrganisation** → **Optionen**
2. **Berechtigungen** konfigurieren
3. **Anreden** prüfen (Herr, Frau, Divers)
4. **Mitgliedschaftstypen** prüfen

---

## 🔄 Updates

Bei Updates:
1. Backup erstellen
2. Neue ZIP-Datei installieren
3. Update-SQL-Scripts werden automatisch ausgeführt
4. Cache leeren

---

## 📄 Lizenz

GNU General Public License version 3 or later

---

## Unterstützung / Contribute

Wenn dir das Projekt gefällt:

[![ko-fi](https://ko-fi.com/img/githubbutton_sm.svg)](https://ko-fi.com/O5O21U13R9)

---

## ✨ Status

| Feature | Status |
|---------|--------|
| Joomla 5 kompatibel | ✅ |
| Joomla 6 kompatibel | ✅ |
| ACL-Integration | ✅ |
| Verschlüsselte Bankdaten | ✅ |
| Mehrsprachig | ✅ |
| Alle Admin-Views | ✅ |
| Filter & Suche | ✅ |
| Dokumentiert | ✅ |

---

**Viel Erfolg mit ClubOrganisation! 🎉**
