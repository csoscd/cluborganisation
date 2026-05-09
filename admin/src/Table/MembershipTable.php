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
 * Membership Table Klasse
 *
 * @since  1.0.0
 */
class MembershipTable extends Table
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
        parent::__construct('#__cluborganisation_memberships', 'id', $db);
    }

    /**
     * Überprüft die Datenintegrität
     *
     * @return  boolean  True bei Erfolg
     *
     * @since   1.0.0
     */
    public function check()
    {
        // Prüfe Pflichtfelder
        if (empty($this->person_id)) {
            $this->setError(Text::_('COM_CLUBORGANISATION_ERROR_PERSON_REQUIRED'));
            return false;
        }

        if (empty($this->type)) {
            $this->setError(Text::_('COM_CLUBORGANISATION_ERROR_TYPE_REQUIRED'));
            return false;
        }

        if (empty($this->begin)) {
            $this->setError(Text::_('COM_CLUBORGANISATION_ERROR_BEGIN_REQUIRED'));
            return false;
        }

        // end ist optional (für laufende Mitgliedschaften)
        // Prüfe nur wenn end gesetzt ist ob es nach begin liegt
        if (!empty($this->end) && strtotime($this->end) < strtotime($this->begin)) {
            $this->setError(Text::_('COM_CLUBORGANISATION_ERROR_END_BEFORE_BEGIN'));
            return false;
        }

        // Prüfe auf überlappende Mitgliedschaften
        if (!$this->checkOverlappingMemberships()) {
            return false;
        }

        // Prüfe abhängige Mitgliedschaftstypen
        if (!$this->checkDependentType()) {
            return false;
        }

        return parent::check();
    }

    /**
     * Prüft ob sich Mitgliedschaften überschneiden
     *
     * @return  boolean  True wenn keine Überschneidung vorliegt
     *
     * @since   1.0.0
     */
    private function checkOverlappingMemberships()
    {
        // Wenn end leer ist (laufende Mitgliedschaft), verwende weit in der Zukunft
        $endDate = !empty($this->end) ? $this->end : '9999-12-31';
        
        $db = $this->getDbo();
        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__cluborganisation_memberships'))
            ->where($db->quoteName('person_id') . ' = ' . (int) $this->person_id)
            ->where('(' .
                // Neue Mitgliedschaft beginnt während einer bestehenden
                '(' . $db->quoteName('begin') . ' <= ' . $db->quote($this->begin) .
                ' AND ((' . $db->quoteName('end') . ' >= ' . $db->quote($this->begin) . ')' .
                ' OR ' . $db->quoteName('end') . ' IS NULL))' .
                ' OR ' .
                // Neue Mitgliedschaft endet während einer bestehenden
                '(' . $db->quoteName('begin') . ' <= ' . $db->quote($endDate) .
                ' AND ((' . $db->quoteName('end') . ' >= ' . $db->quote($endDate) . ')' .
                ' OR ' . $db->quoteName('end') . ' IS NULL))' .
                ' OR ' .
                // Neue Mitgliedschaft umfasst eine bestehende vollständig
                '(' . $db->quoteName('begin') . ' >= ' . $db->quote($this->begin) .
                ' AND ((' . $db->quoteName('end') . ' <= ' . $db->quote($endDate) . ')' .
                ' OR (' . $db->quoteName('end') . ' IS NULL AND ' . $db->quote($endDate) . ' = \'9999-12-31\')))' .
                ')');

        if ($this->id) {
            $query->where($db->quoteName('id') . ' != ' . (int) $this->id);
        }

        $db->setQuery($query);
        $count = $db->loadResult();

        if ($count > 0) {
            $this->setError(Text::_('COM_CLUBORGANISATION_ERROR_MEMBERSHIP_OVERLAP'));
            return false;
        }

        return true;
    }

    /**
     * Prüft ob bei einem abhängigen Mitgliedschaftstyp eine übergeordnete
     * Mitgliedschaft ausgewählt wurde.
     *
     * @return  boolean  True wenn kein Fehler vorliegt
     *
     * @since   2.3.0
     */
    private function checkDependentType(): bool
    {
        $db    = $this->getDbo();
        $query = $db->getQuery(true)
            ->select(['is_dependent', 'depends_on_type'])
            ->from($db->quoteName('#__cluborganisation_membershiptypes'))
            ->where($db->quoteName('id') . ' = ' . (int) $this->type);
        $db->setQuery($query);
        $typeInfo = $db->loadObject();

        if (!$typeInfo || !$typeInfo->is_dependent) {
            return true;
        }

        if (empty($this->depends_on_membership_id)) {
            $this->setError(Text::_('COM_CLUBORGANISATION_ERROR_DEPENDS_ON_MEMBERSHIP_REQUIRED'));
            return false;
        }

        // Prüfe ob die übergeordnete Mitgliedschaft vom korrekten Typ ist
        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__cluborganisation_memberships'))
            ->where($db->quoteName('id') . ' = ' . (int) $this->depends_on_membership_id)
            ->where($db->quoteName('type') . ' = ' . (int) $typeInfo->depends_on_type);
        $db->setQuery($query);
        if ((int) $db->loadResult() === 0) {
            $this->setError(Text::_('COM_CLUBORGANISATION_ERROR_DEPENDS_ON_MEMBERSHIP_INVALID'));
            return false;
        }

        return true;
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

        if (!$this->id) {
            $this->created = $date->toSql();
            $this->created_by = $user->id;
        }

        $this->modified = $date->toSql();
        $this->modified_by = $user->id;

        return parent::store($updateNulls);
    }
}
