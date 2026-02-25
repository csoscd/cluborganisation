# Changelog – ClubOrganisation

All notable changes to this project are documented in this file.  
Format follows [Keep a Changelog](https://keepachangelog.com/).

---

## [2.2.0] – 2026-02-24

### New: Dashboard

New entry point for the component (replaces the persons list as default view).

**KPI tiles:**
- Active persons
- Active memberships
- New members this month
- Memberships ending in the current year (without a follow-up membership) – red when > 0
- Open data gaps from Data Check (only shown when > 0, then red)

**Sections:**
- Installed component version as a badge
- Update notice if the Joomla Update Manager knows of an update for `com_cluborganisation` (with direct link to Update Manager)
- Extension status: Birthday Module and Webservices API Plugin – shows installed badge or download link
- Data Check summary with counts per category and direct link to Data Check
- Anniversaries in the current month (only shown when present)

**New files:** `DashboardModel.php`, `DashboardHtmlView.php`, `dashboard_default.php`

### New: Structured Admin Menu

The submenu is divided into 6 groups (separators without `link` attribute):
Dashboard | Members | Finances | Reporting | Communication | Master Data | System

### Extended: Data Check

#### New check: Active persons without a current membership
- Lists all persons with `active = 1` but no membership with `end IS NULL`
- Each row has an edit link and a **Deactivate** button
- Confirmation dialog before executing
- After deactivation the user stays on the Data Check page
- New controller `DatacheckController` with `deactivatePerson()`:
  - CSRF protection via `Session::checkToken()`
  - Sets `active = 0` and updates `modified` timestamp
  - Task: `datacheck.deactivatePerson`

### Fixed: Statistics filters

Removed `active` filter from all statistical snapshot methods. The admin flag `active`
carries no historical information; statistics must be membership-date based only.

| Method | Impact |
|---|---|
| `countMembersAtDate()` | Monthly/yearly member development – deactivated persons correctly included |
| `countMembersByTypeAtDate()` | Member structure by type |
| `countMembersByAgeAtDate()` | Age structure |
| `getAverageAgeAtDate()` | Average age |

`getMemberJoinsPerYear()` and `getMemberLeavesPerYear()` had no persons join → already correct.

### Technical
- `DisplayController.php`: `$default_view = 'dashboard'`
- `cluborganisation.xml`: version 2.2.0, new structured `<submenu>`
- `auto_install.sh`: Dashboard and DatacheckController integrated; version 2.2.0
- New language constants in `de-DE` and `en-GB`

---

## [2.1.0] – 2026-02-23

### New: Extended Statistics

#### Fluctuation (joins / leaves / net change)
- Line chart **Joins & Leavers per Year** since start year (orange = joins, blue = leavers)
- Bar chart **Net Change per Year**: bars in #f29838 (positive) and #132d6a (negative)
- Entry year = `YEAR(MIN(begin))` across all memberships of the person
- Leave year = `YEAR(MAX(end))` where no `end IS NULL` membership exists

#### Anniversaries
- Table **Anniversaries current year**: active members with 5/10/20/25/40-year anniversaries
- Table **Preview next year**: members with anniversary in the following year; only if an active membership will still exist next year
- Sorted by entry year ascending, then last name / first name

### New: Data Check

New menu item **Data Check** with five check lists for active members:
- Missing date of birth (NULL, 0000-00-00 or 1970-01-01)
- Missing email address
- Missing mobile number
- No Joomla user linked (`user_id` NULL or 0)
- No membership at all

Each row has a direct link to the person edit form (`view=person&layout=edit`).
Badge shows number of affected persons or "Complete" if no gaps.

**New files:** `DatacheckModel.php`, `DatacheckHtmlView.php`, `datacheck_default.php`

### Technical
- `StatisticsModel.php`: new methods `getMemberJoinsPerYear()`, `getMemberLeavesPerYear()`, `getMemberAnniversaries()`
- `StatisticsHtmlView.php`: chart data for fluctuation and anniversaries
- `statistics_default.php`: rows 5 (fluctuation) and 6 (anniversaries) added
- `auto_install.sh`: Data Check view, model and template integrated
- New language constants in `de-DE` and `en-GB`

#### Bug fixes applied in 2.1.0
- **Charts empty (CSP):** Switched to `$doc->addScriptDeclaration()` via `buildInitScript()` – Joomla 4.2+/5.x automatically adds the CSP nonce attribute. `statistics.js` removed.
- **Membership duration wrong count:** New logic: (1) only persons with at least one `end IS NULL` membership; (2) earliest `begin` across **all** memberships; (3) year difference = `YEAR(ref date) – YEAR(earliest begin)`. PHP-side grouping via `loadDurationData()`.
- **Duration/age averages:** New methods `getAverageDurationAtDate()` and updated `getAverageAgeAtDate()`, with `<tfoot>` rows in both tables.
- **Language constants:** `COM_CLUBORGANISATION_STATS_DUR_AVG`, `COM_CLUBORGANISATION_STATS_AGE_AVG` added/corrected in `de-DE` and `en-GB`.
- **GROUP BY alias (MySQL 1056/1111):** `getMemberJoinsPerYear()` and `getMemberLeavesPerYear()` rewritten with subqueries to avoid grouping on calculated aliases or aggregate functions.

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
