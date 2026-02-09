<?php
defined('_JEXEC') or die;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
?>
<form action="<?php echo Route::_('index.php?option=com_cluborganisation&view=salutations'); ?>" method="post" name="adminForm" id="adminForm">
    <table class="table table-striped">
        <thead><tr>
            <td class="w-1"><?php echo HTMLHelper::_('grid.checkall'); ?></td>
            <th>ID</th><th>Titel</th><th>Reihenfolge</th>
        </tr></thead>
        <tbody>
        <?php foreach ($this->items as $i => $item) : ?>
            <tr>
                <td><?php echo HTMLHelper::_('grid.id', $i, $item->id); ?></td>
                <td><?php echo $item->id; ?></td>
                <td><?php echo $this->escape($item->title); ?></td>
                <td><?php echo $item->ordering; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
