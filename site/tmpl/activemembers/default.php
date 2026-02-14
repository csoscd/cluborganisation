<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Site
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @license     GNU General Public License version 3 or later
 */

defined('_JEXEC') or die;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

// Get menu parameters
$params = $this->params;
?>

<div class="cluborganisation-activemembers">
    <h1><?php echo Text::_('COM_CLUBORGANISATION_ACTIVE_MEMBERS'); ?></h1>

    <form action="<?php echo htmlspecialchars(\Joomla\CMS\Uri\Uri::getInstance()->toString()); ?>" method="post" name="adminForm" id="adminForm">
    
    <?php if (empty($this->items)) : ?>
        <div class="alert alert-info">
            <?php echo Text::_('COM_CLUBORGANISATION_NO_MEMBERS'); ?>
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
                    <?php if ($params->get('show_membership_begin', 1)) : ?>
                        <th><?php echo Text::_('COM_CLUBORGANISATION_BEGIN'); ?></th>
                    <?php endif; ?>
                    <?php if ($params->get('show_membership_end', 0)) : ?>
                        <th><?php echo Text::_('COM_CLUBORGANISATION_END'); ?></th>
                    <?php endif; ?>
                    <?php if ($params->get('show_first_membership', 0)) : ?>
                        <th><?php echo Text::_('COM_CLUBORGANISATION_FIELD_FIRST_MEMBERSHIP'); ?></th>
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
                        <?php if ($params->get('show_membership_begin', 1)) : ?>
                            <td><?php echo HTMLHelper::_('date', $item->begin, 'd.m.Y'); ?></td>
                        <?php endif; ?>
                        <?php if ($params->get('show_membership_end', 0)) : ?>
                            <td><?php echo $item->end ? HTMLHelper::_('date', $item->end, 'd.m.Y') : Text::_('COM_CLUBORGANISATION_ONGOING'); ?></td>
                        <?php endif; ?>
                        <?php if ($params->get('show_first_membership', 0)) : ?>
                            <td><?php echo $item->first_membership_begin ? HTMLHelper::_('date', $item->first_membership_begin, 'd.m.Y') : ''; ?></td>
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
    
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="limitstart" value="" />
    <?php echo HTMLHelper::_('form.token'); ?>
    </form>
</div>
