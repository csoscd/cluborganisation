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
 * List model for membership types.
 */
class MembershipTypesModel extends ListModel
{
    /**
     * @inheritDoc
     */
    protected function getListQuery(): QueryInterface
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__cluborganisation_membership_types'));

        return $query;
    }
}
