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
?>
<form action="<?php echo Route::_('index.php?option=com_cluborganisation&view=memberships'); ?>" method="post" name="adminForm" id="adminForm">
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
                <?php if ($this->filterForm) : ?>
                    <div class="js-stools" role="search">
                        <div class="js-stools-container-bar">
                            <div class="btn-toolbar">
                                <div class="filter-search-bar btn-group">
                                    <?php echo $this->filterForm->renderField('search', 'filter'); ?>
                                    <button type="submit" class="btn btn-primary" aria-label="<?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?>">
                                        <span class="icon-search" aria-hidden="true"></span>
                                    </button>
                                    <button type="button" class="btn btn-primary js-stools-btn-clear" onclick="document.getElementById('filter_search').value='';this.form.submit();">
                                        <?php echo Text::_('JSEARCH_FILTER_CLEAR'); ?>
                                    </button>
                                </div>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary js-stools-btn-filter">
                                        <?php echo Text::_('JOPTION_SELECT_FILTER'); ?>
                                        <span class="icon-angle-down" aria-hidden="true"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="js-stools-container-filters clearfix" style="display:none;">
                            <div class="js-stools-field-filter">
                                <?php echo $this->filterForm->renderField('active_only', 'filter'); ?>
                            </div>
                            <div class="js-stools-field-filter">
                                <?php echo $this->filterForm->renderField('type', 'filter'); ?>
                            </div>
                        </div>
                        <div class="js-stools-container-list clearfix">
                            <?php echo $this->filterForm->renderField('fullordering', 'list'); ?>
                            <?php echo $this->filterForm->renderField('limit', 'list'); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (empty($this->items)) : ?>
                    <div class="alert alert-info">
                        <span class="icon-info-circle" aria-hidden="true"></span>
                        <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
                    </div>
                <?php else : ?>
                    <table class="table table-striped" id="membershipList">
                        <thead>
                            <tr>
                                <td class="w-1 text-center">
                                    <?php echo HTMLHelper::_('grid.checkall'); ?>
                                </td>
                                <th scope="col" style="width:5%">
                                    <?php echo Text::_('JGRID_HEADING_ID'); ?>
                                </th>
                                <th scope="col">
                                    <?php echo Text::_('COM_CLUBORGANISATION_FIELD_PERSON'); ?>
                                </th>
                                <th scope="col" style="width:15%">
                                    <?php echo Text::_('COM_CLUBORGANISATION_FIELD_TYPE'); ?>
                                </th>
                                <th scope="col" style="width:12%">
                                    <?php echo Text::_('COM_CLUBORGANISATION_FIELD_BEGIN'); ?>
                                </th>
                                <th scope="col" style="width:12%">
                                    <?php echo Text::_('COM_CLUBORGANISATION_FIELD_END'); ?>
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
                                    <?php echo $item->id; ?>
                                </td>
                                <td>
                                    <a href="<?php echo Route::_('index.php?option=com_cluborganisation&task=membership.edit&id=' . (int) $item->id); ?>">
                                        <?php echo $this->escape($item->lastname . ', ' . $item->firstname); ?>
                                        <?php if ($item->member_no) : ?>
                                            <small class="text-muted">(<?php echo $this->escape($item->member_no); ?>)</small>
                                        <?php endif; ?>
                                    </a>
                                </td>
                                <td>
                                    <?php echo $this->escape($item->type_title); ?>
                                </td>
                                <td>
                                    <?php echo HTMLHelper::_('date', $item->begin, Text::_('DATE_FORMAT_LC4')); ?>
                                </td>
                                <td>
                                    <?php if ($item->end) : ?>
                                        <?php echo HTMLHelper::_('date', $item->end, Text::_('DATE_FORMAT_LC4')); ?>
                                    <?php else : ?>
                                        <span class="badge bg-success">
                                            <?php echo Text::_('COM_CLUBORGANISATION_ONGOING'); ?>
                                        </span>
                                    <?php endif; ?>
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
