<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_cluborganisation
 */

declare(strict_types=1);

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

HTMLHelper::_('behavior.formvalidator');
?>
<form action="" method="post" name="adminForm" id="adminForm" class="form-validate">
    <?php foreach ($this->form->getFieldsets() as $fieldset) : ?>
        <fieldset class="options-form">
            <legend><?php echo Text::_($fieldset->label); ?></legend>
            <?php foreach ($this->form->getFieldset($fieldset->name) as $field) : ?>
                <div class="control-group">
                    <div class="control-label"><?php echo $field->label; ?></div>
                    <div class="controls"><?php echo $field->input; ?></div>
                </div>
            <?php endforeach; ?>
        </fieldset>
    <?php endforeach; ?>
    <input type="hidden" name="task" value="">
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
