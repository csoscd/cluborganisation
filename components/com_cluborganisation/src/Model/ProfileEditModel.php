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
use Joomla\CMS\MVC\Model\FormModel;
use Joomla\Component\Cluborganisation\Administrator\Table\PersonTable;

/**
 * Model for editing the current user's profile.
 */
class ProfileEditModel extends FormModel
{
    /**
     * @inheritDoc
     */
    public function getForm($data = [], $loadData = true)
    {
        return $this->loadForm('com_cluborganisation.profile', 'profile', ['control' => 'jform', 'load_data' => $loadData]);
    }

    /**
     * @inheritDoc
     */
    protected function loadFormData()
    {
        $profileModel = new ProfileModel($this->getDatabase());
        $person = $profileModel->getPerson();

        return $person ? (array) $person : [];
    }

    /**
     * Save the profile data.
     *
     * @param   array  $data  The submitted data.
     *
     * @return  bool
     */
    public function save($data)
    {
        $user = Factory::getApplication()->getIdentity();
        if (!$user || !$user->id) {
            return false;
        }

        $allowed = [
            'email',
            'mobile',
            'telephone',
            'address',
            'city',
            'zip',
            'country',
        ];

        $filtered = array_intersect_key($data, array_flip($allowed));

        $table = new PersonTable($this->getDatabase());
        $person = (new ProfileModel($this->getDatabase()))->getPerson();

        if (!$person) {
            return false;
        }

        $table->load($person->id);
        $table->bind($filtered);

        $table->modifiedby = $user->id;
        $table->modifieddate = Factory::getDate()->toSql();

        return $table->store();
    }
}
