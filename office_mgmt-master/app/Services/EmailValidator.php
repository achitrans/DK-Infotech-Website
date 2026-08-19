<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;

class EmailValidator
{
    /**
     * Validate email using RFC, DNS, and strict rules.
     *
     * @param string $email
     * @return bool
     */
    public static function validate(string $email): bool
    {
        $validator = Validator::make(
            ['email' => $email],
            ['email' => 'required|email:rfc,dns,strict']
        );

        return !$validator->fails();
    }
}
