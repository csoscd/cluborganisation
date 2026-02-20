<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz
 * @license     GNU General Public License version 3 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
?>
<div class="row justify-content-center mt-5">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0">
                    <span class="icon-lock me-2"></span>
                    <?php echo Text::_('COM_CLUBORGANISATION_MEMBERSHIPBANKS_UNLOCK_TITLE'); ?>
                </h4>
            </div>
            <div class="card-body p-4">

                <?php if ($this->hasRecords) : ?>
                    <p class="text-muted mb-4">
                        <span class="icon-shield-alt me-1 text-warning"></span>
                        <?php echo Text::_('COM_CLUBORGANISATION_MEMBERSHIPBANKS_UNLOCK_DESC'); ?>
                    </p>
                <?php else : ?>
                    <div class="alert alert-info">
                        <span class="icon-info-circle me-1"></span>
                        <?php echo Text::_('COM_CLUBORGANISATION_MEMBERSHIPBANKS_UNLOCK_DESC_EMPTY'); ?>
                    </div>
                <?php endif; ?>

                <form
                    action="<?php echo Route::_('index.php?option=com_cluborganisation'); ?>"
                    method="post"
                    name="unlockForm"
                    id="unlockForm"
                >
                    <div class="mb-3">
                        <label for="encryption_key" class="form-label fw-semibold">
                            <?php echo Text::_('COM_CLUBORGANISATION_FIELD_ENCRYPTION_KEY'); ?>
                        </label>
                        <input
                            type="password"
                            class="form-control form-control-lg"
                            id="encryption_key"
                            name="encryption_key"
                            autocomplete="off"
                            autofocus
                            required
                            placeholder="<?php echo Text::_('COM_CLUBORGANISATION_FIELD_ENCRYPTION_KEY'); ?>"
                        />
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <span class="icon-lock-open me-2"></span>
                            <?php echo Text::_('COM_CLUBORGANISATION_MEMBERSHIPBANKS_UNLOCK_BUTTON'); ?>
                        </button>
                    </div>

                    <input type="hidden" name="task" value="membershipbanks.unlock" />
                    <?php echo HTMLHelper::_('form.token'); ?>
                </form>
            </div>

            <div class="card-footer text-muted small">
                <span class="icon-info-circle me-1"></span>
                <?php echo Text::_('COM_CLUBORGANISATION_FIELD_ENCRYPTION_KEY_DESC'); ?>
            </div>
        </div>
    </div>
</div>
