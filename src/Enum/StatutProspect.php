<?php

declare(strict_types=1);

namespace App\Enum;

enum StatutProspect: string
{
    case AContacter = 'a_contacter';
    case Contacte = 'contacte';
    case Interesse = 'interesse';
    case PasInteresse = 'pas_interesse';
    case Client = 'client';

    public function label(): string
    {
        return match ($this) {
            self::AContacter => 'À contacter',
            self::Contacte => 'Contacté',
            self::Interesse => 'Intéressé',
            self::PasInteresse => 'Pas intéressé',
            self::Client => 'Client',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::AContacter => 'secondary',
            self::Contacte => 'primary',
            self::Interesse => 'warning',
            self::PasInteresse => 'danger',
            self::Client => 'success',
        };
    }
}
