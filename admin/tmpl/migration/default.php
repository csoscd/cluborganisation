<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive');

$app = Factory::getApplication();
$checkResult = $app->getUserState('com_cluborganisation.migration.check', null);
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h2><?php echo Text::_('COM_CLUBORGANISATION_MIGRATION_TITLE'); ?></h2>
                <p><?php echo Text::_('COM_CLUBORGANISATION_MIGRATION_DESCRIPTION'); ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Statistics -->
<div class="row mt-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3><?php echo Text::_('COM_CLUBORGANISATION_MIGRATION_SOURCE_DATA'); ?></h3>
            </div>
            <div class="card-body">
                <p><strong><?php echo Text::_('COM_CLUBORGANISATION_MIGRATION_SOURCE_PERSONS'); ?>:</strong> 
                    <?php echo $this->sourceStats['persons']; ?></p>
                <p><strong><?php echo Text::_('COM_CLUBORGANISATION_MIGRATION_SOURCE_MEMBERSHIPS'); ?>:</strong> 
                    <?php echo $this->sourceStats['memberships']; ?></p>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h3><?php echo Text::_('COM_CLUBORGANISATION_MIGRATION_TARGET_DATA'); ?></h3>
            </div>
            <div class="card-body">
                <p><strong><?php echo Text::_('COM_CLUBORGANISATION_MIGRATION_TARGET_PERSONS'); ?>:</strong> 
                    <?php echo $this->targetStats['persons']; ?></p>
                <p><strong><?php echo Text::_('COM_CLUBORGANISATION_MIGRATION_TARGET_MEMBERSHIPS'); ?>:</strong> 
                    <?php echo $this->targetStats['memberships']; ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Step 1: Check Prerequisites -->
<div class="row mt-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h3><?php echo Text::_('COM_CLUBORGANISATION_MIGRATION_STEP1'); ?></h3>
            </div>
            <div class="card-body">
                <p><?php echo Text::_('COM_CLUBORGANISATION_MIGRATION_STEP1_DESC'); ?></p>
                
                <?php if ($checkResult) : ?>
                    <div class="alert alert-<?php echo $checkResult['success'] ? 'success' : 'danger'; ?>">
                        <?php echo $checkResult['message']; ?>
                    </div>
                <?php endif; ?>
                
                <form action="<?php echo Route::_('index.php?option=com_cluborganisation&task=migration.check'); ?>" method="post">
                    <?php echo HTMLHelper::_('form.token'); ?>
                    <button type="submit" class="btn btn-primary">
                        <?php echo Text::_('COM_CLUBORGANISATION_MIGRATION_CHECK_BUTTON'); ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Step 2: Mapping (only if check done and items need mapping) -->
<?php if ($checkResult && $checkResult['success'] && 
    (!empty($checkResult['missing_salutations']) || !empty($checkResult['missing_types']))) : ?>
<div class="row mt-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h3><?php echo Text::_('COM_CLUBORGANISATION_MIGRATION_STEP2_MAPPING'); ?></h3>
            </div>
            <div class="card-body">
                <p><?php echo Text::_('COM_CLUBORGANISATION_MIGRATION_STEP2_MAPPING_DESC'); ?></p>
                
                <!-- Salutation Mappings -->
                <?php if (!empty($checkResult['missing_salutations'])) : ?>
                    <div class="alert alert-info">
                        <h4><?php echo Text::_('COM_CLUBORGANISATION_MIGRATION_MAP_SALUTATIONS'); ?></h4>
                        <p><?php echo Text::_('COM_CLUBORGANISATION_MIGRATION_MAP_SALUTATIONS_DESC'); ?></p>
                        
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th><?php echo Text::_('COM_CLUBORGANISATION_MIGRATION_SOURCE_VALUE'); ?></th>
                                    <th><?php echo Text::_('COM_CLUBORGANISATION_MIGRATION_MAP_TO'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($checkResult['missing_salutations'] as $missingSalutation) : ?>
                                    <tr>
                                        <td><strong><?php echo $this->escape($missingSalutation); ?></strong></td>
                                        <td>
                                            <select name="salutation_mapping[<?php echo $this->escape($missingSalutation); ?>]" 
                                                    class="form-select" required>
                                                <option value=""><?php echo Text::_('COM_CLUBORGANISATION_MIGRATION_SELECT_TARGET'); ?></option>
                                                <?php foreach ($checkResult['available_salutations'] as $salutation) : ?>
                                                    <option value="<?php echo $salutation->id; ?>">
                                                        <?php echo $this->escape($salutation->title); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
                
                <!-- Membership Type Mappings -->
                <?php if (!empty($checkResult['missing_types'])) : ?>
                    <div class="alert alert-info">
                        <h4><?php echo Text::_('COM_CLUBORGANISATION_MIGRATION_MAP_TYPES'); ?></h4>
                        <p><?php echo Text::_('COM_CLUBORGANISATION_MIGRATION_MAP_TYPES_DESC'); ?></p>
                        
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th><?php echo Text::_('COM_CLUBORGANISATION_MIGRATION_SOURCE_VALUE'); ?></th>
                                    <th><?php echo Text::_('COM_CLUBORGANISATION_MIGRATION_MAP_TO'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($checkResult['missing_types'] as $missingType) : ?>
                                    <tr>
                                        <td><strong><?php echo $this->escape($missingType); ?></strong></td>
                                        <td>
                                            <select name="type_mapping[<?php echo $this->escape($missingType); ?>]" 
                                                    class="form-select" required>
                                                <option value=""><?php echo Text::_('COM_CLUBORGANISATION_MIGRATION_SELECT_TARGET'); ?></option>
                                                <?php foreach ($checkResult['available_types'] as $type) : ?>
                                                    <option value="<?php echo $type->id; ?>">
                                                        <?php echo $this->escape($type->title); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Step 3: Migration Options (only if check passed) -->
<?php if ($checkResult && $checkResult['success']) : ?>
<div class="row mt-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h3><?php echo Text::_('COM_CLUBORGANISATION_MIGRATION_STEP3'); ?></h3>
            </div>
            <div class="card-body">
                <p><?php echo Text::_('COM_CLUBORGANISATION_MIGRATION_STEP3_DESC'); ?></p>
                
                <form action="<?php echo Route::_('index.php?option=com_cluborganisation&task=migration.migrate'); ?>" 
                      method="post" id="migration-form">
                    <?php echo HTMLHelper::_('form.token'); ?>
                    
                    <!-- Include mappings in form -->
                    <?php if (!empty($checkResult['missing_salutations'])) : ?>
                        <?php foreach ($checkResult['missing_salutations'] as $missingSalutation) : ?>
                            <input type="hidden" 
                                   name="salutation_mapping[<?php echo $this->escape($missingSalutation); ?>]" 
                                   class="salutation-mapping-<?php echo md5($missingSalutation); ?>" 
                                   value="">
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <?php if (!empty($checkResult['missing_types'])) : ?>
                        <?php foreach ($checkResult['missing_types'] as $missingType) : ?>
                            <input type="hidden" 
                                   name="type_mapping[<?php echo $this->escape($missingType); ?>]" 
                                   class="type-mapping-<?php echo md5($missingType); ?>" 
                                   value="">
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <div class="alert alert-warning">
                        <h4><?php echo Text::_('COM_CLUBORGANISATION_MIGRATION_WARNING'); ?></h4>
                        <p><?php echo Text::_('COM_CLUBORGANISATION_MIGRATION_WARNING_DESC'); ?></p>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <input type="radio" name="truncate" value="0" checked>
                            <?php echo Text::_('COM_CLUBORGANISATION_MIGRATION_MODE_APPEND'); ?>
                        </label>
                        <p class="form-text text-muted">
                            <?php echo Text::_('COM_CLUBORGANISATION_MIGRATION_MODE_APPEND_DESC'); ?>
                        </p>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <input type="radio" name="truncate" value="1">
                            <?php echo Text::_('COM_CLUBORGANISATION_MIGRATION_MODE_REPLACE'); ?>
                        </label>
                        <p class="form-text text-muted">
                            <?php echo Text::_('COM_CLUBORGANISATION_MIGRATION_MODE_REPLACE_DESC'); ?>
                        </p>
                    </div>
                    
                    <div class="form-group mt-3">
                        <label>
                            <input type="checkbox" id="confirm-migration" required>
                            <strong><?php echo Text::_('COM_CLUBORGANISATION_MIGRATION_CONFIRM'); ?></strong>
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-danger btn-lg mt-3" id="migrate-button" disabled>
                        <?php echo Text::_('COM_CLUBORGANISATION_MIGRATION_START_BUTTON'); ?>
                    </button>
                </form>
                
                <script>
                // Enable/disable migrate button based on checkbox
                document.getElementById('confirm-migration').addEventListener('change', function() {
                    document.getElementById('migrate-button').disabled = !this.checked;
                });
                
                // Copy mapping selections to hidden fields in migration form
                <?php if (!empty($checkResult['missing_salutations'])) : ?>
                    <?php foreach ($checkResult['missing_salutations'] as $missingSalutation) : ?>
                        document.querySelector('select[name="salutation_mapping[<?php echo addslashes($missingSalutation); ?>]"]').addEventListener('change', function() {
                            document.querySelector('.salutation-mapping-<?php echo md5($missingSalutation); ?>').value = this.value;
                        });
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <?php if (!empty($checkResult['missing_types'])) : ?>
                    <?php foreach ($checkResult['missing_types'] as $missingType) : ?>
                        document.querySelector('select[name="type_mapping[<?php echo addslashes($missingType); ?>]"]').addEventListener('change', function() {
                            document.querySelector('.type-mapping-<?php echo md5($missingType); ?>').value = this.value;
                        });
                    <?php endforeach; ?>
                <?php endif; ?>
                
                // Validate all mappings are selected before submit
                document.getElementById('migration-form').addEventListener('submit', function(e) {
                    let allSelected = true;
                    
                    // Check salutation mappings
                    document.querySelectorAll('select[name^="salutation_mapping"]').forEach(function(select) {
                        if (!select.value) {
                            allSelected = false;
                            select.classList.add('is-invalid');
                        } else {
                            select.classList.remove('is-invalid');
                        }
                    });
                    
                    // Check type mappings
                    document.querySelectorAll('select[name^="type_mapping"]').forEach(function(select) {
                        if (!select.value) {
                            allSelected = false;
                            select.classList.add('is-invalid');
                        } else {
                            select.classList.remove('is-invalid');
                        }
                    });
                    
                    if (!allSelected) {
                        e.preventDefault();
                        alert('<?php echo Text::_('COM_CLUBORGANISATION_MIGRATION_ERROR_MAPPINGS_INCOMPLETE'); ?>');
                        return false;
                    }
                });
                </script>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Help Section -->
<div class="row mt-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3><?php echo Text::_('COM_CLUBORGANISATION_MIGRATION_HELP_TITLE'); ?></h3>
            </div>
            <div class="card-body">
                <h4><?php echo Text::_('COM_CLUBORGANISATION_MIGRATION_FIELD_MAPPING'); ?></h4>
                <table class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th><?php echo Text::_('COM_CLUBORGANISATION_MIGRATION_SOURCE_FIELD'); ?></th>
                            <th><?php echo Text::_('COM_CLUBORGANISATION_MIGRATION_TARGET_FIELD'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>nokCM_persons.id</td><td>persons.id</td></tr>
                        <tr><td>nokCM_persons.salutation</td><td>persons.salutation</td></tr>
                        <tr><td>nokCM_persons.firstname</td><td>persons.firstname</td></tr>
                        <tr><td>nokCM_persons.name</td><td>persons.lastname</td></tr>
                        <tr><td>nokCM_persons.custom1</td><td>persons.member_no</td></tr>
                        <tr><td colspan="2">... <?php echo Text::_('COM_CLUBORGANISATION_MIGRATION_MORE_FIELDS'); ?></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
