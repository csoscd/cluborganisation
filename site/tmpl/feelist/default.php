<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Site
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @license     GNU General Public License version 3 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

/** @var \CSOSCD\Component\ClubOrganisation\Site\View\Feelist\HtmlView $this */

$pageClass = $this->pageclass_sfx ? ' class="' . $this->pageclass_sfx . '"' : '';
?>

<div class="feelist<?php echo $this->pageclass_sfx; ?>">
    <?php if ($this->params->get('show_page_heading')) : ?>
        <div class="page-header">
            <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
        </div>
    <?php endif; ?>

    <?php if (empty($this->fees['current'])) : ?>
        <div class="alert alert-info">
            <span class="icon-info-circle" aria-hidden="true"></span>
            <?php echo Text::_('COM_CLUBORGANISATION_NO_FEES_AVAILABLE'); ?>
        </div>
    <?php else : ?>
        <div class="card">
            <div class="card-header">
                <h2><?php echo Text::_('COM_CLUBORGANISATION_CURRENT_FEES'); ?></h2>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th><?php echo Text::_('COM_CLUBORGANISATION_FIELD_MEMBERSHIPTYPE'); ?></th>
                            <th><?php echo Text::_('COM_CLUBORGANISATION_FIELD_BEGIN_DATE'); ?></th>
                            <th class="text-end"><?php echo Text::_('COM_CLUBORGANISATION_FIELD_AMOUNT'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($this->fees['current'] as $fee) : ?>
                        <tr>
                            <td><strong><?php echo $this->escape($fee->membershiptype_title); ?></strong></td>
                            <td>
                                <?php echo Text::sprintf('COM_CLUBORGANISATION_VALID_SINCE', 
                                    HTMLHelper::_('date', $fee->begin, Text::_('DATE_FORMAT_LC4'))); ?>
                            </td>
                            <td class="text-end">
                                <strong><?php echo number_format($fee->amount, 2, ',', '.'); ?> €</strong>
                            </td>
                        </tr>
                        
                        <?php if (isset($this->fees['future'][$fee->membershiptype_id])) : ?>
                            <?php foreach ($this->fees['future'][$fee->membershiptype_id] as $futureFee) : ?>
                            <tr class="table-info">
                                <td></td>
                                <td>
                                    <em><?php echo Text::sprintf('COM_CLUBORGANISATION_VALID_FROM', 
                                        HTMLHelper::_('date', $futureFee->begin, Text::_('DATE_FORMAT_LC4'))); ?></em>
                                </td>
                                <td class="text-end">
                                    <em><?php echo number_format($futureFee->amount, 2, ',', '.'); ?> €</em>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if (!empty($this->fees['future'])) : ?>
            <div class="alert alert-info mt-3">
                <span class="icon-info-circle" aria-hidden="true"></span>
                <?php echo Text::_('COM_CLUBORGANISATION_FUTURE_FEES_INFO'); ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
