<?php
namespace CSOSCD\Component\ClubOrganisation\Administrator\Model;
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\MVC\Model\FormBehaviorTrait;

class MembershipsModel extends ListModel
{
    use FormBehaviorTrait;
    public function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'id', 'a.id',
                'person_id', 'a.person_id',
                'type', 'a.type',
                'begin', 'a.begin',
                'end', 'a.end',
                'search',
                'active_only',
                'p.lastname', 'p.firstname',
            ];
        }
        parent::__construct($config);
    }

    protected function getListQuery()
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select('a.*')
            ->from($db->quoteName('#__cluborganisation_memberships', 'a'))
            ->select($db->quoteName('p.firstname') . ', ' . $db->quoteName('p.lastname') . ', ' . $db->quoteName('p.member_no'))
            ->join('LEFT', $db->quoteName('#__cluborganisation_persons', 'p') . ' ON ' . $db->quoteName('p.id') . ' = ' . $db->quoteName('a.person_id'))
            ->select($db->quoteName('t.title', 'type_title'))
            ->join('LEFT', $db->quoteName('#__cluborganisation_membershiptypes', 't') . ' ON ' . $db->quoteName('t.id') . ' = ' . $db->quoteName('a.type'));

        // Filter: Suche nach Name/Vorname/Mitgliedsnummer
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            $search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true)) . '%');
            $query->where('(' .
                $db->quoteName('p.lastname') . ' LIKE ' . $search . ' OR ' .
                $db->quoteName('p.firstname') . ' LIKE ' . $search . ' OR ' .
                $db->quoteName('p.member_no') . ' LIKE ' . $search .
                ')');
        }

        // Filter: Nur aktive Mitgliedschaften
        $activeOnly = $this->getState('filter.active_only');
        if ($activeOnly !== '' && $activeOnly !== null) {
            $today = $db->quote(date('Y-m-d'));
            if ($activeOnly == '1') {
                // Nur aktive: begin <= heute UND (end >= heute ODER end IS NULL)
                $query->where($db->quoteName('a.begin') . ' <= ' . $today);
                $query->where('(' . $db->quoteName('a.end') . ' >= ' . $today . 
                             ' OR ' . $db->quoteName('a.end') . ' IS NULL)');
            } else {
                // Nur inaktive: end < heute UND end IS NOT NULL
                $query->where($db->quoteName('a.end') . ' < ' . $today);
                $query->where($db->quoteName('a.end') . ' IS NOT NULL');
            }
        }

        // Filter: Nach Typ
        $type = $this->getState('filter.type');
        if (is_numeric($type)) {
            $query->where($db->quoteName('a.type') . ' = ' . (int) $type);
        }

        $orderCol = $this->state->get('list.ordering', 'a.begin');
        $orderDirn = $this->state->get('list.direction', 'DESC');
        $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

        return $query;
    }

    protected function populateState($ordering = 'a.begin', $direction = 'DESC')
    {
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search', '', 'string');
        $this->setState('filter.search', $search);

        $activeOnly = $this->getUserStateFromRequest($this->context . '.filter.active_only', 'filter_active_only', '', 'string');
        $this->setState('filter.active_only', $activeOnly);

        $type = $this->getUserStateFromRequest($this->context . '.filter.type', 'filter_type', '', 'int');
        $this->setState('filter.type', $type);

        parent::populateState($ordering, $direction);
    }

    public function getFilterForm($data = [], $loadData = true)
    {
        $form = $this->loadForm(
            'com_cluborganisation.memberships.filter',
            'filter_memberships',
            ['control' => '', 'load_data' => $loadData]
        );

        if (!$form) {
            return false;
        }

        return $form;
    }
}
