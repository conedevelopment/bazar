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
            static::Payment => __('Payment'),
            static::Refund => __('Refund'),
        };
    }

    /**
     * Get the array representation of the enum cases.
     */
    public static function toArray(): array
    {
        return array_reduce(static::cases(), static function (array $types, TransactionType $type): array {
            return array_merge($types, [$type->value => $type->name()]);
        }, []);
    }
}
