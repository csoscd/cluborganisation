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
use Joomla\CMS\Session\Session;
use Joomla\CMS\HTML\HTMLHelper;

?>

<form action="<?php echo Route::_('index.php?option=com_cluborganisation&task=bwpostmansync.step2'); ?>" method="post" name="adminForm" id="adminForm">
    
    <div class="row">
        <div class="col-md-12">
            
            <?php if (!$this->bwPostmanInstalled): ?>
                <div class="alert alert-danger">
                    <h4 class="alert-heading">
                        <span class="icon-warning" aria-hidden="true"></span>
                        <?php echo Text::_('COM_CLUBORGANISATION_BWPOSTMAN_NOT_INSTALLED_TITLE'); ?>
                    </h4>
                    <p><?php echo Text::_('COM_CLUBORGANISATION_BWPOSTMAN_NOT_INSTALLED_DESC'); ?></p>
                    <hr>
                    <p class="mb-0">
                        <strong><?php echo Text::_('COM_CLUBORGANISATION_BWPOSTMAN_REQUIRED_TABLES'); ?>:</strong>
                    </p>
                    <ul>
                        <li><code>#__bwpostman_subscribers</code></li>
                        <li><code>#__bwpostman_mailinglists</code></li>
                        <li><code>#__bwpostman_subscribers_mailinglists</code></li>
                    </ul>
                    <p class="mb-0">
                        <a href="https://extensions.joomla.org/extension/bwpostman/" target="_blank" class="btn btn-primary">
                            <span class="icon-out-2" aria-hidden="true"></span>
                            <?php echo Text::_('COM_CLUBORGANISATION_BWPOSTMAN_DOWNLOAD'); ?>
                        </a>
                    </p>
                </div>
            <?php else: ?>
            
            <div class="card">
                <div class="card-body">
                    <h3><?php echo Text::_('COM_CLUBORGANISATION_BWPOSTMAN_STEP1_SUBTITLE'); ?></h3>
                    <p class="text-muted"><?php echo Text::_('COM_CLUBORGANISATION_BWPOSTMAN_STEP1_DESC'); ?></p>
                    
                    <!-- Member Type Selection -->
                    <div class="mb-4">
                        <h4><?php echo Text::_('COM_CLUBORGANISATION_BWPOSTMAN_SELECT_MEMBER_TYPE'); ?></h4>
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="member_type" id="member_type_active" value="active" checked>
                                <label class="form-check-label" for="member_type_active">
                                    <strong><?php echo Text::_('COM_CLUBORGANISATION_BWPOSTMAN_ACTIVE_MEMBERS'); ?></strong>
                                    <br>
                                    <small class="text-muted"><?php echo Text::_('COM_CLUBORGANISATION_BWPOSTMAN_ACTIVE_MEMBERS_DESC'); ?></small>
                                </label>
                            </div>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="radio" name="member_type" id="member_type_inactive" value="inactive">
                                <label class="form-check-label" for="member_type_inactive">
                                    <strong><?php echo Text::_('COM_CLUBORGANISATION_BWPOSTMAN_INACTIVE_MEMBERS'); ?></strong>
                                    <br>
                                    <small class="text-muted"><?php echo Text::_('COM_CLUBORGANISATION_BWPOSTMAN_INACTIVE_MEMBERS_DESC'); ?></small>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Mailinglist Selection -->
                    <div class="mb-4">
                        <h4><?php echo Text::_('COM_CLUBORGANISATION_BWPOSTMAN_SELECT_MAILINGLIST'); ?></h4>
                        
                        <?php if (empty($this->mailinglists)): ?>
                            <div class="alert alert-warning">
                                <?php echo Text::_('COM_CLUBORGANISATION_BWPOSTMAN_NO_MAILINGLISTS'); ?>
                            </div>
                        <?php else: ?>
                            <div class="list-group">
                                <?php foreach ($this->mailinglists as $i => $item): ?>
                                    <label class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100">
                                            <div class="form-check">
                                                <input 
                                                    class="form-check-input" 
                                                    type="radio" 
                                                    name="mailinglist_id" 
                                                    id="mailinglist_<?php echo $item->id; ?>" 
                                                    value="<?php echo $item->id; ?>"
                                                    <?php echo $i === 0 ? 'checked' : ''; ?>
                                                >
                                            </div>
                                            <div class="ms-3 flex-grow-1">
                                                <h5 class="mb-1">
                                                    <?php echo htmlspecialchars($item->title); ?>
                                                    <?php if ($item->published): ?>
                                                        <span class="badge bg-success"><?php echo Text::_('JPUBLISHED'); ?></span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary"><?php echo Text::_('JUNPUBLISHED'); ?></span>
                                                    <?php endif; ?>
                                                </h5>
                                                <?php if (!empty($item->description)): ?>
                                                    <p class="mb-0 text-muted small">
                                                        <?php echo htmlspecialchars($item->description); ?>
                                                    </p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!empty($this->mailinglists)): ?>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <span class="icon-arrow-right" aria-hidden="true"></span>
                                <?php echo Text::_('COM_CLUBORGANISATION_BWPOSTMAN_CONTINUE_TO_STEP2'); ?>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php endif; ?>
        </div>
    </div>
    
    <input type="hidden" name="task" value="bwpostmansync.step2">
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
