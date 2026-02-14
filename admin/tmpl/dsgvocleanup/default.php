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

$wa = $this->document->getWebAssetManager();
$wa->useScript('table.columns');
$wa->useScript('multiselect');
?>

<form action="<?php echo Route::_('index.php?option=com_cluborganisation&view=dsgvocleanup'); ?>" method="post" name="adminForm" id="adminForm">
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h2><?php echo Text::_('COM_CLUBORGANISATION_DSGVO_CLEANUP_TITLE'); ?></h2>
                    <p><?php echo Text::sprintf('COM_CLUBORGANISATION_DSGVO_CLEANUP_DESCRIPTION', $this->yearsThreshold); ?></p>
                    
                    <?php if (empty($this->items)) : ?>
                        <div class="alert alert-info">
                            <span class="icon-info-circle" aria-hidden="true"></span>
                            <?php echo Text::_('COM_CLUBORGANISATION_DSGVO_NO_PERSONS'); ?>
                        </div>
                    <?php else : ?>
                        <div class="alert alert-warning">
                            <span class="icon-warning" aria-hidden="true"></span>
                            <strong><?php echo Text::_('COM_CLUBORGANISATION_DSGVO_WARNING_TITLE'); ?></strong><br>
                            <?php echo Text::_('COM_CLUBORGANISATION_DSGVO_WARNING_TEXT'); ?>
                        </div>
                        
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col" class="w-1 text-center">
                                        <?php echo HTMLHelper::_('grid.checkall'); ?>
                                    </th>
                                    <th scope="col">
                                        <?php echo Text::_('COM_CLUBORGANISATION_FIELD_FIRSTNAME'); ?>
                                    </th>
                                    <th scope="col">
                                        <?php echo Text::_('COM_CLUBORGANISATION_FIELD_LASTNAME'); ?>
                                    </th>
                                    <th scope="col">
                                        <?php echo Text::_('COM_CLUBORGANISATION_DSGVO_LAST_MEMBERSHIP_END'); ?>
                                    </th>
                                    <th scope="col" class="text-center">
                                        <?php echo Text::_('COM_CLUBORGANISATION_DSGVO_YEARS_SINCE_END'); ?>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($this->items as $i => $item) : ?>
                                    <?php 
                                    $yearsAgo = '';
                                    if ($item->last_membership_end) {
                                        $endDate = new DateTime($item->last_membership_end);
                                        $now = new DateTime();
                                        $interval = $endDate->diff($now);
                                        $yearsAgo = $interval->y;
                                    }
                                    ?>
                                    <tr>
                                        <td class="text-center">
                                            <?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                                        </td>
                                        <td>
                                            <?php echo $this->escape($item->firstname); ?>
                                        </td>
                                        <td>
                                            <?php echo $this->escape($item->lastname); ?>
                                        </td>
                                        <td>
                                            <?php echo HTMLHelper::_('date', $item->last_membership_end, Text::_('DATE_FORMAT_LC4')); ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-<?php echo $yearsAgo >= $this->yearsThreshold ? 'danger' : 'warning'; ?>">
                                                <?php echo $yearsAgo; ?> <?php echo Text::_('COM_CLUBORGANISATION_DSGVO_YEARS'); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5">
                                        <strong><?php echo Text::sprintf('COM_CLUBORGANISATION_DSGVO_TOTAL_COUNT', count($this->items)); ?></strong>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                        
                        <div class="mt-3">
                            <p><strong><?php echo Text::_('COM_CLUBORGANISATION_DSGVO_ANONYMIZATION_INFO'); ?></strong></p>
                            <ul>
                                <li><?php echo Text::_('COM_CLUBORGANISATION_DSGVO_ANONYMIZATION_NAMES'); ?></li>
                                <li><?php echo Text::_('COM_CLUBORGANISATION_DSGVO_ANONYMIZATION_CONTACT'); ?></li>
                                <li><?php echo Text::_('COM_CLUBORGANISATION_DSGVO_ANONYMIZATION_DATES'); ?></li>
                                <li><?php echo Text::_('COM_CLUBORGANISATION_DSGVO_ANONYMIZATION_BANK'); ?></li>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="task" value="">
    <input type="hidden" name="boxchecked" value="0">
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
