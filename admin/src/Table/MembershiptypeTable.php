<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @license     GNU General Public License version 3 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Administrator\Table;
defined('_JEXEC') or die;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;

class MembershiptypeTable extends Table
{
    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__cluborganisation_membershiptypes', 'id', $db);
    }
}
