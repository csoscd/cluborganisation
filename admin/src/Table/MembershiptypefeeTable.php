<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @license     GNU General Public License version 3 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Administrator\Table;

defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

/**
 * Membershiptype Fee Table Klasse
 *
 * @since  1.7.0
 */
class MembershiptypefeeTable extends Table
{
    /**
     * Constructor
     *
     * @param   DatabaseDriver  $db  Database connector object
     *
     * @since   1.7.0
     */
    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__cluborganisation_membershiptype_fees', 'id', $db);
    }

    /**
     * Overridden check method to ensure data integrity
     *
     * @return  boolean  True on success
     *
     * @since   1.7.0
     */
    public function check()
    {
        // Prüfe Pflichtfelder
        if (empty($this->membershiptype_id)) {
            $this->setError(Text::_('COM_CLUBORGANISATION_ERROR_MEMBERSHIPTYPE_REQUIRED'));
            return false;
        }

        if (empty($this->begin)) {
            $this->setError(Text::_('COM_CLUBORGANISATION_ERROR_BEGIN_DATE_REQUIRED'));
            return false;
        }

        if (!isset($this->amount) || $this->amount === '') {
            $this->setError(Text::_('COM_CLUBORGANISATION_ERROR_AMOUNT_REQUIRED'));
            return false;
        }

        // Validiere amount (muss >= 0 sein)
        if ($this->amount < 0) {
            $this->setError(Text::_('COM_CLUBORGANISATION_ERROR_AMOUNT_NEGATIVE'));
            return false;
        }

        return parent::check();
    }

    /**
     * Method to store a row
     *
     * @param   boolean  $updateNulls  True to update null values
     *
     * @return  boolean  True on success
     *
     * @since   1.7.0
     */
    public function store($updateNulls = false)
    {
        $date = Factory::getDate();
        $user = Factory::getApplication()->getIdentity();

        // Setze created Felder bei neuen Einträgen
        if (!$this->id) {
            $this->created = $date->toSql();
            $this->created_by = $user->id;
        }

        // Setze modified Felder bei jedem Speichern
        $this->modified = $date->toSql();
        $this->modified_by = $user->id;

        return parent::store($updateNulls);
    }
}
