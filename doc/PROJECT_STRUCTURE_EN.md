# ClubOrganisation – Project Structure

**Version:** 2.0.0  
**Joomla:** 5.x / 6.x

---

## Directory Structure

```
cluborganisation/
├── admin/                          # Backend (Administrator)
│   ├── forms/                      # XML forms
│   ├── language/de-DE/ en-GB/      # Language files
│   ├── services/provider.php       # DI Service Provider
│   ├── sql/install/ uninstall/ updates/
│   ├── src/
│   │   ├── Controller/             # 13 controllers
│   │   ├── Extension/
│   │   ├── Field/YearrangeField.php
│   │   ├── Helper/EncryptionHelper.php
│   │   ├── Model/                  # 12 models
│   │   ├── Table/                  # 5 table classes
│   │   └── View/                   # 12 views
│   ├── tmpl/                       # Templates
│   ├── access.xml
│   └── config.xml
│
├── api/                            # REST API application
│   ├── services/provider.php
│   └── src/
│       ├── Controller/MembersController.php
│       ├── Extension/ClubOrganisationComponent.php
│       └── Model/ExportModel.php
│
├── site/                           # Frontend
│   ├── language/de-DE/ en-GB/
│   ├── services/provider.php
│   ├── src/
│   │   ├── Controller/DisplayController.php
│   │   ├── Extension/
│   │   └── Model/                  # 4 models
│   └── tmpl/                       # Templates + default.xml
│
├── media/css/ js/ images/
├── cluborganisation.xml            # Component manifest
├── LICENSE
└── README.md
```

---

## Database Schema

### Tables

| Table | Description |
|---|---|
| `#__cluborganisation_persons` | Members / persons |
| `#__cluborganisation_memberships` | Memberships (date-based) |
| `#__cluborganisation_membershipbanks` | Bank accounts (encrypted) |
| `#__cluborganisation_membershiptypes` | Master data: membership types |
| `#__cluborganisation_salutations` | Master data: salutations |

### persons

| Column | Type | Description |
|---|---|---|
| `id` | INT UNSIGNED | Primary key |
| `salutation` | INT UNSIGNED | FK → salutations.id |
| `firstname` | VARCHAR(100) | First name |
| `middlename` | VARCHAR(100) | Middle name |
| `lastname` | VARCHAR(100) | Last name |
| `birthname` | VARCHAR(100) | Birth name |
| `address` | VARCHAR(255) | Street + number |
| `zip` | VARCHAR(20) | Postal code |
| `city` | VARCHAR(100) | City |
| `country` | VARCHAR(100) | Country |
| `telephone` | VARCHAR(50) | Phone |
| `mobile` | VARCHAR(50) | Mobile |
| `email` | VARCHAR(255) | Email |
| `birthday` | DATE | Date of birth |
| `deceased` | DATE | Date of death |
| `member_no` | VARCHAR(50) | Member number (UNIQUE) |
| `active` | TINYINT(1) | Active flag (0 = anonymised) |
| `image` | VARCHAR(255) | Photo path |
| `user_id` | INT UNSIGNED | FK → Joomla users.id |

### memberships

| Column | Type | Description |
|---|---|---|
| `id` | INT UNSIGNED | Primary key |
| `person_id` | INT UNSIGNED | FK → persons.id |
| `type` | INT UNSIGNED | FK → membershiptypes.id |
| `begin` | DATE | Membership start |
| `end` | DATE | Membership end (NULL = active) |
| `comment` | TEXT | Comment |

### membershipbanks

| Column | Type | Description |
|---|---|---|
| `id` | INT UNSIGNED | Primary key |
| `membership_id` | INT UNSIGNED | FK → memberships.id |
| `accountname` | TEXT | Account holder (AES-256 encrypted) |
| `iban` | TEXT | IBAN (AES-256 encrypted) |
| `bic` | TEXT | BIC (AES-256 encrypted) |
| `begin` | DATE | Valid from |

---

## Naming Conventions

### PHP Classes (Namespace)

```
CSOSCD\Component\ClubOrganisation\Administrator\Controller\PersonsController
CSOSCD\Component\ClubOrganisation\Administrator\Model\PersonsModel
CSOSCD\Component\ClubOrganisation\Administrator\View\Persons\HtmlView
CSOSCD\Component\ClubOrganisation\Administrator\Table\PersonTable
CSOSCD\Component\ClubOrganisation\Api\Controller\MembersController
CSOSCD\Component\ClubOrganisation\Site\Model\ActivemembersModel
```

### Plural vs. Singular

| Type | Pattern | Example |
|---|---|---|
| List controller | Plural | `PersonsController` |
| Edit controller | Singular | `PersonController` |
| List model | Plural | `PersonsModel` |
| Edit model | Singular | `PersonModel` |
| Table | Singular | `PersonTable` |

### Language Constants

```
COM_CLUBORGANISATION_[VIEW]_[CONTEXT]_[NAME]
```

---

## Joomla Installation Paths

```
/administrator/components/com_cluborganisation/   # Backend
/api/components/com_cluborganisation/             # REST API
/components/com_cluborganisation/                 # Frontend
/media/com_cluborganisation/                      # CSS, JS, images
/plugins/webservices/cluborganisation/            # Webservices plugin
```

---

## Webservices Plugin

The plugin `plg_webservices_cluborganisation` is a separate extension and must be installed separately. It registers the API route with Joomla's API router:

```php
public function onBeforeApiRoute(&$router): void
{
    $router->createCRUDRoutes(
        'v1/cluborganisation/members',
        'members',
        ['component' => 'com_cluborganisation']
    );
}
```

**Without the plugin enabled:** all API requests to `/api/index.php/v1/cluborganisation/*` return 404.

---

**Date:** February 2026 · **Version:** 2.0.0
