# ClubOrganisation – Joomla 5/6 Component

**Version:** 2.0.0  
**License:** GPLv3 (see LICENSE)

---

## Overview

A complete Joomla component for managing club memberships.

✅ **Person Management** – Member data, photo, Joomla user link  
✅ **Membership Management** – Date-based with overlap validation  
✅ **Bank Data Management** – AES-256 encrypted, session-based key  
✅ **REST API** – JSON export for external systems  
✅ **GDPR Compliant** – Automatic anonymisation after configurable period  
✅ **Migration Tool** – Import from Clubmanagement  
✅ **BwPostman Sync** – Newsletter synchronisation  
✅ **Multilingual** – German & English  
✅ **ACL Integration** – Standard Joomla permission system  
✅ **Joomla 5/6 Compatible** – Modern namespace architecture  

---

## Features

### Backend (10 Views)

#### Persons
- Full personal data: salutation, name, birth name, address, contact details
- Unique member number, date of birth, date of death
- Photo upload, Joomla user link
- Automatic Joomla user creation with credentials email
- Filter by name, member number, active status

#### Memberships
- Date-based (begin/end), max one active at a time
- Type categorisation (individual, family, etc.)
- Overlap check on save

#### Bank Accounts
- AES-256-CBC encryption (account holder, IBAN, BIC)
- Key stored in PHP session only, never in database
- Unlock screen before list access
- Canary mechanism for deterministic key validation
- Key rotation with automatic re-encryption of all records
- Read-only detail view, masked IBAN in list

#### Membership Fees & Fee Report
- Date-based fees per membership type
- Annual fee report with automatic totals

#### BwPostman Synchronisation
- 3-step process: sync active, archive inactive, manage mailing lists
- Configurable assignment and gender mapping

#### Master Data
- Manage and extend salutations and membership types

#### Migration & GDPR
- Import from old Clubmanagement component
- Anonymisation under GDPR Article 17 (configurable period)

### Frontend (5 Views)

- **Active Members** – public list with configurable columns and sorting
- **Entries/Exits** – year-based overview, switchable
- **Membership Fees** – public fee overview
- **My Profile** – own data (logged-in users only)
- **My Memberships** – membership history (logged-in users only)

### REST API (new in 2.0.0)

```
GET /api/index.php/v1/cluborganisation/members
```

Authenticated via `X-Joomla-Token` header. Controlled by parameters:
- `active_memberships` (0/1) – active memberships only
- `include_banks` (0/1) – include bank data (requires `encryption_key`)
- `active_banks` (0/1) – active bank accounts only

---

## Security

- **Encryption:** AES-256-CBC; key never persisted
- **Canary validation:** deterministic, no guessing possible
- **XSS protection:** output escaping throughout
- **SQL injection protection:** prepared statements
- **CSRF protection:** Joomla token
- **ACL:** full Joomla permission integration

---

## Installation

### Requirements

- Joomla 5.x or 6.x
- PHP 8.1+
- MySQL 8.0+ / MariaDB 10.4+

### Install Component

1. Backend → System → Install → Extensions
2. Upload `com_cluborganisation_v2.0.0.zip`
3. Wait for success message

### Install Webservices Plugin (for REST API)

1. Upload `plg_webservices_cluborganisation_v2.0.0.zip`
2. Backend → System → Plugins → Type: Webservices
3. Enable "ClubOrganisation - Webservices"

### Initial Configuration

```
Backend → Components → ClubOrganisation → Options
→ General: GDPR period in years (default: 3)
→ Permissions: configure ACL for user groups
```

**Check master data:** Salutations and membership types are pre-installed.

**Create API token:** Backend → Users → own profile → generate API token.

---

## Updates

```bash
# 1. Backup
mysqldump -u root -p joomladb > backup_$(date +%Y%m%d).sql

# 2. Install new version via Joomla (auto-overwrites)
# Backend → System → Install → Extensions → Upload ZIP

# 3. Clear cache
rm -rf /var/www/html/cache/* /var/www/html/administrator/cache/*
```

---

## License

GNU General Public License version 3 or later – see `LICENSE`.

---

## Feature Status

| Feature | Status |
|---|---|
| Joomla 5 / 6 | ✅ |
| PHP 8.1+ | ✅ |
| Person management | ✅ |
| Membership management | ✅ |
| Bank data (encrypted) | ✅ |
| REST API | ✅ |
| BwPostman sync | ✅ |
| Membership fees | ✅ |
| Migration tool | ✅ |
| GDPR cleanup | ✅ |
| Frontend: 5 views | ✅ |
| Multilingual (DE/EN) | ✅ |
| ACL integration | ✅ |
| Transaction-safe | ✅ |

---

**Version:** 2.0.0 · **Date:** February 2026
