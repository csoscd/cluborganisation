<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @copyright   2026 Christian Schulz
 * @license     GNU General Public License version 2 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

/**
 * Encryption Helper für die Verschlüsselung von Bankdaten
 *
 * Verwendet AES-256-CBC Verschlüsselung für sensible Bankdaten
 *
 * @since  1.0.0
 */
class EncryptionHelper
{
    /**
     * Verschlüsselungsmethode
     *
     * @var    string
     * @since  1.0.0
     */
    private const ENCRYPTION_METHOD = 'AES-256-CBC';

    /**
     * Verschlüsselt einen String
     *
     * @param   string  $data  Die zu verschlüsselnden Daten
     * @param   string  $key   Der Verschlüsselungsschlüssel
     *
     * @return  string|false  Die verschlüsselten Daten oder false bei Fehler
     *
     * @since   1.0.0
     */
    public static function encrypt($data, $key)
    {
        if (empty($data) || empty($key)) {
            return false;
        }

        // Generiere einen zufälligen IV (Initialization Vector)
        $ivLength = openssl_cipher_iv_length(self::ENCRYPTION_METHOD);
        $iv = openssl_random_pseudo_bytes($ivLength);

        // Erstelle einen Hash des Keys für konsistente Länge
        $keyHash = hash('sha256', $key, true);

        // Verschlüssele die Daten
        $encrypted = openssl_encrypt(
            $data,
            self::ENCRYPTION_METHOD,
            $keyHash,
            0,
            $iv
        );

        if ($encrypted === false) {
            return false;
        }

        // Kombiniere IV und verschlüsselte Daten und kodiere als Base64
        return base64_encode($iv . $encrypted);
    }

    /**
     * Entschlüsselt einen String
     *
     * @param   string  $data  Die verschlüsselten Daten
     * @param   string  $key   Der Verschlüsselungsschlüssel
     *
     * @return  string|false  Die entschlüsselten Daten oder false bei Fehler
     *
     * @since   1.0.0
     */
    public static function decrypt($data, $key)
    {
        if (empty($data) || empty($key)) {
            return false;
        }

        // Dekodiere Base64
        $data = base64_decode($data);

        if ($data === false) {
            return false;
        }

        // Extrahiere IV
        $ivLength = openssl_cipher_iv_length(self::ENCRYPTION_METHOD);
        $iv = substr($data, 0, $ivLength);
        $encrypted = substr($data, $ivLength);

        // Erstelle einen Hash des Keys für konsistente Länge
        $keyHash = hash('sha256', $key, true);

        // Entschlüssele die Daten
        $decrypted = openssl_decrypt(
            $encrypted,
            self::ENCRYPTION_METHOD,
            $keyHash,
            0,
            $iv
        );

        return $decrypted;
    }

    /**
     * Validiert ob ein Encryption Key gesetzt ist
     *
     * @return  boolean  True wenn ein Key vorhanden ist
     *
     * @since   1.0.0
     */
    public static function hasEncryptionKey()
    {
        $session = Factory::getApplication()->getSession();
        return !empty($session->get('cluborganisation.encryption_key'));
    }

    /**
     * Setzt den Encryption Key in der Session
     *
     * @param   string  $key  Der Verschlüsselungsschlüssel
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public static function setEncryptionKey($key)
    {
        $session = Factory::getApplication()->getSession();
        $session->set('cluborganisation.encryption_key', $key);
    }

    /**
     * Gibt den Encryption Key aus der Session zurück
     *
     * @return  string|null  Der Verschlüsselungsschlüssel oder null
     *
     * @since   1.0.0
     */
    public static function getEncryptionKey()
    {
        $session = Factory::getApplication()->getSession();
        return $session->get('cluborganisation.encryption_key');
    }

    /**
     * Löscht den Encryption Key aus der Session
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public static function clearEncryptionKey()
    {
        $session = Factory::getApplication()->getSession();
        $session->clear('cluborganisation.encryption_key');
    }
}
