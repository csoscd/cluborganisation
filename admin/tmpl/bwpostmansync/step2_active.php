<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @license     GNU General Public License version 3 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('behavior.multiselect');

$genderOptions = [
    0 => Text::_('COM_CLUBORGANISATION_BWPOSTMAN_GENDER_MALE'),
    1 => Text::_('COM_CLUBORGANISATION_BWPOSTMAN_GENDER_FEMALE'),
    2 => Text::_('COM_CLUBORGANISATION_BWPOSTMAN_GENDER_NOT_SPECIFIED'),
    3 => Text::_('COM_CLUBORGANISATION_BWPOSTMAN_GENDER_NON_BINARY')
];
?>

<form action="<?php echo Route::_('index.php?option=com_cluborganisation&task=bwpostmansync.sync'); ?>" method="post" name="adminForm" id="adminForm">
    
    <div class="row">
        <div class="col-md-12">
            <!-- Info Card -->
            <div class="card mb-3">
                <div class="card-body">
                    <h3><?php echo Text::_('COM_CLUBORGANISATION_BWPOSTMAN_STEP2_ACTIVE_SUBTITLE'); ?></h3>
                    <p><?php echo Text::_('COM_CLUBORGANISATION_BWPOSTMAN_STEP2_ACTIVE_DESC'); ?></p>
                    
                    <?php if ($this->mailinglist): ?>
                        <div class="alert alert-info">
                            <strong><?php echo Text::_('COM_CLUBORGANISATION_BWPOSTMAN_SELECTED_MAILINGLIST'); ?>:</strong>
                            <?php echo htmlspecialchars($this->mailinglist->title); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Gender Mapping -->
            <?php if (!empty($this->salutations)): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <h4><?php echo Text::_('COM_CLUBORGANISATION_BWPOSTMAN_GENDER_MAPPING_TITLE'); ?></h4>
                        <p class="text-muted"><?php echo Text::_('COM_CLUBORGANISATION_BWPOSTMAN_GENDER_MAPPING_DESC'); ?></p>
                        
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th><?php echo Text::_('COM_CLUBORGANISATION_SALUTATION'); ?></th>
                                    <th><?php echo Text::_('COM_CLUBORGANISATION_BWPOSTMAN_GENDER_MAPPING'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($this->salutations as $salutation): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($salutation->title); ?></strong>
                                        </td>
                                        <td>
                                            <select 
                                                name="gender_mapping[<?php echo $salutation->id; ?>]" 
                                                class="form-select"
                                                required
                                            >
                                                <?php foreach ($genderOptions as $value => $label): ?>
                                                    <option value="<?php echo $value; ?>">
                                                        <?php echo $label; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Members List -->
            <div class="card">
                <div class="card-body">
                    <?php if (empty($this->items)): ?>
                        <div class="alert alert-success">
                            <span class="icon-check" aria-hidden="true"></span>
                            <?php echo Text::_('COM_CLUBORGANISATION_BWPOSTMAN_NO_ACTIVE_MEMBERS_TO_SYNC'); ?>
                        </div>
                    <?php else: ?>
                        <?php
                        // Separate items with and without email
                        $itemsWithEmail = [];
                        $itemsWithoutEmail = [];
                        
                        foreach ($this->items as $item) {
                            if ($item->missing_email) {
                                $itemsWithoutEmail[] = $item;
                            } else {
                                $itemsWithEmail[] = $item;
                            }
                        }
                        ?>
                        
                        <?php if (!empty($itemsWithEmail)): ?>
                            <p class="text-muted">
                                <?php echo Text::sprintf('COM_CLUBORGANISATION_BWPOSTMAN_ACTIVE_COUNT', count($itemsWithEmail)); ?>
                            </p>
                            
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th width="1%" class="text-center">
                                            <?php echo HTMLHelper::_('grid.checkall'); ?>
                                        </th>
                                        <th><?php echo Text::_('COM_CLUBORGANISATION_FIELD_MEMBER_NO'); ?></th>
                                        <th><?php echo Text::_('COM_CLUBORGANISATION_FIELD_FIRSTNAME'); ?></th>
                                        <th><?php echo Text::_('COM_CLUBORGANISATION_FIELD_LASTNAME'); ?></th>
                                        <th><?php echo Text::_('COM_CLUBORGANISATION_FIELD_EMAIL'); ?></th>
                                        <th><?php echo Text::_('COM_CLUBORGANISATION_SALUTATION'); ?></th>
                                        <th><?php echo Text::_('COM_CLUBORGANISATION_BWPOSTMAN_STATUS'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($itemsWithEmail as $i => $item): ?>
                                        <tr>
                                            <td class="text-center">
                                                <?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($item->member_no); ?></td>
                                            <td><?php echo htmlspecialchars($item->firstname); ?></td>
                                            <td><?php echo htmlspecialchars($item->lastname); ?></td>
                                            <td><?php echo htmlspecialchars($item->email); ?></td>
                                            <td><?php echo htmlspecialchars($item->salutation_title ?? '-'); ?></td>
                                            <td>
                                                <?php if ($item->needs_creation): ?>
                                                    <span class="badge bg-primary">
                                                        <?php echo Text::_('COM_CLUBORGANISATION_BWPOSTMAN_STATUS_NEW'); ?>
                                                    </span>
                                                <?php elseif ($item->needs_reactivation): ?>
                                                    <span class="badge bg-warning">
                                                        <?php echo Text::_('COM_CLUBORGANISATION_BWPOSTMAN_STATUS_REACTIVATE'); ?>
                                                    </span>
                                                <?php elseif ($item->needs_connection): ?>
                                                    <span class="badge bg-info">
                                                        <?php echo Text::_('COM_CLUBORGANISATION_BWPOSTMAN_STATUS_CONNECT'); ?>
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            
                            <div class="alert alert-info mt-3">
                                <span class="icon-info" aria-hidden="true"></span>
                                <?php echo Text::_('COM_CLUBORGANISATION_BWPOSTMAN_SYNC_INFO'); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($itemsWithoutEmail)): ?>
                            <div class="mt-4">
                                <h4 class="text-danger">
                                    <span class="icon-warning" aria-hidden="true"></span>
                                    <?php echo Text::_('COM_CLUBORGANISATION_BWPOSTMAN_MISSING_EMAIL_TITLE'); ?>
                                </h4>
                                <p class="text-muted">
                                    <?php echo Text::sprintf('COM_CLUBORGANISATION_BWPOSTMAN_MISSING_EMAIL_DESC', count($itemsWithoutEmail)); ?>
                                </p>
                                
                                <table class="table table-bordered table-striped">
                                    <thead class="table-danger">
                                        <tr>
                                            <th><?php echo Text::_('COM_CLUBORGANISATION_FIELD_MEMBER_NO'); ?></th>
                                            <th><?php echo Text::_('COM_CLUBORGANISATION_FIELD_FIRSTNAME'); ?></th>
                                            <th><?php echo Text::_('COM_CLUBORGANISATION_FIELD_LASTNAME'); ?></th>
                                            <th><?php echo Text::_('COM_CLUBORGANISATION_SALUTATION'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($itemsWithoutEmail as $item): ?>
                                            <tr class="table-danger">
                                                <td><?php echo htmlspecialchars($item->member_no); ?></td>
                                                <td><?php echo htmlspecialchars($item->firstname); ?></td>
                                                <td><?php echo htmlspecialchars($item->lastname); ?></td>
                                                <td><?php echo htmlspecialchars($item->salutation_title ?? '-'); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                
                                <div class="alert alert-warning">
                                    <span class="icon-info" aria-hidden="true"></span>
                                    <?php echo Text::_('COM_CLUBORGANISATION_BWPOSTMAN_MISSING_EMAIL_ACTION'); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (empty($itemsWithEmail) && !empty($itemsWithoutEmail)): ?>
                            <div class="alert alert-warning mt-3">
                                <span class="icon-warning" aria-hidden="true"></span>
                                <?php echo Text::_('COM_CLUBORGANISATION_BWPOSTMAN_ALL_MISSING_EMAIL'); ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <input type="hidden" name="task" value="bwpostmansync.sync">
    <input type="hidden" name="boxchecked" value="0">
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
