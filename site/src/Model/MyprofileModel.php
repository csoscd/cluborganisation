<?php
namespace CSOSCD\Component\ClubOrganisation\Site\Model;
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Factory;

class MyprofileModel extends BaseDatabaseModel
{
    public function getMyData()
    {
        $user = Factory::getApplication()->getIdentity();
        if ($user->guest) {
            return null;
        }

        $db = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select('p.*')
            ->select('s.title AS salutation_title')
            ->from($db->quoteName('#__cluborganisation_persons', 'p'))
            ->join('LEFT', $db->quoteName('#__cluborganisation_salutations', 's') . ' ON s.id = p.salutation')
            ->where($db->quoteName('p.user_id') . ' = ' . (int)$user->id);

        $db->setQuery($query);
        return $db->loadObject();
    }

    public function getMyMemberships()
    {
        $user = Factory::getApplication()->getIdentity();
        if ($user->guest) {
            return [];
        }

        $db = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select('m.*, t.title AS type_title')
            ->from($db->quoteName('#__cluborganisation_memberships', 'm'))
            ->join('LEFT', $db->quoteName('#__cluborganisation_membershiptypes', 't') . ' ON t.id = m.type')
            ->join('LEFT', $db->quoteName('#__cluborganisation_persons', 'p') . ' ON p.id = m.person_id')
            ->where($db->quoteName('p.user_id') . ' = ' . (int)$user->id)
            ->order('m.begin DESC');

        $db->setQuery($query);
        return $db->loadObjectList();
    }
}
