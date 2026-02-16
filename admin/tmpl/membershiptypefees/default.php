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
use Joomla\CMS\Layout\LayoutHelper;

/** @var \CSOSCD\Component\ClubOrganisation\Administrator\View\Membershiptypefees\HtmlView $this */

HTMLHelper::_('behavior.multiselect');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
?>

<form action="<?php echo Route::_('index.php?option=com_cluborganisation&view=membershiptypefees'); ?>" 
      method="post" name="adminForm" id="adminForm">
    
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
                <?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>
                
                <?php if (empty($this->items)) : ?>
                    <div class="alert alert-info">
                        <span class="icon-info-circle" aria-hidden="true"></span>
                        <span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
                        <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
                    </div>
                <?php else : ?>
                    <table class="table table-striped" id="membershiptypefeesList">
                        <thead>
                            <tr>
                                <th width="1%" class="text-center">
                                    <?php echo HTMLHelper::_('grid.checkall'); ?>
                                </th>
                                <th>
                                    <?php echo HTMLHelper::_('searchtools.sort', 'COM_CLUBORGANISATION_FIELD_MEMBERSHIPTYPE', 'membershiptype_title', $listDirn, $listOrder); ?>
                                </th>
                                <th width="15%">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'COM_CLUBORGANISATION_FIELD_BEGIN_DATE', 'f.begin', $listDirn, $listOrder); ?>
                                </th>
                                <th width="15%" class="text-end">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'COM_CLUBORGANISATION_FIELD_AMOUNT', 'f.amount', $listDirn, $listOrder); ?>
                                </th>
                                <th width="10%" class="text-center">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'f.published', $listDirn, $listOrder); ?>
                                </th>
                                <th width="5%" class="text-center">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'f.id', $listDirn, $listOrder); ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($this->items as $i => $item) : ?>
                            <tr class="row<?php echo $i % 2; ?>">
                                <td class="text-center">
                                    <?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                                </td>
                                <td>
                                    <a href="<?php echo Route::_('index.php?option=com_cluborganisation&task=membershiptypefee.edit&id=' . (int) $item->id); ?>">
                                        <?php echo $this->escape($item->membershiptype_title); ?>
                                    </a>
                                </td>
                                <td>
                                    <?php echo HTMLHelper::_('date', $item->begin, Text::_('DATE_FORMAT_LC4')); ?>
                                </td>
                                <td class="text-end">
                                    <?php echo number_format($item->amount, 2, ',', '.'); ?> €
                                </td>
                                <td class="text-center">
                                    <?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'membershiptypefees.', true); ?>
                                </td>
                                <td class="text-center">
                                    <?php echo (int) $item->id; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <?php echo $this->pagination->getListFooter(); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
