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
     * Overridden bind method to handle user_id correctly
     *
     * @param   array  $array   Named array to bind to the table
     * @param   mixed  $ignore  An optional array or space separated list of properties to ignore
     *
     * @return  boolean  True on success
     *
     * @since   1.6.0
     */
    public function bind($array, $ignore = '')
    {
        // KRITISCH: Behandle user_id BEVOR Joomla die Daten bindet
        
        // Wenn user_id im Array vorhanden ist
        if (array_key_exists('user_id', $array)) {
            $userId = $array['user_id'];
            
            // Prüfe alle Varianten von "kein Benutzer"
            if ($userId === null || 
                $userId === '' || 
                $userId === 0 || 
                $userId === '0' || 
                $userId === false) {
                
                // Explizit auf NULL setzen
                $array['user_id'] = null;
            } else {
                // Stelle sicher, dass es eine Integer ist
                $array['user_id'] = (int) $userId;
                
                // Prüfe ob User existiert
                if ($array['user_id'] > 0) {
                    $db = $this->getDbo();
                    $query = $db->getQuery(true)
                        ->select('COUNT(*)')
                        ->from($db->quoteName('#__users'))
                        ->where($db->quoteName('id') . ' = ' . (int) $array['user_id']);
                    
                    $db->setQuery($query);
                    $exists = (int) $db->loadResult() > 0;
                    
                    if (!$exists) {
                        // User existiert nicht, setze auf NULL
                        $array['user_id'] = null;
                    }
                }
            }
        }
        
        return parent::bind($array, $ignore);
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

        // WICHTIG: Wenn user_id NULL ist, muss updateNulls=true sein
        // damit NULL auch wirklich in die DB geschrieben wird
        if ($this->user_id === null) {
            $updateNulls = true;
        }

        return parent::store($updateNulls);
    }
}
