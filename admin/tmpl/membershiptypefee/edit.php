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

/** @var \CSOSCD\Component\ClubOrganisation\Administrator\View\Membershiptypefee\HtmlView $this */

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
?>

<form action="<?php echo Route::_('index.php?option=com_cluborganisation&view=membershiptypefee&layout=edit&id=' . (int) $this->item->id); ?>" 
      method="post" name="adminForm" id="membershiptypefee-form" class="form-validate">
    
    <div class="row">
        <div class="col-lg-9">
            <div class="card">
                <div class="card-body">
                    <?php echo $this->form->renderField('membershiptype_id'); ?>
                    <?php echo $this->form->renderField('begin'); ?>
                    <?php echo $this->form->renderField('amount'); ?>
                    <?php echo $this->form->renderField('published'); ?>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3">
            <?php if ($this->item->id) : ?>
                <div class="card">
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
