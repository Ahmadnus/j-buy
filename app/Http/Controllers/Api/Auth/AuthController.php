<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponses;
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

class AuthController extends Controller
{
    use ApiResponses;

    public function __construct(private AuthService $authService) {}

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
        $isEn  = str_starts_with((string) $request->header('Accept-Language', 'ar'), 'en');

        return $this->created([
            'token'      => $token,
            'token_type' => 'Bearer',
            'user'       => new UserResource($user),
        ], $isEn ? 'Account created successfully' : 'تم إنشاء الحساب بنجاح');
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('phone', $request->phone)->first();
        $isEn = str_starts_with((string) $request->header('Accept-Language', 'ar'), 'en');

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return $this->error(
                $isEn ? 'Invalid phone number or password' : 'بيانات تسجيل الدخول غير صحيحة',
                401
            );
        }

        $token = $user->createToken('mobile')->plainTextToken;

        return $this->success([
            'token'      => $token,
            'token_type' => 'Bearer',
            'user'       => new UserResource($user),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->where('name', 'mobile')->delete();
        $isEn = str_starts_with((string) $request->header('Accept-Language', 'ar'), 'en');
        return $this->success(null, $isEn ? 'Logged out successfully' : 'تم تسجيل الخروج بنجاح');
    }

    public function me(Request $request): JsonResponse
    {
        return $this->success(new UserResource($request->user()));
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $otp  = $this->authService->generateResetOtpForPhone($request->phone);
        $isEn = str_starts_with((string) $request->header('Accept-Language', 'ar'), 'en');

        Log::info("Password reset OTP for {$request->phone}: {$otp}");

        $data = app()->environment('production') ? null : ['otp' => $otp];

        return $this->success(
            $data,
            $isEn ? 'Password reset code sent to your phone' : 'تم إرسال رمز إعادة تعيين كلمة المرور إلى رقم هاتفك'
        );
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $success = $this->authService->resetPasswordWithOtpByPhone(
            $request->phone,
            $request->otp,
            $request->password
        );

        $isEn = str_starts_with((string) $request->header('Accept-Language', 'ar'), 'en');

        if (! $success) {
            return $this->error(
                $isEn ? 'Invalid or expired reset code' : 'رمز إعادة التعيين غير صحيح أو منتهي الصلاحية',
                422
            );
        }

        return $this->success(null, $isEn ? 'Password changed successfully' : 'تم تغيير كلمة المرور بنجاح');
    }
}