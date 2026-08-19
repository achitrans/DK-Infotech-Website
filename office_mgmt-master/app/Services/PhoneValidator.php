<?php

namespace App\Services;

class PhoneValidator
{
    /**
     * Validate an Indian phone number.
     * Accepts 10-digit numbers starting with 6, 7, 8, or 9.
     * Allows optional +91 or 91 prefix.
     */
    public static function isValid($number): bool
    {
        // Remove spaces, dashes, and parentheses
        $number = preg_replace('/[\s\-\(\)]/', '', $number);
        // Remove country code if present
        if (preg_match('/^(\+91)/', $number)) {
            $number = preg_replace('/^(\+91)/', '', $number);
        }
        // Validate 10 digits starting with 6-9
        return preg_match('/^[6-9]\d{9}$/', $number) === 1;
    }
}
