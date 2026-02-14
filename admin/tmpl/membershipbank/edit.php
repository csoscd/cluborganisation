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

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
?>
<form action="<?php echo Route::_('index.php?option=com_cluborganisation&view=membershipbank&layout=edit&id=' . (int) $this->item->id); ?>" 
      method="post" name="adminForm" id="membershipbank-form" class="form-validate">
    
    <div class="alert alert-info">
        <span class="icon-info-circle"></span>
        <?php echo Text::_('COM_CLUBORGANISATION_MEMBERSHIPBANK_ENCRYPTION_INFO'); ?>
    </div>
    
    <div class="row">
        <div class="col-lg-9">
            <div class="card">
                <div class="card-body">
                    <?php echo $this->form->renderField('membership_id'); ?>
                    
                    <hr>
                    <h4><?php echo Text::_('COM_CLUBORGANISATION_ENCRYPTION'); ?></h4>
                    <?php echo $this->form->renderField('encryption_key'); ?>
                    
                    <hr>
                    <h4><?php echo Text::_('COM_CLUBORGANISATION_BANK_DATA'); ?></h4>
                    <?php echo $this->form->renderField('accountname'); ?>
                    <?php echo $this->form->renderField('iban'); ?>
                    <?php echo $this->form->renderField('bic'); ?>
                    <?php echo $this->form->renderField('begin'); ?>
                </div>
            </div>
        </div>
    </div>
    
    <input type="hidden" name="task" value="" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
