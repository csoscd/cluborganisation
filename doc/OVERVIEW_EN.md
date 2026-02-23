# ClubOrganisation – Feature Overview

**Version:** 2.0.0  
**Joomla:** 5.x / 6.x

---

## Target Audience

ClubOrganisation is aimed at clubs, associations and organisations of any size that want to manage their memberships within Joomla – from data maintenance and GDPR-compliant anonymisation to machine-readable data output via REST API.

---

## Backend Features

### 1. Person Management

Full personal data:
- Salutation, first/last/birth/middle name, date of birth, date of death
- Address, telephone, mobile, email
- Unique member number, photo upload
- Joomla user link, automatic user creation with credentials email
- Filter by name, member number, active status

### 2. Membership Management

Date-based memberships:
- Begin (mandatory), end (NULL = active)
- Max one active membership at a time; overlap check on save
- Multiple historical memberships per person
- Categorised by membership type

### 3. Bank Account Management

Encrypted storage of sensitive payment data:
- Account holder, IBAN, BIC – all AES-256-CBC encrypted
- Key held in PHP session only, never in database
- Unlock screen before list access
- **Canary mechanism**: deterministic key validation
- Key rotation with automatic re-encryption of all records
- Masked IBAN in list view; read-only detail view

### 4. Membership Fees & Fee Report

- Date-based fees per membership type (multiple, effective from date)
- Backend: annual fee report with automatic totals
- Frontend: public display of current and future fees

### 5. BwPostman Synchronisation

3-step process:
1. Create/reactivate active members as subscribers
2. Archive inactive members in BwPostman
3. Manage mailing list assignments

Configurable field mapping, gender mapping. Transaction-safe.

### 6. Migration Tool

Import from Clubmanagement component:
- Full field mapping, username → user ID conversion
- Validation, error log, transaction-safe rollback

### 7. GDPR Cleanup

Automatic anonymisation after configurable period (1–20 years):
- Only shows persons with exclusively ended memberships
- Protects against accidental anonymisation of active members
- Anonymises: names, contact data, birthday, email
- Deletes completely: all bank accounts
- Sets active flag to 0; GDPR Article 17 compliant, transaction-safe

### 8. Master Data

- Salutations: Mr, Ms, Other (extensible, sortable)
- Membership types: Individual, Individual (reduced), Family (paying), Family (extensible)

---

## REST API (since 2.0.0)

Joomla-native REST API for machine-readable export:

**Endpoint:** `GET /api/index.php/v1/cluborganisation/members`  
**Auth:** `X-Joomla-Token` header  
**Permission:** `core.manage` on `com_cluborganisation`

Controlled via query parameters:

| Parameter | Default | Function |
|---|---|---|
| `active_memberships` | 1 | Only persons with active membership |
| `include_banks` | 0 | Include decrypted bank data |
| `active_banks` | 1 | Only current bank accounts |
| `encryption_key` | – | Required when `include_banks=1` |

Persons without any matching membership are excluded entirely from filtered output.

**Requires:** Plugin `plg_webservices_cluborganisation` installed and enabled.

---

## Frontend Features

### Active Members

Public member list with configurable columns (member number, name, address, contact, birthday, membership type, entry year), sorting and pagination.

### Entries/Exits

Year-based overview, switchable between entries and exits. Based on first/last membership date.

### Membership Fees

Public display of current fees per type, including future changes.

### My Profile / My Memberships

Logged-in users only. Shows own personal data and complete membership history.

### Menu Item Configuration

All frontend views support configurable options in the Joomla menu item:
- Column visibility (individually toggled)
- Primary and secondary sorting
- Items per page
- Movement type and year (for entries/exits)

---

## User Roles

| Role | Rights |
|---|---|
| **Administrator** | Full access, GDPR cleanup, migration, configuration |
| **Manager** | Read and edit, no delete, no configuration |
| **Member (logged in)** | Own profile and membership history in frontend |
| **Public** | Active members, entries/exits, fees |

---

## Typical Workflows

### Register New Member

```
1. Persons → New → enter personal data → Save
2. Memberships → New → select person, type, begin date → Save
3. Optional: Bank accounts → New → enter key, IBAN/BIC → Save
```

### Change Membership Type

```
1. Open current membership → set end date → Save
2. Memberships → New → new type and begin date
   (overlap check prevents errors)
```

### GDPR Cleanup

```
1. Options → configure GDPR period (e.g. 3 years)
2. Open GDPR Cleanup → review list
3. Select persons → Anonymise → Confirm
```

### Export Data via API

```bash
curl -H "X-Joomla-Token: <TOKEN>" \
     https://example.com/api/index.php/v1/cluborganisation/members

# With bank data
curl -H "X-Joomla-Token: <TOKEN>" \
     "https://example.com/api/index.php/v1/cluborganisation/members\
?include_banks=1&encryption_key=MySecret"
```

---

## Useful SQL Queries

**Current member count:**
```sql
SELECT COUNT(DISTINCT p.id) AS active_members
FROM #__cluborganisation_persons p
JOIN #__cluborganisation_memberships m ON p.id = m.person_id
WHERE m.begin <= CURDATE()
  AND (m.end IS NULL OR m.end >= CURDATE())
  AND p.active = 1 AND p.deceased IS NULL;
```

**Distribution by membership type:**
```sql
SELECT t.title, COUNT(*) AS count
FROM #__cluborganisation_memberships m
JOIN #__cluborganisation_membershiptypes t ON m.type = t.id
WHERE m.begin <= CURDATE()
  AND (m.end IS NULL OR m.end >= CURDATE())
GROUP BY t.title ORDER BY count DESC;
```

---

**Date:** February 2026 · **Version:** 2.0.0
