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
use Joomla\CMS\MVC\Model\AdminModel;

/**
 * Model for a person.
 */
class PersonModel extends AdminModel
{
    /**
     * @inheritDoc
     */
    public function getTable($name = 'Person', $prefix = 'Administrator\\', $options = [])
    {
        return parent::getTable($name, $prefix, $options);
    }

    /**
     * @inheritDoc
     */
    public function getForm($data = [], $loadData = true)
    {
        return $this->loadForm('com_cluborganisation.person', 'person', ['control' => 'jform', 'load_data' => $loadData]);
    }

    /**
     * @inheritDoc
     */
    public function save($data)
    {
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
}
