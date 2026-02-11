# ClubOrganisation - Projektstruktur

**Version:** 1.0.0  
**Joomla:** 5.x / 6.x

---

## 📁 Verzeichnisstruktur

```
cluborganisation/
├── admin/                          # Backend (Administrator)
│   ├── forms/                      # XML-Formulare
│   │   ├── filter_persons.xml      # Filter für Personen-Liste
│   │   ├── membership.xml          # Mitgliedschaft bearbeiten
│   │   ├── membershipbank.xml      # Bankverbindung bearbeiten
│   │   ├── membershiptype.xml      # Mitgliedschaftstyp bearbeiten
│   │   ├── person.xml              # Person bearbeiten
│   │   └── salutation.xml          # Anrede bearbeiten
│   │
│   ├── language/                   # Sprachdateien Backend
│   │   ├── de-DE/
│   │   │   ├── de-DE.com_cluborganisation.ini      # Deutsche Übersetzungen
│   │   │   └── de-DE.com_cluborganisation.sys.ini  # System-Übersetzungen (Menu)
│   │   └── en-GB/
│   │       ├── en-GB.com_cluborganisation.ini      # Englische Übersetzungen
│   │       └── en-GB.com_cluborganisation.sys.ini  # System-Übersetzungen (Menu)
│   │
│   ├── services/                   # Dependency Injection
│   │   └── provider.php            # Service Provider
│   │
│   ├── sql/                        # Datenbank
│   │   ├── install.mysql.utf8.sql  # Installation
│   │   └── uninstall.mysql.utf8.sql # Deinstallation (leer)
│   │
│   ├── src/                        # PHP-Quellcode
│   │   ├── Controller/             # Controller
│   │   │   ├── DisplayController.php           # Dashboard
│   │   │   ├── DsgvocleanupController.php      # DSGVO Cleanup
│   │   │   ├── MembershipbankController.php    # Bankverbindung Edit
│   │   │   ├── MembershipbanksController.php   # Bankverbindungen Liste
│   │   │   ├── MembershipController.php        # Mitgliedschaft Edit
│   │   │   ├── MembershipsController.php       # Mitgliedschaften Liste
│   │   │   ├── MembershiptypeController.php    # Mitgliedschaftstyp Edit
│   │   │   ├── MembershiptypesController.php   # Mitgliedschaftstypen Liste
│   │   │   ├── MigrationController.php         # Migration Tool
│   │   │   ├── PersonController.php            # Person Edit
│   │   │   ├── PersonsController.php           # Personen Liste
│   │   │   ├── SalutationController.php        # Anrede Edit
│   │   │   └── SalutationsController.php       # Anreden Liste
│   │   │
│   │   ├── Extension/              # Extension-Klasse
│   │   │   └── ClubOrganisationComponent.php
│   │   │
│   │   ├── Field/                  # Custom Field Types
│   │   │   └── YearrangeField.php  # Jahr-Auswahl mit Bereich
│   │   │
│   │   ├── Helper/                 # Helper-Klassen
│   │   │   └── EncryptionHelper.php # AES-256 Verschlüsselung
│   │   │
│   │   ├── Model/                  # Models
│   │   │   ├── DsgvocleanupModel.php       # DSGVO Cleanup
│   │   │   ├── MembershipbankModel.php     # Bankverbindung Edit
│   │   │   ├── MembershipbanksModel.php    # Bankverbindungen Liste
│   │   │   ├── MembershipModel.php         # Mitgliedschaft Edit
│   │   │   ├── MembershipsModel.php        # Mitgliedschaften Liste
│   │   │   ├── MembershiptypeModel.php     # Mitgliedschaftstyp Edit
│   │   │   ├── MembershiptypesModel.php    # Mitgliedschaftstypen Liste
│   │   │   ├── MigrationModel.php          # Migration Tool
│   │   │   ├── PersonModel.php             # Person Edit
│   │   │   ├── PersonsModel.php            # Personen Liste
│   │   │   ├── SalutationModel.php         # Anrede Edit
│   │   │   └── SalutationsModel.php        # Anreden Liste
│   │   │
│   │   ├── Table/                  # Table-Klassen (ORM)
│   │   │   ├── MembershipbankTable.php     # Bankverbindung
│   │   │   ├── MembershipTable.php         # Mitgliedschaft
│   │   │   ├── MembershiptypeTable.php     # Mitgliedschaftstyp
│   │   │   ├── PersonTable.php             # Person
│   │   │   └── SalutationTable.php         # Anrede
│   │   │
│   │   └── View/                   # Views
│   │       ├── Dsgvocleanup/
│   │       │   └── HtmlView.php            # DSGVO Cleanup View
│   │       ├── Membershipbank/
│   │       │   └── HtmlView.php            # Bankverbindung Edit View
│   │       ├── Membershipbanks/
│   │       │   └── HtmlView.php            # Bankverbindungen Liste View
│   │       ├── Membership/
│   │       │   └── HtmlView.php            # Mitgliedschaft Edit View
│   │       ├── Memberships/
│   │       │   └── HtmlView.php            # Mitgliedschaften Liste View
│   │       ├── Membershiptype/
│   │       │   └── HtmlView.php            # Mitgliedschaftstyp Edit View
│   │       ├── Membershiptypes/
│   │       │   └── HtmlView.php            # Mitgliedschaftstypen Liste View
│   │       ├── Migration/
│   │       │   └── HtmlView.php            # Migration View
│   │       ├── Person/
│   │       │   └── HtmlView.php            # Person Edit View
│   │       ├── Persons/
│   │       │   └── HtmlView.php            # Personen Liste View
│   │       ├── Salutation/
│   │       │   └── HtmlView.php            # Anrede Edit View
│   │       └── Salutations/
│   │           └── HtmlView.php            # Anreden Liste View
│   │
│   ├── tmpl/                       # Templates (Ausgabe)
│   │   ├── dsgvocleanup/
│   │   │   └── default.php                 # DSGVO Cleanup Liste
│   │   ├── membershipbank/
│   │   │   └── edit.php                    # Bankverbindung Formular
│   │   ├── membershipbanks/
│   │   │   └── default.php                 # Bankverbindungen Liste
│   │   ├── membership/
│   │   │   └── edit.php                    # Mitgliedschaft Formular
│   │   ├── memberships/
│   │   │   └── default.php                 # Mitgliedschaften Liste
│   │   ├── membershiptype/
│   │   │   └── edit.php                    # Mitgliedschaftstyp Formular
│   │   ├── membershiptypes/
│   │   │   └── default.php                 # Mitgliedschaftstypen Liste
│   │   ├── migration/
│   │   │   └── default.php                 # Migration Interface
│   │   ├── person/
│   │   │   └── edit.php                    # Person Formular
│   │   ├── persons/
│   │   │   └── default.php                 # Personen Liste
│   │   ├── salutation/
│   │   │   └── edit.php                    # Anrede Formular
│   │   └── salutations/
│   │       └── default.php                 # Anreden Liste
│   │
│   ├── access.xml                  # ACL-Konfiguration
│   ├── cluborganisation.xml        # Component Manifest
│   └── config.xml                  # Komponenten-Konfiguration
│
├── site/                           # Frontend (Site)
│   ├── language/                   # Sprachdateien Frontend
│   │   ├── de-DE/
│   │   │   ├── de-DE.com_cluborganisation.ini      # Deutsche Übersetzungen
│   │   │   └── de-DE.com_cluborganisation.sys.ini  # System-Übersetzungen
│   │   └── en-GB/
│   │       ├── en-GB.com_cluborganisation.ini      # Englische Übersetzungen
│   │       └── en-GB.com_cluborganisation.sys.ini  # System-Übersetzungen
│   │
│   ├── src/                        # PHP-Quellcode
│   │   ├── Controller/
│   │   │   └── DisplayController.php       # Frontend Controller
│   │   │
│   │   ├── Model/                  # Models
│   │   │   ├── ActivemembersModel.php      # Aktive Mitglieder
│   │   │   ├── MembermovementsModel.php    # Eintritte/Austritte
│   │   │   ├── MembershiplistModel.php     # Mitgliedschaftsliste
│   │   │   └── MyprofileModel.php          # Mein Profil
│   │   │
│   │   └── View/                   # Views
│   │       ├── Activemembers/
│   │       │   └── HtmlView.php            # Aktive Mitglieder View
│   │       ├── Membermovements/
│   │       │   └── HtmlView.php            # Eintritte/Austritte View
│   │       ├── Membershiplist/
│   │       │   └── HtmlView.php            # Mitgliedschaftsliste View
│   │       └── Myprofile/
│   │           └── HtmlView.php            # Mein Profil View
│   │
│   └── tmpl/                       # Templates
│       ├── activemembers/
│       │   ├── default.php                 # Aktive Mitglieder Liste
│       │   └── default.xml                 # Menu Item Parameters
│       ├── membermovements/
│       │   ├── default.php                 # Eintritte/Austritte Liste
│       │   └── default.xml                 # Menu Item Parameters
│       ├── membershiplist/
│       │   ├── default.php                 # Mitgliedschaftsliste
│       │   └── default.xml                 # Menu Item Parameters
│       └── myprofile/
│           └── default.php                 # Profil-Ansicht
│
├── doc/                            # Dokumentation (im Build)
│   ├── PROJECT_STRUCTURE.md        # Diese Datei
│   ├── PROJEKTDOKUMENTATION.md     # Technische Dokumentation
│   └── UEBERSICHT.md              # Feature-Übersicht
│
├── auto_install.sh                 # Build-Script
├── LICENSE                         # GPLv3 Lizenz
└── README.md                       # Projekt-README
```

---

## 🗂️ Datei-Kategorien

### Manifest & Konfiguration

| Datei | Zweck |
|-------|-------|
| `cluborganisation.xml` | Component Manifest (Joomla Installation) |
| `access.xml` | ACL-Berechtigungen definieren |
| `config.xml` | Komponenten-Optionen |

### SQL-Dateien

| Datei | Zweck |
|-------|-------|
| `install.mysql.utf8.sql` | Tabellen erstellen, Stammdaten |
| `uninstall.mysql.utf8.sql` | Leer (Datenschutz - Tabellen bleiben) |

### PHP-Klassen

#### Controller (15 Dateien)
- **Display:** Dashboard-Steuerung
- **Liste + Edit:** Personen, Mitgliedschaften, Bankverbindungen, Anreden, Typen
- **Spezial:** Migration, DSGVO Cleanup

#### Models (17 Dateien)
- **Listen-Models:** Laden Datensätze mit Filter/Sortierung
- **Admin-Models:** CRUD-Operationen, Validierung
- **Spezial-Models:** Migration, DSGVO, Frontend-Views

#### Views (17 Dateien)
- **Backend:** 13 Views (7 Listen, 5 Edit, Migration, DSGVO)
- **Frontend:** 4 Views (Aktive, Bewegungen, Mein Profil, Mitgliedschaften)

#### Tables (5 Dateien)
- ORM-Schicht für Datenbank-Zugriff
- Validierung, Speichern, Löschen

#### Helper (2 Dateien)
- **EncryptionHelper:** AES-256 Verschlüsselung
- **YearrangeField:** Custom Field Type

### Templates (17 Dateien)

#### Backend-Templates
- **Listen:** `default.php` (Tabellen-Ansicht)
- **Edit:** `edit.php` (Formulare)
- **Spezial:** Migration, DSGVO Cleanup

#### Frontend-Templates
- **Listen:** Aktive Mitglieder, Eintritte/Austritte, Mitgliedschaften
- **Detail:** Mein Profil

### Formulare (12 XML-Dateien)

#### Backend-Forms
- **Edit-Forms:** Person, Mitgliedschaft, Bankverbindung, Anrede, Typ
- **Filter-Forms:** Personen-Filter

#### Frontend-Forms
- **Menu Item Parameters:** 4 XML-Dateien für Spalten-Konfiguration

### Sprachdateien (8 Dateien)

#### Struktur
```
[language]/[language].com_cluborganisation.ini       # Komponenten-Texte
[language]/[language].com_cluborganisation.sys.ini   # System-Texte (Menu)
```

#### Inhalte
- **`.ini`:** Formulare, Labels, Meldungen, Buttons
- **`.sys.ini`:** Menu Item Types, Backend-Menü

---

## 📊 Statistik

### Gesamt-Übersicht

| Kategorie | Backend | Frontend | Gesamt |
|-----------|---------|----------|--------|
| **Controller** | 13 | 1 | 14 |
| **Models** | 13 | 4 | 17 |
| **Views** | 13 | 4 | 17 |
| **Templates** | 13 | 4 | 17 |
| **Tables** | 5 | - | 5 |
| **Forms (XML)** | 6 | 4 | 10 |
| **Helper** | 2 | - | 2 |
| **Sprachdateien** | 4 | 4 | 8 |

### Zeilen-Code (ca.)

| Typ | Zeilen | Anteil |
|-----|--------|--------|
| PHP | 8.000 | 60% |
| XML | 2.500 | 19% |
| SQL | 800 | 6% |
| Dokumentation | 2.000 | 15% |
| **Gesamt** | **~13.300** | **100%** |

---

## 🔍 Namenskonventionen

### Dateien

**Pattern:** `[Name][Type].php`

| Typ | Beispiel | Zweck |
|-----|----------|-------|
| Liste Controller | `PersonsController.php` | Plural, List-Operationen |
| Edit Controller | `PersonController.php` | Singular, CRUD-Operationen |
| Liste Model | `PersonsModel.php` | ListModel, getItems() |
| Edit Model | `PersonModel.php` | AdminModel, save(), delete() |
| Liste View | `Persons/HtmlView.php` | Display-Liste |
| Edit View | `Person/HtmlView.php` | Display-Formular |
| Liste Template | `persons/default.php` | Tabelle |
| Edit Template | `person/edit.php` | Formular |

### Klassen

**Namespace:** `CSOSCD\Component\ClubOrganisation\[Area]\[Type]\[Class]`

Beispiele:
```php
// Backend
CSOSCD\Component\ClubOrganisation\Administrator\Controller\PersonsController
CSOSCD\Component\ClubOrganisation\Administrator\Model\PersonsModel
CSOSCD\Component\ClubOrganisation\Administrator\View\Persons\HtmlView

// Frontend
CSOSCD\Component\ClubOrganisation\Site\Model\ActivemembersModel
CSOSCD\Component\ClubOrganisation\Site\View\Activemembers\HtmlView
```

### Datenbank

**Tabellen:** `#__cluborganisation_[name]`

Beispiele:
- `#__cluborganisation_persons`
- `#__cluborganisation_memberships`
- `#__cluborganisation_membershipbanks`

**Felder:** lowercase mit Unterstrichen
- `member_no`, `entry_year`, `exit_year`

---

## 🎯 Wichtige Pfade

### Development

```bash
# Entwicklungsverzeichnis
/opt/dev/cluborganisation/

# Build-Output
/opt/dev/cluborganisation/build/

# ZIP-Package
/opt/dev/cluborganisation/build/cluborganisation_site_components_v1.0.0.zip
```

### Joomla Installation

```bash
# Backend
/var/www/html/administrator/components/com_cluborganisation/

# Frontend
/var/www/html/components/com_cluborganisation/

# Media (Fotos)
/var/www/html/images/cluborganisation/
```

### Logs & Cache

```bash
# Joomla Logs
/var/www/html/administrator/logs/

# Cache
/var/www/html/cache/
/var/www/html/administrator/cache/
```

---

## 🔧 Build-Prozess

### auto_install.sh

**Funktionen:**
1. Cleanup alter Builds
2. Verzeichnisstruktur erstellen
3. Dateien kopieren (Controller, Models, Views, Templates)
4. Sprachdateien kopieren
5. SQL-Dateien kopieren
6. Dokumentation kopieren (README, doc/)
7. ZIP-Package erstellen

**Output:**
```
build/
├── admin/                  # Backend-Dateien
├── site/                   # Frontend-Dateien
├── doc/                    # Dokumentation
├── README.md              # Projekt-README
├── LICENSE                # Lizenz (falls vorhanden)
└── cluborganisation.xml   # Manifest
```

**ZIP-Struktur:**
```
cluborganisation_site_components_v1.0.0.zip
├── admin/                 # Komplett
├── site/                  # Komplett
├── doc/                   # Dokumentation
├── README.md
└── cluborganisation.xml
```

---

## 📝 Hinweise für Entwickler

### Neue Dateien hinzufügen

**In auto_install.sh:**
```bash
# Arrays erweitern
MODELS=("..." "NewModel")
CONTROLLERS=("..." "NewController")
view_files=("..." "New/HtmlView.php")
template_files=("..." "new/default.php")
```

### Namenskonventionen beachten

- **Groß-/Kleinschreibung:** Klassen PascalCase, Dateien lowercase
- **Plural/Singular:** Listen=Plural, Edit=Singular
- **Verzeichnisse:** Lowercase mit Bindestrichen

### Sprachdateien aktualisieren

**Neue Konstante hinzufügen:**
1. In `.ini` Dateien (4 Stück: DE/EN, Admin/Site)
2. Falls Menu Item Type: in `.sys.ini` Dateien

**Pattern:**
```ini
COM_CLUBORGANISATION_[VIEW]_[CONTEXT]_[NAME]="Übersetzung"
```

---

**Stand:** Februar 2026  
**Version:** 1.0.0
