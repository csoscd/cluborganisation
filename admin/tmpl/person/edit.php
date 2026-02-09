<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @copyright   2026 Christian Schulz
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Layout\LayoutHelper;

/** @var \CSOSCD\Component\ClubOrganisation\Administrator\View\Person\HtmlView $this */

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
?>

<form action="<?php echo Route::_('index.php?option=com_cluborganisation&view=person&layout=edit&id=' . (int) $this->item->id); ?>" 
      method="post" name="adminForm" id="person-form" class="form-validate">
    
    <div class="row">
        <div class="col-lg-9">
            <div class="card">
                <div class="card-body">
                    <?php echo $this->form->renderField('salutation'); ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <?php echo $this->form->renderField('firstname'); ?>
                        </div>
                        <div class="col-md-6">
                            <?php echo $this->form->renderField('middlename'); ?>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <?php echo $this->form->renderField('lastname'); ?>
                        </div>
                        <div class="col-md-6">
                            <?php echo $this->form->renderField('birthname'); ?>
                        </div>
                    </div>
                    
                    <?php echo $this->form->renderField('address'); ?>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <?php echo $this->form->renderField('zip'); ?>
                        </div>
                        <div class="col-md-4">
                            <?php echo $this->form->renderField('city'); ?>
                        </div>
                        <div class="col-md-4">
                            <?php echo $this->form->renderField('country'); ?>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <?php echo $this->form->renderField('telephone'); ?>
                        </div>
                        <div class="col-md-6">
                            <?php echo $this->form->renderField('mobile'); ?>
                        </div>
                    </div>
                    
                    <?php echo $this->form->renderField('email'); ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <?php echo $this->form->renderField('birthday'); ?>
                        </div>
                        <div class="col-md-6">
                            <?php echo $this->form->renderField('deceased'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    <?php echo $this->form->renderField('member_no'); ?>
                    <?php echo $this->form->renderField('active'); ?>
                    <?php echo $this->form->renderField('user_id'); ?>
                    <?php echo $this->form->renderField('image'); ?>
                </div>
            </div>
            
            <?php if ($this->item->id) : ?>
                <div class="card mt-3">
                    <div class="card-header">
                        <?php echo Text::_('COM_CLUBORGANISATION_TAB_PUBLISHING'); ?>
                    </div>
                    <div class="card-body">
                        <?php echo $this->form->renderField('created'); ?>
                        <?php echo $this->form->renderField('created_by'); ?>
                        <?php echo $this->form->renderField('modified'); ?>
                        <?php echo $this->form->renderField('modified_by'); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <input type="hidden" name="task" value="" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
