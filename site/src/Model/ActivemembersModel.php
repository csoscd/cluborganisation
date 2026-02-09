<?php
namespace CSOSCD\Component\ClubOrganisation\Site\Model;
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Model\ListModel;

class ActivemembersModel extends ListModel
{
    protected function getListQuery()
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        $today = date('Y-m-d');
        
        // Get params for ordering
        $app = \Joomla\CMS\Factory::getApplication();
        $params = $app->getParams();

        // Subquery für frühesten Mitgliedschaftsbeginn
        $subQuery = $db->getQuery(true);
        $subQuery->select('MIN(' . $db->quoteName('m2.begin') . ')')
            ->from($db->quoteName('#__cluborganisation_memberships', 'm2'))
            ->where($db->quoteName('m2.person_id') . ' = ' . $db->quoteName('p.id'));

        // Subquery für letztes Mitgliedschaftsende
        $subQueryEnd = $db->getQuery(true);
        $subQueryEnd->select('MAX(' . $db->quoteName('m3.end') . ')')
            ->from($db->quoteName('#__cluborganisation_memberships', 'm3'))
            ->where($db->quoteName('m3.person_id') . ' = ' . $db->quoteName('p.id'))
            ->where($db->quoteName('m3.end') . ' IS NOT NULL');

        $query->select('a.*, p.firstname, p.lastname, p.member_no, p.email, p.mobile')
            ->select('p.salutation, p.address, p.zip, p.city, p.telephone, p.birthday')
            ->select('s.title AS salutation_title')
            ->select('t.title AS type_title')
            ->select('(' . $subQuery . ') AS first_membership_begin')
            ->select('YEAR((' . $subQuery . ')) AS entry_year')
            ->select('(' . $subQueryEnd . ') AS last_membership_end')
            ->select('YEAR((' . $subQueryEnd . ')) AS exit_year')
            ->from($db->quoteName('#__cluborganisation_memberships', 'a'))
            ->join('LEFT', $db->quoteName('#__cluborganisation_persons', 'p') . ' ON p.id = a.person_id')
            ->join('LEFT', $db->quoteName('#__cluborganisation_salutations', 's') . ' ON s.id = p.salutation')
            ->join('LEFT', $db->quoteName('#__cluborganisation_membershiptypes', 't') . ' ON t.id = a.type')
            ->where($db->quoteName('a.begin') . ' <= ' . $db->quote($today))
            ->where('(' . $db->quoteName('a.end') . ' >= ' . $db->quote($today) . ' OR ' . $db->quoteName('a.end') . ' IS NULL)')
            ->where($db->quoteName('p.active') . ' = 1')
            ->where($db->quoteName('p.deceased') . ' IS NULL');

        // Build ORDER BY clause
        $orderby_pri = $params->get('orderby_pri', 'lastname');
        $order_dir = $params->get('order_dir', 'ASC');
        $orderby_sec = $params->get('orderby_sec', 'firstname');
        
        // Map ordering field to database column
        $orderMap = [
            'member_no' => 'p.member_no',
            'lastname' => 'p.lastname',
            'firstname' => 'p.firstname',
            'city' => 'p.city',
            'zip' => 'p.zip',
            'birthday' => 'p.birthday',
            'membership_type' => 't.title',
            'membership_begin' => 'a.begin',
            'first_membership' => 'first_membership_begin',
            'entry_year' => 'entry_year',
            'exit_year' => 'exit_year'
        ];
        
        // Primary ordering
        if (isset($orderMap[$orderby_pri])) {
            $query->order($db->quoteName($orderMap[$orderby_pri]) . ' ' . $order_dir);
        }
        
        // Secondary ordering
        if ($orderby_sec && isset($orderMap[$orderby_sec]) && $orderby_sec != $orderby_pri) {
            $query->order($db->quoteName($orderMap[$orderby_sec]) . ' ASC');
        }

        return $query;
    }
}
