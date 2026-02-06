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
 * List model for memberships.
 */
class MembershipsModel extends ListModel
{
    /**
     * @inheritDoc
     */
    protected function populateState($ordering = 'begin', $direction = 'DESC')
    {
        $app = $this->getApplication();
        $this->setState('filter.begin', $app->getUserStateFromRequest($this->context . '.filter.begin', 'filter_begin'));
        $this->setState('filter.end', $app->getUserStateFromRequest($this->context . '.filter.end', 'filter_end'));
        $this->setState('filter.person_id', $app->getUserStateFromRequest($this->context . '.filter.person_id', 'filter_person_id'));
        $this->setState('filter.catid', $app->getUserStateFromRequest($this->context . '.filter.catid', 'filter_catid'));

        parent::populateState($ordering, $direction);
    }

    /**
     * @inheritDoc
     */
    protected function getListQuery(): QueryInterface
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('m.*')
            ->from($db->quoteName('#__cluborganisation_memberships', 'm'));

        if ($begin = $this->getState('filter.begin')) {
            $query->where($db->quoteName('m.begin') . ' >= ' . $db->quote($begin));
        }

        if ($end = $this->getState('filter.end')) {
            $query->where('(' . $db->quoteName('m.end') . ' IS NULL OR ' . $db->quoteName('m.end') . ' <= ' . $db->quote($end) . ')');
        }

        if ($personId = $this->getState('filter.person_id')) {
            $query->where($db->quoteName('m.person_id') . ' = ' . (int) $personId);
        }

        if ($catid = $this->getState('filter.catid')) {
            $query->where($db->quoteName('m.catid') . ' = ' . (int) $catid);
        }

        return $query;
    }
}
