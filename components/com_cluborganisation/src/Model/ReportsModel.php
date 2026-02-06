<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_cluborganisation
 *
 * @copyright   Copyright (C) 2025 Christian Schulz
 * @license     GPL-2.0-or-later
 */

declare(strict_types=1);

namespace Joomla\Component\Cluborganisation\Site\Model;

use Joomla\CMS\MVC\Model\BaseDatabaseModel;

/**
 * Model for membership reports.
 */
class ReportsModel extends BaseDatabaseModel
{
    /**
     * Get memberships starting after a given year start.
     *
     * @param   int  $year  Year.
     *
     * @return  array
     */
    public function getBegins(int $year): array
    {
        $db = $this->getDatabase();
        $start = sprintf('%d-01-01', $year);

        $query = $db->getQuery(true)
            ->select('m.*, p.firstname, p.lastname, p.member_no, t.title AS membership_type_title')
            ->from($db->quoteName('#__cluborganisation_memberships', 'm'))
            ->join('INNER', $db->quoteName('#__cluborganisation_persons', 'p') . ' ON p.id = m.person_id')
            ->join('LEFT', $db->quoteName('#__cluborganisation_membership_types', 't') . ' ON t.id = m.type_id')
            ->where($db->quoteName('m.begin') . ' >= ' . $db->quote($start))
            ->order($db->quoteName('m.begin') . ' ASC');

        $db->setQuery($query);

        return $db->loadObjectList();
    }

    /**
     * Get memberships ending within a given year.
     *
     * @param   int  $year  Year.
     *
     * @return  array
     */
    public function getEnds(int $year): array
    {
        $db = $this->getDatabase();
        $start = sprintf('%d-01-01', $year);
        $end = sprintf('%d-12-31', $year);

        $query = $db->getQuery(true)
            ->select('m.*, p.firstname, p.lastname, p.member_no, t.title AS membership_type_title')
            ->from($db->quoteName('#__cluborganisation_memberships', 'm'))
            ->join('INNER', $db->quoteName('#__cluborganisation_persons', 'p') . ' ON p.id = m.person_id')
            ->join('LEFT', $db->quoteName('#__cluborganisation_membership_types', 't') . ' ON t.id = m.type_id')
            ->where($db->quoteName('m.end') . ' >= ' . $db->quote($start))
            ->where($db->quoteName('m.end') . ' <= ' . $db->quote($end))
            ->order($db->quoteName('m.end') . ' ASC');

        $db->setQuery($query);

        return $db->loadObjectList();
    }
}
