<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * AuthService handles password reset OTP flow only.
 *
 * Email verification has been removed from this application.
 * Registration immediately returns an authenticated token.
 *
 * Password reset flow:
 *   1. POST /auth/forgot-password  → generates 6-digit OTP, emails it
 *   2. POST /auth/reset-password   → validates OTP + email, sets new password
 */
class AuthService
{
    private const RESET_EXPIRY_MINUTES = 60;

    /**
     * Generate a 6-digit OTP, store it hashed in password_reset_tokens,
     * and return the plain code to be emailed synchronously.
     */
    public function generateResetOtp(string $email): string
    {
        $otp = (string) random_int(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token'      => Hash::make($otp),
                'created_at' => now(),
            ]
        );

        return $otp;
    }

    /**
     * Validate the OTP sent by the user, then update their password.
     *
     * @return bool  true = success, false = invalid or expired OTP
     */
    public function resetPasswordWithOtp(string $email, string $otp, string $newPassword): bool
    {
        $record = DB::table('password_reset_tokens')
                    ->where('email', $email)
                    ->first();

        if (! $record) {
            return false;
        }

        if (now()->diffInMinutes($record->created_at) > self::RESET_EXPIRY_MINUTES) {
            // Expired — clean up
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return false;
        }

        if (! Hash::check($otp, $record->token)) {
            return false;
        }

        User::where('email', $email)->update([
            'password' => Hash::make($newPassword),
        ]);

        DB::table('password_reset_tokens')->where('email', $email)->delete();

        return true;
    }
}
