# ClubOrganisation – Technical Documentation

**Version:** 2.0.0  
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

## Database Design

### ER Diagram

```
persons (1) ──< (n) memberships (n) >── (1) membershiptypes
   │                    │
   │             (1) ──< (0..n) membershipbanks
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

**Date:** February 2026 · **Version:** 2.0.0
