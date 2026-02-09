<?php
namespace CSOSCD\Component\ClubOrganisation\Site\Model;
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Model\ListModel;

class MembermovementsModel extends ListModel
{
    protected function getListQuery()
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        
        // Get params
        $app = \Joomla\CMS\Factory::getApplication();
        $params = $app->getParams();
        
        $movementType = $params->get('movement_type', 'entries');
        $selectedYear = $params->get('movement_year', date('Y'));

        // Subquery for earliest membership begin (first_membership_begin)
        $subQueryFirst = $db->getQuery(true);
        $subQueryFirst->select('MIN(' . $db->quoteName('m2.begin') . ')')
            ->from($db->quoteName('#__cluborganisation_memberships', 'm2'))
            ->where($db->quoteName('m2.person_id') . ' = ' . $db->quoteName('p.id'));

        // Subquery for latest membership end (last_membership_end)
        $subQueryLast = $db->getQuery(true);
        $subQueryLast->select('MAX(' . $db->quoteName('m3.end') . ')')
            ->from($db->quoteName('#__cluborganisation_memberships', 'm3'))
            ->where($db->quoteName('m3.person_id') . ' = ' . $db->quoteName('p.id'))
            ->where($db->quoteName('m3.end') . ' IS NOT NULL');

        $query->select('a.*, p.firstname, p.lastname, p.member_no, p.email, p.mobile')
            ->select('p.salutation, p.address, p.zip, p.city, p.telephone, p.birthday')
            ->select('s.title AS salutation_title')
            ->select('t.title AS type_title')
            ->select('(' . $subQueryFirst . ') AS first_membership_begin')
            ->select('YEAR((' . $subQueryFirst . ')) AS entry_year')
            ->select('(' . $subQueryLast . ') AS last_membership_end')
            ->select('YEAR((' . $subQueryLast . ')) AS exit_year')
            ->from($db->quoteName('#__cluborganisation_persons', 'p'))
            ->join('LEFT', $db->quoteName('#__cluborganisation_salutations', 's') . ' ON s.id = p.salutation');

        if ($movementType === 'entries') {
            // Entries: Show persons whose earliest membership begin year = selected year
            // Join with the membership that has the earliest begin date
            $query->join('LEFT', $db->quoteName('#__cluborganisation_memberships', 'a') . ' ON a.person_id = p.id AND a.begin = (' . $subQueryFirst . ')')
                ->join('LEFT', $db->quoteName('#__cluborganisation_membershiptypes', 't') . ' ON t.id = a.type')
                ->where('YEAR((' . $subQueryFirst . ')) = ' . $db->quote($selectedYear));
        } else {
            // Exits: Show persons whose latest membership end year = selected year
            // Join with the membership that has the latest end date
            $query->join('LEFT', $db->quoteName('#__cluborganisation_memberships', 'a') . ' ON a.person_id = p.id AND a.end = (' . $subQueryLast . ')')
                ->join('LEFT', $db->quoteName('#__cluborganisation_membershiptypes', 't') . ' ON t.id = a.type')
                ->where('YEAR((' . $subQueryLast . ')) = ' . $db->quote($selectedYear))
                ->where('(' . $subQueryLast . ') IS NOT NULL');
        }

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
            'membership_end' => 'a.end',
            'first_membership' => 'first_membership_begin',
            'last_membership' => 'last_membership_end',
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
