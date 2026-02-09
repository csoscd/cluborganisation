<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @copyright   2026 Christian Schulz
 * @license     GNU General Public License version 2 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\FormController;

/**
 * Person Edit Controller
 *
 * @since  1.0.0
 */
class PersonController extends FormController
{
    /**
     * Der Präfix für Proxy-Methoden
     *
     * @var    string
     * @since  1.0.0
     */
    protected $text_prefix = 'COM_CLUBORGANISATION_PERSON';

    /**
     * Der Name der Listenansicht
     *
     * @var    string
     * @since  1.0.0
     */
    protected $view_list = 'persons';
}
