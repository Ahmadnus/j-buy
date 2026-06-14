<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending   = 'pending';
    case Confirmed = 'confirmed';
    case Preparing = 'preparing';
    case Shipping  = 'shipping';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function labelAr(): string
    {
        return match($this) {
            self::Pending   => 'قيد الانتظار',
            self::Confirmed => 'تم التأكيد',
            self::Preparing => 'جاري التحضير',
            self::Shipping  => 'في الطريق',
            self::Delivered => 'تم التوصيل',
            self::Cancelled => 'ملغي',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Pending   => '#D4AF37',
            self::Confirmed => '#1976D2',
            self::Preparing => '#7B1FA2',
            self::Shipping  => '#E65100',
            self::Delivered => '#2E7D32',
            self::Cancelled => '#C62828',
        };
    }

    public function allowedTransitions(): array
    {
        return match($this) {
            self::Pending   => [self::Confirmed, self::Cancelled],
            self::Confirmed => [self::Preparing, self::Cancelled],
            self::Preparing => [self::Shipping,  self::Cancelled],
            self::Shipping  => [self::Delivered, self::Cancelled],
            self::Delivered => [],
            self::Cancelled => [],
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return in_array($next, $this->allowedTransitions(), true);
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Delivered, self::Cancelled], true);
    }
}