<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_cluborganisation
 */

declare(strict_types=1);

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

HTMLHelper::_('behavior.core');
?>
<h1><?php echo Text::_('COM_CLUBORGANISATION_TABLE_MEMBERSHIPS'); ?></h1>
<form method="get">
    <div class="filters">
        <label>
            <?php echo Text::_('COM_CLUBORGANISATION_FILTER_BEGIN'); ?>
            <input type="date" name="filter_begin" value="<?php echo $this->escape($this->state->get('filter.begin')); ?>">
        </label>
        <label>
            <?php echo Text::_('COM_CLUBORGANISATION_FILTER_END'); ?>
            <input type="date" name="filter_end" value="<?php echo $this->escape($this->state->get('filter.end')); ?>">
        </label>
        <label>
            <?php echo Text::_('COM_CLUBORGANISATION_FILTER_PERSON'); ?>
            <input type="number" name="filter_person_id" value="<?php echo $this->escape($this->state->get('filter.person_id')); ?>">
        </label>
        <label>
            <?php echo Text::_('COM_CLUBORGANISATION_FILTER_CATEGORY'); ?>
            <input type="number" name="filter_catid" value="<?php echo $this->escape($this->state->get('filter.catid')); ?>">
        </label>
        <button type="submit" class="btn btn-primary"><?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?></button>
    </div>
</form>
<table class="table">
    <thead>
        <tr>
            <th><?php echo Text::_('JGLOBAL_FIELD_ID_LABEL'); ?></th>
            <th><?php echo Text::_('COM_CLUBORGANISATION_FIELD_PERSON_LABEL'); ?></th>
            <th><?php echo Text::_('COM_CLUBORGANISATION_FIELD_MEMBERSHIP_TYPE_LABEL'); ?></th>
            <th><?php echo Text::_('COM_CLUBORGANISATION_FIELD_BEGIN_LABEL'); ?></th>
            <th><?php echo Text::_('COM_CLUBORGANISATION_FIELD_END_LABEL'); ?></th>
            <th><?php echo Text::_('COM_CLUBORGANISATION_FIELD_CATID_LABEL'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($this->items as $item) : ?>
            <tr>
                <td><?php echo (int) $item->id; ?></td>
                <td><?php echo (int) $item->person_id; ?></td>
                <td><?php echo (int) $item->type_id; ?></td>
                <td><?php echo $this->escape($item->begin); ?></td>
                <td><?php echo $this->escape($item->end); ?></td>
                <td><?php echo (int) $item->catid; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php echo $this->pagination->getListFooter(); ?>
