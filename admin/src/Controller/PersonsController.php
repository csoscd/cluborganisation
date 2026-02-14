<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @license     GNU General Public License version 3 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\AdminController;

/**
 * Persons List Controller
 *
 * @since  1.0.0
 */
class PersonsController extends AdminController
{
    /**
     * Der Präfix für Proxy-Methoden
     *
     * @var    string
     * @since  1.0.0
     */
    protected $text_prefix = 'COM_CLUBORGANISATION_PERSONS';

    /**
     * Gibt den Namen des Model zurück
     *
     * @param   string  $name    Name des Models (optional)
     * @param   string  $prefix  Präfix für das Model (optional)
     * @param   array   $config  Konfigurations-Array (optional)
     *
     * @return  \Joomla\CMS\MVC\Model\BaseDatabaseModel
     *
     * @since   1.0.0
     */
    public function getModel($name = 'Person', $prefix = 'Administrator', $config = ['ignore_request' => true])
    {
        return parent::getModel($name, $prefix, $config);
    }
}
