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
class MembershiptypesModel extends ListModel {
    protected function getListQuery() {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select(['a.*', 'parent.title AS parent_type_title'])
            ->from($db->quoteName('#__cluborganisation_membershiptypes', 'a'))
            ->join('LEFT', $db->quoteName('#__cluborganisation_membershiptypes', 'parent') . ' ON ' . $db->quoteName('parent.id') . ' = ' . $db->quoteName('a.depends_on_type'))
            ->order($db->quoteName('a.ordering') . ' ASC');
        return $query;
    }
}
