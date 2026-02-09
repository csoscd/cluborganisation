<?php
namespace CSOSCD\Component\ClubOrganisation\Administrator\Model;
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Model\ListModel;
class MembershiptypesModel extends ListModel {
    protected function getListQuery() {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)->select('*')->from('#__cluborganisation_membershiptypes')->order('ordering ASC');
        return $query;
    }
}
