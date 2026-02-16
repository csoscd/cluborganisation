<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @license     GNU General Public License version 3 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <span class="icon-check-circle display-1 text-success" aria-hidden="true"></span>
                </div>
                
                <h2><?php echo Text::_('COM_CLUBORGANISATION_BWPOSTMAN_STEP3_TITLE'); ?></h2>
                <p class="lead"><?php echo Text::_('COM_CLUBORGANISATION_BWPOSTMAN_STEP3_DESC'); ?></p>
                
                <div class="mt-4">
                    <a href="<?php echo Route::_('index.php?option=com_cluborganisation&view=bwpostmansync', false); ?>" 
                       class="btn btn-primary btn-lg">
                        <span class="icon-loop" aria-hidden="true"></span>
                        <?php echo Text::_('COM_CLUBORGANISATION_BWPOSTMAN_NEW_SYNC'); ?>
                    </a>
                    
                    <a href="<?php echo Route::_('index.php?option=com_cluborganisation', false); ?>" 
                       class="btn btn-secondary btn-lg ms-2">
                        <span class="icon-home" aria-hidden="true"></span>
                        <?php echo Text::_('COM_CLUBORGANISATION_BWPOSTMAN_BACK_TO_DASHBOARD'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
