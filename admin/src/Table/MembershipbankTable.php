<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz
 * @license     GNU General Public License version 3 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Administrator\Table;

defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\CMS\Factory;
use CSOSCD\Component\ClubOrganisation\Administrator\Helper\EncryptionHelper;

/**
 * MembershipBank Table mit Verschlüsselung und Datumsvalidierung
 *
 * Beim Speichern des allerersten Datensatzes wird der Canary-Wert in
 * #__extensions gesichert, damit nachfolgende Schlüsselvalidierungen
 * deterministisch funktionieren.
 *
 * @since  1.0.0
 */
class MembershipbankTable extends Table
{
    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__cluborganisation_membershipbanks', 'id', $db);
    }

    /**
     * Prüft Datenintegrität inkl. Datumsvalidierung gegen die Mitgliedschaft.
     *
     * @return  boolean
     *
     * @since   1.0.0
     */
    public function check()
    {
        if (empty($this->membership_id)) {
            $this->setError('COM_CLUBORGANISATION_ERROR_MEMBERSHIP_REQUIRED');
            return false;
        }

        if (empty($this->accountname)) {
            $this->setError('COM_CLUBORGANISATION_ERROR_ACCOUNTNAME_REQUIRED');
            return false;
        }

        if (empty($this->iban)) {
            $this->setError('COM_CLUBORGANISATION_ERROR_IBAN_REQUIRED');
            return false;
        }

        if (!$this->isEncrypted($this->iban) && !$this->validateIBAN($this->iban)) {
            $this->setError('COM_CLUBORGANISATION_ERROR_IBAN_INVALID');
            return false;
        }

        if (!empty($this->bic) && !$this->isEncrypted($this->bic) && !$this->validateBIC($this->bic)) {
            $this->setError('COM_CLUBORGANISATION_ERROR_BIC_INVALID');
            return false;
        }

        if (empty($this->begin)) {
            $this->setError('COM_CLUBORGANISATION_ERROR_BEGIN_REQUIRED');
            return false;
        }

        // ── Datumsvalidierung gegen die zugehörige Mitgliedschaft ─────────────
        $db    = $this->getDbo();
        $query = $db->getQuery(true)
            ->select([$db->quoteName('begin', 'mbegin'), $db->quoteName('end', 'mend')])
            ->from($db->quoteName('#__cluborganisation_memberships'))
            ->where($db->quoteName('id') . ' = ' . (int) $this->membership_id);
        $db->setQuery($query);
        $membership = $db->loadObject();

        if ($membership) {
            $bankBegin = $this->begin;
            $today     = date('Y-m-d');

            // Neue Bankverbindung: Mitgliedschaft darf nicht beendet sein
            if (!$this->id && !empty($membership->mend) && $membership->mend < $today) {
                $this->setError('COM_CLUBORGANISATION_ERROR_BANK_MEMBERSHIP_ENDED');
                return false;
            }

            // Beginn nicht vor Mitgliedschaftsbeginn
            if (!empty($membership->mbegin) && $bankBegin < $membership->mbegin) {
                $this->setError('COM_CLUBORGANISATION_ERROR_BANK_BEGIN_BEFORE_MEMBERSHIP');
                return false;
            }

            // Beginn nicht nach Mitgliedschaftsende
            if (!empty($membership->mend) && $bankBegin > $membership->mend) {
                $this->setError('COM_CLUBORGANISATION_ERROR_BANK_BEGIN_AFTER_MEMBERSHIP_END');
                return false;
            }
        }
        // ─────────────────────────────────────────────────────────────────────

        return parent::check();
    }

    private function validateIBAN($iban)
    {
        $iban = strtoupper(str_replace(' ', '', $iban));
        if (strlen($iban) < 15 || strlen($iban) > 34) {
            return false;
        }
        if (!preg_match('/^[A-Z]{2}[0-9]{2}[A-Z0-9]+$/', $iban)) {
            return false;
        }
        $checkString = substr($iban, 4) . substr($iban, 0, 4);
        $checkSum    = '';
        foreach (str_split($checkString) as $char) {
            $checkSum .= is_numeric($char) ? $char : (ord($char) - 55);
        }
        return bcmod($checkSum, '97') === '1';
    }

    private function validateBIC($bic)
    {
        $bic = strtoupper(str_replace(' ', '', $bic));
        return preg_match('/^[A-Z]{4}[A-Z]{2}[A-Z0-9]{2}([A-Z0-9]{3})?$/', $bic) === 1;
    }

    /**
     * Verschlüsselt sensible Felder vor dem Speichern.
     *
     * @param   array  $array   Daten
     * @param   mixed  $ignore  Ignorierte Felder
     *
     * @return  boolean
     *
     * @since   1.0.0
     */
    public function bind($array, $ignore = '')
    {
        $encryptionKey = EncryptionHelper::getEncryptionKey();

        if (!$encryptionKey) {
            $this->setError('COM_CLUBORGANISATION_ERROR_NO_ENCRYPTION_KEY');
            return false;
        }

        if (isset($array['accountname']) && !empty($array['accountname'])) {
            if (!$this->isEncrypted($array['accountname'])) {
                $array['accountname'] = EncryptionHelper::encrypt($array['accountname'], $encryptionKey);
            }
        }

        if (isset($array['iban']) && !empty($array['iban'])) {
            if (!$this->isEncrypted($array['iban'])) {
                $array['iban'] = EncryptionHelper::encrypt($array['iban'], $encryptionKey);
            }
        }

        if (isset($array['bic']) && !empty($array['bic'])) {
            if (!$this->isEncrypted($array['bic'])) {
                $array['bic'] = EncryptionHelper::encrypt($array['bic'], $encryptionKey);
            }
        }

        return parent::bind($array, $ignore);
    }

    /**
     * Entschlüsselt sensible Felder nach dem Laden.
     *
     * @param   mixed    $keys   Primärschlüssel
     * @param   boolean  $reset  Standardwerte zurücksetzen
     *
     * @return  boolean
     *
     * @since   1.0.0
     */
    public function load($keys = null, $reset = true)
    {
        $result = parent::load($keys, $reset);

        if ($result) {
            $encryptionKey = EncryptionHelper::getEncryptionKey();

            if (!$encryptionKey) {
                $this->accountname = '***';
                $this->iban        = '***';
                $this->bic         = '***';
            } else {
                $this->accountname = EncryptionHelper::decrypt($this->accountname, $encryptionKey);
                $this->iban        = EncryptionHelper::decrypt($this->iban, $encryptionKey);
                if (!empty($this->bic)) {
                    $this->bic = EncryptionHelper::decrypt($this->bic, $encryptionKey);
                }
            }
        }

        return $result;
    }

    /**
     * Speichert den Datensatz und setzt ggf. den Canary beim ersten Eintrag.
     *
     * Der Canary wird genau einmal angelegt: wenn noch kein Canary in
     * #__extensions vorhanden ist (= kein Bankdatensatz existierte bisher).
     * Bei späteren Speichervorgängen bleibt der Canary unverändert.
     *
     * @param   boolean  $updateNulls  Null-Werte ebenfalls speichern
     *
     * @return  boolean
     *
     * @since   1.0.0
     */
    public function store($updateNulls = false)
    {
        $date = Factory::getDate();
        $user = Factory::getApplication()->getIdentity();

        if (!$this->id) {
            $this->created    = $date->toSql();
            $this->created_by = $user->id;
        }

        $this->modified    = $date->toSql();
        $this->modified_by = $user->id;

        $result = parent::store($updateNulls);

        // Canary beim ersten Bankdatensatz setzen
        if ($result && EncryptionHelper::getStoredCanary() === null) {
            $key = EncryptionHelper::getEncryptionKey();
            if ($key) {
                EncryptionHelper::saveCanary($key);
            }
        }

        return $result;
    }

    /**
     * Prüft ob ein Wert bereits verschlüsselt (Base64-kodiert, ausreichend lang) ist.
     *
     * @param   string  $data  Zu prüfender Wert
     *
     * @return  boolean
     *
     * @since   1.0.0
     */
    private function isEncrypted($data)
    {
        if (empty($data) || strlen($data) <= 50) {
            return false;
        }
        $decoded = base64_decode($data, true);
        return $decoded !== false && base64_encode($decoded) === $data;
    }
}
