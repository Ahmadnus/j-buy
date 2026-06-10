<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * AuthService handles the password-reset OTP flow.
 *
 * Uses the existing `password_reset_tokens` table — we just store the phone
 * number in the `email` column since the primary key is on that column.
 * (The column is repurposed as a generic identifier; a future migration
 * can rename it if needed.)
 */
class AuthService
{
    private const RESET_EXPIRY_MINUTES = 60;

    public function generateResetOtpForPhone(string $phone): string
    {
        $otp = (string) random_int(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $phone],          // primary key — reused as identifier
            [
                'token'      => Hash::make($otp),
                'created_at' => now(),
            ]
        );

        return $otp;
    }

    public function resetPasswordWithOtpByPhone(string $phone, string $otp, string $newPassword): bool
    {
        $record = DB::table('password_reset_tokens')
                    ->where('email', $phone)
                    ->first();

        if (! $record) {
            return false;
        }

        if (now()->diffInMinutes($record->created_at) > self::RESET_EXPIRY_MINUTES) {
            DB::table('password_reset_tokens')->where('email', $phone)->delete();
            return false;
        }

        if (! Hash::check($otp, $record->token)) {
            return false;
        }

        User::where('phone', $phone)->update([
            'password' => Hash::make($newPassword),
        ]);

        DB::table('password_reset_tokens')->where('email', $phone)->delete();

        return true;
    }
}