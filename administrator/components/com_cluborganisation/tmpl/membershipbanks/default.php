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
<h1><?php echo Text::_('COM_CLUBORGANISATION_TABLE_MEMBERSHIP_BANKS'); ?></h1>
<table class="table">
    <thead>
        <tr>
            <th><?php echo Text::_('JGLOBAL_FIELD_ID_LABEL'); ?></th>
            <th><?php echo Text::_('COM_CLUBORGANISATION_FIELD_MEMBERSHIP_LABEL'); ?></th>
            <th><?php echo Text::_('COM_CLUBORGANISATION_FIELD_BEGIN_LABEL'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($this->items as $item) : ?>
            <tr>
                <td><?php echo (int) $item->id; ?></td>
                <td><?php echo (int) $item->membership_id; ?></td>
                <td><?php echo $this->escape($item->begin); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php echo $this->pagination->getListFooter(); ?>
