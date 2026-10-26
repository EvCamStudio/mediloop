<?php

namespace App\Support;

use DateTimeInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Number;

final class Formatter
{
    /**
     * Format an Indonesian rupiah amount using the active interface locale.
     */
    public static function idr(int|float|string $amount): string
    {
        return 'Rp '.self::number($amount, 0);
    }

    /**
     * Format an integer or decimal using the active interface locale.
     */
    public static function number(int|float|string $value, int $maxPrecision = 0): string
    {
        $val = (float) $value;
        if (extension_loaded('intl')) {
            try {
                $formatted = Number::format($val, maxPrecision: $maxPrecision, locale: self::locale());
                if ($formatted !== false) {
                    return $formatted;
                }
            } catch (\Throwable) {
                // Fallback to native PHP formatting if intl is unavailable or misconfigured
            }
        }

        $isId = self::locale() === 'id';
        $decPoint = $isId ? ',' : '.';
        $thousandsSep = $isId ? '.' : ',';

        if ($maxPrecision > 0 && floor($val) != $val) {
            return rtrim(rtrim(number_format($val, $maxPrecision, $decPoint, $thousandsSep), '0'), $decPoint);
        }

        return number_format($val, 0, $decPoint, $thousandsSep);
    }

    /**
     * Format a percentage value using the active interface locale.
     */
    public static function percent(int|float|string $value, int $maxPrecision = 1): string
    {
        return self::number($value, $maxPrecision).'%';
    }

    /**
     * Format a date for display in the active interface locale.
     */
    public static function date(DateTimeInterface|string|null $date, string $format = 'd M Y'): string
    {
        if ($date === null || $date === '') {
            return '—';
        }

        $instance = $date instanceof DateTimeInterface
            ? Carbon::instance($date)
            : Carbon::parse($date);

        return $instance->translatedFormat($format);
    }

    /**
     * Format a date and time for display in the active interface locale.
     */
    public static function dateTime(DateTimeInterface|string|null $date, string $format = 'd M Y, H:i'): string
    {
        return self::date($date, $format);
    }

    private static function locale(): string
    {
        return app()->getLocale();
    }
}
