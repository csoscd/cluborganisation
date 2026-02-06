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
<h1><?php echo Text::_('COM_CLUBORGANISATION_TABLE_PERSONS'); ?></h1>
<form method="get">
    <div class="filters">
        <label>
            <?php echo Text::_('COM_CLUBORGANISATION_FILTER_LASTNAME'); ?>
            <input type="text" name="filter_lastname" value="<?php echo $this->escape($this->state->get('filter.lastname')); ?>">
        </label>
        <label>
            <?php echo Text::_('COM_CLUBORGANISATION_FILTER_FIRSTNAME'); ?>
            <input type="text" name="filter_firstname" value="<?php echo $this->escape($this->state->get('filter.firstname')); ?>">
        </label>
        <label>
            <?php echo Text::_('COM_CLUBORGANISATION_FILTER_MEMBER_NO'); ?>
            <input type="text" name="filter_member_no" value="<?php echo $this->escape($this->state->get('filter.member_no')); ?>">
        </label>
        <label>
            <?php echo Text::_('COM_CLUBORGANISATION_FILTER_ACTIVE'); ?>
            <select name="filter_active">
                <option value="">-</option>
                <option value="1" <?php echo $this->state->get('filter.active') === '1' ? 'selected' : ''; ?>><?php echo Text::_('JYES'); ?></option>
                <option value="0" <?php echo $this->state->get('filter.active') === '0' ? 'selected' : ''; ?>><?php echo Text::_('JNO'); ?></option>
            </select>
        </label>
        <button type="submit" class="btn btn-primary"><?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?></button>
    </div>
</form>
<table class="table">
    <thead>
        <tr>
            <th><?php echo Text::_('JGLOBAL_FIELD_ID_LABEL'); ?></th>
            <th><?php echo Text::_('COM_CLUBORGANISATION_FIELD_LASTNAME_LABEL'); ?></th>
            <th><?php echo Text::_('COM_CLUBORGANISATION_FIELD_FIRSTNAME_LABEL'); ?></th>
            <th><?php echo Text::_('COM_CLUBORGANISATION_FIELD_MEMBER_NO_LABEL'); ?></th>
            <th><?php echo Text::_('COM_CLUBORGANISATION_FIELD_ACTIVE_LABEL'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($this->items as $item) : ?>
            <tr>
                <td><?php echo (int) $item->id; ?></td>
                <td><?php echo $this->escape($item->lastname); ?></td>
                <td><?php echo $this->escape($item->firstname); ?></td>
                <td><?php echo $this->escape($item->member_no); ?></td>
                <td><?php echo $item->active ? Text::_('JYES') : Text::_('JNO'); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php echo $this->pagination->getListFooter(); ?>
