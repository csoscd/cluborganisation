<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_cluborganisation
 *
 * @copyright   Copyright (C) 2025 Christian Schulz
 * @license     GPL-2.0-or-later
 */

declare(strict_types=1);

namespace Joomla\Component\Cluborganisation\Administrator\Model;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\QueryInterface;

/**
 * List model for persons.
 */
class PersonsModel extends ListModel
{
    /**
     * @inheritDoc
     */
    protected function populateState($ordering = 'lastname', $direction = 'ASC')
    {
        $app = $this->getApplication();
        $this->setState('filter.lastname', $app->getUserStateFromRequest($this->context . '.filter.lastname', 'filter_lastname'));
        $this->setState('filter.firstname', $app->getUserStateFromRequest($this->context . '.filter.firstname', 'filter_firstname'));
        $this->setState('filter.member_no', $app->getUserStateFromRequest($this->context . '.filter.member_no', 'filter_member_no'));
        $this->setState('filter.active', $app->getUserStateFromRequest($this->context . '.filter.active', 'filter_active'));

        parent::populateState($ordering, $direction);
    }

    /**
     * @inheritDoc
     */
    protected function getListQuery(): QueryInterface
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('p.*')
            ->from($db->quoteName('#__cluborganisation_persons', 'p'));

        if ($lastname = $this->getState('filter.lastname')) {
            $query->where($db->quoteName('p.lastname') . ' LIKE ' . $db->quote('%' . $db->escape($lastname, true) . '%'));
        }

        if ($firstname = $this->getState('filter.firstname')) {
            $query->where($db->quoteName('p.firstname') . ' LIKE ' . $db->quote('%' . $db->escape($firstname, true) . '%'));
        }

        if ($memberNo = $this->getState('filter.member_no')) {
            $query->where($db->quoteName('p.member_no') . ' LIKE ' . $db->quote('%' . $db->escape($memberNo, true) . '%'));
        }

        if ($active = $this->getState('filter.active')) {
            $query->where($db->quoteName('p.active') . ' = ' . (int) $active);
        }

        return $query;
    }
}
