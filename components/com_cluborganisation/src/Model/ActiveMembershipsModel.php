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

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\QueryInterface;

/**
 * Model for active memberships.
 */
class ActiveMembershipsModel extends ListModel
{
    /**
     * @inheritDoc
     */
    protected function getListQuery(): QueryInterface
    {
        $db = $this->getDatabase();
        $now = $db->quote(date('Y-m-d'));

        $query = $db->getQuery(true)
            ->select('m.*, p.firstname, p.lastname, p.member_no, t.title AS membership_type_title')
            ->from($db->quoteName('#__cluborganisation_memberships', 'm'))
            ->join('INNER', $db->quoteName('#__cluborganisation_persons', 'p') . ' ON p.id = m.person_id')
            ->join('LEFT', $db->quoteName('#__cluborganisation_membership_types', 't') . ' ON t.id = m.type_id')
            ->where($db->quoteName('m.begin') . ' <= ' . $now)
            ->where('(' . $db->quoteName('m.end') . ' IS NULL OR ' . $db->quoteName('m.end') . ' >= ' . $now . ')');

        return $query;
    }
}
