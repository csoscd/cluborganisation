# ClubOrganisation - Projektstruktur

**Version:** 1.1.0  
**Joomla:** 5.x / 6.x

---

## 📁 Verzeichnisstruktur

```
cluborganisation/
├── admin/                          # Backend (Administrator)
│   ├── forms/                      # XML-Formulare
│   │   ├── filter_persons.xml      # Filter für Personen-Liste
│   │   ├── filter_memberships.xml  # Filter für Mitgliedschaften-Liste
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
│   │   ├── install/
│   │   │   └── mysql.sql           # Installation
│   │   ├── uninstall/
│   │   │   └── mysql.sql           # Deinstallation (leer)
│   │   └── updates/                # Update-Scripts
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
│   │   │   ├── default.php         # Bankverbindungsliste (mit Personenname, IBAN-Maskierung, Key-Rotation-Modal)
│   │   │   └── unlock.php          # Entsperr-Maske für Verschlüsselungsschlüssel
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
│   ├── services/                   # Service Provider
│   │   └── provider.php            # Dependency Injection
│   │
│   ├── src/                        # PHP-Quellcode
│   │   ├── Controller/
│   │   │   └── DisplayController.php       # Frontend Controller
│   │   │
│   │   ├── Extension/
│   │   │   └── ClubOrganisationComponent.php
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
├── media/                          # Frontend-Ressourcen
│   ├── css/
│   │   └── cluborganisation.css    # Komponenten-Styles
│   ├── js/
│   │   └── cluborganisation.js     # JavaScript
│   └── images/                     # Komponenten-Bilder
│
├── doc/                            # Dokumentation (im Build)
│   ├── PROJECT_STRUCTURE.md        # Diese Datei
│   ├── PROJEKTDOKUMENTATION.md     # Technische Dokumentation
│   └── UEBERSICHT.md              # Feature-Übersicht
│
├── cluborganisation.xml            # Component Manifest
├── LICENSE                         # GPLv3 Lizenz
└── README.md                       # Projekt-README
```

---

## 🗂️ Datei-Kategorien

### Manifest & Konfiguration

| Datei | Zweck | Ort |
|-------|-------|-----|
| `cluborganisation.xml` | Component Manifest (Joomla Installation) | Root |
| `access.xml` | ACL-Berechtigungen definieren | admin/ |
| `config.xml` | Komponenten-Optionen | admin/ |

### SQL-Dateien

| Datei | Zweck | Ort |
|-------|-------|-----|
| `mysql.sql` | Tabellen erstellen, Stammdaten | admin/sql/install/ |
| `mysql.sql` | Leer (Datenschutz - Tabellen bleiben) | admin/sql/uninstall/ |

### PHP-Klassen

#### Controller (14 Dateien)
**Backend (13):**
- **Display:** Dashboard-Steuerung
- **Liste + Edit:** Personen, Mitgliedschaften, Bankverbindungen, Anreden, Typen
- **Spezial:** Migration, DSGVO Cleanup

**Frontend (1):**
- DisplayController: Frontend-Routing

#### Models (17 Dateien)
**Backend (13):**
- **Listen-Models:** Personen, Mitgliedschaften, Bankverbindungen, Anreden, Typen
- **Admin-Models:** CRUD-Operationen, Validierung
- **Spezial-Models:** Migration, DSGVO Cleanup

**Frontend (4):**
- Aktive Mitglieder, Eintritte/Austritte, Mein Profil, Mitgliedschaften

#### Views (17 Dateien)
**Backend (13):** 
- 7 Listen-Views, 5 Edit-Views, Migration, DSGVO Cleanup

**Frontend (4):** 
- Aktive Mitglieder, Eintritte/Austritte, Mein Profil, Mitgliedschaften

#### Tables (5 Dateien)
- ORM-Schicht für Datenbank-Zugriff
- Validierung, Speichern, Löschen
- Person, Membership, Membershipbank, Salutation, Membershiptype

#### Helper & Extension (3 Dateien)
- **EncryptionHelper:** AES-256 Verschlüsselung
- **YearrangeField:** Custom Field Type
- **ClubOrganisationComponent:** Extension-Klasse

### Templates (17 Dateien)

#### Backend-Templates (13)
- **Listen:** `default.php` (Tabellen-Ansicht)
- **Edit:** `edit.php` (Formulare)
- **Spezial:** Migration, DSGVO Cleanup

#### Frontend-Templates (4)
- **Listen:** Aktive Mitglieder, Eintritte/Austritte, Mitgliedschaften
- **Detail:** Mein Profil

### Formulare (10 XML-Dateien)

#### Backend-Forms (6)
- **Edit-Forms:** Person, Mitgliedschaft, Bankverbindung, Anrede, Typ
- **Filter-Forms:** Personen-Filter, Mitgliedschaften-Filter

#### Frontend-Forms (4)
- **Menu Item Parameters:** XML-Dateien für Spalten-Konfiguration
- Activemembers, Membermovements, Membershiplist, Myprofile

### Sprachdateien (8 Dateien)

#### Struktur
```
[language]/[language].com_cluborganisation.ini       # Komponenten-Texte
[language]/[language].com_cluborganisation.sys.ini   # System-Texte (Menu)
```

#### Inhalte
- **`.ini`:** Formulare, Labels, Meldungen, Buttons, Fehlermeldungen
- **`.sys.ini`:** Menu Item Types, Backend-Menü, Komponenten-Beschreibung

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
| **Extension** | 1 | 1 | 2 |
| **Sprachdateien** | 4 | 4 | 8 |
| **Service Provider** | 1 | 1 | 2 |

### Zeilen-Code (ca.)

| Typ | Zeilen | Anteil |
|-----|--------|--------|
| PHP | 8.500 | 62% |
| XML | 2.200 | 16% |
| SQL | 800 | 6% |
| Dokumentation | 2.200 | 16% |
| **Gesamt** | **~13.700** | **100%** |

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
| Table | `PersonTable.php` | ORM-Klasse |

### Klassen

**Namespace:** `CSOSCD\Component\ClubOrganisation\[Area]\[Type]\[Class]`

Beispiele:
```php
// Backend
CSOSCD\Component\ClubOrganisation\Administrator\Controller\PersonsController
CSOSCD\Component\ClubOrganisation\Administrator\Model\PersonsModel
CSOSCD\Component\ClubOrganisation\Administrator\View\Persons\HtmlView
CSOSCD\Component\ClubOrganisation\Administrator\Table\PersonTable

// Frontend
CSOSCD\Component\ClubOrganisation\Site\Controller\DisplayController
CSOSCD\Component\ClubOrganisation\Site\Model\ActivemembersModel
CSOSCD\Component\ClubOrganisation\Site\View\Activemembers\HtmlView
```

### Datenbank

**Tabellen:** `#__cluborganisation_[name]`

Beispiele:
- `#__cluborganisation_persons`
- `#__cluborganisation_memberships`
- `#__cluborganisation_membershipbanks`
- `#__cluborganisation_salutations`
- `#__cluborganisation_membershiptypes`

**Felder:** lowercase mit Unterstrichen
- `member_no`, `entry_year`, `exit_year`, `user_id`
- `firstname`, `lastname`, `birthname`, `middlename`
- `begin`, `end`, `fee_amount`

### Sprachkonstanten

**Pattern:** `COM_CLUBORGANISATION_[VIEW]_[CONTEXT]_[NAME]`

Beispiele:
```ini
COM_CLUBORGANISATION_PERSONS_TITLE="Personen"
COM_CLUBORGANISATION_PERSON_FIELD_FIRSTNAME_LABEL="Vorname"
COM_CLUBORGANISATION_MEMBERSHIPS_FILTER_SEARCH="Suchen"
COM_CLUBORGANISATION_MENU_ACTIVEMEMBERS_TITLE="Aktive Mitglieder"
```

---

## 🎯 Wichtige Pfade

### Development

```bash
# Entwicklungsverzeichnis (Quellcode)
/opt/dev/cluborganisation/

# Build-Output
/opt/dev/cluborganisation/
/opt/dev/com_cluborganisation_v1.0.0.zip

# Auto-Install Script
./auto_install.sh
```

### Joomla Installation

```bash
# Backend-Komponente
/var/www/html/administrator/components/com_cluborganisation/

# Frontend-Komponente
/var/www/html/components/com_cluborganisation/

# Media-Dateien
/var/www/html/media/com_cluborganisation/

# Mitgliederfotos
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
1. Cleanup alter Builds (außer .git)
2. Verzeichnisstruktur erstellen (admin/, site/, doc/, media/)
3. Dateien kopieren (Controller, Models, Views, Templates, Tables, Helper)
4. Sprachdateien kopieren (DE/EN, .ini/.sys.ini)
5. SQL-Dateien kopieren (install/uninstall)
6. Formulare kopieren (admin/site XML)
7. Dokumentation kopieren (README, LICENSE, doc/)
8. Index.html Schutz-Dateien erstellen
9. ZIP-Package erstellen

**Ausführung:**
```bash
cd /opt/dev/cluborganisation
./auto_install.sh
```

**Output-Struktur:**
```
/opt/dev/cluborganisation/
├── admin/                  # Backend-Dateien
├── site/                   # Frontend-Dateien
├── media/                  # Ressourcen
├── doc/                    # Dokumentation
├── README.md              # Projekt-README
├── LICENSE                # GPLv3 Lizenz
└── cluborganisation.xml   # Manifest

/opt/dev/com_cluborganisation_v1.0.0.zip
```

**ZIP-Struktur:**
```
com_cluborganisation_v1.0.0.zip
├── admin/                 # Komplett (src/, tmpl/, forms/, language/, sql/, services/)
├── site/                  # Komplett (src/, tmpl/, language/, services/)
├── media/                 # CSS, JS, Images
├── doc/                   # Dokumentation
├── README.md
├── LICENSE
└── cluborganisation.xml
```

---

## 📝 Hinweise für Entwickler

### Neue Dateien hinzufügen

**In auto_install.sh anpassen:**
```bash
# Controller
for ctrl in PersonsController ... NewController; do
    [ -f "$CURRENT_DIR/${ctrl}.php" ] && cp ...
done

# Models
for model in PersonsModel ... NewModel; do
    [ -f "$CURRENT_DIR/${model}.php" ] && cp ...
done

# Views
[ -f "$CURRENT_DIR/NewHtmlView.php" ] && cp ... "$BUILD_DIR/admin/src/View/New/HtmlView.php"

# Templates
[ -f "$CURRENT_DIR/new_default.php" ] && cp ... "$BUILD_DIR/admin/tmpl/new/default.php"
```

### Namenskonventionen beachten

- **Groß-/Kleinschreibung:** Klassen PascalCase, Verzeichnisse lowercase
- **Plural/Singular:** Listen=Plural, Edit=Singular
- **Verzeichnisse:** Lowercase für tmpl/, PascalCase für src/View/
- **Dateien:** PascalCase für PHP-Klassen, lowercase für Templates

### Sprachdateien aktualisieren

**Neue Konstante hinzufügen:**
1. In allen 4 `.ini` Dateien (DE/EN, Admin/Site)
2. Falls Menu Item Type: auch in `.sys.ini` Dateien
3. Pattern: `COM_CLUBORGANISATION_[VIEW]_[CONTEXT]_[NAME]="Übersetzung"`

**Beispiel:**
```ini
# de-DE.com_cluborganisation.ini
COM_CLUBORGANISATION_NEWVIEW_TITLE="Neue Ansicht"
COM_CLUBORGANISATION_NEWVIEW_FIELD_NAME="Name"

# de-DE.com_cluborganisation.sys.ini (für Menu Items)
COM_CLUBORGANISATION_MENU_NEWVIEW_TITLE="Neue Ansicht"
COM_CLUBORGANISATION_MENU_NEWVIEW_DESC="Beschreibung"
```

### Neue View erstellen

**Erforderliche Schritte:**
1. **Model:** `src/Model/NewModel.php` (ListModel oder AdminModel)
2. **View:** `src/View/New/HtmlView.php`
3. **Controller:** `src/Controller/NewController.php` (optional)
4. **Template:** `tmpl/new/default.php` (oder `edit.php`)
5. **Formular:** `forms/new.xml` (falls Edit-View)
6. **Sprachdateien:** Konstanten in allen .ini Dateien
7. **auto_install.sh:** Kopier-Logik hinzufügen

### Verzeichnisstruktur erweitern

**Neue Bereiche:**
```bash
# In auto_install.sh
mkdir -p "$BUILD_DIR/admin/src/NewArea"
mkdir -p "$BUILD_DIR/admin/tmpl/newarea"
```

**Neue Unterverzeichnisse:**
```bash
mkdir -p "$BUILD_DIR/admin/src/Helper/NewHelper"
mkdir -p "$BUILD_DIR/media/newtype"
```

---

## 🔍 Dateisystem-Konventionen

### Index.html Schutz

Alle Verzeichnisse enthalten `index.html` zum Schutz vor Directory Listing:
```html
<html><body></body></html>
```

Wird automatisch von `auto_install.sh` erstellt.

### Berechtigungen

**Empfohlene Berechtigungen:**
```bash
# Verzeichnisse
755 (rwxr-xr-x)

# PHP-Dateien
644 (rw-r--r--)

# Scripts
755 (rwxr-xr-x) für auto_install.sh
```

**Owner:**
```bash
# Development
user:user

# Production (Joomla)
www-data:www-data
```

### Git-Integration

**.gitignore empfohlen:**
```
# Build-Output
/opt/dev/cluborganisation/
*.zip

# IDE
.vscode/
.idea/

# Logs
*.log

# OS
.DS_Store
Thumbs.db
```

---

**Stand:** Februar 2026  
**Version:** 1.1.0
