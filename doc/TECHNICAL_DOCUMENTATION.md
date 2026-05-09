# ClubOrganisation – Technical Documentation

**Version:** 2.3.0
**Joomla:** 5.x / 6.x
**PHP:** 8.1+

---

## Architecture

### MVC Pattern

ClubOrganisation follows the Joomla MVC pattern across three applications:

| Application | Namespace suffix | Purpose |
|---|---|---|
| `admin/` | `Administrator` | Backend management |
| `api/` | `Api` | REST API |
| `site/` | `Site` | Frontend display |

### Namespace Structure

```
CSOSCD\Component\ClubOrganisation\
├── Administrator\  (Controller, Extension, Field, Helper, Model, Table, View)
├── Api\            (Controller, Extension, Model)
└── Site\           (Controller, Extension, Model, View)
```

### Dependency Injection

Each application has its own `services/provider.php` registering MVCFactory and ComponentDispatcherFactory with Joomla's DI container.

---

## Encryption (EncryptionHelper)

### Methods

```php
EncryptionHelper::encrypt(string $plaintext, string $key): string
EncryptionHelper::decrypt(string $ciphertext, string $key): string|false
EncryptionHelper::saveCanary(string $key): void
EncryptionHelper::verifyKey(string $key): bool
EncryptionHelper::getStoredCanary(): string|null
```

### Canary Mechanism

```
Constant: CLUBORG_KEY_CHECK_v1
    ↓
Encrypted with active key
    ↓
Stored in #__extensions.params['encryption_canary']

Validation:
    stored canary + key → decrypt() → compare with constant → true/false
```

The canary is written when:
- First `MembershipbankTable::store()` call (idempotent)
- Successful key rotation (`MembershipbanksModel::reencryptAll()`)

### Key Lifecycle

```
1. Admin enters key in unlock screen
2. Key validated via EncryptionHelper::verifyKey()
3. On success: stored in PHP session
4. Bank data access: key read from session
5. Lock: session entry deleted
6. Session end: automatically cleared
```

---

## REST API

### Routing

The webservices plugin registers the route:

```php
$router->createCRUDRoutes(
    'v1/cluborganisation/members',
    'members',
    ['component' => 'com_cluborganisation']
);
```

`createCRUDRoutes()` maps GET (collection) to `MembersController::displayList()`.

### MembersController

`displayList()` overrides the `ApiController` method to bypass Joomla's View layer (no `jsonapi` view exists). Flow:

```
1. Check authentication ($app->getIdentity())
2. Check permission (core.manage on com_cluborganisation)
3. Read parameters from input
4. Call ExportModel::getMembers($options)
5. Output JSON directly (header() + echo + $app->close())
```

### ExportModel

Standalone class (no Joomla base model), gets DB connection via `Factory::getContainer()`:

```
Load persons (LEFT JOIN salutations)
    ↓
Load memberships (optionally filtered to active)
    ↓
Load + decrypt bank accounts (optionally filtered to active)
    ↓
Exclude persons without matching memberships when active_memberships=1
    ↓
Return array
```

---

## Statistics (new in 2.1.0)

### Architecture

Follows the same pattern as `Feereport`: no dedicated controller (Joomla default dispatch), `StatisticsModel` extends `BaseDatabaseModel`.

### Reference Date Queries

All queries use a reference date parameter:

```sql
-- Active members at reference date
SELECT COUNT(DISTINCT m.person_id)
FROM #__cluborganisation_memberships m
INNER JOIN #__cluborganisation_persons p ON p.id = m.person_id
WHERE m.begin <= :refDate
  AND (m.end IS NULL OR m.end >= :refDate)
  AND p.active = 1
```

- Monthly development: last day of each month
- Yearly development: 31 Dec of each year; current year uses today
- Memberships ending on 31 Dec are still counted for that year
- Duration calculated as `DATEDIFF(refDate, MIN(begin)) / 365.25`
- Age groups derived from birthday vs. reference date using PHP `strtotime`

### Charts

Chart.js 4.4.1 loaded from `cdnjs.cloudflare.com`. All charts use `responsive: true` inside a 300 px wrapper div. Future months are passed as `null` and rendered as gaps.

### Configuration

New `reporting` fieldset in `config.xml` with field `statistics_start_year` (default: 2020). Accessed via `ComponentHelper::getParams('com_cluborganisation')`.

---

## Database Design

### ER Diagram

```
persons (1) ──< (n) memberships (n) >── (1) membershiptypes
   │                    │                        │
   │             (1) ──< (0..n) membershipbanks  │ (self-ref: depends_on_type)
   │                    │
   │             memberships.depends_on_membership_id ──> memberships.id
   │
(n) >── (1) salutations
```

### Key SQL Patterns

**Active membership:**
```sql
WHERE m.begin <= CURDATE()
  AND (m.end IS NULL OR m.end >= CURDATE())
```

**Active bank account:**
```sql
WHERE b.begin <= CURDATE()
```

**Person with no active membership (GDPR protection):**
```sql
WHERE (SELECT COUNT(*) FROM #__cluborganisation_memberships m
       WHERE m.person_id = p.id AND m.end IS NULL) = 0
```

---

## Dependent Membership Types (new in 2.3.0)

### Concept

A membership type can be marked as *dependent* (`is_dependent = 1`) and linked to a *parent type* (`depends_on_type → membershiptypes.id`). When creating a membership of a dependent type, the user must select an existing membership of the parent type (`depends_on_membership_id → memberships.id`).

**Example:** "Familienmitglied (zahlend)" is the paying parent type. "Familienmitglied" is the dependent type. Every membership of type "Familienmitglied" must reference a specific membership of type "Familienmitglied (zahlend)".

### Database Columns

**`membershiptypes`:**

| Column | Type | Description |
|---|---|---|
| `is_dependent` | TINYINT(1) | 0 = normal type, 1 = dependent type |
| `depends_on_type` | INT UNSIGNED | FK → membershiptypes.id (the parent type) |

**`memberships`:**

| Column | Type | Description |
|---|---|---|
| `depends_on_membership_id` | INT UNSIGNED | FK → memberships.id (the parent membership) |

### Validation (`MembershipTable::checkDependentType()`)

Called from `check()` before every save:

```
1. Load is_dependent + depends_on_type for $this->type
2. If is_dependent = 0 → pass
3. If is_dependent = 1 and depends_on_membership_id is empty → error
4. Load the referenced parent membership
5. Check that parent membership's type equals depends_on_type → error if not
```

### Cascade: Setting an End Date

When `MembershipModel::save()` is called and `$data['end']` is set, after a successful save `cascadeEndDateToDependents()` is invoked:

```php
private function cascadeEndDateToDependents(int $parentId, string $endDate): void
{
    // UPDATE memberships
    // SET end = $endDate
    // WHERE depends_on_membership_id = $parentId
    //   AND (end IS NULL OR end > $endDate)
}
```

Condition `end IS NULL OR end > $endDate` ensures dependents that already have an *earlier* end date are never modified.

### Cascade: Removing an End Date

When the parent membership's end date is *cleared*, `cascadeRemoveEndDateFromDependents()` is invoked with the *previous* end date (read from DB before the save):

```php
private function cascadeRemoveEndDateFromDependents(int $parentId, string $oldEndDate): void
{
    // UPDATE memberships
    // SET end = NULL
    // WHERE depends_on_membership_id = $parentId
    //   AND end = $oldEndDate
}
```

The exact-match condition (`end = $oldEndDate`) means only dependents whose end date was *inherited* from the parent are reverted. Dependents with a manually set (earlier) end date are left unchanged.

### Pre-Save Old End Date Read

`MembershipModel::save()` reads the current end date from the database *before* calling `parent::save()`:

```php
$oldEnd = null;
if ($itemId > 0) {
    $oldRow = $db->setQuery(
        $db->getQuery(true)->select('end')->from('#__cluborganisation_memberships')->where('id = ' . $itemId)
    )->loadObject();
    $oldEnd = $oldRow ? ($oldRow->end ?: null) : null;
}
$result = parent::save($data);
if ($result) {
    if ($newEnd !== null) {
        $this->cascadeEndDateToDependents($savedId, $newEnd);
    } elseif ($oldEnd !== null && $newEnd === null) {
        $this->cascadeRemoveEndDateFromDependents($savedId, $oldEnd);
    }
}
```

### `has_dependents` Detection in the View

`Membership/HtmlView` must know, for each type, whether other types depend on it (so it can show a cascade warning). This is resolved server-side:

```php
// 1. Load all types with their is_dependent / depends_on_type flags
$membershipTypes = $db->loadObjectList('id');

// 2. Collect all type IDs that are referenced as a parent
$parentTypeIds = $db->loadColumn(); // SELECT DISTINCT depends_on_type WHERE is_dependent=1

// 3. Flag each type
foreach ($membershipTypes as $mt) {
    $mt->has_dependents = in_array($mt->id, $parentTypeIds);
}
```

This data is serialised as JSON and passed to the template's JavaScript so the warning box can be shown/hidden dynamically when the user changes the type dropdown.

### Dynamic Parent Membership Dropdown

The `depends_on_membership_id` field is an empty `<select>` in `membership.xml`. Its options are loaded on the fly via AJAX:

```
type dropdown change
    → fetch task=membership.getParentMemberships&type_id=X
    → MembershipController::getParentMemberships()
    → DB: load memberships of parent type (across all persons)
    → JSON: {success, is_dependent, data:[{id, begin, end, person_name}]}
    → JS populates <select> or hides the wrapper
```

### Cascade Warning Box

A `#co-cascade-warning` alert div is rendered by PHP (hidden by default). JavaScript shows it whenever the selected type has `has_dependents = true`. The message distinguishes between the save-with-end-date and the save-without-end-date scenarios via two language constants:

| Constant | Trigger |
|---|---|
| `MEMBERSHIP_CASCADE_WARNING` | Type has dependents (always shown when has_dependents) |
| `MEMBERSHIP_CASCADE_WARNING_COUNT` | Count of currently linked dependent memberships |

---

## Key Code Patterns

### ListModel with populateState

```php
protected function populateState($ordering = null, $direction = null)
{
    $app    = Factory::getApplication();
    $params = $app->getParams();
    $this->setState('params', $params);
    $limit = $app->getUserStateFromRequest('global.list.limit', 'limit',
        $params->get('display_num', 20), 'uint');
    $this->setState('list.limit', $limit);
    $this->setState('list.start', $app->input->get('limitstart', 0, 'uint'));
    $this->setState('list.ordering', $params->get('orderby_pri', 'lastname'));
    $this->setState('list.direction', $params->get('order_dir', 'ASC'));
    parent::populateState($ordering, $direction);
}
```

### Transaction-Safe Operation

```php
public function criticalOperation(): bool
{
    $db = $this->getDatabase();
    try {
        $db->transactionStart();
        // ... operations ...
        $db->transactionCommit();
        return true;
    } catch (\Exception $e) {
        $db->transactionRollback();
        $this->setError($e->getMessage());
        return false;
    }
}
```

### Prepared Statements

```php
// Correct
$query->where($db->quoteName('id') . ' = ' . $db->quote($id));

// Wrong – SQL injection risk
$query->where('id = ' . $id);
```

---

## Request Flow

### Backend: Edit Person

```
GET ...&view=person&id=42
    → PersonController::display()
    → PersonModel::getItem(42)
    → Person/HtmlView::display()
    → person/edit.php
    → HTML response

POST (save)
    → PersonController::save()
    → $this->checkToken()
    → PersonModel::save()
    → PersonTable::check() + store()
    → Redirect to list
```

### API: GET /members

```
GET /api/index.php/v1/cluborganisation/members
    → ApiApplication::dispatch()
    → PlgWebservicesCluborganisation::onBeforeApiRoute() (route registered)
    → MembersController::displayList()
    → Auth + permission check
    → ExportModel::getMembers($options)
    → SQL: persons + memberships + membershipbanks
    → JSON: echo + $app->close()
```

---

## Frontend Template: Pagination

For working pagination, templates must include:
- `<form>` wrapping both table and pagination output
- Hidden fields: `task`, `limitstart`
- CSRF token
- `populateState()` implemented in the model

---

## Security Checklist

| Measure | Implementation |
|---|---|
| XSS | `$this->escape()` / `htmlspecialchars()` in all templates |
| SQL injection | `$db->quote()` / `$db->quoteName()` everywhere |
| CSRF | `HTMLHelper::_('form.token')` + `$this->checkToken()` |
| ACL | `$user->authorise()` before critical actions |
| Encryption | AES-256-CBC via EncryptionHelper |
| Key | PHP session only, never in DB or logs |
| API auth | Joomla API token, permission check in controller |

---

## GDPR Implementation

### Anonymised Values

| Field | Anonymised value |
|---|---|
| `firstname` | `Anonymised` |
| `lastname` | `Person [ID]` |
| `email` | `anonymised_[ID]@deleted.local` |
| `birthday` | `1970-01-01` |
| `address`, `telephone`, `mobile` | Empty string |
| `active` | `0` |
| Bank data | Completely deleted |

### Protection Against Accidental Anonymisation

```sql
-- Check: does the person have active memberships?
SELECT COUNT(*) FROM #__cluborganisation_memberships
WHERE person_id = :id AND end IS NULL
-- Must be 0, otherwise error
```

---

**Date:** March 2026 · **Version:** 2.3.0
