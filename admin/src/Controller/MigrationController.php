<?php
namespace CSOSCD\Component\ClubOrganisation\Administrator\Controller;
defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

class MigrationController extends BaseController
{
    public function check()
    {
        $app = Factory::getApplication();
        $model = $this->getModel('Migration');
        $check = $model->checkPrerequisites();
        
        // Store check result in user state for display in view
        $app->setUserState('com_cluborganisation.migration.check', $check);
        
        $this->setRedirect(
            Route::_('index.php?option=com_cluborganisation&view=migration', false),
            $check['message'],
            $check['success'] ? 'message' : 'warning'
        );
    }

    public function migrate()
    {
        // Check for token
        $this->checkToken();
        
        $app = Factory::getApplication();
        $input = $app->input;
        
        $truncate = $input->getInt('truncate', 0);
        
        // Get custom mappings
        $salutationMappings = $input->get('salutation_mapping', [], 'array');
        $typeMappings = $input->get('type_mapping', [], 'array');
        
        $model = $this->getModel('Migration');
        
        // Perform migration with custom mappings
        $result = $model->performMigration($truncate, $salutationMappings, $typeMappings);
        
        $this->setRedirect(
            Route::_('index.php?option=com_cluborganisation&view=migration', false),
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
