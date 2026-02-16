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
?>

<form action="<?php echo Route::_('index.php?option=com_cluborganisation&task=bwpostmansync.sync'); ?>" method="post" name="adminForm" id="adminForm">
    
    <div class="row">
        <div class="col-md-12">
            <!-- Info Card -->
            <div class="card mb-3">
                <div class="card-body">
                    <h3><?php echo Text::_('COM_CLUBORGANISATION_BWPOSTMAN_STEP2_INACTIVE_SUBTITLE'); ?></h3>
                    <p><?php echo Text::_('COM_CLUBORGANISATION_BWPOSTMAN_STEP2_INACTIVE_DESC'); ?></p>
                    
                    <?php if ($this->mailinglist): ?>
                        <div class="alert alert-info">
                            <strong><?php echo Text::_('COM_CLUBORGANISATION_BWPOSTMAN_SELECTED_MAILINGLIST'); ?>:</strong>
                            <?php echo htmlspecialchars($this->mailinglist->title); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Members List -->
            <div class="card">
                <div class="card-body">
                    <?php if (empty($this->items)): ?>
                        <div class="alert alert-success">
                            <span class="icon-check" aria-hidden="true"></span>
                            <?php echo Text::_('COM_CLUBORGANISATION_BWPOSTMAN_NO_INACTIVE_MEMBERS'); ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">
                            <?php echo Text::sprintf('COM_CLUBORGANISATION_BWPOSTMAN_INACTIVE_COUNT', count($this->items)); ?>
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
                                    <th><?php echo Text::_('COM_CLUBORGANISATION_BWPOSTMAN_SUBSCRIBER_ID'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($this->items as $i => $item): ?>
                                    <tr>
                                        <td class="text-center">
                                            <?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($item->member_no); ?></td>
                                        <td><?php echo htmlspecialchars($item->firstname); ?></td>
                                        <td><?php echo htmlspecialchars($item->lastname); ?></td>
                                        <td><?php echo htmlspecialchars($item->email); ?></td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                ID: <?php echo $item->subscriber_id; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        
                        <div class="alert alert-warning mt-3">
                            <span class="icon-warning" aria-hidden="true"></span>
                            <?php echo Text::_('COM_CLUBORGANISATION_BWPOSTMAN_ARCHIVE_WARNING'); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <input type="hidden" name="task" value="bwpostmansync.sync">
    <input type="hidden" name="boxchecked" value="0">
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
