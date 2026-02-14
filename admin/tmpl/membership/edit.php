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
<form action="<?php echo Route::_('index.php?option=com_cluborganisation&view=membership&layout=edit&id=' . (int) $this->item->id); ?>" 
      method="post" name="adminForm" id="membership-form" class="form-validate">
    <div class="row">
        <div class="col-lg-9">
            <div class="card">
                <div class="card-body">
                    <?php echo $this->form->renderField('person_id'); ?>
                    <?php echo $this->form->renderField('type'); ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <?php echo $this->form->renderField('begin'); ?>
                        </div>
                        <div class="col-md-6">
                            <?php echo $this->form->renderField('end'); ?>
                        </div>
                    </div>
                    
                    <?php echo $this->form->renderField('comment'); ?>
                    <?php echo $this->form->renderField('catid'); ?>
                </div>
            </div>
        </div>
    </div>
    
    <input type="hidden" name="task" value="" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
