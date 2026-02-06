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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;

/**
 * Model for a membership.
 */
class MembershipModel extends AdminModel
{
    /**
     * @inheritDoc
     */
    public function getTable($name = 'Membership', $prefix = 'Administrator\\', $options = [])
    {
        return parent::getTable($name, $prefix, $options);
    }

    /**
     * @inheritDoc
     */
    public function getForm($data = [], $loadData = true)
    {
        return $this->loadForm('com_cluborganisation.membership', 'membership', ['control' => 'jform', 'load_data' => $loadData]);
    }

    /**
     * @inheritDoc
     */
    public function save($data)
    {
        if (!$this->checkOverlap($data)) {
            $this->setError(Text::_('COM_CLUBORGANISATION_ERROR_MEMBERSHIP_OVERLAP'));
            return false;
        }

        $user = Factory::getApplication()->getIdentity();
        $now = Factory::getDate()->toSql();

        if (empty($data['id'])) {
            $data['createdby'] = $user->id;
            $data['createddate'] = $now;
        }

        $data['modifiedby'] = $user->id;
        $data['modifieddate'] = $now;

        return parent::save($data);
    }

    /**
     * Check if membership dates overlap.
     *
     * @param   array  $data  Membership data.
     *
     * @return  bool
     */
    private function checkOverlap(array $data): bool
    {
        if (empty($data['person_id']) || empty($data['begin'])) {
            return true;
        }

        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__cluborganisation_memberships'))
            ->where($db->quoteName('person_id') . ' = ' . (int) $data['person_id']);

        if (!empty($data['id'])) {
            $query->where($db->quoteName('id') . ' != ' . (int) $data['id']);
        }

        $begin = $db->quote($data['begin']);
        $end = empty($data['end']) ? null : $db->quote($data['end']);

        if ($end === null) {
            $query->where('(' . $db->quoteName('end') . ' IS NULL OR ' . $db->quoteName('end') . ' >= ' . $begin . ')');
        } else {
            $query->where('(' . $db->quoteName('end') . ' IS NULL OR ' . $db->quoteName('end') . ' >= ' . $begin . ')')
                ->where($db->quoteName('begin') . ' <= ' . $end);
        }

        $db->setQuery($query);

        return (int) $db->loadResult() === 0;
    }
}
