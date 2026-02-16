<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @license     GNU General Public License version 3 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\FormController;

/**
 * Membershiptype Fee Controller
 *
 * @since  1.7.0
 */
class MembershiptypefeeController extends FormController
{
    /**
     * The URL view list variable
     *
     * @var    string
     * @since  1.7.0
     */
    protected $view_list = 'membershiptypefees';
}
