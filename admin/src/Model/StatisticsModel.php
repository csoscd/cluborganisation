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
 * Statistics Model
 *
 * @since  2.1.0
 */
class StatisticsModel extends BaseDatabaseModel
{
    // ─────────────────────────────────────────────────────────────────────────
    // Hilfsmethoden
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Anzahl der aktiven Mitglieder am Stichtag (eindeutige Personen).
     *
     * @param   string  $date  Stichtag im Format Y-m-d
     *
     * @return  int
     *
     * @since   2.1.0
     */
    protected function countMembersAtDate(string $date): int
    {
        $db    = $this->getDbo();
        $query = $db->getQuery(true);

        // Kein active-Filter: historische Zählung basiert ausschließlich auf
        // Mitgliedschaftsdaten (begin/end). Deaktivierte Personen bleiben in
        // Langzeit-Statistiken (Monats-/Jahresentwicklung) korrekt enthalten.
        $query->select('COUNT(DISTINCT ' . $db->quoteName('person_id') . ')')
            ->from($db->quoteName('#__cluborganisation_memberships'))
            ->where($db->quoteName('begin') . ' <= ' . $db->quote($date))
            ->where(
                '(' . $db->quoteName('end') . ' IS NULL'
                . ' OR ' . $db->quoteName('end') . ' >= ' . $db->quote($date) . ')'
            );

        $db->setQuery($query);

        return (int) $db->loadResult();
    }

    /**
     * Letzter Tag eines Monats als Y-m-d.
     *
     * @param   int  $year   Jahr
     * @param   int  $month  Monat (1–12)
     *
     * @return  string
     *
     * @since   2.1.0
     */
    protected function lastDayOfMonth(int $year, int $month): string
    {
        return date('Y-m-d', mktime(0, 0, 0, $month + 1, 0, $year));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Öffentliche Methoden
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Monatliche Mitgliederentwicklung für ein Jahr.
     * Stichtag je Monat: letzter Kalendertag.
     * Zukünftige Monate werden als null zurückgegeben.
     *
     * @param   int  $year  Jahr
     *
     * @return  array  [['label' => 'Jan', 'date' => '2025-01-31', 'count' => 42], ...]
     *
     * @since   2.1.0
     */
    public function getMemberDevelopmentForYear(int $year): array
    {
        $today   = date('Y-m-d');
        $results = [];

        for ($m = 1; $m <= 12; $m++) {
            $refDate = $this->lastDayOfMonth($year, $m);
            $results[] = [
                'label' => date('M', mktime(0, 0, 0, $m, 1, $year)),
                'date'  => $refDate,
                'count' => ($refDate > $today) ? null : $this->countMembersAtDate($refDate),
            ];
        }

        return $results;
    }

    /**
     * Jährliche Mitgliederentwicklung seit $startYear bis heute.
     * Stichtag: 31.12. des jeweiligen Jahres (laufendes Jahr: heute).
     * Mitgliedschaften, die am 31.12. enden, zählen noch mit.
     *
     * @param   int  $startYear  Erstes Jahr
     *
     * @return  array  [['year' => 2020, 'count' => 123], ...]
     *
     * @since   2.1.0
     */
    public function getMemberDevelopmentByYear(int $startYear): array
    {
        $currentYear = (int) date('Y');
        $today       = date('Y-m-d');
        $results     = [];

        for ($y = $startYear; $y <= $currentYear; $y++) {
            $refDate   = ($y < $currentYear) ? $y . '-12-31' : $today;
            $results[] = [
                'year'  => $y,
                'count' => $this->countMembersAtDate($refDate),
            ];
        }

        return $results;
    }

    /**
     * Mitgliederstruktur nach Mitgliedschaftsart.
     * Stichtag aktuelles Jahr: heute. Stichtag Vorjahr: 31.12.
     *
     * @return  array  [['type_id' => 1, 'type_title' => '...', 'current' => 40, 'prev' => 38], ...]
     *
     * @since   2.1.0
     */
    public function getMemberStructure(): array
    {
        $db          = $this->getDbo();
        $currentYear = (int) date('Y');
        $refCurrent  = date('Y-m-d');
        $refPrev     = ($currentYear - 1) . '-12-31';

        $query = $db->getQuery(true);
        $query->select('id, title')
            ->from($db->quoteName('#__cluborganisation_membershiptypes'))
            ->where($db->quoteName('published') . ' = 1')
            ->order($db->quoteName('title') . ' ASC');
        $db->setQuery($query);
        $types = $db->loadObjectList();

        $results = [];
        foreach ($types as $type) {
            $results[] = [
                'type_id'    => (int) $type->id,
                'type_title' => $type->title,
                'current'    => $this->countMembersByTypeAtDate((int) $type->id, $refCurrent),
                'prev'       => $this->countMembersByTypeAtDate((int) $type->id, $refPrev),
            ];
        }

        return $results;
    }

    /**
     * Mitglieder eines Typs zum Stichtag.
     *
     * @param   int     $typeId  Mitgliedschaftstyp-ID
     * @param   string  $date    Stichtag Y-m-d
     *
     * @return  int
     *
     * @since   2.1.0
     */
    protected function countMembersByTypeAtDate(int $typeId, string $date): int
    {
        $db    = $this->getDbo();
        $query = $db->getQuery(true);

        // Kein active-Filter: historische Zählung basiert auf Mitgliedschaftsdaten.
        $query->select('COUNT(DISTINCT ' . $db->quoteName('person_id') . ')')
            ->from($db->quoteName('#__cluborganisation_memberships'))
            ->where($db->quoteName('type') . ' = ' . $typeId)
            ->where($db->quoteName('begin') . ' <= ' . $db->quote($date))
            ->where(
                '(' . $db->quoteName('end') . ' IS NULL'
                . ' OR ' . $db->quoteName('end') . ' >= ' . $db->quote($date) . ')'
            );

        $db->setQuery($query);

        return (int) $db->loadResult();
    }

    /**
     * Altersstruktur der aktiven Mitglieder.
     * Gruppen: <18 | 18–29 | 30–49 | 50–65 | >65.
     * Stichtage: heute / 31.12. Vorjahr.
     *
     * @return  array  [['label' => '...', 'current' => n, 'prev' => n], ...]
     *
     * @since   2.1.0
     */
    public function getAgeStructure(): array
    {
        $currentYear = (int) date('Y');
        $today       = date('Y-m-d');
        $refPrev     = ($currentYear - 1) . '-12-31';

        $groups = [
            ['label' => 'COM_CLUBORGANISATION_STATS_AGE_U18',  'minAge' => null, 'maxAge' => 17],
            ['label' => 'COM_CLUBORGANISATION_STATS_AGE_18_29','minAge' => 18,   'maxAge' => 29],
            ['label' => 'COM_CLUBORGANISATION_STATS_AGE_30_49','minAge' => 30,   'maxAge' => 49],
            ['label' => 'COM_CLUBORGANISATION_STATS_AGE_50_65','minAge' => 50,   'maxAge' => 65],
            ['label' => 'COM_CLUBORGANISATION_STATS_AGE_O65',  'minAge' => 66,   'maxAge' => null],
        ];

        $results = [];
        foreach ($groups as $group) {
            $results[] = [
                'label'   => $group['label'],
                'current' => $this->countMembersByAgeAtDate($group['minAge'], $group['maxAge'], $today),
                'prev'    => $this->countMembersByAgeAtDate($group['minAge'], $group['maxAge'], $refPrev),
            ];
        }

        return $results;
    }

    /**
     * Aktive Mitglieder in einer Altersgruppe zum Stichtag.
     *
     * @param   int|null  $minAge   Mindestalter (inklusiv), null = kein Minimum
     * @param   int|null  $maxAge   Höchstalter (inklusiv), null = kein Maximum
     * @param   string    $refDate  Stichtag Y-m-d
     *
     * @return  int
     *
     * @since   2.1.0
     */
    protected function countMembersByAgeAtDate(?int $minAge, ?int $maxAge, string $refDate): int
    {
        $db    = $this->getDbo();
        $query = $db->getQuery(true);

        $query->select('COUNT(DISTINCT ' . $db->quoteName('m.person_id') . ')')
            ->from($db->quoteName('#__cluborganisation_memberships', 'm'))
            ->join(
                'INNER',
                $db->quoteName('#__cluborganisation_persons', 'p')
                . ' ON ' . $db->quoteName('p.id') . ' = ' . $db->quoteName('m.person_id')
            )
            ->where($db->quoteName('m.begin') . ' <= ' . $db->quote($refDate))
            ->where(
                '(' . $db->quoteName('m.end') . ' IS NULL'
                . ' OR ' . $db->quoteName('m.end') . ' >= ' . $db->quote($refDate) . ')'
            )
            // Kein active-Filter: Altersstruktur basiert auf Mitgliedschaftsdaten.
            ->where($db->quoteName('p.birthday') . ' IS NOT NULL');

        // Altersberechnung: Geburtstag muss im gültigen Bereich liegen.
        // maxAge → Geburtstag >= (refDate minus (maxAge+1) Jahre + 1 Tag)
        // minAge → Geburtstag <= (refDate minus minAge Jahre)
        if ($maxAge !== null) {
            $minBirthday = date('Y-m-d', strtotime($refDate . ' -' . ($maxAge + 1) . ' years +1 day'));
            $query->where($db->quoteName('p.birthday') . ' >= ' . $db->quote($minBirthday));
        }
        if ($minAge !== null) {
            $maxBirthday = date('Y-m-d', strtotime($refDate . ' -' . $minAge . ' years'));
            $query->where($db->quoteName('p.birthday') . ' <= ' . $db->quote($maxBirthday));
        }

        $db->setQuery($query);

        return (int) $db->loadResult();
    }

    /**
     * Durchschnittsalter aller aktiven Mitglieder zum Stichtag.
     * Nur Personen mit hinterlegtem Geburtsdatum werden berücksichtigt.
     *
     * @param   string  $refDate  Stichtag Y-m-d
     *
     * @return  float|null  Durchschnittsalter oder null wenn keine Daten
     *
     * @since   2.1.0
     */
    public function getAverageAgeAtDate(string $refDate): ?float
    {
        $db = $this->getDbo();

        // Subquery: eindeutige Personen-IDs mit aktiver Mitgliedschaft am Stichtag
        $subQuery = $db->getQuery(true);
        $subQuery->select('DISTINCT ' . $db->quoteName('m2.person_id'))
            ->from($db->quoteName('#__cluborganisation_memberships', 'm2'))
            ->where($db->quoteName('m2.begin') . ' <= ' . $db->quote($refDate))
            ->where(
                '(' . $db->quoteName('m2.end') . ' IS NULL'
                . ' OR ' . $db->quoteName('m2.end') . ' >= ' . $db->quote($refDate) . ')'
            );

        // Hauptquery: AVG(TIMESTAMPDIFF) über alle berechtigten Personen
        $query = $db->getQuery(true);
        $query->select(
            'AVG(TIMESTAMPDIFF(YEAR, ' . $db->quoteName('p.birthday') . ', ' . $db->quote($refDate) . '))'
        )
            ->from($db->quoteName('#__cluborganisation_persons', 'p'))
            // Kein active-Filter: Durchschnittsalter basiert auf Mitgliedschaftsdaten.
            ->where($db->quoteName('p.birthday') . ' IS NOT NULL')
            ->where($db->quoteName('p.id') . ' IN (' . $subQuery . ')');

        $db->setQuery($query);
        $result = $db->loadResult();

        return ($result !== null) ? round((float) $result, 1) : null;
    }

    /**
     * Mitgliedschaftsdauer-Struktur.
     *
     * Berechnung: YEAR(Stichtag) - YEAR(frühestes begin einer Person)
     * Gruppen:
     *   - ≤ 1 Jahr   : Jahresdiff 0 oder 1
     *   - 1 – 5 Jahre: Jahresdiff 2 – 5
     *   - 6 – 10 J.  : Jahresdiff 6 – 10
     *   - 11 – 15 J. : Jahresdiff 11 – 15
     *   - 16 – 20 J. : Jahresdiff 16 – 20
     *   - > 20 J.    : Jahresdiff > 20
     *
     * Stichtage: heute / 31.12. Vorjahr.
     *
     * @return  array  [['label' => '...', 'current' => n, 'prev' => n], ...]
     *
     * @since   2.1.0
     */
    /**
     * Mitgliedschaftsdauer-Struktur.
     *
     * Regeln:
     *  1. Nur Personen mit mindestens einer Mitgliedschaft mit end = NULL.
     *  2. Frühestes begin über ALLE Mitgliedschaften der Person (nicht nur aktive).
     *  3. Dauer = YEAR(Stichtag) - YEAR(frühestes begin).
     *
     * Gruppen (Jahresdifferenz):
     *   0–1  | 2–5  | 6–10  | 11–15  | 16–20  | > 20
     *
     * @return  array  [['label' => '...', 'current' => n, 'prev' => n], ...]
     *
     * @since   2.1.0
     */
    public function getMembershipDuration(): array
    {
        $currentYear = (int) date('Y');
        $today       = date('Y-m-d');
        $refPrev     = ($currentYear - 1) . '-12-31';

        $groups = [
            ['label' => 'COM_CLUBORGANISATION_STATS_DUR_1',     'min' => null, 'max' => 1],
            ['label' => 'COM_CLUBORGANISATION_STATS_DUR_1_5',   'min' => 2,    'max' => 5],
            ['label' => 'COM_CLUBORGANISATION_STATS_DUR_6_10',  'min' => 6,    'max' => 10],
            ['label' => 'COM_CLUBORGANISATION_STATS_DUR_11_15', 'min' => 11,   'max' => 15],
            ['label' => 'COM_CLUBORGANISATION_STATS_DUR_16_20', 'min' => 16,   'max' => 20],
            ['label' => 'COM_CLUBORGANISATION_STATS_DUR_O20',   'min' => 21,   'max' => null],
        ];

        // Alle aktiven Personen mit ihren Jahresdifferenzen laden
        $currentData = $this->loadDurationData($today);
        $prevData    = $this->loadDurationData($refPrev);

        $results = [];
        foreach ($groups as $group) {
            $results[] = [
                'label'   => $group['label'],
                'current' => $this->countInRange($currentData, $group['min'], $group['max']),
                'prev'    => $this->countInRange($prevData,    $group['min'], $group['max']),
            ];
        }

        return $results;
    }

    /**
     * Lädt für alle aktiven Personen (mind. eine end=NULL-Mitgliedschaft)
     * die Jahresdifferenz: YEAR(refDate) - YEAR(frühestes begin aller Mitgliedschaften).
     *
     * @param   string  $refDate  Stichtag Y-m-d
     *
     * @return  int[]  Array von Jahresdifferenzen (eine pro Person)
     *
     * @since   2.1.0
     */
    protected function loadDurationData(string $refDate): array
    {
        $db      = $this->getDbo();
        $refYear = (int) date('Y', strtotime($refDate));

        // Subquery: Personen-IDs mit mindestens einer end=NULL-Mitgliedschaft
        $sqActive = $db->getQuery(true);
        $sqActive->select('DISTINCT ' . $db->quoteName('person_id'))
            ->from($db->quoteName('#__cluborganisation_memberships'))
            ->where($db->quoteName('end') . ' IS NULL');

        // Hauptquery: frühestes begin über ALLE Mitgliedschaften der Person
        $query = $db->getQuery(true);
        $query->select('YEAR(MIN(' . $db->quoteName('begin') . ')) AS entry_year')
            ->from($db->quoteName('#__cluborganisation_memberships'))
            ->where($db->quoteName('person_id') . ' IN (' . $sqActive . ')')
            ->group($db->quoteName('person_id'));

        $db->setQuery($query);
        $rows = $db->loadAssocList();

        $diffs = [];
        foreach ($rows as $row) {
            $diffs[] = $refYear - (int) $row['entry_year'];
        }

        return $diffs;
    }

    /**
     * Zählt Werte in einem Array, die in den angegebenen Bereich fallen.
     *
     * @param   int[]     $data  Array von Ganzzahlen
     * @param   int|null  $min   Untergrenze inklusiv (null = kein Limit)
     * @param   int|null  $max   Obergrenze inklusiv (null = kein Limit)
     *
     * @return  int
     *
     * @since   2.1.0
     */
    protected function countInRange(array $data, ?int $min, ?int $max): int
    {
        $count = 0;
        foreach ($data as $v) {
            if ($min !== null && $v < $min) {
                continue;
            }
            if ($max !== null && $v > $max) {
                continue;
            }
            $count++;
        }
        return $count;
    }

    /**
     * Durchschnittliche Mitgliedschaftsdauer aller aktiven Personen zum Stichtag.
     *
     * @param   string  $refDate  Stichtag Y-m-d
     *
     * @return  float|null
     *
     * @since   2.1.0
     */
    public function getAverageDurationAtDate(string $refDate): ?float
    {
        $data = $this->loadDurationData($refDate);
        if (empty($data)) {
            return null;
        }
        return round(array_sum($data) / count($data), 1);
    }

    public function getStatisticsStartYear(): int
    {
        $params    = ComponentHelper::getParams('com_cluborganisation');
        $startYear = (int) $params->get('statistics_start_year', 0);

        if ($startYear < 1900 || $startYear > (int) date('Y')) {
            $startYear = (int) date('Y') - 5;
        }

        return $startYear;
    }

    // =========================================================================
    // Fluktuation: Eintritte und Austritte pro Jahr
    // =========================================================================

    /**
     * Eintritte pro Jahr: Personen, deren MIN(begin) über alle Mitgliedschaften
     * in das jeweilige Jahr fällt.
     *
     * @param   int  $startYear  Erstes auszuwertendes Jahr
     *
     * @return  array  [['year' => 2020, 'count' => 4], ...]
     *
     * @since   2.1.0
     */
    public function getMemberJoinsPerYear(int $startYear): array
    {
        $db = $this->getDbo();

        // Subquery: frühestes begin je Person
        $sqEarliest = $db->getQuery(true);
        $sqEarliest->select($db->quoteName('person_id'))
            ->select('MIN(' . $db->quoteName('begin') . ') AS earliest_begin')
            ->from($db->quoteName('#__cluborganisation_memberships'))
            ->group($db->quoteName('person_id'));

        // Äußere Query: nach Jahr gruppieren – GROUP BY auf Spaltenausdruck, kein Alias
        $query = $db->getQuery(true);
        $query->select('YEAR(sub.earliest_begin) AS entry_year')
            ->select('COUNT(*) AS cnt')
            ->from('(' . $sqEarliest . ') AS sub')
            ->where('YEAR(sub.earliest_begin) >= ' . (int) $startYear)
            ->group('YEAR(sub.earliest_begin)')
            ->order('entry_year ASC');

        $db->setQuery($query);
        $rows = $db->loadAssocList('entry_year');

        $currentYear = (int) date('Y');
        $results     = [];
        for ($y = $startYear; $y <= $currentYear; $y++) {
            $results[] = [
                'year'  => $y,
                'count' => isset($rows[$y]) ? (int) $rows[$y]['cnt'] : 0,
            ];
        }

        return $results;
    }

    /**
     * Austritte pro Jahr: Personen ohne jede end=NULL-Mitgliedschaft,
     * deren MAX(end) in das jeweilige Jahr fällt.
     *
     * @param   int  $startYear  Erstes auszuwertendes Jahr
     *
     * @return  array  [['year' => 2020, 'count' => 2], ...]
     *
     * @since   2.1.0
     */
    public function getMemberLeavesPerYear(int $startYear): array
    {
        $db = $this->getDbo();

        // Subquery 1: Personen mit mind. einer end=NULL → noch aktiv, kein Austritt
        $sqActive = $db->getQuery(true);
        $sqActive->select('DISTINCT ' . $db->quoteName('person_id'))
            ->from($db->quoteName('#__cluborganisation_memberships'))
            ->where($db->quoteName('end') . ' IS NULL');

        // Subquery 2: MAX(end) je ausgetretener Person – Aggregat nur hier
        $sqMax = $db->getQuery(true);
        $sqMax->select($db->quoteName('person_id'))
            ->select('MAX(' . $db->quoteName('end') . ') AS last_end')
            ->from($db->quoteName('#__cluborganisation_memberships'))
            ->where($db->quoteName('person_id') . ' NOT IN (' . $sqActive . ')')
            ->group($db->quoteName('person_id'));

        // Äußere Query: nach YEAR(last_end) gruppieren – kein Aggregat im GROUP BY
        $query = $db->getQuery(true);
        $query->select('YEAR(sub.last_end) AS leave_year')
            ->select('COUNT(*) AS cnt')
            ->from('(' . $sqMax . ') AS sub')
            ->where('YEAR(sub.last_end) >= ' . (int) $startYear)
            ->group('YEAR(sub.last_end)')
            ->order('leave_year ASC');

        $db->setQuery($query);
        $rows = $db->loadAssocList('leave_year');

        $currentYear = (int) date('Y');
        $results     = [];
        for ($y = $startYear; $y <= $currentYear; $y++) {
            $results[] = [
                'year'  => $y,
                'count' => isset($rows[$y]) ? (int) $rows[$y]['cnt'] : 0,
            ];
        }

        return $results;
    }

    // =========================================================================
    // Jubiläen
    // =========================================================================

    /**
     * Liste der Personen mit einem Jubiläum in einem bestimmten Jahr.
     *
     * Jubiläum = aktuelles Jahr - YEAR(MIN(begin aller Mitgliedschaften)) ist in den Meilensteinen.
     *
     * Für $checkNextYear = true wird zusätzlich geprüft, ob die Person im
     * nächsten Jahr noch eine aktive Mitgliedschaft hat (end IS NULL oder end >= 1.1. des Jahres).
     *
     * @param   int    $forYear       Jahr, für das Jubiläen ermittelt werden
     * @param   bool   $mustBeActive  Wenn true: Person muss im $forYear noch aktiv sein
     *
     * @return  array  [['person_id' => 1, 'firstname' => '...', 'lastname' => '...', 'entry_year' => 2016, 'years' => 10], ...]
     *
     * @since   2.1.0
     */
    public function getMemberAnniversaries(int $forYear, bool $mustBeActive = true): array
    {
        $milestones = [5, 10, 20, 25, 40];
        $db         = $this->getDbo();

        // Frühestes begin je Person
        $sqEarliest = $db->getQuery(true);
        $sqEarliest->select($db->quoteName('person_id'))
            ->select('YEAR(MIN(' . $db->quoteName('begin') . ')) AS entry_year')
            ->from($db->quoteName('#__cluborganisation_memberships'))
            ->group($db->quoteName('person_id'));

        // Filter: nur Jubiläumsjahre (entry_year muss Differenz zu $forYear in Milestones ergeben)
        $yearConditions = [];
        foreach ($milestones as $m) {
            $yearConditions[] = 'entry_year = ' . ($forYear - $m);
        }

        // Wenn mustBeActive: Person muss im forYear eine laufende Mitgliedschaft haben
        $sqPersonIds = $db->getQuery(true);
        $sqPersonIds->select('sub.person_id, sub.entry_year')
            ->from('(' . $sqEarliest . ') AS sub')
            ->where('(' . implode(' OR ', $yearConditions) . ')');

        if ($mustBeActive) {
            $sqOpen = $db->getQuery(true);
            $sqOpen->select('DISTINCT ' . $db->quoteName('person_id'))
                ->from($db->quoteName('#__cluborganisation_memberships'))
                ->where(
                    '(' . $db->quoteName('end') . ' IS NULL'
                    . ' OR ' . $db->quoteName('end') . ' >= ' . $db->quote($forYear . '-01-01') . ')'
                )
                ->where($db->quoteName('begin') . ' <= ' . $db->quote($forYear . '-12-31'));

            $sqPersonIds->where('sub.person_id IN (' . $sqOpen . ')');
        }

        // Hauptquery mit Personendaten
        $query = $db->getQuery(true);
        $query->select(
            'p.' . $db->quoteName('id') . ', '
            . 'p.' . $db->quoteName('firstname') . ', '
            . 'p.' . $db->quoteName('lastname') . ', '
            . 'ann.entry_year'
        )
            ->from('(' . $sqPersonIds . ') AS ann')
            ->join(
                'INNER',
                $db->quoteName('#__cluborganisation_persons', 'p')
                . ' ON p.' . $db->quoteName('id') . ' = ann.person_id'
            )
            ->where('p.' . $db->quoteName('active') . ' = 1')
            ->order('ann.entry_year ASC, p.' . $db->quoteName('lastname') . ' ASC, p.' . $db->quoteName('firstname') . ' ASC');

        $db->setQuery($query);
        $rows = $db->loadAssocList();

        $results = [];
        foreach ($rows as $row) {
            $results[] = [
                'person_id'  => (int) $row['id'],
                'firstname'  => $row['firstname'],
                'lastname'   => $row['lastname'],
                'entry_year' => (int) $row['entry_year'],
                'years'      => $forYear - (int) $row['entry_year'],
            ];
        }

        return $results;
    }

}
