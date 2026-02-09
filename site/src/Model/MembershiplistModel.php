<?php
namespace CSOSCD\Component\ClubOrganisation\Site\Model;
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

class MembershiplistModel extends BaseDatabaseModel
{
    public function getNewMemberships($year = null)
    {
        if ($year === null) {
            $year = date('Y');
        }

        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        $startDate = $year . '-01-01';

        $query->select('a.*, p.firstname, p.lastname, p.member_no')
            ->select('t.title AS type_title')
            ->from($db->quoteName('#__cluborganisation_memberships', 'a'))
            ->join('LEFT', $db->quoteName('#__cluborganisation_persons', 'p') . ' ON p.id = a.person_id')
            ->join('LEFT', $db->quoteName('#__cluborganisation_membershiptypes', 't') . ' ON t.id = a.type')
            ->where($db->quoteName('a.begin') . ' >= ' . $db->quote($startDate))
            ->where($db->quoteName('p.active') . ' = 1')
            ->order('a.begin ASC');

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public function getEndedMemberships($year = null)
    {
        if ($year === null) {
            $year = date('Y');
        }

        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        $startDate = $year . '-01-01';
        $endDate = $year . '-12-31';

        $query->select('a.*, p.firstname, p.lastname, p.member_no')
            ->select('t.title AS type_title')
            ->from($db->quoteName('#__cluborganisation_memberships', 'a'))
            ->join('LEFT', $db->quoteName('#__cluborganisation_persons', 'p') . ' ON p.id = a.person_id')
            ->join('LEFT', $db->quoteName('#__cluborganisation_membershiptypes', 't') . ' ON t.id = a.type')
            ->where($db->quoteName('a.end') . ' >= ' . $db->quote($startDate))
            ->where($db->quoteName('a.end') . ' <= ' . $db->quote($endDate))
            ->order('a.end ASC');

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public function getYearOptions()
    {
        $currentYear = (int)date('Y');
        $years = [];
        for ($i = -5; $i <= 1; $i++) {
            $year = $currentYear + $i;
            $years[$year] = $year;
        }
        return $years;
    }
}
