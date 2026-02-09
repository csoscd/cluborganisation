<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @copyright   2026 Christian Schulz
 * @license     GNU General Public License version 2 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;

/**
 * Persons List Model
 *
 * Model für die Personen-Übersichtsliste im Administrator-Bereich
 *
 * @since  1.0.0
 */
class PersonsModel extends ListModel
{
    /**
     * Constructor
     *
     * @param   array  $config  An optional associative array of configuration settings
     *
     * @since   1.0.0
     */
    public function __construct($config = [])
    {
        // Filter-Felder definieren
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'id', 'a.id',
                'firstname', 'a.firstname',
                'lastname', 'a.lastname',
                'member_no', 'a.member_no',
                'email', 'a.email',
                'active', 'a.active',
                'created', 'a.created',
                'salutation', 'a.salutation',
                'user_id', 'a.user_id',
            ];
        }

        parent::__construct($config);
    }

    /**
     * Methode zum Aufbau der SQL-Query für die Liste
     *
     * @return  \Joomla\Database\DatabaseQuery
     *
     * @since   1.0.0
     */
    protected function getListQuery()
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);

        // Haupttabelle auswählen
        $query->select(
            $this->getState(
                'list.select',
                [
                    $db->quoteName('a.id'),
                    $db->quoteName('a.salutation'),
                    $db->quoteName('a.firstname'),
                    $db->quoteName('a.middlename'),
                    $db->quoteName('a.lastname'),
                    $db->quoteName('a.birthname'),
                    $db->quoteName('a.email'),
                    $db->quoteName('a.member_no'),
                    $db->quoteName('a.active'),
                    $db->quoteName('a.user_id'),
                    $db->quoteName('a.created'),
                    $db->quoteName('a.created_by'),
                    $db->quoteName('a.modified'),
                    $db->quoteName('a.modified_by'),
                ]
            )
        )
        ->from($db->quoteName('#__cluborganisation_persons', 'a'));

        // Join mit Salutations-Tabelle
        $query->select($db->quoteName('s.title', 'salutation_title'))
            ->join(
                'LEFT',
                $db->quoteName('#__cluborganisation_salutations', 's'),
                $db->quoteName('s.id') . ' = ' . $db->quoteName('a.salutation')
            );

        // Join mit Users-Tabelle für created_by
        $query->select($db->quoteName('uc.name', 'created_by_name'))
            ->join(
                'LEFT',
                $db->quoteName('#__users', 'uc'),
                $db->quoteName('uc.id') . ' = ' . $db->quoteName('a.created_by')
            );

        // Join mit Users-Tabelle für modified_by
        $query->select($db->quoteName('um.name', 'modified_by_name'))
            ->join(
                'LEFT',
                $db->quoteName('#__users', 'um'),
                $db->quoteName('um.id') . ' = ' . $db->quoteName('a.modified_by')
            );

        // Join mit Users-Tabelle für verknüpften Joomla-User
        $query->select($db->quoteName('u.username', 'user_username'))
            ->join(
                'LEFT',
                $db->quoteName('#__users', 'u'),
                $db->quoteName('u.id') . ' = ' . $db->quoteName('a.user_id')
            );

        // Filter nach Suchbegriff
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where($db->quoteName('a.id') . ' = ' . (int) substr($search, 3));
            } else {
                $search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
                $query->where(
                    '(' . $db->quoteName('a.firstname') . ' LIKE ' . $search
                    . ' OR ' . $db->quoteName('a.lastname') . ' LIKE ' . $search
                    . ' OR ' . $db->quoteName('a.member_no') . ' LIKE ' . $search
                    . ' OR ' . $db->quoteName('a.email') . ' LIKE ' . $search . ')'
                );
            }
        }

        // Filter nach Aktiv-Status
        $active = $this->getState('filter.active');
        if (is_numeric($active)) {
            $query->where($db->quoteName('a.active') . ' = :active')
                ->bind(':active', $active, ParameterType::INTEGER);
        }

        // Filter nach Anrede
        $salutation = $this->getState('filter.salutation');
        if (is_numeric($salutation)) {
            $query->where($db->quoteName('a.salutation') . ' = :salutation')
                ->bind(':salutation', $salutation, ParameterType::INTEGER);
        }

        // Filter nach verknüpftem User
        $userId = $this->getState('filter.user_id');
        if (is_numeric($userId)) {
            $query->where($db->quoteName('a.user_id') . ' = :userid')
                ->bind(':userid', $userId, ParameterType::INTEGER);
        }

        // Sortierung hinzufügen
        $orderCol = $this->state->get('list.ordering', 'a.lastname');
        $orderDirn = $this->state->get('list.direction', 'ASC');

        $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

        return $query;
    }

    /**
     * Methode zum Abrufen der Daten
     *
     * @return  mixed  Ein Array mit Daten bei Erfolg, false bei Fehler
     *
     * @since   1.0.0
     */
    public function getItems()
    {
        $items = parent::getItems();

        return $items;
    }

    /**
     * Populiert den State mit Request-Variablen
     *
     * @param   string  $ordering   Spalte für Sortierung
     * @param   string  $direction  Sortierrichtung
     *
     * @return  void
     *
     * @since   1.0.0
     */
    protected function populateState($ordering = 'a.lastname', $direction = 'ASC')
    {
        // Suchfilter
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        // Aktiv-Filter
        $active = $this->getUserStateFromRequest($this->context . '.filter.active', 'filter_active', '');
        $this->setState('filter.active', $active);

        // Anreden-Filter
        $salutation = $this->getUserStateFromRequest($this->context . '.filter.salutation', 'filter_salutation', '');
        $this->setState('filter.salutation', $salutation);

        // User-Filter
        $userId = $this->getUserStateFromRequest($this->context . '.filter.user_id', 'filter_user_id', '');
        $this->setState('filter.user_id', $userId);

        // Parent-Methode aufrufen
        parent::populateState($ordering, $direction);
    }

    /**
     * Gibt die Store-ID basierend auf dem State zurück
     *
     * @param   string  $id  Ein Identifier-String
     *
     * @return  string  Ein Store-ID
     *
     * @since   1.0.0
     */
    protected function getStoreId($id = '')
    {
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.active');
        $id .= ':' . $this->getState('filter.salutation');
        $id .= ':' . $this->getState('filter.user_id');

        return parent::getStoreId($id);
    }
}
