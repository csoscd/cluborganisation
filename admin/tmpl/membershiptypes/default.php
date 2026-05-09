<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @license     GNU General Public License version 3 or later
 */

defined('_JEXEC') or die;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
?>
<form action="<?php echo Route::_('index.php?option=com_cluborganisation&view=membershiptypes'); ?>" method="post" name="adminForm" id="adminForm">
    <table class="table table-striped">
        <thead><tr>
            <td class="w-1"><?php echo HTMLHelper::_('grid.checkall'); ?></td>
            <th>ID</th>
            <th><?php echo Text::_('COM_CLUBORGANISATION_FIELD_TITLE'); ?></th>
            <th><?php echo Text::_('COM_CLUBORGANISATION_FIELD_IS_DEPENDENT'); ?></th>
            <th><?php echo Text::_('COM_CLUBORGANISATION_FIELD_ORDERING'); ?></th>
        </tr></thead>
        <tbody>
        <?php foreach ($this->items as $i => $item) : ?>
            <tr>
                <td><?php echo HTMLHelper::_('grid.id', $i, $item->id); ?></td>
                <td><?php echo $item->id; ?></td>
                <td>
                    <a href="<?php echo Route::_('index.php?option=com_cluborganisation&task=membershiptype.edit&id=' . (int) $item->id); ?>">
                        <?php echo $this->escape($item->title); ?>
                    </a>
                </td>
                <td>
                    <?php if ($item->is_dependent) : ?>
                        <span class="badge bg-info"><?php echo Text::_('JYES'); ?></span>
                        <?php if (!empty($item->parent_type_title)) : ?>
                            <small class="text-muted">(<?php echo $this->escape($item->parent_type_title); ?>)</small>
                        <?php endif; ?>
                    <?php else : ?>
                        <span class="badge bg-secondary"><?php echo Text::_('JNO'); ?></span>
                    <?php endif; ?>
                </td>
                <td><?php echo $item->ordering; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
