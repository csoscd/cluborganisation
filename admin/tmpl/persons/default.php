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
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

/** @var \CSOSCD\Component\ClubOrganisation\Administrator\View\Persons\HtmlView $this */

HTMLHelper::_('behavior.multiselect');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>

<form action="<?php echo Route::_('index.php?option=com_cluborganisation&view=persons'); ?>" 
      method="post" name="adminForm" id="adminForm">
    
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
                <?php
                // Suchfeld und Filter
                echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));
                ?>
                
                <?php if (empty($this->items)) : ?>
                    <div class="alert alert-info">
                        <span class="icon-info-circle" aria-hidden="true"></span>
                        <span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
                        <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
                    </div>
                <?php else : ?>
                    <table class="table table-striped" id="personList">
                        <thead>
                            <tr>
                                <td class="w-1 text-center">
                                    <?php echo HTMLHelper::_('grid.checkall'); ?>
                                </td>
                                <th scope="col" class="w-1 text-center">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'COM_CLUBORGANISATION_FIELD_ID', 'a.id', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'COM_CLUBORGANISATION_FIELD_MEMBER_NO', 'a.member_no', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'COM_CLUBORGANISATION_FIELD_LASTNAME', 'a.lastname', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'COM_CLUBORGANISATION_FIELD_FIRSTNAME', 'a.firstname', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col">
                                    <?php echo Text::_('COM_CLUBORGANISATION_FIELD_EMAIL'); ?>
                                </th>
                                <th scope="col" class="w-10 text-center">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'COM_CLUBORGANISATION_FIELD_ACTIVE', 'a.active', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col" class="w-10 text-center">
                                    <?php echo Text::_('COM_CLUBORGANISATION_FIELD_CREATED'); ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($this->items as $i => $item) : ?>
                                <tr class="row<?php echo $i % 2; ?>">
                                    <td class="text-center">
                                        <?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo $item->id; ?>
                                    </td>
                                    <td>
                                        <strong><?php echo $this->escape($item->member_no); ?></strong>
                                    </td>
                                    <td>
                                        <a href="<?php echo Route::_('index.php?option=com_cluborganisation&task=person.edit&id=' . (int) $item->id); ?>">
                                            <?php echo $this->escape($item->lastname); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <?php echo $this->escape($item->firstname); ?>
                                        <?php if ($item->middlename): ?>
                                            <?php echo $this->escape($item->middlename); ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php echo $this->escape($item->email); ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($item->active): ?>
                                            <span class="badge bg-success"><?php echo Text::_('JYES'); ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary"><?php echo Text::_('JNO'); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo HTMLHelper::_('date', $item->created, Text::_('DATE_FORMAT_LC4')); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <?php // Pagination laden ?>
                    <?php echo $this->pagination->getListFooter(); ?>
                <?php endif; ?>
                
                <input type="hidden" name="task" value="" />
                <input type="hidden" name="boxchecked" value="0" />
                <?php echo HTMLHelper::_('form.token'); ?>
            </div>
        </div>
    </div>
</form>
