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

/**
 * Display Controller - Standard Controller für die Komponente
 *
 * @since  1.0.0
 */
class DisplayController extends BaseController
{
    /**
     * Die Standard-View
     *
     * @var    string
     * @since  1.0.0
     */
    protected $default_view = 'persons';

    /**
     * Display-Methode
     *
     * @param   boolean  $cachable   Wenn true, wird die View gecacht
     * @param   array    $urlparams  URL-Parameter für das Caching
     *
     * @return  BaseController|boolean
     *
     * @since   1.0.0
     */
    public function display($cachable = false, $urlparams = [])
    {
        return parent::display($cachable, $urlparams);
    }
}
