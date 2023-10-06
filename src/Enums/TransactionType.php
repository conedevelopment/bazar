<?php

namespace Cone\Bazar\Enums;

enum TransactionType: string
{
    case Payment = 'payment';
    case Refund = 'refund';

    /**
     * Get the name of the transaction type.
     */
    public function name(): string
    {
        return match ($this) {
            self::Payment => __('Payment'),
            self::Refund => __('Refund'),
        };
    }

    /**
     * Get the array representation of the enum cases.
     */
    public static function toArray(): array
    {
        return array_reduce(self::cases(), static function (array $types, TransactionType $type): array {
            return array_merge($types, [$type->value => $type->name()]);
        }, []);
    }
}
