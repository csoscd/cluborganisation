<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_cluborganisation
 *
 * @copyright   Copyright (C) 2025 Christian Schulz
 * @license     GPL-2.0-or-later
 */

declare(strict_types=1);

namespace Joomla\Component\Cluborganisation\Administrator\Service;

/**
 * Helper for encrypting and decrypting sensitive data.
 */
class CryptoHelper
{
    /**
     * Encrypt a string with the provided passphrase.
     *
     * @param   string  $plaintext   Data to encrypt.
     * @param   string  $passphrase  Passphrase.
     *
     * @return  string
     */
    public static function encrypt(string $plaintext, string $passphrase): string
    {
        $key = hash('sha256', $passphrase, true);
        $iv = random_bytes(16);
        $ciphertext = openssl_encrypt($plaintext, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

        return base64_encode($iv . $ciphertext);
    }

    /**
     * Decrypt a string with the provided passphrase.
     *
     * @param   string  $payload     Encrypted payload.
     * @param   string  $passphrase  Passphrase.
     *
     * @return  string
     */
    public static function decrypt(string $payload, string $passphrase): string
    {
        $raw = base64_decode($payload, true);
        if ($raw === false || strlen($raw) < 17) {
            return '';
        }

        $iv = substr($raw, 0, 16);
        $ciphertext = substr($raw, 16);
        $key = hash('sha256', $passphrase, true);

        $plaintext = openssl_decrypt($ciphertext, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

        return $plaintext === false ? '' : $plaintext;
    }
}
