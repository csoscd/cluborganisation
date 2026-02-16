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
use Joomla\CMS\Factory;

/**
 * Membershiptype Fees List Model
 *
 * @since  1.7.0
 */
class MembershiptypefeesModel extends ListModel
{
    /**
     * Constructor
     *
     * @param   array  $config  An optional associative array of configuration settings
     *
     * @since   1.7.0
     */
    public function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'id', 'f.id',
                'membershiptype_id', 'f.membershiptype_id',
                'begin', 'f.begin',
                'amount', 'f.amount',
                'published', 'f.published',
                'membershiptype_title',
            ];
        }

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state
     *
     * @param   string  $ordering   An optional ordering field
     * @param   string  $direction  An optional direction (asc|desc)
     *
     * @return  void
     *
     * @since   1.7.0
     */
    protected function populateState($ordering = 'f.begin', $direction = 'DESC')
    {
        // Search
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        // Filter: Membershiptype
        $membershiptypeId = $this->getUserStateFromRequest($this->context . '.filter.membershiptype_id', 'filter_membershiptype_id', '');
        $this->setState('filter.membershiptype_id', $membershiptypeId);

        // Filter: Published
        $published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
        $this->setState('filter.published', $published);

        parent::populateState($ordering, $direction);
    }

    /**
     * Method to get a store id
     *
     * @param   string  $id  A prefix string
     *
     * @return  string
     *
     * @since   1.7.0
     */
    protected function getStoreId($id = '')
    {
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.membershiptype_id');
        $id .= ':' . $this->getState('filter.published');

        return parent::getStoreId($id);
    }

    /**
     * Build an SQL query to load the list data
     *
     * @return  \Joomla\Database\DatabaseQuery
     *
     * @since   1.7.0
     */
    protected function getListQuery()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        // Select
        $query->select('f.*')
            ->select('mt.title AS membershiptype_title')
            ->from($db->quoteName('#__cluborganisation_membershiptype_fees', 'f'))
            ->join('LEFT', $db->quoteName('#__cluborganisation_membershiptypes', 'mt') . 
                   ' ON ' . $db->quoteName('f.membershiptype_id') . ' = ' . $db->quoteName('mt.id'));

        // Filter: Search
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where($db->quoteName('f.id') . ' = ' . (int) substr($search, 3));
            } else {
                $search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
                $query->where('(mt.title LIKE ' . $search . ')');
            }
        }

        // Filter: Membershiptype
        $membershiptypeId = $this->getState('filter.membershiptype_id');
        if (is_numeric($membershiptypeId)) {
            $query->where($db->quoteName('f.membershiptype_id') . ' = ' . (int) $membershiptypeId);
        }

        // Filter: Published
        $published = $this->getState('filter.published');
        if (is_numeric($published)) {
            $query->where($db->quoteName('f.published') . ' = ' . (int) $published);
        } elseif ($published === '') {
            $query->where('(' . $db->quoteName('f.published') . ' = 0 OR ' . $db->quoteName('f.published') . ' = 1)');
        }

        // Ordering
        $orderCol = $this->state->get('list.ordering', 'f.begin');
        $orderDirn = $this->state->get('list.direction', 'DESC');
        $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

        return $query;
    }
}
