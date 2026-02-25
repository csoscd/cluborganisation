<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @license     GNU General Public License version 3 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

/**
 * Dashboard Model – liefert Kennzahlen und Status-Informationen für das Dashboard.
 *
 * @since  2.2.0
 */
class DashboardModel extends BaseDatabaseModel
{
    // ─────────────────────────────────────────────────────────────────────────
    // Mitglieder-Kennzahlen
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Anzahl der aktiven Personen.
     *
     * @return  int
     * @since   2.2.0
     */
    public function getActivePersonsCount(): int
    {
        $db    = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('COUNT(*)')
            ->from($db->quoteName('#__cluborganisation_persons'))
            ->where($db->quoteName('active') . ' = 1');
        $db->setQuery($query);
        return (int) $db->loadResult();
    }

    /**
     * Anzahl der aktiven Mitgliedschaften (end IS NULL).
     *
     * @return  int
     * @since   2.2.0
     */
    public function getActiveMembershipsCount(): int
    {
        $db    = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('COUNT(*)')
            ->from($db->quoteName('#__cluborganisation_memberships'))
            ->where($db->quoteName('end') . ' IS NULL');
        $db->setQuery($query);
        return (int) $db->loadResult();
    }

    /**
     * Personen, deren letzte Mitgliedschaft im aktuellen Jahr endet –
     * d. h. es gibt eine Mitgliedschaft mit end im aktuellen Jahr,
     * aber keine weitere Mitgliedschaft, die danach beginnt (end=NULL oder begin > end).
     *
     * @return  int
     * @since   2.2.0
     */
    public function getMembershipsEndingThisYear(): int
    {
        $db          = $this->getDbo();
        $currentYear = (int) date('Y');
        $yearStart   = $currentYear . '-01-01';
        $yearEnd     = $currentYear . '-12-31';

        // Subquery: Mitgliedschaften, die in diesem Jahr enden
        // (end im aktuellen Jahr und end IS NOT NULL)
        $sqEnding = $db->getQuery(true);
        $sqEnding->select('DISTINCT ' . $db->quoteName('person_id'))
            ->from($db->quoteName('#__cluborganisation_memberships'))
            ->where($db->quoteName('end') . ' >= ' . $db->quote($yearStart))
            ->where($db->quoteName('end') . ' <= ' . $db->quote($yearEnd));

        // Subquery: Personen, die NACH dem Enddatum noch eine weitere Mitgliedschaft haben
        // (end=NULL = noch aktiv, oder begin > Enddatum dieser Mitgliedschaft)
        // Vereinfachung: Personen mit mind. einer end=NULL oder
        // einer Mitgliedschaft die nach Jahresende beginnt → noch nicht wirklich "endend"
        $sqContinuing = $db->getQuery(true);
        $sqContinuing->select('DISTINCT ' . $db->quoteName('person_id'))
            ->from($db->quoteName('#__cluborganisation_memberships'))
            ->where(
                '(' . $db->quoteName('end') . ' IS NULL'
                . ' OR ' . $db->quoteName('begin') . ' > ' . $db->quote($yearEnd) . ')'
            );

        // Personen, die zwar in diesem Jahr enden, aber KEINE Folgemitgliedschaft haben
        $query = $db->getQuery(true);
        $query->select('COUNT(DISTINCT ' . $db->quoteName('person_id') . ')')
            ->from($db->quoteName('#__cluborganisation_memberships'))
            ->where($db->quoteName('person_id') . ' IN (' . $sqEnding . ')')
            ->where($db->quoteName('person_id') . ' NOT IN (' . $sqContinuing . ')');

        $db->setQuery($query);
        return (int) $db->loadResult();
    }

    /**
     * Neue Mitglieder im laufenden Monat (Eintrittsdatum MIN(begin) = aktueller Monat).
     *
     * @return  int
     * @since   2.2.0
     */
    public function getNewMembersThisMonth(): int
    {
        $db        = $this->getDbo();
        $firstDay  = date('Y-m-01');
        $lastDay   = date('Y-m-t');

        // Subquery: MIN(begin) je Person
        $sqEarliest = $db->getQuery(true);
        $sqEarliest->select($db->quoteName('person_id'))
            ->select('MIN(' . $db->quoteName('begin') . ') AS earliest')
            ->from($db->quoteName('#__cluborganisation_memberships'))
            ->group($db->quoteName('person_id'));

        $query = $db->getQuery(true);
        $query->select('COUNT(*)')
            ->from('(' . $sqEarliest . ') AS sub')
            ->where('sub.earliest >= ' . $db->quote($firstDay))
            ->where('sub.earliest <= ' . $db->quote($lastDay));

        $db->setQuery($query);
        return (int) $db->loadResult();
    }

    /**
     * Jubiläen im laufenden Monat (aktive Mitglieder).
     *
     * @return  array  [['firstname' => '...', 'lastname' => '...', 'years' => 10], ...]
     * @since   2.2.0
     */
    public function getAnniversariesThisMonth(): array
    {
        $milestones  = [5, 10, 20, 25, 40];
        $currentYear = (int) date('Y');
        $currentMonth = (int) date('m');
        $db          = $this->getDbo();

        // Frühestes begin je Person mit end=NULL-Mitgliedschaft
        $sqActive = $db->getQuery(true);
        $sqActive->select('DISTINCT ' . $db->quoteName('person_id'))
            ->from($db->quoteName('#__cluborganisation_memberships'))
            ->where($db->quoteName('end') . ' IS NULL');

        $sqEarliest = $db->getQuery(true);
        $sqEarliest->select($db->quoteName('person_id'))
            ->select('MIN(' . $db->quoteName('begin') . ') AS earliest_begin')
            ->from($db->quoteName('#__cluborganisation_memberships'))
            ->where($db->quoteName('person_id') . ' IN (' . $sqActive . ')')
            ->group($db->quoteName('person_id'));

        // Jubiläumsjahre-Filter
        $yearConds = [];
        foreach ($milestones as $m) {
            $yearConds[] = 'YEAR(sub.earliest_begin) = ' . ($currentYear - $m);
        }

        $query = $db->getQuery(true);
        $query->select('p.' . $db->quoteName('firstname'))
            ->select('p.' . $db->quoteName('lastname'))
            ->select('YEAR(sub.earliest_begin) AS entry_year')
            ->from('(' . $sqEarliest . ') AS sub')
            ->join('INNER', $db->quoteName('#__cluborganisation_persons', 'p')
                . ' ON p.' . $db->quoteName('id') . ' = sub.person_id')
            ->where('(' . implode(' OR ', $yearConds) . ')')
            ->where('MONTH(sub.earliest_begin) = ' . $currentMonth)
            ->where('p.' . $db->quoteName('active') . ' = 1')
            ->order('p.' . $db->quoteName('lastname') . ' ASC');

        $db->setQuery($query);
        $rows    = $db->loadAssocList() ?: [];
        $results = [];
        foreach ($rows as $row) {
            $results[] = [
                'firstname'  => $row['firstname'],
                'lastname'   => $row['lastname'],
                'entry_year' => (int) $row['entry_year'],
                'years'      => $currentYear - (int) $row['entry_year'],
            ];
        }
        return $results;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Datacheck-Zusammenfassung
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Datacheck-Zähler: wie viele aktive Mitglieder haben fehlende Pflichtdaten?
     *
     * @return  array  ['birthday' => n, 'email' => n, 'mobile' => n, 'user' => n, 'noMembership' => n]
     * @since   2.2.0
     */
    public function getDatacheckSummary(): array
    {
        $db = $this->getDbo();

        // Aktive Personen mit offener Mitgliedschaft
        $sqOpen = $db->getQuery(true);
        $sqOpen->select('DISTINCT ' . $db->quoteName('person_id'))
            ->from($db->quoteName('#__cluborganisation_memberships'))
            ->where($db->quoteName('end') . ' IS NULL');

        $base = $db->getQuery(true);
        $base->from($db->quoteName('#__cluborganisation_persons', 'p'))
            ->where($db->quoteName('p.active') . ' = 1')
            ->where($db->quoteName('p.id') . ' IN (' . $sqOpen . ')');

        $counts = [];

        foreach ([
            'birthday' => '(' . $db->quoteName('p.birthday') . ' IS NULL OR ' . $db->quoteName('p.birthday') . ' = ' . $db->quote('0000-00-00') . ' OR ' . $db->quoteName('p.birthday') . ' = ' . $db->quote('1970-01-01') . ')',
            'email'    => '(' . $db->quoteName('p.email') . ' IS NULL OR ' . $db->quoteName('p.email') . ' = ' . $db->quote('') . ')',
            'mobile'   => '(' . $db->quoteName('p.mobile') . ' IS NULL OR ' . $db->quoteName('p.mobile') . ' = ' . $db->quote('') . ')',
            'user'     => '(' . $db->quoteName('p.user_id') . ' IS NULL OR ' . $db->quoteName('p.user_id') . ' = 0)',
        ] as $key => $cond) {
            $q = clone $base;
            $q->select('COUNT(*)')->where($cond);
            $db->setQuery($q);
            $counts[$key] = (int) $db->loadResult();
        }

        // Aktive Personen ohne jede Mitgliedschaft
        $sqHas = $db->getQuery(true);
        $sqHas->select('DISTINCT ' . $db->quoteName('person_id'))
            ->from($db->quoteName('#__cluborganisation_memberships'));
        $qNo = $db->getQuery(true);
        $qNo->select('COUNT(*)')
            ->from($db->quoteName('#__cluborganisation_persons', 'p'))
            ->where($db->quoteName('p.active') . ' = 1')
            ->where($db->quoteName('p.id') . ' NOT IN (' . $sqHas . ')');
        $db->setQuery($qNo);
        $counts['noMembership'] = (int) $db->loadResult();

        // Aktive Personen ohne aktive Mitgliedschaft (kein end=NULL)
        $sqOpen = $db->getQuery(true);
        $sqOpen->select('DISTINCT ' . $db->quoteName('person_id'))
            ->from($db->quoteName('#__cluborganisation_memberships'))
            ->where($db->quoteName('end') . ' IS NULL');

        $qNoActive = $db->getQuery(true);
        $qNoActive->select('COUNT(*)')
            ->from($db->quoteName('#__cluborganisation_persons', 'p'))
            ->where($db->quoteName('p.active') . ' = 1')
            ->where($db->quoteName('p.id') . ' NOT IN (' . $sqOpen . ')');
        $db->setQuery($qNoActive);
        $counts['activeNoActive'] = (int) $db->loadResult();

        return $counts;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Erweiterungs-Status
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Prüft ob ein Plugin installiert und aktiviert ist.
     * Aktiviert = enabled = 1 in #__extensions.
     *
     * @param   string  $element  Element-Name (z. B. 'cluborganisation')
     * @param   string  $folder   Plugin-Folder (z. B. 'webservices')
     *
     * @return  array  ['installed' => bool, 'enabled' => bool]
     * @since   2.2.0
     */
    protected function getPluginStatus(string $element, string $folder): array
    {
        $db    = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select($db->quoteName('enabled'))
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
            ->where($db->quoteName('element') . ' = ' . $db->quote($element))
            ->where($db->quoteName('folder') . ' = ' . $db->quote($folder));
        $db->setQuery($query);
        $rows = $db->loadColumn();

        if (empty($rows)) {
            return ['installed' => false, 'enabled' => false];
        }

        // Aktiviert wenn mind. eine Instanz enabled = 1
        return [
            'installed' => true,
            'enabled'   => in_array('1', array_map('strval', $rows), true)
                        || in_array(1, $rows, true),
        ];
    }

    /**
     * Prüft ob ein Modul installiert und mind. eine Instanz veröffentlicht ist.
     * Installiert = Eintrag in #__extensions.
     * Aktiviert   = mind. eine Zeile in #__modules mit published = 1.
     *
     * @param   string  $module  Modul-Element (z. B. 'mod_cluborganisation_birthday')
     *
     * @return  array  ['installed' => bool, 'enabled' => bool]
     * @since   2.2.0
     */
    protected function getModuleStatus(string $module): array
    {
        $db = $this->getDbo();

        // Installiert?
        $qExt = $db->getQuery(true);
        $qExt->select('COUNT(*)')
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('type') . ' = ' . $db->quote('module'))
            ->where($db->quoteName('element') . ' = ' . $db->quote($module));
        $db->setQuery($qExt);
        $installed = ((int) $db->loadResult()) > 0;

        if (!$installed) {
            return ['installed' => false, 'enabled' => false];
        }

        // Mind. eine aktive Instanz in #__modules?
        $qMod = $db->getQuery(true);
        $qMod->select('COUNT(*)')
            ->from($db->quoteName('#__modules'))
            ->where($db->quoteName('module') . ' = ' . $db->quote($module))
            ->where($db->quoteName('published') . ' = 1');
        $db->setQuery($qMod);
        $enabled = ((int) $db->loadResult()) > 0;

        return ['installed' => true, 'enabled' => $enabled];
    }

    /**
     * Status aller ClubOrganisation-Erweiterungen.
     *
     * @return  array  [['name' => '...', 'installed' => bool, 'enabled' => bool, 'url' => '...'], ...]
     * @since   2.2.0
     */
    public function getExtensionsStatus(): array
    {
        $birthday    = $this->getModuleStatus('mod_cluborganisation_birthday');
        $webservices = $this->getPluginStatus('cluborganisation', 'webservices');
        $menuplugin  = $this->getPluginStatus('cluborganisationmenu', 'system');

        return [
            [
                'name'      => 'ClubOrganisation Birthday Module',
                'installed' => $birthday['installed'],
                'enabled'   => $birthday['enabled'],
                'type'      => 'module',
                'url'       => 'https://extensions.joomla.org/extension/cluborganisation-vereinverwaltung-birthday/',
            ],
            [
                'name'      => 'ClubOrganisation Webservices API Plugin',
                'installed' => $webservices['installed'],
                'enabled'   => $webservices['enabled'],
                'type'      => 'plugin',
                'url'       => 'https://extensions.joomla.org/extension/clients-a-communities/communities/cluborganisation-vereinverwaltung-webservices/',
            ],
            [
                'name'      => 'System - ClubOrganisation Menu',
                'installed' => $menuplugin['installed'],
                'enabled'   => $menuplugin['enabled'],
                'type'      => 'plugin',
                'url'       => 'https://github.com/csoscd/cluborganisation_plgmenu/archive/refs/tags/v2.0.0.zip',
            ],
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Version & Update-Status
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Gibt die aktuell installierte Version der Komponente zurück.
     *
     * @return  string
     * @since   2.2.0
     */
    public function getInstalledVersion(): string
    {
        $db    = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select($db->quoteName('manifest_cache'))
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('element') . ' = ' . $db->quote('com_cluborganisation'))
            ->where($db->quoteName('type') . ' = ' . $db->quote('component'));
        $db->setQuery($query);
        $cache = $db->loadResult();
        if ($cache) {
            $data = json_decode($cache, true);
            if (isset($data['version'])) {
                return $data['version'];
            }
        }
        // Fallback: aus ComponentHelper
        $params = ComponentHelper::getComponent('com_cluborganisation');
        return $params->version ?? '–';
    }

    /**
     * Prüft ob laut Joomla-Update-Manager ein Update für com_cluborganisation verfügbar ist.
     *
     * @return  string|null  Neue Versionsnummer oder null wenn kein Update verfügbar
     * @since   2.2.0
     */
    public function getAvailableUpdate(): ?string
    {
        $db    = $this->getDbo();
        // Tabelle #__updates enthält ausstehende Updates (nach "Nach Updates suchen")
        try {
            $query = $db->getQuery(true);
            $query->select($db->quoteName('version'))
                ->from($db->quoteName('#__updates'))
                ->where($db->quoteName('element') . ' = ' . $db->quote('com_cluborganisation'));
            $db->setQuery($query);
            $version = $db->loadResult();
            return $version ?: null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
