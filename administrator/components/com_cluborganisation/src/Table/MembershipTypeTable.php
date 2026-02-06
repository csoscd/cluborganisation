<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_cluborganisation
 *
 * @copyright   Copyright (C) 2025 Christian Schulz
 * @license     GPL-2.0-or-later
 */

declare(strict_types=1);

namespace Joomla\Component\Cluborganisation\Administrator\Table;

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;

/**
 * Table class for membership types.
 */
class MembershipTypeTable extends Table
{
    /**
     * Constructor.
     *
     * @param   DatabaseDriver  $db  The database connector object.
     */
    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__cluborganisation_membership_types', 'id', $db);
    }
}
