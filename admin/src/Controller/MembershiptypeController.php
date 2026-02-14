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

class MembershiptypeController extends FormController
{
    protected $text_prefix = 'COM_CLUBORGANISATION_MEMBERSHIPTYPE';
    protected $view_list = 'membershiptypes';
}
