<?php
namespace CSOSCD\Component\ClubOrganisation\Administrator\Controller;
defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

class DsgvocleanupController extends BaseController
{
    public function anonymize()
    {
        // Check for token
        $this->checkToken();
        
        $app = Factory::getApplication();
        $input = $app->input;
        
        // Get selected person IDs
        $cid = $input->get('cid', [], 'array');
        
        if (empty($cid)) {
            $this->setRedirect(
                Route::_('index.php?option=com_cluborganisation&view=dsgvocleanup', false),
                Text::_('COM_CLUBORGANISATION_DSGVO_ERROR_NO_SELECTION'),
                'warning'
            );
            return;
        }
        
        $model = $this->getModel('Dsgvocleanup');
        $result = $model->anonymizePersons($cid);
        
        $this->setRedirect(
            Route::_('index.php?option=com_cluborganisation&view=dsgvocleanup', false),
            $result['message'],
            $result['success'] ? 'message' : 'error'
        );
    }

    public function cancel()
    {
        $this->setRedirect(
            Route::_('index.php?option=com_cluborganisation', false)
        );
    }
}
