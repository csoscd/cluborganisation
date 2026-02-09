<?php
defined('_JEXEC') or die;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

// Get menu parameters
$params = $this->params;
$movementType = $params->get('movement_type', 'entries');
$selectedYear = $params->get('movement_year', date('Y'));

// Title based on movement type
$titleKey = $movementType === 'entries' ? 'COM_CLUBORGANISATION_ENTRIES' : 'COM_CLUBORGANISATION_EXITS';
?>

<div class="cluborganisation-membermovements">
    <h1><?php echo Text::_($titleKey) . ' ' . $selectedYear; ?></h1>

    <?php if (empty($this->items)) : ?>
        <div class="alert alert-info">
            <?php echo $movementType === 'entries' 
                ? Text::_('COM_CLUBORGANISATION_NO_ENTRIES') 
                : Text::_('COM_CLUBORGANISATION_NO_EXITS'); ?>
        </div>
    <?php else : ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <?php if ($params->get('show_member_no', 1)) : ?>
                        <th><?php echo Text::_('COM_CLUBORGANISATION_FIELD_MEMBER_NO'); ?></th>
                    <?php endif; ?>
                    <?php if ($params->get('show_salutation', 0)) : ?>
                        <th><?php echo Text::_('COM_CLUBORGANISATION_FIELD_SALUTATION'); ?></th>
                    <?php endif; ?>
                    <?php if ($params->get('show_firstname', 1)) : ?>
                        <th><?php echo Text::_('COM_CLUBORGANISATION_FIELD_FIRSTNAME'); ?></th>
                    <?php endif; ?>
                    <?php if ($params->get('show_lastname', 1)) : ?>
                        <th><?php echo Text::_('COM_CLUBORGANISATION_FIELD_LASTNAME'); ?></th>
                    <?php endif; ?>
                    <?php if ($params->get('show_address', 0)) : ?>
                        <th><?php echo Text::_('COM_CLUBORGANISATION_FIELD_ADDRESS'); ?></th>
                    <?php endif; ?>
                    <?php if ($params->get('show_zip', 0)) : ?>
                        <th><?php echo Text::_('COM_CLUBORGANISATION_FIELD_ZIP'); ?></th>
                    <?php endif; ?>
                    <?php if ($params->get('show_city', 0)) : ?>
                        <th><?php echo Text::_('COM_CLUBORGANISATION_FIELD_CITY'); ?></th>
                    <?php endif; ?>
                    <?php if ($params->get('show_telephone', 0)) : ?>
                        <th><?php echo Text::_('COM_CLUBORGANISATION_FIELD_TELEPHONE'); ?></th>
                    <?php endif; ?>
                    <?php if ($params->get('show_mobile', 0)) : ?>
                        <th><?php echo Text::_('COM_CLUBORGANISATION_FIELD_MOBILE'); ?></th>
                    <?php endif; ?>
                    <?php if ($params->get('show_email', 0)) : ?>
                        <th><?php echo Text::_('COM_CLUBORGANISATION_FIELD_EMAIL'); ?></th>
                    <?php endif; ?>
                    <?php if ($params->get('show_birthdate', 0)) : ?>
                        <th><?php echo Text::_('COM_CLUBORGANISATION_FIELD_BIRTHDATE'); ?></th>
                    <?php endif; ?>
                    <?php if ($params->get('show_membership_type', 1)) : ?>
                        <th><?php echo Text::_('COM_CLUBORGANISATION_MEMBERSHIP_TYPE'); ?></th>
                    <?php endif; ?>
                    <?php if ($movementType === 'entries') : ?>
                        <?php if ($params->get('show_entry_date', 1)) : ?>
                            <th><?php echo Text::_('COM_CLUBORGANISATION_ENTRY_DATE'); ?></th>
                        <?php endif; ?>
                    <?php else : ?>
                        <?php if ($params->get('show_exit_date', 1)) : ?>
                            <th><?php echo Text::_('COM_CLUBORGANISATION_EXIT_DATE'); ?></th>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if ($params->get('show_entry_year', 0)) : ?>
                        <th><?php echo Text::_('COM_CLUBORGANISATION_ENTRY_YEAR'); ?></th>
                    <?php endif; ?>
                    <?php if ($params->get('show_exit_year', 0)) : ?>
                        <th><?php echo Text::_('COM_CLUBORGANISATION_EXIT_YEAR'); ?></th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->items as $item) : ?>
                    <tr>
                        <?php if ($params->get('show_member_no', 1)) : ?>
                            <td><?php echo $this->escape($item->member_no); ?></td>
                        <?php endif; ?>
                        <?php if ($params->get('show_salutation', 0)) : ?>
                            <td><?php echo $this->escape($item->salutation_title); ?></td>
                        <?php endif; ?>
                        <?php if ($params->get('show_firstname', 1)) : ?>
                            <td><?php echo $this->escape($item->firstname); ?></td>
                        <?php endif; ?>
                        <?php if ($params->get('show_lastname', 1)) : ?>
                            <td><?php echo $this->escape($item->lastname); ?></td>
                        <?php endif; ?>
                        <?php if ($params->get('show_address', 0)) : ?>
                            <td><?php echo $this->escape($item->address); ?></td>
                        <?php endif; ?>
                        <?php if ($params->get('show_zip', 0)) : ?>
                            <td><?php echo $this->escape($item->zip); ?></td>
                        <?php endif; ?>
                        <?php if ($params->get('show_city', 0)) : ?>
                            <td><?php echo $this->escape($item->city); ?></td>
                        <?php endif; ?>
                        <?php if ($params->get('show_telephone', 0)) : ?>
                            <td><?php echo $this->escape($item->telephone); ?></td>
                        <?php endif; ?>
                        <?php if ($params->get('show_mobile', 0)) : ?>
                            <td><?php echo $this->escape($item->mobile); ?></td>
                        <?php endif; ?>
                        <?php if ($params->get('show_email', 0)) : ?>
                            <td><?php echo $this->escape($item->email); ?></td>
                        <?php endif; ?>
                        <?php if ($params->get('show_birthdate', 0)) : ?>
                            <td><?php echo $item->birthday ? HTMLHelper::_('date', $item->birthday, 'd.m.Y') : ''; ?></td>
                        <?php endif; ?>
                        <?php if ($params->get('show_membership_type', 1)) : ?>
                            <td><?php echo $this->escape($item->type_title); ?></td>
                        <?php endif; ?>
                        <?php if ($movementType === 'entries') : ?>
                            <?php if ($params->get('show_entry_date', 1)) : ?>
                                <td><?php echo $item->first_membership_begin ? HTMLHelper::_('date', $item->first_membership_begin, 'd.m.Y') : ''; ?></td>
                            <?php endif; ?>
                        <?php else : ?>
                            <?php if ($params->get('show_exit_date', 1)) : ?>
                                <td><?php echo $item->last_membership_end ? HTMLHelper::_('date', $item->last_membership_end, 'd.m.Y') : ''; ?></td>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if ($params->get('show_entry_year', 0)) : ?>
                            <td><?php echo $this->escape($item->entry_year); ?></td>
                        <?php endif; ?>
                        <?php if ($params->get('show_exit_year', 0)) : ?>
                            <td><?php echo $this->escape($item->exit_year); ?></td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php echo $this->pagination->getListFooter(); ?>
    <?php endif; ?>
</div>
