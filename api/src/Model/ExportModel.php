<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Api
 * @author      Christian Schulz
 * @license     GNU General Public License version 3 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Api\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use CSOSCD\Component\ClubOrganisation\Administrator\Helper\EncryptionHelper;

/**
 * Model für den Mitglieder-Export über die REST-API.
 *
 * Einfache Klasse ohne Joomla-Basisklasse, um Probleme mit dem
 * MVC-Factory-Mechanismus im API-Kontext zu vermeiden.
 *
 * @since  2.0.0
 */
class ExportModel
{
    /** @var DatabaseInterface */
    private DatabaseInterface $db;

    public function __construct()
    {
        $this->db = Factory::getContainer()->get(DatabaseInterface::class);
    }

    /**
     * Gibt alle Mitglieder mit den angeforderten Daten zurück.
     *
     * @param   array  $options  Export-Optionen
     *
     * @return  array
     *
     * @throws  \RuntimeException  Bei fehlendem oder falschem Schlüssel
     *
     * @since   2.0.0
     */
    public function getMembers(array $options): array
    {
        $activeMemberships = (bool) ($options['active_memberships'] ?? true);
        $includeBanks      = (bool) ($options['include_banks'] ?? false);
        $activeBanks       = (bool) ($options['active_banks'] ?? true);
        $encryptionKey     = trim((string) ($options['encryption_key'] ?? ''));

        if ($includeBanks) {
            if (empty($encryptionKey)) {
                throw new \RuntimeException('encryption_key is required when include_banks=1', 401);
            }
            if (!EncryptionHelper::verifyKey($encryptionKey)) {
                throw new \RuntimeException('Invalid encryption_key', 401);
            }
        }

        $db    = $this->db;
        $today = date('Y-m-d');

        // ── Personen ────────────────────────────────────────────────────────
        $q = $db->getQuery(true)
            ->select([
                'p.id', 'p.member_no', 'p.firstname', 'p.middlename', 'p.lastname',
                'p.birthname', 'p.address', 'p.zip', 'p.city', 'p.country',
                'p.telephone', 'p.mobile', 'p.email', 'p.birthday', 'p.deceased',
                'p.active', 'p.image', 'p.user_id',
                's.title AS salutation',
            ])
            ->from($db->quoteName('#__cluborganisation_persons', 'p'))
            ->join('LEFT', $db->quoteName('#__cluborganisation_salutations', 's') . ' ON s.id = p.salutation')
            ->order('p.lastname ASC, p.firstname ASC');

        $db->setQuery($q);
        $persons = $db->loadObjectList('id');

        if (empty($persons)) {
            return [];
        }

        $personIds = array_keys($persons);

        foreach ($persons as $p) {
            $p->memberships = [];
        }

        // ── Mitgliedschaften ────────────────────────────────────────────────
        $q2 = $db->getQuery(true)
            ->select(['m.id', 'm.person_id', 'm.begin', 'm.end', 'm.comment', 't.title AS type_title'])
            ->from($db->quoteName('#__cluborganisation_memberships', 'm'))
            ->join('LEFT', $db->quoteName('#__cluborganisation_membershiptypes', 't') . ' ON t.id = m.type')
            ->where('m.person_id IN (' . implode(',', array_map('intval', $personIds)) . ')');

        if ($activeMemberships) {
            $q2->where('m.begin <= ' . $db->quote($today))
               ->where('(m.end IS NULL OR m.end >= ' . $db->quote($today) . ')');
        }

        $q2->order('m.begin ASC');
        $db->setQuery($q2);
        $memberships = $db->loadObjectList();

        $membershipIds = [];
        foreach ($memberships as $m) {
            $m->banks = [];
            $persons[$m->person_id]->memberships[] = $m;
            $membershipIds[] = (int) $m->id;
        }

        // ── Bankdaten ────────────────────────────────────────────────────────
        if ($includeBanks && !empty($membershipIds)) {
            $q3 = $db->getQuery(true)
                ->select(['b.id', 'b.membership_id', 'b.accountname', 'b.iban', 'b.bic', 'b.begin'])
                ->from($db->quoteName('#__cluborganisation_membershipbanks', 'b'))
                ->where('b.membership_id IN (' . implode(',', $membershipIds) . ')');

            if ($activeBanks) {
                $q3->where('b.begin <= ' . $db->quote($today));
            }

            $q3->order('b.begin ASC');
            $db->setQuery($q3);
            $banks = $db->loadObjectList();

            // Bankdaten entschlüsseln und Mitgliedschaften zuordnen
            $banksByMembership = [];
            foreach ($banks as $b) {
                $accountname = EncryptionHelper::decrypt($b->accountname, $encryptionKey);
                $iban        = EncryptionHelper::decrypt($b->iban, $encryptionKey);
                $bic         = !empty($b->bic) ? EncryptionHelper::decrypt($b->bic, $encryptionKey) : null;

                $banksByMembership[$b->membership_id][] = [
                    'id'          => (int) $b->id,
                    'accountname' => $accountname !== false ? $accountname : null,
                    'iban'        => $iban !== false ? $iban : null,
                    'bic'         => ($bic !== false && $bic !== null) ? $bic : null,
                    'begin'       => $b->begin,
                ];
            }

            foreach ($memberships as $m) {
                $m->banks = $banksByMembership[$m->id] ?? [];
            }
        }

        // ── Ausgabe aufbereiten ──────────────────────────────────────────────
        $result = [];

        foreach ($persons as $person) {
            // Bei aktivem Mitgliedschaftsfilter: Personen ohne passende
            // Mitgliedschaft nicht in die Ausgabe aufnehmen.
            if ($activeMemberships && empty($person->memberships)) {
                continue;
            }

            $membershipList = [];

            foreach ($person->memberships as $m) {
                $entry = [
                    'id'      => (int) $m->id,
                    'type'    => $m->type_title,
                    'begin'   => $m->begin,
                    'end'     => $m->end,
                    'comment' => $m->comment,
                ];

                if ($includeBanks) {
                    $entry['banks'] = $m->banks;
                }

                $membershipList[] = $entry;
            }

            $result[] = [
                'id'          => (int) $person->id,
                'member_no'   => $person->member_no,
                'salutation'  => $person->salutation,
                'firstname'   => $person->firstname,
                'middlename'  => $person->middlename,
                'lastname'    => $person->lastname,
                'birthname'   => $person->birthname,
                'address'     => $person->address,
                'zip'         => $person->zip,
                'city'        => $person->city,
                'country'     => $person->country,
                'telephone'   => $person->telephone,
                'mobile'      => $person->mobile,
                'email'       => $person->email,
                'birthday'    => $person->birthday,
                'deceased'    => $person->deceased,
                'active'      => (bool) $person->active,
                'memberships' => $membershipList,
            ];
        }

        return $result;
    }
}
