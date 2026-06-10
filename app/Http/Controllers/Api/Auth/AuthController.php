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
use Illuminate\Support\Facades\Mail;

class AuthController extends ApiController
{
    public function __construct(private AuthService $authService) {}

    // ── POST /auth/register ───────────────────────────────────────────────────
    // Registration immediately returns token + user.
    // No email verification step exists in this application.

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name_ar'  => $request->name_ar,
            'username' => $request->username,
            'email'    => $request->email,
            'phone'    => $request->phone,
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
        $user = User::where('email', $request->email)->first();

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
        $request->user()->currentAccessToken()->delete();

        return $this->success(null, 'تم تسجيل الخروج بنجاح');
    }

    // ── GET /auth/me ──────────────────────────────────────────────────────────

    public function me(Request $request): JsonResponse
    {
        return $this->success(new UserResource($request->user()));
    }

    // ── POST /auth/forgot-password ────────────────────────────────────────────
    // Generates a 6-digit OTP and emails it to the user.

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->firstOrFail();
        $otp  = $this->authService->generateResetOtp($request->email);

        $this->sendResetOtpEmail($user, $otp);

        return $this->success(
            null,
            'تم إرسال رمز إعادة تعيين كلمة المرور إلى بريدك الإلكتروني'
        );
    }

    // ── POST /auth/reset-password ─────────────────────────────────────────────
    // Validates the 6-digit OTP from the email and sets the new password.

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $success = $this->authService->resetPasswordWithOtp(
            $request->email,
            $request->otp,
            $request->password
        );

        if (! $success) {
            return $this->error('رمز إعادة التعيين غير صحيح أو منتهي الصلاحية', 422);
        }

        return $this->success(null, 'تم تغيير كلمة المرور بنجاح');
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function sendResetOtpEmail(User $user, string $otp): void
    {
        try {
            Mail::raw(
                "رمز إعادة تعيين كلمة المرور: {$otp}\nصالح لمدة 60 دقيقة.",
                fn($m) => $m->to($user->email)->subject('إعادة تعيين كلمة المرور — J-Buy')
            );
        } catch (\Throwable) {
            logger()->warning("Failed to send reset OTP email to {$user->email}");
        }
    }
}
