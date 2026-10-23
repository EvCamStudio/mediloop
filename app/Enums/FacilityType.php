<?php

namespace App\Enums;

enum FacilityType: string
{
    case Rsud = 'RSUD';
    case Puskesmas = 'PUSKESMAS';
    case Klinik = 'KLINIK';
    case RsSwasta = 'RS_SWASTA';
    case Dinkes = 'DINKES';

    public function label(): string
    {
        return match ($this) {
            self::Rsud => 'RSUD',
            self::Puskesmas => 'Puskesmas',
            self::Klinik => 'Klinik',
            self::RsSwasta => 'RS Swasta',
            self::Dinkes => 'Dinkes',
        };
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $type): string => $type->value, self::cases());
    }

    /**
     * @return array<int, string>
     */
    public static function operationalValues(): array
    {
        return array_values(array_filter(
            self::values(),
            fn (string $type): bool => $type !== self::Dinkes->value
        ));
    }
}
