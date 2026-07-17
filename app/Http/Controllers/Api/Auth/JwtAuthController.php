<?php

namespace App\Http\Controllers\Api\Auth;

use App\Application\Services\Audit\AuditLogger;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\JWTGuard;

class JwtAuthController extends Controller
{
    public function __construct(
        private readonly AuditLogger $auditLogger,
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');
        $credentials['is_active'] = true;

        $guard = $this->apiGuard();
        $token = $guard->attempt($credentials);

        if ($token === false) {
            $this->auditLogger->log('api.login_failed', null, null, [
                'email' => $credentials['email'],
            ]);

            return response()->json([
                'message' => 'E-posta veya şifre hatalı.',
            ], 401);
        }

        /** @var User $user */
        $user = $guard->user();
        $user->forceFill(['last_login_at' => now()])->save();

        $this->auditLogger->log('api.login_success', $user);

        return $this->respondWithToken($token);
    }

    public function me(): JsonResponse
    {
        /** @var User $user */
        $user = $this->apiGuard()->user();

        return response()->json([
            'data' => [
                'uuid' => $user->uuid,
                'name' => $user->name,
                'email' => $user->email,
                'is_super_admin' => $user->is_super_admin,
            ],
        ]);
    }

    public function logout(): JsonResponse
    {
        $guard = $this->apiGuard();

        /** @var User|null $user */
        $user = $guard->user();
        if ($user !== null) {
            $this->auditLogger->log('api.logout', $user);
        }

        $guard->logout();

        return response()->json(['message' => 'Çıkış yapıldı.']);
    }

    public function refresh(): JsonResponse
    {
        return $this->respondWithToken($this->apiGuard()->refresh());
    }

    protected function respondWithToken(string $token): JsonResponse
    {
        $guard = $this->apiGuard();

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $guard->factory()->getTTL() * 60,
        ]);
    }

    protected function apiGuard(): JWTGuard
    {
        /** @var JWTGuard $guard */
        $guard = Auth::guard('api');

        return $guard;
    }
}
