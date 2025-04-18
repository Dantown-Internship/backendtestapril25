<?php

namespace App\Helpers;

use App\Exceptions\AppException;
use Illuminate\Support\Facades\Log;

class EncryptionHelper
{
    private static string $key = '32-char-key-here-1234567890abcdef';
    private static string $iv = '16-char-iv-heree';

    /**
     * Encrypt data using AES-256-CBC.
     *
     * @param mixed $data Data to encrypt (will be JSON-encoded)
     * @return string Base64-encoded encrypted data
     * @throws AppException
     */
    public static function encrypt(mixed $data): string
    {
        try {
            $jsonData = json_encode($data);
            if ($jsonData === false) {
                throw new AppException('Failed to encode data to JSON');
            }

            $encrypted = openssl_encrypt(
                $jsonData,
                'AES-256-CBC',
                self::$key,
                0,
                self::$iv
            );
            if ($encrypted === false) {
                throw new AppException('Encryption failed');
            }

            return base64_encode($encrypted);
        } catch (\Exception $e) {
            Log::error('Encryption error: ' . $e->getMessage());
            Log::error($e->getMessage());
            throw $e;
        }
    }

    /**
     * Decrypt data using AES-256-CBC.
     *
     * @param string $encryptedData Base64-encoded encrypted data
     * @return mixed Decrypted data (JSON-decoded)
     * @throws AppException
     */
    public static function decrypt(string $encryptedData): mixed
    {
        try {
            $decoded = base64_decode($encryptedData, true);
            if ($decoded === false) {
                throw new AppException('Invalid payload: Base64 decoding failed');
            }

            $decrypted = openssl_decrypt(
                $decoded,
                'AES-256-CBC',
                self::$key,
                0,
                self::$iv
            );
            if ($decrypted === false) {
                throw new AppException('Decryption failed');
            }

            $decryptedData = json_decode($decrypted, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new AppException('Failed to decode decrypted JSON: ' . json_last_error_msg());
            }

            return $decryptedData;
        } catch (\Exception $e) {
            Log::error('Decryption error: ' . $e->getMessage());
            Log::error($e->getMessage());
            throw $e;
        }
    }
}