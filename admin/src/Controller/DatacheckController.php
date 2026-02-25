<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @license     GNU General Public License version 3 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;

/**
 * Controller für Datacheck-Aktionen.
 *
 * @since  2.2.0
 */
class DatacheckController extends BaseController
{
    /**
     * Deaktiviert eine Person (active = 0) und – sofern verknüpft –
     * auch den zugehörigen Joomla-Benutzer (block = 1).
     *
     * @return  void
     * @since   2.2.0
     */
    public function deactivatePerson(): void
    {
        $this->checkToken();

        $app      = Factory::getApplication();
        $personId = (int) $app->input->getInt('person_id', 0);

        if ($personId <= 0) {
            $this->setRedirect(
                Route::_('index.php?option=com_cluborganisation&view=datacheck', false),
                Text::_('COM_CLUBORGANISATION_DATACHECK_DEACTIVATE_ERROR_ID'),
                'error'
            );
            return;
        }

        /** @var \CSOSCD\Component\ClubOrganisation\Administrator\Model\DatacheckModel $model */
        $model  = $this->getModel('Datacheck');
        $result = $model->deactivatePerson($personId);

        $this->setRedirect(
            Route::_('index.php?option=com_cluborganisation&view=datacheck', false),
            $result['message'],
            $result['success'] ? 'message' : 'error'
        );
    }
}
