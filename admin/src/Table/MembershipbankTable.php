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
use CSOSCD\Component\ClubOrganisation\Administrator\Helper\EncryptionHelper;

/**
 * MembershipBank Table Klasse mit Verschlüsselung
 *
 * @since  1.0.0
 */
class MembershipbankTable extends Table
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
        parent::__construct('#__cluborganisation_membershipbanks', 'id', $db);
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

        // Validiere IBAN (nur wenn nicht verschlüsselt)
        if (!$this->isEncrypted($this->iban) && !$this->validateIBAN($this->iban)) {
            $this->setError('COM_CLUBORGANISATION_ERROR_IBAN_INVALID');
            return false;
        }

        // Validiere BIC wenn vorhanden (nur wenn nicht verschlüsselt)
        if (!empty($this->bic) && !$this->isEncrypted($this->bic) && !$this->validateBIC($this->bic)) {
            $this->setError('COM_CLUBORGANISATION_ERROR_BIC_INVALID');
            return false;
        }

        if (empty($this->begin)) {
            $this->setError('COM_CLUBORGANISATION_ERROR_BEGIN_REQUIRED');
            return false;
        }

        return parent::check();
    }

    /**
     * Validiert eine IBAN
     *
     * @param   string  $iban  Die zu validierende IBAN
     *
     * @return  boolean  True wenn gültig
     *
     * @since   1.0.0
     */
    private function validateIBAN($iban)
    {
        // Entferne Leerzeichen und wandle in Großbuchstaben um
        $iban = strtoupper(str_replace(' ', '', $iban));

        // Prüfe Länge (15-34 Zeichen für IBAN)
        if (strlen($iban) < 15 || strlen($iban) > 34) {
            return false;
        }

        // Prüfe Format: 2 Buchstaben, 2 Ziffern, dann Buchstaben/Ziffern
        if (!preg_match('/^[A-Z]{2}[0-9]{2}[A-Z0-9]+$/', $iban)) {
            return false;
        }

        // IBAN Prüfsummen-Validierung (Modulo 97)
        $checkString = substr($iban, 4) . substr($iban, 0, 4);
        $checkString = str_split($checkString);
        $checkSum = '';

        foreach ($checkString as $char) {
            if (is_numeric($char)) {
                $checkSum .= $char;
            } else {
                // A=10, B=11, ..., Z=35
                $checkSum .= (ord($char) - 55);
            }
        }

        // Modulo 97 muss 1 ergeben
        return bcmod($checkSum, '97') === '1';
    }

    /**
     * Validiert einen BIC
     *
     * @param   string  $bic  Der zu validierende BIC
     *
     * @return  boolean  True wenn gültig
     *
     * @since   1.0.0
     */
    private function validateBIC($bic)
    {
        // Entferne Leerzeichen und wandle in Großbuchstaben um
        $bic = strtoupper(str_replace(' ', '', $bic));

        // BIC hat 8 oder 11 Zeichen
        // Format: AAAA BB CC DDD
        // AAAA = Bankcode (4 Buchstaben)
        // BB = Ländercode (2 Buchstaben)
        // CC = Ortscode (2 Buchstaben/Ziffern)
        // DDD = Filialcode (3 Buchstaben/Ziffern, optional)
        return preg_match('/^[A-Z]{4}[A-Z]{2}[A-Z0-9]{2}([A-Z0-9]{3})?$/', $bic) === 1;
    }

    /**
     * Method to bind data
     * Verschlüsselt sensible Daten vor dem Speichern
     *
     * @param   array  $array   Named array
     * @param   mixed  $ignore  An optional array or space separated list of properties to ignore
     *
     * @return  boolean  True on success
     *
     * @since   1.0.0
     */
    public function bind($array, $ignore = '')
    {
        // Hole Encryption Key aus Session
        $encryptionKey = EncryptionHelper::getEncryptionKey();

        if (!$encryptionKey) {
            $this->setError('COM_CLUBORGANISATION_ERROR_NO_ENCRYPTION_KEY');
            return false;
        }

        // Verschlüssele sensible Daten
        // Wichtig: Prüfe ob Daten nicht leer sind und nicht bereits verschlüsselt
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
     * Method to load a row
     * Entschlüsselt sensible Daten nach dem Laden
     *
     * @param   mixed    $keys   An optional primary key value to load the row by
     * @param   boolean  $reset  True to reset the default values before loading the new row
     *
     * @return  boolean  True if successful, false otherwise
     *
     * @since   1.0.0
     */
    public function load($keys = null, $reset = true)
    {
        $result = parent::load($keys, $reset);

        if ($result) {
            $encryptionKey = EncryptionHelper::getEncryptionKey();

            if (!$encryptionKey) {
                // Wenn kein Key vorhanden, setze Platzhalter
                $this->accountname = '***';
                $this->iban = '***';
                $this->bic = '***';
            } else {
                // Entschlüssele die Daten
                $this->accountname = EncryptionHelper::decrypt($this->accountname, $encryptionKey);
                $this->iban = EncryptionHelper::decrypt($this->iban, $encryptionKey);
                
                if (!empty($this->bic)) {
                    $this->bic = EncryptionHelper::decrypt($this->bic, $encryptionKey);
                }
            }
        }

        return $result;
    }

    /**
     * Prüft ob ein String bereits verschlüsselt ist (Base64)
     *
     * @param   string  $data  Der zu prüfende String
     *
     * @return  boolean  True wenn verschlüsselt
     *
     * @since   1.0.0
     */
    /**
     * Prüft ob Daten bereits verschlüsselt sind
     *
     * @param   string  $data  Die zu prüfenden Daten
     *
     * @return  boolean  True wenn bereits verschlüsselt
     *
     * @since   1.0.0
     */
    private function isEncrypted($data)
    {
        if (empty($data)) {
            return false;
        }

        // Verschlüsselte Daten haben ein spezielles Präfix
        // oder sind deutlich länger als Klartextdaten
        // Eine IBAN ist max 34 Zeichen, verschlüsselt deutlich länger
        if (strlen($data) > 100) {
            return true;
        }

        // Prüfe ob es ein gültiges Base64 mit genug Länge ist
        $decoded = base64_decode($data, true);
        if ($decoded === false) {
            return false;
        }

        // Wenn der dekodierte String wieder Base64-kodiert identisch ist
        // UND deutlich länger als normale Bankdaten, ist es wahrscheinlich verschlüsselt
        return (base64_encode($decoded) === $data && strlen($data) > 50);
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
