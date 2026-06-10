<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * Phone-based authentication.
 *
 * Account identifier is the user's Jordanian phone number. Registration
 * captures name, username, phone, region, and password — no email.
 * Login accepts only phone + password.
 */
class AuthController extends ApiController
{
    public function __construct(private AuthService $authService) {}

    // ── POST /auth/register ───────────────────────────────────────────────────
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name_ar'  => $request->name_ar,
            'username' => $request->username,
            'phone'    => $request->phone,
            'region'   => $request->region,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('mobile')->plainTextToken;

        return $this->created([
            'token'      => $token,
            'token_type' => 'Bearer',
            'user'       => new UserResource($user),
        ], 'تم إنشاء الحساب بنجاح');
    }

    // ── POST /auth/login ──────────────────────────────────────────────────────
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('phone', $request->phone)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return $this->error('بيانات تسجيل الدخول غير صحيحة', 401);
        }

        $token = $user->createToken('mobile')->plainTextToken;

        return $this->success([
            'token'      => $token,
            'token_type' => 'Bearer',
            'user'       => new UserResource($user),
        ]);
    }

    // ── POST /auth/logout ─────────────────────────────────────────────────────
    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->where('name', 'mobile')->delete();
        return $this->success(null, 'تم تسجيل الخروج بنجاح');
    }

    // ── GET /auth/me ──────────────────────────────────────────────────────────
    public function me(Request $request): JsonResponse
    {
        return $this->success(new UserResource($request->user()));
    }

    // ── POST /auth/forgot-password ────────────────────────────────────────────
    // Generates a 6-digit OTP. In a real deployment this would be sent via SMS.
    // For now we log the OTP server-side and ALSO return it in non-production
    // environments so QA can test without an SMS provider.
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $user = User::where('phone', $request->phone)->firstOrFail();
        $otp  = $this->authService->generateResetOtpForPhone($request->phone);

        // TODO: integrate an SMS provider (Twilio, MessageBird, local Jordan SMS gateway).
        // For now log so it appears in storage/logs/laravel.log during development.
        Log::info("Password reset OTP for {$request->phone}: {$otp}");

        $data = app()->environment('production') ? null : ['otp' => $otp];

        return $this->success(
            $data,
            'تم إرسال رمز إعادة تعيين كلمة المرور إلى رقم هاتفك'
        );
    }

    // ── POST /auth/reset-password ─────────────────────────────────────────────
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $success = $this->authService->resetPasswordWithOtpByPhone(
            $request->phone,
            $request->otp,
            $request->password
        );

        if (! $success) {
            return $this->error('رمز إعادة التعيين غير صحيح أو منتهي الصلاحية', 422);
        }

        return $this->success(null, 'تم تغيير كلمة المرور بنجاح');
    }
}