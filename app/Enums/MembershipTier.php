<?php

namespace App\Enums;

enum MembershipTier: string
{
    case Standard = 'standard';
    case Silver   = 'silver';
    case Gold     = 'gold';

    /** Arabic label — matches Flutter UserModel._tierLabel() */
    public function labelAr(): string
    {
        return match($this) {
            self::Standard => 'عادية',
            self::Silver   => 'فضية',
            self::Gold     => 'ذهبية',
        };
    }
}
