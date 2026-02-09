<?php
defined('_JEXEC') or die;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
?>
<form action="<?php echo Route::_('index.php?option=com_cluborganisation&view=salutation&layout=edit&id=' . (int) $this->item->id); ?>" 
      method="post" name="adminForm" id="salutation-form" class="form-validate">
    
    <div class="row">
        <div class="col-lg-9">
            <div class="card">
                <div class="card-body">
                    <?php echo $this->form->renderField('title'); ?>
                    <?php echo $this->form->renderField('published'); ?>
                    <?php echo $this->form->renderField('ordering'); ?>
                </div>
            </div>
        </div>
    </div>
    
    <input type="hidden" name="task" value="" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
