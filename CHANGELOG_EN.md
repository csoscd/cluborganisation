# Changelog – ClubOrganisation

All notable changes to this project are documented in this file.  
Format follows [Keep a Changelog](https://keepachangelog.com/).

---

## [2.0.0] – 2026-02-20

### New: REST API Export

Joomla-native REST API for machine-readable export of membership data over HTTP.

#### Endpoint

```
GET /api/index.php/v1/cluborganisation/members
```

#### Authentication

Joomla API token in HTTP header:
```
X-Joomla-Token: <Token from user profile>
```
Required permission: `core.manage` on `com_cluborganisation`.

#### Query Parameters

| Parameter | Type | Default | Description |
|---|---|---|---|
| `active_memberships` | 0/1 | 1 | Only active memberships (begin ≤ today, end IS NULL or ≥ today) |
| `include_banks` | 0/1 | 0 | Include bank account data in export |
| `active_banks` | 0/1 | 1 | Only bank accounts with begin ≤ today (only if `include_banks=1`) |
| `encryption_key` | string | – | Required if `include_banks=1`; validated against stored canary value |

Persons without any matching membership are excluded entirely when `active_memberships=1`.

#### Examples

```bash
# All active members (default)
curl -H "X-Joomla-Token: <TOKEN>" \
     https://example.com/api/index.php/v1/cluborganisation/members

# Including ended memberships
curl -H "X-Joomla-Token: <TOKEN>" \
     "https://example.com/api/index.php/v1/cluborganisation/members?active_memberships=0"

# With bank data (key required)
curl -H "X-Joomla-Token: <TOKEN>" \
     "https://example.com/api/index.php/v1/cluborganisation/members?include_banks=1&encryption_key=MySecret"
```

#### Response Format

```json
{
  "success": true,
  "exported": "2026-02-20T14:30:00+01:00",
  "options": { "active_memberships": true, "include_banks": false, "active_banks": null },
  "count": 42,
  "members": [
    {
      "id": 1, "member_no": "2024-001", "salutation": "Mr",
      "firstname": "John", "lastname": "Doe",
      "email": "john@example.com", "address": "1 Main St",
      "memberships": [
        { "id": 5, "type": "Full Member", "begin": "2024-01-01", "end": null, "comment": null }
      ]
    }
  ]
}
```

#### Error Responses

| HTTP Code | Cause |
|---|---|
| 401 | Not authenticated or `encryption_key` missing/invalid |
| 403 | Insufficient permissions (`core.manage`) |
| 500 | Internal server error |

#### New Files (Component)

| Source file | Installation target |
|---|---|
| `api_provider.php` | `api/services/provider.php` |
| `ApiClubOrganisationComponent.php` | `api/src/Extension/ClubOrganisationComponent.php` |
| `ApiMembersController.php` | `api/src/Controller/MembersController.php` |
| `ApiExportModel.php` | `api/src/Model/ExportModel.php` |

#### Also Required: Webservices Plugin

The plugin `plg_webservices_cluborganisation` must be installed and enabled separately. It registers the API route with Joomla's API router via `onBeforeApiRoute`. Without it, all requests return 404.

#### Changed Files

- `cluborganisation.xml`: Version 2.0.0, `<api>` block added

---

## [1.9.0] – 2026-02-20

### New: Encryption Key Management for Bank Data

- Unlock screen before accessing bank data list; key stored in PHP session only
- **Canary mechanism**: `CLUBORG_KEY_CHECK_v1` is encrypted and stored in `#__extensions` on first save; `EncryptionHelper::verifyKey()` provides deterministic validation
- Key rotation modal (custom JS overlay) re-encrypts all records and updates canary
- Read-only detail view (`layout=view`) for bank accounts
- Date validation: bank begin must fall within membership period
- Dropdown filtered to active/future memberships only
- Auto-fill account holder from person name
- Masked IBAN display in list view

---

## [1.8.0] – 2026-02

### New: BwPostman Synchronisation
- 3-step sync with BwPostman newsletter component
- Auto-create/reactivate active members as subscribers
- Archive inactive members in BwPostman
- Configurable mailing list assignment and gender mapping; transaction-safe

---

## [1.7.0] – 2026-01

### New: Membership Fees & Fee Report
- Fee management per membership type (date-based)
- Frontend view for public fee display
- Backend fee report with annual totals

---

## [1.1.0] – 2026-01

### New
- Frontend views: Active Members, Entries/Exits, My Profile, Membership List
- Configurable column display via menu item parameters
- GDPR Cleanup Tool
- Migration Tool for Clubmanagement import

---

## [1.0.0] – 2025-12

### Initial Release
- Person management (full contact data)
- Membership management (date-based, overlap validation)
- Bank data management (AES-256-CBC encrypted)
- Master data: salutations, membership types
- ACL integration, multilingual (DE/EN), Joomla 5.x/6.x
