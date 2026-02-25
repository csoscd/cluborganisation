<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @license     GNU General Public License version 3 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

/**
 * Datacheck Model – prüft Vollständigkeit und Konsistenz der Mitgliederdaten.
 *
 * @since  2.1.0
 */
class DatacheckModel extends BaseDatabaseModel
{
    /**
     * Basis-Query: alle aktiven Personen mit mindestens einer offenen Mitgliedschaft.
     * Gibt id, firstname, lastname zurück plus optionale weitere Felder.
     *
     * @param   string[]  $extraCols  Zusätzliche Spalten (mit Alias-Präfix 'p.')
     *
     * @return  \Joomla\Database\DatabaseQuery
     *
     * @since   2.1.0
     */
    protected function baseActiveMembersQuery(array $extraCols = []): \Joomla\Database\DatabaseQuery
    {
        $db    = $this->getDbo();
        $query = $db->getQuery(true);

        $cols = [
            $db->quoteName('p.id'),
            $db->quoteName('p.firstname'),
            $db->quoteName('p.lastname'),
        ];
        foreach ($extraCols as $col) {
            $cols[] = $db->quoteName('p.' . $col);
        }

        $sqOpen = $db->getQuery(true);
        $sqOpen->select('DISTINCT ' . $db->quoteName('person_id'))
            ->from($db->quoteName('#__cluborganisation_memberships'))
            ->where($db->quoteName('end') . ' IS NULL');

        $query->select($cols)
            ->from($db->quoteName('#__cluborganisation_persons', 'p'))
            ->where($db->quoteName('p.active') . ' = 1')
            ->where($db->quoteName('p.id') . ' IN (' . $sqOpen . ')')
            ->order($db->quoteName('p.lastname') . ' ASC, ' . $db->quoteName('p.firstname') . ' ASC');

        return $query;
    }

    /**
     * Aktive Mitglieder ohne Geburtsdatum.
     *
     * @return  array  [['id' => 1, 'firstname' => '...', 'lastname' => '...'], ...]
     *
     * @since   2.1.0
     */
    public function getMissingBirthday(): array
    {
        $db    = $this->getDbo();
        $query = $this->baseActiveMembersQuery();
        $query->where(
            '(' . $db->quoteName('p.birthday') . ' IS NULL'
            . ' OR ' . $db->quoteName('p.birthday') . ' = ' . $db->quote('0000-00-00')
            . ' OR ' . $db->quoteName('p.birthday') . ' = ' . $db->quote('1970-01-01') . ')'
        );
        $db->setQuery($query);
        return $db->loadAssocList() ?: [];
    }

    /**
     * Aktive Mitglieder ohne E-Mail-Adresse.
     *
     * @return  array
     *
     * @since   2.1.0
     */
    public function getMissingEmail(): array
    {
        $db    = $this->getDbo();
        $query = $this->baseActiveMembersQuery(['email']);
        $query->where(
            '(' . $db->quoteName('p.email') . ' IS NULL'
            . ' OR ' . $db->quoteName('p.email') . ' = ' . $db->quote('') . ')'
        );
        $db->setQuery($query);
        return $db->loadAssocList() ?: [];
    }

    /**
     * Aktive Mitglieder ohne Mobilfunknummer.
     *
     * @return  array
     *
     * @since   2.1.0
     */
    public function getMissingMobile(): array
    {
        $db    = $this->getDbo();
        $query = $this->baseActiveMembersQuery(['mobile']);
        $query->where(
            '(' . $db->quoteName('p.mobile') . ' IS NULL'
            . ' OR ' . $db->quoteName('p.mobile') . ' = ' . $db->quote('') . ')'
        );
        $db->setQuery($query);
        return $db->loadAssocList() ?: [];
    }

    /**
     * Aktive Mitglieder ohne verknüpften Joomla-Benutzer.
     *
     * @return  array
     *
     * @since   2.1.0
     */
    public function getMissingUser(): array
    {
        $db    = $this->getDbo();
        $query = $this->baseActiveMembersQuery(['user_id']);
        $query->where(
            '(' . $db->quoteName('p.user_id') . ' IS NULL'
            . ' OR ' . $db->quoteName('p.user_id') . ' = 0)'
        );
        $db->setQuery($query);
        return $db->loadAssocList() ?: [];
    }

    /**
     * Zusammenfassung: Anzahl der aktiven Mitglieder je Kategorie.
     *
     * @return  array  ['total' => n, 'missingBirthday' => n, 'missingEmail' => n, ...]
     *
     * @since   2.1.0
     */
    /**
     * Personen ohne jede Mitgliedschaft (weder aktiv noch historisch).
     * Nur aktive Personen (active = 1).
     *
     * @return  array
     *
     * @since   2.1.0
     */
    public function getNoMembership(): array
    {
        $db    = $this->getDbo();
        $query = $db->getQuery(true);

        // Alle Personen-IDs, die irgendeine Mitgliedschaft haben
        $sqHas = $db->getQuery(true);
        $sqHas->select('DISTINCT ' . $db->quoteName('person_id'))
            ->from($db->quoteName('#__cluborganisation_memberships'));

        $query->select([
            $db->quoteName('p.id'),
            $db->quoteName('p.firstname'),
            $db->quoteName('p.lastname'),
        ])
            ->from($db->quoteName('#__cluborganisation_persons', 'p'))
            ->where($db->quoteName('p.active') . ' = 1')
            ->where($db->quoteName('p.id') . ' NOT IN (' . $sqHas . ')')
            ->order($db->quoteName('p.lastname') . ' ASC, ' . $db->quoteName('p.firstname') . ' ASC');

        $db->setQuery($query);
        return $db->loadAssocList() ?: [];
    }

    /**
     * Aktive Personen ohne aktive Mitgliedschaft (kein end=NULL-Eintrag).
     * Das sind Personen, die noch als active=1 markiert sind, aber keine
     * laufende Mitgliedschaft mehr haben.
     *
     * @return  array  [['id' => 1, 'firstname' => '...', 'lastname' => '...'], ...]
     * @since   2.2.0
     */
    /**
     * Deaktiviert eine Person (active = 0) und – sofern verknüpft –
     * auch den zugehörigen Joomla-Benutzer (block = 1).
     *
     * @param   int  $personId  ID der Person
     *
     * @return  array  ['success' => bool, 'message' => string]
     * @since   2.2.0
     */
    public function deactivatePerson(int $personId): array
    {
        $db = $this->getDbo();

        // user_id der Person auslesen
        $queryUser = $db->getQuery(true);
        $queryUser->select($db->quoteName('user_id'))
            ->from($db->quoteName('#__cluborganisation_persons'))
            ->where($db->quoteName('id') . ' = ' . $personId);
        $db->setQuery($queryUser);
        $userId = (int) $db->loadResult();

        // Person deaktivieren
        $query = $db->getQuery(true);
        $query->update($db->quoteName('#__cluborganisation_persons'))
            ->set($db->quoteName('active') . ' = 0')
            ->set($db->quoteName('modified') . ' = ' . $db->quote(date('Y-m-d H:i:s')))
            ->where($db->quoteName('id') . ' = ' . $personId);
        $db->setQuery($query);
        $db->execute();

        // Verknüpften Joomla-Benutzer sperren (falls vorhanden)
        $userBlocked = false;
        if ($userId > 0) {
            $queryBlock = $db->getQuery(true);
            $queryBlock->update($db->quoteName('#__users'))
                ->set($db->quoteName('block') . ' = 1')
                ->where($db->quoteName('id') . ' = ' . $userId);
            $db->setQuery($queryBlock);
            $db->execute();
            $userBlocked = true;
        }

        $message = $userBlocked
            ? Text::sprintf(
                'COM_CLUBORGANISATION_DATACHECK_DEACTIVATE_SUCCESS_WITH_USER',
                $personId
              )
            : Text::sprintf(
                'COM_CLUBORGANISATION_DATACHECK_DEACTIVATE_SUCCESS',
                $personId
              );

        return ['success' => true, 'message' => $message];
    }

    public function getActivePersonsWithoutActiveMembership(): array
    {
        $db = $this->getDbo();

        // Personen-IDs mit mind. einer end=NULL-Mitgliedschaft
        $sqOpen = $db->getQuery(true);
        $sqOpen->select('DISTINCT ' . $db->quoteName('person_id'))
            ->from($db->quoteName('#__cluborganisation_memberships'))
            ->where($db->quoteName('end') . ' IS NULL');

        $query = $db->getQuery(true);
        $query->select([
            $db->quoteName('p.id'),
            $db->quoteName('p.firstname'),
            $db->quoteName('p.lastname'),
        ])
            ->from($db->quoteName('#__cluborganisation_persons', 'p'))
            ->where($db->quoteName('p.active') . ' = 1')
            ->where($db->quoteName('p.id') . ' NOT IN (' . $sqOpen . ')')
            ->order($db->quoteName('p.lastname') . ' ASC, ' . $db->quoteName('p.firstname') . ' ASC');

        $db->setQuery($query);
        return $db->loadAssocList() ?: [];
    }

    public function getSummary(): array
    {
        return [
            'missingBirthday' => count($this->getMissingBirthday()),
            'missingEmail'    => count($this->getMissingEmail()),
            'missingMobile'   => count($this->getMissingMobile()),
            'missingUser'     => count($this->getMissingUser()),
            'noMembership'    => count($this->getNoMembership()),
            'activeNoActive'  => count($this->getActivePersonsWithoutActiveMembership()),
        ];
    }
}
