# ClubOrganisation - Technische Dokumentation

**Version:** 1.1.0  
**Joomla:** 5.x / 6.x  
**PHP:** 8.1+

---

## 🏗️ Architektur

### MVC-Pattern

ClubOrganisation folgt dem Joomla MVC-Pattern:

```
Request → Controller → Model → Database
                     ↓
                   View → Template → Response
```

**Komponenten:**
- **Model:** Geschäftslogik, Datenzugriff, Validierung
- **View:** Daten für Template vorbereiten, State setzen
- **Controller:** Request-Steuerung, Aktionen, Redirects
- **Template:** HTML-Ausgabe, Formulare, Listen

### Dependency Injection

**Service Provider:** `admin/services/provider.php`

```php
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\Extension\Service\Provider\CategoryFactory;

public function register(Container $container): void
{
    $container->registerServiceProvider(new CategoryFactory(...));
    $container->registerServiceProvider(new MVCFactory(...));
    $container->registerServiceProvider(new ComponentDispatcherFactory(...));
    
    $container->set(
        ComponentInterface::class,
        function (Container $container) {
            $component = new ClubOrganisationComponent($container->get(ComponentDispatcherFactoryInterface::class));
            $component->setMVCFactory($container->get(MVCFactoryInterface::class));
            return $component;
        }
    );
}
```

### Namespace-Struktur

```
CSOSCD\Component\ClubOrganisation\
├── Administrator\           # Backend
│   ├── Controller\
│   ├── Extension\
│   ├── Field\
│   ├── Helper\
│   ├── Model\
│   ├── Table\
│   └── View\
└── Site\                   # Frontend
    ├── Controller\
    ├── Extension\
    ├── Model\
    └── View\
```

---

## 🗄️ Datenbank-Design

### ER-Diagramm (vereinfacht)

```
persons (1) ──< (n) memberships (n) >── (1) membershiptypes
   │                    │
   │                    │
   │             (1) ──< (0..1) membershipbanks
   │
(1) >── (0..1) salutations
```

### Tabellen-Details

#### 1. persons

**Primärschlüssel:** `id`

**Felder:**
- **Stammdaten:** salutation, firstname, middlename, lastname, birthname
- **Kontakt:** address, city, zip, country, telephone, mobile, email
- **Mitgliedschaft:** member_no, entry_year, exit_year
- **System:** user_id, birthday, deceased, image, active
- **Audit:** created_by, created, modified_by, modified

**Besonderheiten:**
- `entry_year`: Berechnet aus MIN(memberships.begin)
- `exit_year`: Berechnet aus MAX(memberships.end)
- `active`: 0 = anonymisiert, 1 = aktiv
- `member_no`: Eindeutig (UNIQUE)

**Indizes:**
```sql
PRIMARY KEY (id)
UNIQUE KEY member_no (member_no)
KEY user_id (user_id)
KEY active (active)
KEY entry_year (entry_year)
KEY exit_year (exit_year)
```

#### 2. memberships

**Primärschlüssel:** `id`

**Felder:**
- **Verknüpfungen:** person_id, type (→ membershiptypes)
- **Zeitraum:** begin, end
- **Beitrag:** fee_amount
- **Audit:** created_by, created, modified_by, modified

**Besonderheiten:**
- Zeitraum-Überschneidungsprüfung im Model
- Maximal eine aktive Mitgliedschaft (end IS NULL) pro Person
- Soft-Delete möglich

**Indizes:**
```sql
PRIMARY KEY (id)
KEY person_id (person_id)
KEY type (type)
KEY begin (begin)
KEY end (end)
```

#### 3. membershipbanks

**Primärschlüssel:** `id`

**Felder:**
- **Verknüpfung:** membership_id
- **Verschlüsselt:** accountname, iban, bic (AES-256-CBC)
- **Audit:** created_by, created, modified_by, modified

**Besonderheiten:**
- Alle sensiblen Felder verschlüsselt
- Schlüssel nie in Datenbank
- Session-basierter Zugriff

**Indizes:**
```sql
PRIMARY KEY (id)
KEY membership_id (membership_id)
```

#### 4. salutations

**Primärschlüssel:** `id`

**Felder:**
- **Stammdaten:** title, ordering, state

**Standard-Einträge:**
1. Herr
2. Frau
3. Divers

#### 5. membershiptypes

**Primärschlüssel:** `id`

**Felder:**
- **Stammdaten:** title, description, ordering, state

**Standard-Einträge:**
1. Einzelmitglied
2. Einzelmitglied (reduziert)
3. Familienmitglied (zahlend)
4. Familienmitglied

---

## 🔐 Sicherheit

### Verschlüsselung

**Methode:** AES-256-CBC (Sodium)

**EncryptionHelper.php:**
```php
use SodiumException;

class EncryptionHelper
{
    public static function encrypt(string $data, string $key): string
    {
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $encrypted = sodium_crypto_secretbox($data, $nonce, $key);
        return base64_encode($nonce . $encrypted);
    }
    
    public static function decrypt(string $encrypted, string $key): string
    {
        $decoded = base64_decode($encrypted);
        $nonce = substr($decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $ciphertext = substr($decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        return sodium_crypto_secretbox_open($ciphertext, $nonce, $key);
    }
}
```

**Verwendung:**
```php
// Speichern
$encrypted = EncryptionHelper::encrypt($plaintext, $sessionKey);
$table->iban = $encrypted;
$table->store();

// Laden
$encrypted = $table->iban;
$plaintext = EncryptionHelper::decrypt($encrypted, $sessionKey);
```

**Schlüssel-Management:**
- Schlüssel in Session gespeichert
- Nie in Datenbank
- Eingabe bei jedem Zugriff erforderlich
- 256-Bit Schlüssel

### Validierung

**E-Mail:**
```php
use Joomla\CMS\Filter\InputFilter;

$filter = InputFilter::getInstance();
$email = $filter->clean($input, 'string');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    throw new \Exception('Invalid email format');
}
```

**Zeitraum-Überschneidung:**
```php
// In MembershipModel::validate()
$db = $this->getDatabase();
$query = $db->getQuery(true);

$query->select('COUNT(*)')
    ->from($db->quoteName('#__cluborganisation_memberships'))
    ->where($db->quoteName('person_id') . ' = ' . $db->quote($personId))
    ->where($db->quoteName('id') . ' != ' . $db->quote($id))
    ->where('(' .
        '(' . $db->quoteName('begin') . ' <= ' . $db->quote($end) . ' AND ' .
         $db->quoteName('end') . ' >= ' . $db->quote($begin) . ')' .
        ' OR ' .
        '(' . $db->quoteName('end') . ' IS NULL AND ' .
         $db->quoteName('begin') . ' <= ' . $db->quote($end) . ')' .
    ')');

if ($db->setQuery($query)->loadResult() > 0) {
    throw new \Exception('Membership period overlaps');
}
```

### SQL-Injection-Schutz

**Prepared Statements:**
```php
$query = $db->getQuery(true);
$query->select('*')
    ->from($db->quoteName('#__cluborganisation_persons'))
    ->where($db->quoteName('id') . ' = ' . $db->quote($id));

$db->setQuery($query);
$result = $db->loadObject();
```

**Niemals:**
```php
// FALSCH - Anfällig für SQL Injection
$query = "SELECT * FROM persons WHERE id = " . $id;
```

### XSS-Schutz

**Output Escaping:**
```php
// In Templates
<?php echo $this->escape($item->firstname); ?>
<?php echo htmlspecialchars($item->email, ENT_QUOTES, 'UTF-8'); ?>
```

### CSRF-Schutz

**Tokens:**
```php
// In Templates
<?php echo HTMLHelper::_('form.token'); ?>

// Im Controller
$this->checkToken() or jexit(Text::_('JINVALID_TOKEN'));
```

### ACL

**Berechtigungs-Prüfung:**
```php
$user = Factory::getApplication()->getIdentity();

// Komponenten-Level
if (!$user->authorise('core.manage', 'com_cluborganisation')) {
    throw new \Exception('Access denied');
}

// Asset-Level
if (!$user->authorise('core.edit', 'com_cluborganisation.person.' . $id)) {
    throw new \Exception('Access denied');
}
```

---

## 📊 Daten-Flow

### Backend: Person bearbeiten

```
1. Request: index.php?option=com_cluborganisation&view=person&id=123

2. Controller:
   - PersonController::edit()
   - Prüft ACL (core.edit)
   - Lädt Model

3. Model:
   - PersonModel::getItem(123)
   - Lädt Datensatz aus DB
   - Füllt Form-Data

4. View:
   - PersonHtmlView::display()
   - Rendert Formular
   - Lädt Template

5. Template:
   - person/edit.php
   - Zeigt Formular an
   - CSRF Token

6. POST (Speichern):
   - PersonController::save()
   - Prüft Token
   - Validiert Daten
   - PersonModel::save()
   - Redirect zur Liste

7. Response:
   - Success Message
   - Redirect zu Persons-Liste
```

### Frontend: Aktive Mitglieder

```
1. Request: index.php?option=com_cluborganisation&view=activemembers

2. Controller:
   - DisplayController::display()
   - Routing zu View

3. Model:
   - ActivemembersModel::getItems()
   - populateState() - Limit, Ordering lesen
   - getListQuery() - SQL Query bauen
   - Subqueries für Entry/Exit Year

4. View:
   - ActivemembersHtmlView::display()
   - Items laden
   - Pagination vorbereiten

5. Template:
   - activemembers/default.php
   - Tabelle rendern
   - Spalten nach Params
   - Pagination

6. Response:
   - HTML-Output
   - Tabelle mit Mitgliedern
   - Pagination-Controls
```

---

## 🔧 Wichtige Code-Patterns

### Pattern 1: ListModel mit PopulateState

```php
class MyModel extends ListModel
{
    protected function populateState($ordering = null, $direction = null)
    {
        $app = Factory::getApplication();
        $params = $app->getParams();
        $this->setState('params', $params);
        
        // Limit lesen und speichern (mit Session)
        $limit = $app->getUserStateFromRequest(
            'global.list.limit',
            'limit',
            $params->get('display_num', 20),
            'uint'
        );
        $this->setState('list.limit', $limit);
        
        // Start-Position
        $limitstart = $app->input->get('limitstart', 0, 'uint');
        $this->setState('list.start', $limitstart);
        
        // Ordering
        $this->setState('list.ordering', $params->get('orderby_pri', 'lastname'));
        $this->setState('list.direction', $params->get('order_dir', 'ASC'));
        
        parent::populateState($ordering, $direction);
    }
    
    protected function getListQuery()
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        
        // SELECT, FROM, WHERE, ORDER
        
        return $query;
    }
}
```

### Pattern 2: Subquery für Entry/Exit Year

```php
// Entry Year (MIN)
$subQueryEntry = $db->getQuery(true);
$subQueryEntry->select('MIN(m2.begin)')
    ->from($db->quoteName('#__cluborganisation_memberships', 'm2'))
    ->where('m2.person_id = p.id');

// Exit Year (MAX)
$subQueryExit = $db->getQuery(true);
$subQueryExit->select('MAX(m3.end)')
    ->from($db->quoteName('#__cluborganisation_memberships', 'm3'))
    ->where('m3.person_id = p.id')
    ->where('m3.end IS NOT NULL');

// In Query verwenden
$query->select([
    'p.*',
    '(' . $subQueryEntry . ') AS first_membership_begin',
    'YEAR((' . $subQueryEntry . ')) AS entry_year',
    '(' . $subQueryExit . ') AS last_membership_end',
    'YEAR((' . $subQueryExit . ')) AS exit_year'
])
->from($db->quoteName('#__cluborganisation_persons', 'p'));
```

### Pattern 3: Active Membership Check

```php
// Subquery für aktive Mitgliedschaften
$activeSubQuery = $db->getQuery(true);
$activeSubQuery->select('COUNT(*)')
    ->from($db->quoteName('#__cluborganisation_memberships', 'm4'))
    ->where('m4.person_id = p.id')
    ->where('m4.end IS NULL');

// In WHERE verwenden
->where('(' . $activeSubQuery . ') = 0')  // Keine aktiven Mitgliedschaften
```

### Pattern 4: Transaction-Safe Operations

```php
public function criticalOperation($data)
{
    $db = $this->getDatabase();
    
    try {
        $db->transactionStart();
        
        // Operation 1
        $query1 = $db->getQuery(true);
        // ... build query
        $db->setQuery($query1);
        $db->execute();
        
        // Operation 2
        $query2 = $db->getQuery(true);
        // ... build query
        $db->setQuery($query2);
        $db->execute();
        
        $db->transactionCommit();
        
        return ['success' => true];
        
    } catch (\Exception $e) {
        $db->transactionRollback();
        
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}
```

### Pattern 5: Custom Field Type

```php
class YearrangeField extends ListField
{
    protected $type = 'Yearrange';

    protected function getOptions()
    {
        $options = [];
        $currentYear = (int) date('Y');
        $startYear = $currentYear - 50;
        
        for ($year = $currentYear; $year >= $startYear; $year--) {
            $options[] = HTMLHelper::_('select.option', $year, $year);
        }
        
        return array_merge(parent::getOptions(), $options);
    }
}
```

---

## 📝 Template-Strukturen

### Backend-Template (Liste)

**Struktur mit Filter, Sortierung, Pagination:**
```php
<?php
defined('_JEXEC') or die;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$saveOrder = $listOrder == 'a.ordering';
?>

<form action="<?php echo Route::_('index.php?option=com_cluborganisation&view=persons'); ?>" 
      method="post" 
      name="adminForm" 
      id="adminForm">
    
    <!-- Filter -->
    <?php echo $this->filterForm->renderField('search'); ?>
    
    <table class="table table-striped">
        <thead>
            <tr>
                <th width="1%">
                    <?php echo HTMLHelper::_('grid.checkall'); ?>
                </th>
                <th>
                    <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                </th>
                <!-- Weitere Spalten -->
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->items as $i => $item) : ?>
                <tr>
                    <td>
                        <?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                    </td>
                    <td>
                        <a href="<?php echo $editLink; ?>">
                            <?php echo $this->escape($item->title); ?>
                        </a>
                    </td>
                    <!-- Weitere Zellen -->
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <!-- Pagination -->
    <?php echo $this->pagination->getListFooter(); ?>
    
    <!-- Hidden Fields -->
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
```

### Frontend-Template (Liste)

**Struktur mit Pagination:**
```php
<?php
defined('_JEXEC') or die;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$params = $this->params;
?>

<div class="cluborganisation-view">
    <h1><?php echo Text::_('COM_CLUBORGANISATION_TITLE'); ?></h1>

    <form action="<?php echo htmlspecialchars(\Joomla\CMS\Uri\Uri::getInstance()->toString()); ?>" 
          method="post" 
          name="adminForm" 
          id="adminForm">
    
        <?php if (empty($this->items)) : ?>
            <div class="alert alert-info">
                <?php echo Text::_('COM_CLUBORGANISATION_NO_ITEMS'); ?>
            </div>
        <?php else : ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <?php if ($params->get('show_member_no', 1)) : ?>
                            <th><?php echo Text::_('COM_CLUBORGANISATION_FIELD_MEMBER_NO'); ?></th>
                        <?php endif; ?>
                        <!-- Weitere Spalten basierend auf Params -->
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($this->items as $item) : ?>
                        <tr>
                            <?php if ($params->get('show_member_no', 1)) : ?>
                                <td><?php echo $this->escape($item->member_no); ?></td>
                            <?php endif; ?>
                            <!-- Weitere Zellen -->
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php echo $this->pagination->getListFooter(); ?>
        <?php endif; ?>
        
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="limitstart" value="" />
        <?php echo HTMLHelper::_('form.token'); ?>
    </form>
</div>
```

**Wichtig für Pagination:**
- `<form>` Element umschließt Tabelle und Pagination
- Hidden Fields: `task`, `limitstart`
- CSRF Token
- `populateState()` im Model

---

## 📝 Best Practices

### 1. Immer Escapen

```php
// Im Template
<?php echo $this->escape($item->name); ?>
<?php echo htmlspecialchars($item->email, ENT_QUOTES, 'UTF-8'); ?>
```

### 2. Prepared Statements

```php
// RICHTIG
$query->where($db->quoteName('id') . ' = ' . $db->quote($id));

// FALSCH
$query->where('id = ' . $id);
```

### 3. ACL prüfen

```php
// Vor kritischen Operationen
$user = Factory::getApplication()->getIdentity();
if (!$user->authorise('core.edit', 'com_cluborganisation.person.' . $id)) {
    throw new \Exception(Text::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'));
}
```

### 4. CSRF Token

```php
// Im Formular
<?php echo HTMLHelper::_('form.token'); ?>

// Im Controller
$this->checkToken() or jexit(Text::_('JINVALID_TOKEN'));
```

### 5. Error Handling

```php
try {
    // Operation
} catch (\Exception $e) {
    $this->setMessage($e->getMessage(), 'error');
    $this->setRedirect($returnUrl);
    return false;
}
```

### 6. populateState() für ListModels

```php
// Immer implementieren
protected function populateState($ordering = null, $direction = null)
{
    // Params, Limit, Start, Ordering setzen
    parent::populateState($ordering, $direction);
}
```

### 7. Subqueries statt JOINs

```php
// Bevorzugt für berechnete Werte
$subQuery = $db->getQuery(true);
$subQuery->select('COUNT(*)')...;

$query->select('(' . $subQuery . ') AS count');
```

---

**Stand:** Februar 2026  
**Version:** 1.1.0
