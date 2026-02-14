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

/**
 * Person Table Klasse
 *
 * @since  1.0.0
 */
class PersonTable extends Table
{
    /**
     * Constructor
     *
     * @param   DatabaseDriver  $db  Database connector object
     *
     * @since   1.0.0
     */
    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__cluborganisation_persons', 'id', $db);
    }

    /**
     * Overridden check method to ensure data integrity
     *
     * @return  boolean  True on success
     *
     * @since   1.0.0
     */
    public function check()
    {
        // Prüfe Pflichtfelder
        if (empty($this->firstname)) {
            $this->setError('COM_CLUBORGANISATION_ERROR_FIRSTNAME_REQUIRED');
            return false;
        }

        if (empty($this->lastname)) {
            $this->setError('COM_CLUBORGANISATION_ERROR_LASTNAME_REQUIRED');
            return false;
        }

        if (empty($this->email)) {
            $this->setError('COM_CLUBORGANISATION_ERROR_EMAIL_REQUIRED');
            return false;
        }

        if (empty($this->member_no)) {
            $this->setError('COM_CLUBORGANISATION_ERROR_MEMBER_NO_REQUIRED');
            return false;
        }

        // Validiere E-Mail-Format
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->setError('COM_CLUBORGANISATION_ERROR_EMAIL_INVALID');
            return false;
        }

        // Prüfe auf doppelte Mitgliedsnummer
        $db = $this->getDbo();
        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__cluborganisation_persons'))
            ->where($db->quoteName('member_no') . ' = ' . $db->quote($this->member_no));

        if ($this->id) {
            $query->where($db->quoteName('id') . ' != ' . (int) $this->id);
        }

        $db->setQuery($query);
        $count = $db->loadResult();

        if ($count > 0) {
            $this->setError('COM_CLUBORGANISATION_ERROR_MEMBER_NO_EXISTS');
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
     * @since   1.0.0
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
