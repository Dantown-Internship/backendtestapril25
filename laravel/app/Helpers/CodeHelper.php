<?php

namespace App\Helpers;

class CodeHelper
{
    /**
     * Generate a random Code of specified length.
     *
     * @param int $length
     * @return string
     */
    public static function generate(
        int $length,
        bool $numbersOnly = false
    ): string {
        $characters = "";
        if ($numbersOnly) {
            $characters = "0123456789";
        } else {
            $characters =
                "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        }

        $otp = "";

        for ($i = 0; $i < $length; $i++) {
            $otp .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return $otp;
    }

    public static function generateSecureReference(): string
    {
        $prefix = "OLINK-";
        $timestamp = microtime(true);
        $randomNumber = rand(0, 1000000);
        $userId = auth()->id();
        $hash = hash('sha256', $timestamp . $randomNumber . $userId);
        $secureReference = substr($hash, 0, 14);

        return $prefix . $secureReference;
    }

    public static function extractErrorMessage($error)
    {
        if (is_string($error)) {
            return $error;
        }

        if (is_array($error)) {
            return $error['message'] ?? 'An unknown error occurred.';
        }

        if (is_object($error)) {
            return $error->message ?? $error->getMessage() ?? 'An unknown error occurred.';
        }

        return 'An unknown error occurred.';
    }

    public static function getIdentifyType(string $input): IdentifierType
    {
        if (preg_match('/^@[a-zA-Z0-9]+_\d{3,9}$/', $input)) {
            return IdentifierType::Tag;
        } elseif (preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $input)) {
            return IdentifierType::Email;
        } elseif (preg_match('/^\+?\d{11}$/', $input)) {
            return IdentifierType::Phone;
        } elseif (preg_match('/^\d{10}$/', $input)) {
            return IdentifierType::AccountNumber;
        } else {
            return IdentifierType::Unknown;
        }
    }
}
