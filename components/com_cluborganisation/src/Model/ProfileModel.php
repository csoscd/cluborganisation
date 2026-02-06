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

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

/**
 * Model for user profile.
 */
class ProfileModel extends BaseDatabaseModel
{
    /**
     * Get the person for the current user.
     *
     * @return  object|null
     */
    public function getPerson(): ?object
    {
        $user = Factory::getApplication()->getIdentity();
        if (!$user || !$user->id) {
            return null;
        }

        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__cluborganisation_persons'))
            ->where($db->quoteName('user_id') . ' = ' . (int) $user->id);

        $db->setQuery($query);

        return $db->loadObject();
    }

    /**
     * Get memberships for a person.
     *
     * @param   int  $personId  Person id.
     *
     * @return  array
     */
    public function getMemberships(int $personId): array
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('m.*, t.title AS membership_type_title')
            ->from($db->quoteName('#__cluborganisation_memberships', 'm'))
            ->join('LEFT', $db->quoteName('#__cluborganisation_membership_types', 't') . ' ON t.id = m.type_id')
            ->where($db->quoteName('m.person_id') . ' = ' . (int) $personId)
            ->order($db->quoteName('m.begin') . ' DESC');

        $db->setQuery($query);

        return $db->loadObjectList();
    }
}
