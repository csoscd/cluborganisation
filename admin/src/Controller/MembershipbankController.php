<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz
 * @license     GNU General Public License version 3 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

/**
 * Controller für das Bankverbindungs-Formular (Einzeldatensatz)
 *
 * @since  1.0.0
 */
class MembershipbankController extends FormController
{
    protected $text_prefix = 'COM_CLUBORGANISATION_MEMBERSHIPBANK';
    protected $view_list   = 'membershipbanks';

    /**
     * Leitet zur schreibgeschützten Detailansicht einer Bankverbindung weiter.
     * Wird vom "Anzeigen"-Button in der Bankverbindungs-Liste aufgerufen.
     *
     * @return  void
     *
     * @since   1.9.0
     */
    public function view()
    {
        $this->checkToken();

        $cid = $this->input->get('cid', [], 'array');
        $id  = !empty($cid) ? (int) $cid[0] : 0;

        if (!$id) {
            $this->app->enqueueMessage(Text::_('COM_CLUBORGANISATION_ERROR_NO_ITEM_SELECTED'), 'error');
            $this->setRedirect(Route::_('index.php?option=com_cluborganisation&view=membershipbanks', false));
            return;
        }

        $this->setRedirect(
            Route::_('index.php?option=com_cluborganisation&view=membershipbank&layout=view&id=' . $id, false)
        );
    }
}
