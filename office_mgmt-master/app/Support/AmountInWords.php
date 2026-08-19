<?php

namespace App\Support;

class AmountInWords
{
    private static ?\NumberFormatter $formatter = null;

    private static function formatter(): \NumberFormatter
    {
        return self::$formatter ??= new \NumberFormatter('en_IN', \NumberFormatter::SPELLOUT);
    }

    public static function convert(float $value): string
    {
        $value = round($value, 2);
        $integerPart = (int) floor($value);
        $decimalPart = (int) round(($value - $integerPart) * 100);
        $integerWords = ucfirst(self::formatter()->format($integerPart) ?: 'zero');
        $result = "$integerWords rupees";

        if ($decimalPart > 0) {
            $decimalWords = ucfirst(self::formatter()->format($decimalPart) ?: 'zero');
            $result .= " and $decimalWords paise";
        }

        return ucwords("$result only");
    }
}
