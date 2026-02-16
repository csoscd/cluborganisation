<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @license     GNU General Public License version 3 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Administrator\Controller;
defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

class BwpostmansyncController extends BaseController
{
    /**
     * Display Step 1: Select member type and mailinglist
     */
    public function display($cachable = false, $urlparams = [])
    {
        $view = $this->getView('Bwpostmansync', 'html');
        $view->setLayout('step1');
        $view->display();
    }

    /**
     * Process Step 1 and show Step 2
     */
    public function step2()
    {
        Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));
        
        $app = Factory::getApplication();
        $input = $app->input;
        
        $memberType = $input->getString('member_type', '');
        $mailinglistId = $input->getInt('mailinglist_id', 0);
        
        if (empty($memberType) || $mailinglistId <= 0) {
            $app->enqueueMessage(Text::_('COM_CLUBORGANISATION_BWPOSTMAN_ERROR_INVALID_INPUT'), 'error');
            $this->setRedirect(Route::_('index.php?option=com_cluborganisation&view=bwpostmansync', false));
            return;
        }
        
        // Store selections in session
        $app->setUserState('com_cluborganisation.bwpostmansync.member_type', $memberType);
        $app->setUserState('com_cluborganisation.bwpostmansync.mailinglist_id', $mailinglistId);
        
        // Redirect to Step 2 view
        $layout = ($memberType === 'active') ? 'step2_active' : 'step2_inactive';
        $this->setRedirect(Route::_('index.php?option=com_cluborganisation&view=bwpostmansync&layout=' . $layout, false));
    }

    /**
     * Process Step 2 and execute Step 3 (final synchronization)
     */
    public function sync()
    {
        Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));
        
        $app = Factory::getApplication();
        $input = $app->input;
        
        // Get member type from session
        $memberType = $app->getUserState('com_cluborganisation.bwpostmansync.member_type', '');
        $mailinglistId = $app->getUserState('com_cluborganisation.bwpostmansync.mailinglist_id', 0);
        
        // Get selected person IDs
        $personIds = $input->get('cid', [], 'array');
        $personIds = array_map('intval', $personIds);
        
        if (empty($personIds)) {
            $app->enqueueMessage(Text::_('COM_CLUBORGANISATION_BWPOSTMAN_ERROR_NO_SELECTION'), 'warning');
            $layout = ($memberType === 'active') ? 'step2_active' : 'step2_inactive';
            $this->setRedirect(Route::_('index.php?option=com_cluborganisation&view=bwpostmansync&layout=' . $layout, false));
            return;
        }
        
        $model = $this->getModel('Bwpostmansync');
        
        if ($memberType === 'active') {
            // Get gender mapping from form
            $genderMapping = $input->get('gender_mapping', [], 'array');
            $app->setUserState('com_cluborganisation.bwpostmansync.gender_mapping', $genderMapping);
            
            $result = $model->syncActiveMembers($personIds, $mailinglistId, $genderMapping);
        } else {
            $result = $model->archiveInactiveMembers($personIds);
        }
        
        // Redirect with message
        $this->setRedirect(
            Route::_('index.php?option=com_cluborganisation&view=bwpostmansync&layout=step3', false),
            $result['message'],
            $result['success'] ? 'message' : 'error'
        );
    }

    /**
     * Cancel and return to dashboard
     */
    public function cancel()
    {
        // Clear session data
        $app = Factory::getApplication();
        $app->setUserState('com_cluborganisation.bwpostmansync.member_type', null);
        $app->setUserState('com_cluborganisation.bwpostmansync.mailinglist_id', null);
        $app->setUserState('com_cluborganisation.bwpostmansync.gender_mapping', null);
        
        $this->setRedirect(Route::_('index.php?option=com_cluborganisation', false));
    }
}
