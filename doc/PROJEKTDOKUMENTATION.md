# ClubOrganisation – Technische Dokumentation

**Version:** 2.0.0  
**Joomla:** 5.x / 6.x  
**PHP:** 8.1+

---

## Architektur

### MVC-Pattern

ClubOrganisation folgt dem Joomla MVC-Pattern:

```
Request → Controller → Model → Database
                     ↓
                   View → Template → Response
```

Die Komponente ist in drei Applikationen aufgeteilt:

| Applikation | Namespace-Präfix | Zweck |
|---|---|---|
| `admin/` | `Administrator` | Backend-Verwaltung |
| `api/` | `Api` | REST-API |
| `site/` | `Site` | Frontend-Darstellung |

### Namespace-Struktur

```
CSOSCD\Component\ClubOrganisation\
├── Administrator\
│   ├── Controller\
│   ├── Extension\
│   ├── Field\
│   ├── Helper\
│   ├── Model\
│   ├── Table\
│   └── View\
├── Api\
│   ├── Controller\
│   ├── Extension\
│   └── Model\
└── Site\
    ├── Controller\
    ├── Extension\
    ├── Model\
    └── View\
```

### Dependency Injection

Jede Applikation hat einen eigenen Service Provider (`services/provider.php`), der MVCFactory und ComponentDispatcherFactory registriert.

```php
// admin/services/provider.php (vereinfacht)
$container->registerServiceProvider(new MVCFactory('\\CSOSCD\\Component\\ClubOrganisation'));
$container->registerServiceProvider(new ComponentDispatcherFactory('\\CSOSCD\\Component\\ClubOrganisation'));
```

---

## Verschlüsselung (EncryptionHelper)

### Methoden

```php
// Verschlüsseln
EncryptionHelper::encrypt(string $plaintext, string $key): string

// Entschlüsseln
EncryptionHelper::decrypt(string $ciphertext, string $key): string|false

// Canary speichern (beim ersten Bankdatensatz)
EncryptionHelper::saveCanary(string $key): void

// Canary validieren
EncryptionHelper::verifyKey(string $key): bool

// Gespeicherten Canary lesen
EncryptionHelper::getStoredCanary(): string|null
```

### Canary-Mechanismus

```
Konstante: CLUBORG_KEY_CHECK_v1
    ↓
verschlüsselt mit aktivem Schlüssel
    ↓
gespeichert in #__extensions.params['encryption_canary']

Validierung:
    gespeicherter Canary + Schlüssel
    → decrypt()
    → Vergleich mit CLUBORG_KEY_CHECK_v1
    → true / false
```

Der Canary wird geschrieben beim:
- Ersten Aufruf von `MembershipbankTable::store()` (idempotent)
- Erfolgreicher Key Rotation (`MembershipbanksModel::reencryptAll()`)

### Schlüssel-Lifecycle

```
1. Admin gibt Schlüssel in Entsperr-Maske ein
2. Schlüssel wird mit EncryptionHelper::verifyKey() geprüft
3. Bei Erfolg: in PHP-Session speichern
4. Zugriff auf Bankdaten: Schlüssel aus Session lesen
5. Sperren: Session-Eintrag löschen
6. Session-Ende: automatisch gelöscht
```

---

## REST-API

### Routing

Das Webservices-Plugin (`plg_webservices_cluborganisation`) registriert die Route:

```php
$router->createCRUDRoutes(
    'v1/cluborganisation/members',
    'members',
    ['component' => 'com_cluborganisation']
);
```

`createCRUDRoutes()` mappt GET (Collection) auf `MembersController::displayList()`.

### MembersController

`displayList()` überschreibt die Methode des `ApiController`, um die Joomla-View-Schicht zu umgehen (es gibt keine `jsonapi`-View). Ablauf:

```
1. Authentifizierung prüfen ($app->getIdentity())
2. Berechtigung prüfen (core.manage on com_cluborganisation)
3. Parameter aus Input lesen
4. ExportModel::getMembers($options) aufrufen
5. JSON direkt ausgeben (header() + echo + $app->close())
```

### ExportModel

Eigenständige Klasse (kein Joomla-Basis-Model), holt DB-Verbindung via `Factory::getContainer()`:

```
Personen laden (LEFT JOIN salutations)
    ↓
Mitgliedschaften laden (ggf. aktiv-gefiltert)
    ↓
Bankdaten laden + entschlüsseln (ggf. aktiv-gefiltert)
    ↓
Personen ohne passende Mitgliedschaft bei active_memberships=1 ausschließen
    ↓
Array-Ausgabe
```

---

## Datenbank-Design

### ER-Diagramm

```
persons (1) ──< (n) memberships (n) >── (1) membershiptypes
   │                    │
   │             (1) ──< (0..n) membershipbanks
   │
(n) >── (1) salutations
```

### Wichtige SQL-Patterns

**Aktive Mitgliedschaft:**
```sql
WHERE m.begin <= CURDATE()
  AND (m.end IS NULL OR m.end >= CURDATE())
```

**Aktive Bankverbindung:**
```sql
WHERE b.begin <= CURDATE()
```

**Mitglied mit aktiver Mitgliedschaft (für DSGVO-Schutz):**
```sql
WHERE (
    SELECT COUNT(*) FROM #__cluborganisation_memberships m
    WHERE m.person_id = p.id AND m.end IS NULL
) = 0
```

---

## Wichtige Code-Patterns

### ListModel mit populateState

```php
protected function populateState($ordering = null, $direction = null)
{
    $app    = Factory::getApplication();
    $params = $app->getParams();
    $this->setState('params', $params);

    $limit = $app->getUserStateFromRequest(
        'global.list.limit', 'limit',
        $params->get('display_num', 20), 'uint'
    );
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
        // ... Operationen ...
        $db->transactionCommit();
        return true;
    } catch (\Exception $e) {
        $db->transactionRollback();
        $this->setError($e->getMessage());
        return false;
    }
}
```

### Subquery für Entry/Exit Year

```php
$subQueryEntry = $db->getQuery(true)
    ->select('MIN(m2.begin)')
    ->from($db->quoteName('#__cluborganisation_memberships', 'm2'))
    ->where('m2.person_id = p.id');

$query->select('YEAR((' . $subQueryEntry . ')) AS entry_year');
```

### Prepared Statements

```php
// Richtig
$query->where($db->quoteName('id') . ' = ' . $db->quote($id));

// Falsch
$query->where('id = ' . $id);
```

---

## Request-Flow

### Backend: Person bearbeiten

```
GET index.php?option=com_cluborganisation&view=person&id=42
    → PersonController::display()
    → PersonModel::getItem(42)
    → SQL: SELECT * FROM persons WHERE id=42
    → Person/HtmlView::display()
    → person/edit.php (Formular)
    → Response: HTML

POST (Speichern)
    → PersonController::save()
    → $this->checkToken()
    → PersonModel::save()
    → PersonTable::check() + store()
    → Redirect zur Liste
```

### API: GET /members

```
GET /api/index.php/v1/cluborganisation/members
    → ApiApplication::dispatch()
    → PlgWebservicesCluborganisation::onBeforeApiRoute() (Route registriert)
    → MembersController::displayList()
    → Auth + Berechtigungsprüfung
    → ExportModel::getMembers($options)
    → SQL: persons + memberships + membershipbanks
    → JSON: echo + $app->close()
```

---

## Template-Struktur

### Backend Liste (Grundstruktur)

```php
<form action="<?php echo Route::_('...'); ?>" method="post" name="adminForm">
    <?php echo $this->filterForm->renderField('search'); ?>
    <table class="table">
        <thead>
            <tr>
                <th><?php echo HTMLHelper::_('grid.checkall'); ?></th>
                <th><?php echo HTMLHelper::_('searchtools.sort', 'Label', 'a.field', $listDirn, $listOrder); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->items as $i => $item): ?>
                <tr>
                    <td><?php echo HTMLHelper::_('grid.id', $i, $item->id); ?></td>
                    <td><?php echo $this->escape($item->title); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php echo $this->pagination->getListFooter(); ?>
    <input type="hidden" name="task" value="">
    <input type="hidden" name="boxchecked" value="0">
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
```

### Frontend Liste (Pagination)

Für funktionierende Pagination müssen zwingend enthalten sein:
- `<form>` umschließt Tabelle + Pagination
- Hidden Fields: `task`, `limitstart`
- CSRF-Token
- `populateState()` im Model implementiert

---

## Sicherheit

### Checkliste

| Maßnahme | Implementierung |
|---|---|
| XSS | `$this->escape()` / `htmlspecialchars()` in allen Templates |
| SQL-Injection | `$db->quote()` / `$db->quoteName()` überall |
| CSRF | `HTMLHelper::_('form.token')` + `$this->checkToken()` |
| ACL | `$user->authorise()` vor kritischen Aktionen |
| Verschlüsselung | AES-256-CBC via EncryptionHelper |
| Schlüssel | Nur in PHP-Session, nie in DB oder Logs |
| API-Auth | Joomla API-Token, Berechtigungsprüfung im Controller |

---

## DSGVO-Implementierung

### Anonymisierungs-Felder

| Feld | Anonymisierter Wert |
|---|---|
| `firstname` | `Anonymisiert` |
| `lastname` | `Person [ID]` |
| `email` | `anonymisiert_[ID]@deleted.local` |
| `birthday` | `1970-01-01` |
| `address`, `telephone`, `mobile` | Leerstring |
| `active` | `0` |
| Bankdaten | Vollständig gelöscht |

### Schutz vor falscher Anonymisierung

```sql
-- Prüfung: hat die Person aktive Mitgliedschaften?
SELECT COUNT(*) FROM #__cluborganisation_memberships
WHERE person_id = :id AND end IS NULL
-- Muss 0 ergeben, sonst Fehler
```

---

**Stand:** Februar 2026 · **Version:** 2.0.0
