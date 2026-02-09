<?php
namespace CSOSCD\Component\ClubOrganisation\Administrator\Model;
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Model\ListModel;
class SalutationsModel extends ListModel {
    protected function getListQuery() {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)->select('*')->from('#__cluborganisation_salutations')->order('ordering ASC');
        return $query;
    }
}
