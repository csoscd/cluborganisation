# ClubOrganisation – Projektstruktur

**Version:** 2.0.0  
**Joomla:** 5.x / 6.x

---

## Verzeichnisstruktur

```
cluborganisation/
├── admin/                          # Backend (Administrator)
│   ├── forms/                      # XML-Formulare
│   │   ├── filter_persons.xml
│   │   ├── filter_memberships.xml
│   │   ├── membership.xml
│   │   ├── membershipbank.xml
│   │   ├── membershiptype.xml
│   │   ├── person.xml
│   │   └── salutation.xml
│   │
│   ├── language/                   # Sprachdateien Backend
│   │   ├── de-DE/
│   │   │   ├── de-DE.com_cluborganisation.ini
│   │   │   └── de-DE.com_cluborganisation.sys.ini
│   │   └── en-GB/
│   │       ├── en-GB.com_cluborganisation.ini
│   │       └── en-GB.com_cluborganisation.sys.ini
│   │
│   ├── services/
│   │   └── provider.php            # Service Provider (DI-Container)
│   │
│   ├── sql/
│   │   ├── install/mysql.sql       # Tabellen & Stammdaten
│   │   ├── uninstall/mysql.sql     # Leer (Tabellen bleiben erhalten)
│   │   └── updates/                # Versions-Update-Scripts
│   │
│   ├── src/
│   │   ├── Controller/
│   │   │   ├── DisplayController.php
│   │   │   ├── DsgvocleanupController.php
│   │   │   ├── MembershipbankController.php
│   │   │   ├── MembershipbanksController.php
│   │   │   ├── MembershipController.php
│   │   │   ├── MembershipsController.php
│   │   │   ├── MembershiptypeController.php
│   │   │   ├── MembershiptypesController.php
│   │   │   ├── MigrationController.php
│   │   │   ├── PersonController.php
│   │   │   ├── PersonsController.php
│   │   │   ├── SalutationController.php
│   │   │   └── SalutationsController.php
│   │   │
│   │   ├── Extension/
│   │   │   └── ClubOrganisationComponent.php
│   │   │
│   │   ├── Field/
│   │   │   └── YearrangeField.php
│   │   │
│   │   ├── Helper/
│   │   │   └── EncryptionHelper.php   # AES-256-CBC + Canary
│   │   │
│   │   ├── Model/
│   │   │   ├── DsgvocleanupModel.php
│   │   │   ├── MembershipbankModel.php
│   │   │   ├── MembershipbanksModel.php
│   │   │   ├── MembershipModel.php
│   │   │   ├── MembershipsModel.php
│   │   │   ├── MembershiptypeModel.php
│   │   │   ├── MembershiptypesModel.php
│   │   │   ├── MigrationModel.php
│   │   │   ├── PersonModel.php
│   │   │   ├── PersonsModel.php
│   │   │   ├── SalutationModel.php
│   │   │   └── SalutationsModel.php
│   │   │
│   │   ├── Table/
│   │   │   ├── MembershipbankTable.php
│   │   │   ├── MembershipTable.php
│   │   │   ├── MembershiptypeTable.php
│   │   │   ├── PersonTable.php
│   │   │   └── SalutationTable.php
│   │   │
│   │   └── View/
│   │       ├── Membershipbank/HtmlView.php
│   │       ├── Membershipbanks/HtmlView.php
│   │       ├── Membership/HtmlView.php
│   │       ├── Memberships/HtmlView.php
│   │       ├── Membershiptype/HtmlView.php
│   │       ├── Membershiptypes/HtmlView.php
│   │       ├── Person/HtmlView.php
│   │       ├── Persons/HtmlView.php
│   │       ├── Salutation/HtmlView.php
│   │       ├── Salutations/HtmlView.php
│   │       ├── Migration/HtmlView.php
│   │       └── Dsgvocleanup/HtmlView.php
│   │
│   ├── tmpl/
│   │   ├── membershipbank/
│   │   │   ├── edit.php
│   │   │   └── view.php            # Schreibgeschützte Detailansicht
│   │   ├── membershipbanks/
│   │   │   ├── default.php         # Liste mit IBAN-Maskierung, Key-Rotation-Modal
│   │   │   └── unlock.php          # Entsperr-Maske
│   │   ├── membership/edit.php
│   │   ├── memberships/default.php
│   │   ├── membershiptype/edit.php
│   │   ├── membershiptypes/default.php
│   │   ├── person/edit.php
│   │   ├── persons/default.php
│   │   ├── salutation/edit.php
│   │   ├── salutations/default.php
│   │   ├── migration/default.php
│   │   └── dsgvocleanup/default.php
│   │
│   ├── access.xml                  # ACL-Konfiguration
│   └── config.xml                  # Komponenten-Konfiguration
│
├── api/                            # REST-API Applikation (Joomla API)
│   ├── services/
│   │   └── provider.php            # DI Service Provider für API-App
│   └── src/
│       ├── Controller/
│       │   └── MembersController.php  # GET-Handler, Auth, Parameter
│       ├── Extension/
│       │   └── ClubOrganisationComponent.php
│       └── Model/
│           └── ExportModel.php     # Datenbankabfragen, Entschlüsselung
│
├── site/                           # Frontend (Site)
│   ├── language/
│   │   ├── de-DE/de-DE.com_cluborganisation.ini
│   │   └── en-GB/en-GB.com_cluborganisation.ini
│   │
│   ├── services/
│   │   └── provider.php
│   │
│   ├── src/
│   │   ├── Controller/
│   │   │   └── DisplayController.php
│   │   ├── Extension/
│   │   │   └── ClubOrganisationComponent.php
│   │   └── Model/
│   │       ├── ActivemembersModel.php
│   │       ├── MembermovementsModel.php
│   │       ├── MembershiplistModel.php
│   │       └── MyprofileModel.php
│   │
│   └── tmpl/
│       ├── activemembers/
│       │   ├── default.php
│       │   └── default.xml         # Menu Item Parameter
│       ├── membermovements/
│       │   ├── default.php
│       │   └── default.xml
│       ├── membershiplist/
│       │   ├── default.php
│       │   └── default.xml
│       └── myprofile/
│           └── default.php
│
├── media/
│   ├── css/cluborganisation.css
│   ├── js/cluborganisation.js
│   └── images/
│
├── cluborganisation.xml            # Component Manifest
├── LICENSE                         # GPLv3
└── README.md
```

---

## Datenbankschema

### Tabellen

| Tabelle | Beschreibung |
|---|---|
| `#__cluborganisation_persons` | Personen/Mitglieder |
| `#__cluborganisation_memberships` | Mitgliedschaften (zeitraum-basiert) |
| `#__cluborganisation_membershipbanks` | Bankverbindungen (verschlüsselt) |
| `#__cluborganisation_membershiptypes` | Stammdaten Mitgliedschaftstypen |
| `#__cluborganisation_salutations` | Stammdaten Anreden |

### persons

| Spalte | Typ | Beschreibung |
|---|---|---|
| `id` | INT UNSIGNED | Primärschlüssel |
| `salutation` | INT UNSIGNED | FK → salutations.id |
| `firstname` | VARCHAR(100) | Vorname |
| `middlename` | VARCHAR(100) | Zweiter Vorname |
| `lastname` | VARCHAR(100) | Nachname |
| `birthname` | VARCHAR(100) | Geburtsname |
| `address` | VARCHAR(255) | Straße + Hausnummer |
| `zip` | VARCHAR(20) | Postleitzahl |
| `city` | VARCHAR(100) | Ort |
| `country` | VARCHAR(100) | Land |
| `telephone` | VARCHAR(50) | Telefon |
| `mobile` | VARCHAR(50) | Mobil |
| `email` | VARCHAR(255) | E-Mail |
| `birthday` | DATE | Geburtsdatum |
| `deceased` | DATE | Sterbedatum |
| `member_no` | VARCHAR(50) | Mitgliedsnummer (UNIQUE) |
| `active` | TINYINT(1) | Aktiv-Flag (0 = anonymisiert) |
| `image` | VARCHAR(255) | Foto-Pfad |
| `user_id` | INT UNSIGNED | FK → Joomla users.id |

### memberships

| Spalte | Typ | Beschreibung |
|---|---|---|
| `id` | INT UNSIGNED | Primärschlüssel |
| `person_id` | INT UNSIGNED | FK → persons.id |
| `type` | INT UNSIGNED | FK → membershiptypes.id |
| `begin` | DATE | Beginn der Mitgliedschaft |
| `end` | DATE | Ende (NULL = aktiv) |
| `comment` | TEXT | Kommentar |

### membershipbanks

| Spalte | Typ | Beschreibung |
|---|---|---|
| `id` | INT UNSIGNED | Primärschlüssel |
| `membership_id` | INT UNSIGNED | FK → memberships.id |
| `accountname` | TEXT | Kontoinhaber (AES-256 verschlüsselt) |
| `iban` | TEXT | IBAN (AES-256 verschlüsselt) |
| `bic` | TEXT | BIC (AES-256 verschlüsselt) |
| `begin` | DATE | Gültig ab |

---

## Namenskonventionen

### PHP-Klassen (Namespace)

```
CSOSCD\Component\ClubOrganisation\Administrator\Controller\PersonsController
CSOSCD\Component\ClubOrganisation\Administrator\Model\PersonsModel
CSOSCD\Component\ClubOrganisation\Administrator\View\Persons\HtmlView
CSOSCD\Component\ClubOrganisation\Administrator\Table\PersonTable
CSOSCD\Component\ClubOrganisation\Administrator\Helper\EncryptionHelper

CSOSCD\Component\ClubOrganisation\Api\Controller\MembersController
CSOSCD\Component\ClubOrganisation\Api\Model\ExportModel

CSOSCD\Component\ClubOrganisation\Site\Model\ActivemembersModel
```

### Plural vs. Singular

| Typ | Schema | Beispiel |
|---|---|---|
| Listen-Controller | Plural | `PersonsController` |
| Edit-Controller | Singular | `PersonController` |
| Listen-Model | Plural | `PersonsModel` |
| Edit-Model | Singular | `PersonModel` |
| Table | Singular | `PersonTable` |
| View-Verzeichnis | PascalCase | `View/Persons/HtmlView.php` |
| Template-Verzeichnis | lowercase | `tmpl/persons/default.php` |

### Sprachkonstanten

```
COM_CLUBORGANISATION_[VIEW]_[CONTEXT]_[NAME]

Beispiele:
COM_CLUBORGANISATION_PERSONS_TITLE
COM_CLUBORGANISATION_PERSON_FIELD_FIRSTNAME_LABEL
COM_CLUBORGANISATION_MEMBERSHIPBANKS_UNLOCK_TITLE
```

---

## Joomla-Installationspfade

```
/administrator/components/com_cluborganisation/   # Backend
/api/components/com_cluborganisation/             # REST-API
/components/com_cluborganisation/                 # Frontend
/media/com_cluborganisation/                      # CSS, JS, Bilder
/plugins/webservices/cluborganisation/            # Webservices-Plugin
```

---

## Webservices-Plugin

Das Plugin `plg_webservices_cluborganisation` ist eine eigenständige Erweiterung und wird separat als ZIP installiert. Es registriert die API-Route bei Joomla's API-Router:

```php
// Plugin-Klasse: PlgWebservicesCluborganisation
public function onBeforeApiRoute(&$router): void
{
    $router->createCRUDRoutes(
        'v1/cluborganisation/members',
        'members',
        ['component' => 'com_cluborganisation']
    );
}
```

**Ohne aktiviertes Plugin:** alle API-Anfragen an `/api/index.php/v1/cluborganisation/*` geben 404 zurück.

---

**Stand:** Februar 2026 · **Version:** 2.0.0
