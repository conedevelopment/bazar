<?php

declare(strict_types=1);

namespace Cone\Bazar\Enums;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Number;

enum Currency: string
{
    case AED = 'AED';
    case ARS = 'ARS';
    case AUD = 'AUD';
    case BDT = 'BDT';
    case BGN = 'BGN';
    case BRL = 'BRL';
    case CAD = 'CAD';
    case CHF = 'CHF';
    case CLP = 'CLP';
    case CNY = 'CNY';
    case COP = 'COP';
    case CZK = 'CZK';
    case DKK = 'DKK';
    case EGP = 'EGP';
    case EUR = 'EUR';
    case GBP = 'GBP';
    case GHS = 'GHS';
    case HKD = 'HKD';
    case HRK = 'HRK';
    case HUF = 'HUF';
    case IDR = 'IDR';
    case ILS = 'ILS';
    case INR = 'INR';
    case ISK = 'ISK';
    case JPY = 'JPY';
    case KES = 'KES';
    case KRW = 'KRW';
    case KWD = 'KWD';
    case LKR = 'LKR';
    case MXN = 'MXN';
    case MYR = 'MYR';
    case NGN = 'NGN';
    case NOK = 'NOK';
    case NZD = 'NZD';
    case PEN = 'PEN';
    case PHP = 'PHP';
    case PKR = 'PKR';
    case PLN = 'PLN';
    case QAR = 'QAR';
    case RON = 'RON';
    case RUB = 'RUB';
    case SAR = 'SAR';
    case SEK = 'SEK';
    case SGD = 'SGD';
    case THB = 'THB';
    case TRY = 'TRY';
    case USD = 'USD';
    case UYU = 'UYU';
    case VND = 'VND';
    case ZAR = 'ZAR';

    /**
     * Get the symbol for the currency.
     */
    public function symbol(): string
    {
        return match ($this) {
            self::AUD => 'A$',
            self::CAD => 'C$',
            self::CHF => 'Fr',
            self::CNY => '¥',
            self::EUR => '€',
            self::GBP => '£',
            self::HUF => 'Ft',
            self::IDR => 'Rp',
            self::ILS => '₪',
            self::INR => '₹',
            self::JPY => '¥',
            self::KRW => '₩',
            self::NGN => '₦',
            self::RUB => '₽',
            self::TRY => '₺',
            self::USD => '$',
            self::ZAR => 'R',
            default => $this->value,
        };
    }

    /**
     * Determine if the currency uses precision (decimals).
     */
    public function precision(): int
    {
        return match ($this) {
            self::HUF,
            self::JPY,
            self::KRW,
            self::IDR,
            self::VND => 0,
            default => 2,
        };
    }

    /**
     * Format the given amount in this currency.
     */
    public function format(float $value): string
    {
        return Number::currency(number: $value, in: $this->value, precision: $this->precision());
    }

    /**
     * Determine if the currency is available.
     */
    public function available(): bool
    {
        return in_array($this, Config::get('bazar.currencies.available', []));
    }

    /**
     * Get the key for the currency.
     */
    public function key(): string
    {
        return strtolower($this->value);
    }
}
