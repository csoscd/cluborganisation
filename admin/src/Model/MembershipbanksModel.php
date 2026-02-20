<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz
 * @license     GNU General Public License version 3 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use CSOSCD\Component\ClubOrganisation\Administrator\Helper\EncryptionHelper;

/**
 * Model für die Bankverbindungs-Liste
 *
 * @since  1.0.0
 */
class MembershipbanksModel extends ListModel
{
    /**
     * Erstellt die Datenbankabfrage für die Listenansicht.
     *
     * @return  \Joomla\Database\QueryInterface
     *
     * @since   1.0.0
     */
    protected function getListQuery()
    {
        $db    = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('a.*')
            ->from($db->quoteName('#__cluborganisation_membershipbanks', 'a'))
            ->join(
                'LEFT',
                $db->quoteName('#__cluborganisation_memberships', 'm') .
                ' ON ' . $db->quoteName('m.id') . ' = ' . $db->quoteName('a.membership_id')
            )
            ->join(
                'LEFT',
                $db->quoteName('#__cluborganisation_persons', 'p') .
                ' ON ' . $db->quoteName('p.id') . ' = ' . $db->quoteName('m.person_id')
            )
            ->select([
                $db->quoteName('m.begin', 'membership_begin'),
                $db->quoteName('m.end',   'membership_end'),
                $db->quoteName('p.lastname'),
                $db->quoteName('p.firstname'),
                $db->quoteName('p.member_no'),
            ])
            ->order($db->quoteName('a.id') . ' DESC');

        return $query;
    }

    /**
     * Prüft ob bereits Bankdatensätze vorhanden sind.
     *
     * @return  bool
     *
     * @since   1.9.0
     */
    public function hasRecords(): bool
    {
        $db    = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__cluborganisation_membershipbanks'));
        $db->setQuery($query);
        return (int) $db->loadResult() > 0;
    }

    /**
     * Validiert den Schlüssel über den Canary-Wert aus #__extensions.
     *
     * Ist noch kein Canary gespeichert (= kein erster Bankdatensatz vorhanden),
     * wird jeder Schlüssel akzeptiert – der Nutzer legt den initialen Schlüssel fest.
     * Nach dem Speichern des ersten Datensatzes wird der Canary gesetzt und
     * jede spätere Validierung ist deterministisch (encrypt/decrypt CANARY_VALUE).
     *
     * @param   string  $key  Zu prüfender Schlüssel
     *
     * @return  bool
     *
     * @since   1.9.0
     */
    public function verifyEncryptionKey(string $key): bool
    {
        return EncryptionHelper::verifyKey($key);
    }

    /**
     * Verschlüsselt alle Bankdatensätze mit einem neuen Schlüssel (Key Rotation).
     * Aktualisiert anschließend den Canary in #__extensions.
     *
     * @param   string  $oldKey  Aktuell gültiger Schlüssel
     * @param   string  $newKey  Neuer Schlüssel
     *
     * @return  int|false  Anzahl aktualisierter Datensätze oder false bei Fehler
     *
     * @since   1.9.0
     */
    public function reencryptAll(string $oldKey, string $newKey)
    {
        $db    = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select(['id', 'accountname', 'iban', 'bic'])
            ->from($db->quoteName('#__cluborganisation_membershipbanks'));
        $db->setQuery($query);
        $records = $db->loadObjectList();

        if (empty($records)) {
            // Auch bei leerer DB den Canary auf den neuen Schlüssel umstellen
            EncryptionHelper::saveCanary($newKey);
            return 0;
        }

        $count = 0;

        foreach ($records as $record) {
            $accountname = EncryptionHelper::decrypt($record->accountname, $oldKey);
            $iban        = EncryptionHelper::decrypt($record->iban, $oldKey);

            if ($accountname === false || $iban === false) {
                $this->setError('Decryption failed for record ID ' . (int) $record->id);
                return false;
            }

            $bic = null;
            if (!empty($record->bic)) {
                $bic = EncryptionHelper::decrypt($record->bic, $oldKey);
                if ($bic === false) {
                    $bic = $record->bic; // Fallback: Rohwert behalten
                }
            }

            $newAccountname = EncryptionHelper::encrypt($accountname, $newKey);
            $newIban        = EncryptionHelper::encrypt($iban, $newKey);

            if ($newAccountname === false || $newIban === false) {
                $this->setError('Encryption failed for record ID ' . (int) $record->id);
                return false;
            }

            $newBic = null;
            if ($bic !== null && $bic !== '') {
                $newBic = (strlen($bic) <= 11)
                    ? EncryptionHelper::encrypt($bic, $newKey)
                    : $bic; // War bereits Rohwert (Entschlüsselung schlug fehl)
            }

            $updateQuery = $db->getQuery(true)
                ->update($db->quoteName('#__cluborganisation_membershipbanks'))
                ->set([
                    $db->quoteName('accountname') . ' = ' . $db->quote($newAccountname),
                    $db->quoteName('iban')         . ' = ' . $db->quote($newIban),
                    $db->quoteName('bic')          . ' = ' . ($newBic !== null ? $db->quote($newBic) : 'NULL'),
                ])
                ->where($db->quoteName('id') . ' = ' . (int) $record->id);
            $db->setQuery($updateQuery);

            if (!$db->execute()) {
                $this->setError('Database update failed for record ID ' . (int) $record->id);
                return false;
            }

            $count++;
        }

        // Canary auf neuen Schlüssel aktualisieren
        if (!EncryptionHelper::saveCanary($newKey)) {
            $this->setError('Canary update failed after re-encryption');
            return false;
        }

        return $count;
    }
}
