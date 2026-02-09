<?php
defined('_JEXEC') or die;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
?>

<div class="cluborganisation-membershiplist">
    <h1><?php echo Text::_('COM_CLUBORGANISATION_MEMBERSHIP_LIST'); ?></h1>

    <form method="get" action="<?php echo Route::_('index.php?option=com_cluborganisation&view=membershiplist'); ?>">
        <div class="mb-3">
            <label for="year"><?php echo Text::_('COM_CLUBORGANISATION_SELECT_YEAR'); ?>:</label>
            <select name="year" id="year" class="form-select" onchange="this.form.submit()">
                <?php foreach ($this->yearOptions as $year) : ?>
                    <option value="<?php echo $year; ?>" <?php echo ($year == $this->selectedYear) ? 'selected' : ''; ?>>
                        <?php echo $year; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <input type="hidden" name="option" value="com_cluborganisation" />
        <input type="hidden" name="view" value="membershiplist" />
    </form>

    <h2><?php echo Text::_('COM_CLUBORGANISATION_NEW_MEMBERSHIPS') . ' ' . $this->selectedYear; ?></h2>
    <?php if (empty($this->newMemberships)) : ?>
        <p><?php echo Text::_('COM_CLUBORGANISATION_NO_NEW_MEMBERSHIPS'); ?></p>
    <?php else : ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th><?php echo Text::_('COM_CLUBORGANISATION_FIELD_MEMBER_NO'); ?></th>
                    <th><?php echo Text::_('COM_CLUBORGANISATION_NAME'); ?></th>
                    <th><?php echo Text::_('COM_CLUBORGANISATION_MEMBERSHIP_TYPE'); ?></th>
                    <th><?php echo Text::_('COM_CLUBORGANISATION_BEGIN'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->newMemberships as $item) : ?>
                    <tr>
                        <td><?php echo $this->escape($item->member_no); ?></td>
                        <td><?php echo $this->escape($item->lastname . ', ' . $item->firstname); ?></td>
                        <td><?php echo $this->escape($item->type_title); ?></td>
                        <td><?php echo HTMLHelper::_('date', $item->begin, 'd.m.Y'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <h2><?php echo Text::_('COM_CLUBORGANISATION_ENDED_MEMBERSHIPS') . ' ' . $this->selectedYear; ?></h2>
    <?php if (empty($this->endedMemberships)) : ?>
        <p><?php echo Text::_('COM_CLUBORGANISATION_NO_ENDED_MEMBERSHIPS'); ?></p>
    <?php else : ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th><?php echo Text::_('COM_CLUBORGANISATION_FIELD_MEMBER_NO'); ?></th>
                    <th><?php echo Text::_('COM_CLUBORGANISATION_NAME'); ?></th>
                    <th><?php echo Text::_('COM_CLUBORGANISATION_MEMBERSHIP_TYPE'); ?></th>
                    <th><?php echo Text::_('COM_CLUBORGANISATION_END'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->endedMemberships as $item) : ?>
                    <tr>
                        <td><?php echo $this->escape($item->member_no); ?></td>
                        <td><?php echo $this->escape($item->lastname . ', ' . $item->firstname); ?></td>
                        <td><?php echo $this->escape($item->type_title); ?></td>
                        <td><?php echo HTMLHelper::_('date', $item->end, 'd.m.Y'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
