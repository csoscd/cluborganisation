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
 * Membershiptype Fees Controller
 *
 * @since  1.7.0
 */
class MembershiptypefeesController extends AdminController
{
    /**
     * Method to get a model object
     *
     * @param   string  $name    The model name
     * @param   string  $prefix  The model prefix
     * @param   array   $config  Configuration array
     *
     * @return  \Joomla\CMS\MVC\Model\BaseDatabaseModel
     *
     * @since   1.7.0
     */
    public function getModel($name = 'Membershiptypefee', $prefix = 'Administrator', $config = ['ignore_request' => true])
    {
        return parent::getModel($name, $prefix, $config);
    }
}
