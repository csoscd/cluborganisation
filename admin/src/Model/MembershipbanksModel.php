<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @license     GNU General Public License version 3 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Administrator\Model;
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Model\ListModel;
class MembershipbanksModel extends ListModel {
    protected function getListQuery() {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)->select('a.*')
            ->from('#__cluborganisation_membershipbanks AS a')
            ->select('m.begin AS membership_begin')
            ->join('LEFT', '#__cluborganisation_memberships AS m ON m.id = a.membership_id')
            ->order('a.id DESC');
        return $query;
    }
}
