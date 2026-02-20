<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz
 * @license     GNU General Public License version 3 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;

/**
 * Encryption Helper für die Verschlüsselung von Bankdaten
 *
 * Verwendet AES-256-CBC Verschlüsselung für sensible Bankdaten.
 *
 * Schlüsselvalidierung über Canary-Wert:
 * Ein bekannter fixer String (CANARY_VALUE) wird beim Speichern des ersten
 * Bankdatensatzes mit dem aktiven Schlüssel verschlüsselt und als
 * Komponenten-Parameter in #__extensions gesichert. Bei der Validierung
 * wird der Canary entschlüsselt und mit dem Original verglichen – damit ist
 * die Prüfung 100 % deterministisch, keine Heuristik erforderlich.
 *
 * @since  1.0.0
 */
class EncryptionHelper
{
    /** @var string  AES-Verschlüsselungsmethode */
    private const ENCRYPTION_METHOD = 'AES-256-CBC';

    /** @var string  Bekannter Prüfstring für den Canary-Mechanismus */
    private const CANARY_VALUE = 'CLUBORG_KEY_CHECK_v1';

    /** @var string  Parameter-Name in #__extensions */
    private const CANARY_PARAM = 'encryption_canary';

    // ─────────────────────────────────────────────────────────────────────────
    // Verschlüsselung / Entschlüsselung
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Verschlüsselt einen String mit AES-256-CBC.
     *
     * @param   string  $data  Klartext
     * @param   string  $key   Verschlüsselungsschlüssel
     *
     * @return  string|false  Base64-kodierter Chiffretext (IV + Daten) oder false
     *
     * @since   1.0.0
     */
    public static function encrypt($data, $key)
    {
        if (empty($data) || empty($key)) {
            return false;
        }

        $ivLength  = openssl_cipher_iv_length(self::ENCRYPTION_METHOD);
        $iv        = openssl_random_pseudo_bytes($ivLength);
        $keyHash   = hash('sha256', $key, true);
        $encrypted = openssl_encrypt($data, self::ENCRYPTION_METHOD, $keyHash, 0, $iv);

        if ($encrypted === false) {
            return false;
        }

        return base64_encode($iv . $encrypted);
    }

    /**
     * Entschlüsselt einen AES-256-CBC-verschlüsselten String.
     *
     * @param   string  $data  Base64-kodierter Chiffretext
     * @param   string  $key   Verschlüsselungsschlüssel
     *
     * @return  string|false  Klartext oder false bei Fehler / falschem Schlüssel
     *
     * @since   1.0.0
     */
    public static function decrypt($data, $key)
    {
        if (empty($data) || empty($key)) {
            return false;
        }

        $raw = base64_decode($data);
        if ($raw === false) {
            return false;
        }

        $ivLength  = openssl_cipher_iv_length(self::ENCRYPTION_METHOD);
        $iv        = substr($raw, 0, $ivLength);
        $encrypted = substr($raw, $ivLength);
        $keyHash   = hash('sha256', $key, true);

        return openssl_decrypt($encrypted, self::ENCRYPTION_METHOD, $keyHash, 0, $iv);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Session-Verwaltung des Schlüssels
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Gibt an ob ein Schlüssel in der Session vorhanden ist.
     *
     * @return  boolean
     *
     * @since   1.0.0
     */
    public static function hasEncryptionKey()
    {
        return !empty(Factory::getApplication()->getSession()->get('cluborganisation.encryption_key'));
    }

    /**
     * Speichert den Schlüssel in der PHP-Session.
     *
     * @param   string  $key  Verschlüsselungsschlüssel
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public static function setEncryptionKey($key)
    {
        Factory::getApplication()->getSession()->set('cluborganisation.encryption_key', $key);
    }

    /**
     * Gibt den Schlüssel aus der Session zurück.
     *
     * @return  string|null
     *
     * @since   1.0.0
     */
    public static function getEncryptionKey()
    {
        return Factory::getApplication()->getSession()->get('cluborganisation.encryption_key');
    }

    /**
     * Entfernt den Schlüssel aus der Session.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public static function clearEncryptionKey()
    {
        Factory::getApplication()->getSession()->clear('cluborganisation.encryption_key');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Canary-Mechanismus (Schlüsselvalidierung)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Liest den gespeicherten Canary-Wert aus den Komponenten-Parametern.
     *
     * @return  string|null  Verschlüsselter Canary oder null wenn noch nicht gesetzt
     *
     * @since   1.9.0
     */
    public static function getStoredCanary(): ?string
    {
        $params = ComponentHelper::getParams('com_cluborganisation');
        $canary = $params->get(self::CANARY_PARAM, '');
        return !empty($canary) ? $canary : null;
    }

    /**
     * Verschlüsselt den Canary-String mit dem gegebenen Schlüssel und speichert
     * ihn als Komponenten-Parameter in #__extensions.
     *
     * Wird beim Speichern des ersten Bankdatensatzes aufgerufen, sofern noch
     * kein Canary gespeichert ist. Bei Key Rotation muss diese Methode mit dem
     * neuen Schlüssel erneut aufgerufen werden.
     *
     * @param   string  $key  Aktiver Verschlüsselungsschlüssel
     *
     * @return  boolean  True bei Erfolg
     *
     * @since   1.9.0
     */
    public static function saveCanary(string $key): bool
    {
        $encrypted = self::encrypt(self::CANARY_VALUE, $key);

        if ($encrypted === false) {
            return false;
        }

        // Aktuelle Parameter laden und Canary setzen
        $params = ComponentHelper::getParams('com_cluborganisation');
        $params->set(self::CANARY_PARAM, $encrypted);

        // In #__extensions persistieren
        try {
            $db    = Factory::getContainer()->get(\Joomla\Database\DatabaseInterface::class);
            $query = $db->getQuery(true)
                ->update($db->quoteName('#__extensions'))
                ->set($db->quoteName('params') . ' = ' . $db->quote($params->toString()))
                ->where($db->quoteName('element') . ' = ' . $db->quote('com_cluborganisation'))
                ->where($db->quoteName('type') . ' = ' . $db->quote('component'));
            $db->setQuery($query);
            $db->execute();
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Prüft ob der gegebene Schlüssel korrekt ist.
     *
     * Logik:
     * 1. Ist kein Canary gespeichert → noch kein Bankdatensatz vorhanden →
     *    Schlüssel akzeptieren (Nutzer legt den initialen Schlüssel fest).
     * 2. Canary entschlüsseln und mit CANARY_VALUE vergleichen.
     *    Korrekte Übereinstimmung → Schlüssel gültig.
     *
     * @param   string  $key  Zu prüfender Schlüssel
     *
     * @return  boolean
     *
     * @since   1.9.0
     */
    public static function verifyKey(string $key): bool
    {
        $storedCanary = self::getStoredCanary();

        // Noch kein Canary → erster Schlüssel wird akzeptiert
        if ($storedCanary === null) {
            return true;
        }

        $decrypted = self::decrypt($storedCanary, $key);

        return $decrypted === self::CANARY_VALUE;
    }
}
