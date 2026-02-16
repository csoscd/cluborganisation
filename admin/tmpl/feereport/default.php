<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @license     GNU General Public License version 3 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

/** @var \CSOSCD\Component\ClubOrganisation\Administrator\View\Feereport\HtmlView $this */
?>

<div class="row">
    <!-- Current Year -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3><?php echo Text::sprintf('COM_CLUBORGANISATION_FEEREPORT_YEAR', $this->currentYear['year']); ?></h3>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th><?php echo Text::_('COM_CLUBORGANISATION_FIELD_MEMBERSHIPTYPE'); ?></th>
                            <th class="text-end"><?php echo Text::_('COM_CLUBORGANISATION_MEMBERSHIP_COUNT'); ?></th>
                            <th class="text-end"><?php echo Text::_('COM_CLUBORGANISATION_FEE_AMOUNT'); ?></th>
                            <th class="text-end"><?php echo Text::_('COM_CLUBORGANISATION_TOTAL_AMOUNT'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($this->currentYear['items'] as $item) : ?>
                        <tr>
                            <td><?php echo $this->escape($item->membershiptype_title); ?></td>
                            <td class="text-end"><?php echo (int) $item->membership_count; ?></td>
                            <td class="text-end"><?php echo number_format($item->fee_amount, 2, ',', '.'); ?> €</td>
                            <td class="text-end"><strong><?php echo number_format($item->total_amount, 2, ',', '.'); ?> €</strong></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="table-info">
                            <td><strong><?php echo Text::_('COM_CLUBORGANISATION_TOTAL'); ?></strong></td>
                            <td class="text-end"><strong><?php echo (int) $this->currentYear['total_memberships']; ?></strong></td>
                            <td></td>
                            <td class="text-end"><strong><?php echo number_format($this->currentYear['total_amount'], 2, ',', '.'); ?> €</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Next Year -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3><?php echo Text::sprintf('COM_CLUBORGANISATION_FEEREPORT_YEAR', $this->nextYear['year']); ?></h3>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th><?php echo Text::_('COM_CLUBORGANISATION_FIELD_MEMBERSHIPTYPE'); ?></th>
                            <th class="text-end"><?php echo Text::_('COM_CLUBORGANISATION_MEMBERSHIP_COUNT'); ?></th>
                            <th class="text-end"><?php echo Text::_('COM_CLUBORGANISATION_FEE_AMOUNT'); ?></th>
                            <th class="text-end"><?php echo Text::_('COM_CLUBORGANISATION_TOTAL_AMOUNT'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($this->nextYear['items'] as $item) : ?>
                        <tr>
                            <td><?php echo $this->escape($item->membershiptype_title); ?></td>
                            <td class="text-end"><?php echo (int) $item->membership_count; ?></td>
                            <td class="text-end"><?php echo number_format($item->fee_amount, 2, ',', '.'); ?> €</td>
                            <td class="text-end"><strong><?php echo number_format($item->total_amount, 2, ',', '.'); ?> €</strong></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="table-info">
                            <td><strong><?php echo Text::_('COM_CLUBORGANISATION_TOTAL'); ?></strong></td>
                            <td class="text-end"><strong><?php echo (int) $this->nextYear['total_memberships']; ?></strong></td>
                            <td></td>
                            <td class="text-end"><strong><?php echo number_format($this->nextYear['total_amount'], 2, ',', '.'); ?> €</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
