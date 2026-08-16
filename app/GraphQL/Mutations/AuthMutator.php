<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use Exception;
use GraphQL\Error\Error;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthMutator
{
    protected function redisTtlSeconds(): int
    {
        return (int) config('jwt.ttl', 60) * 60;
    }

    protected function buildPayload(string $token, User $user): array
    {
        return [
            'token' => $token,
            'tokenType' => 'Bearer',
            'expiresIn' => $this->redisTtlSeconds(),
            'user' => $user,
        ];
    }

    /**
     * Simpan metadata sesi ke Redis supaya token bisa di-revoke kapan pun
     * (mis. saat logout, atau paksa logout dari device lain).
     */
    protected function storeSession(string $token, User $user): void
    {
        $payload = JWTAuth::setToken($token)->getPayload();
        $jti = $payload->get('jti');

        Redis::setex(
            "session:user:{$user->id}:{$jti}",
            $this->redisTtlSeconds(),
            json_encode([
                'user_id' => $user->id,
                'issued_at' => now()->toIso8601String(),
            ])
        );
    }

    public function register($_, array $args): array
    {
        $validator = Validator::make($args, [
            'username' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:users,username'],
            'email' => ['nullable', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'passwordConfirmation' => ['required', 'same:password'],
        ]);

        if ($validator->fails()) {
            throw new Error($validator->errors()->first());
        }

        $user = User::create([
            'username' => strtolower($args['username']),
            'email' => $args['email'] ?? null,
            'password' => Hash::make($args['password']),
            'role' => 'user', // default role untuk pendaftar baru, sama seperti RegisteredUserController lama
        ]);

        $token = JWTAuth::fromUser($user);
        $this->storeSession($token, $user);

        return $this->buildPayload($token, $user);
    }

    public function login($_, array $args): array
    {
        // Login pakai "username", bukan "email" — konsisten dengan LoginRequest asli.
        $credentials = [
            'username' => $args['username'],
            'password' => $args['password'],
        ];

       try {
            $token = JWTAuth::attempt($credentials);
       } catch (Exception $e) {
        throw new Exception('Email Atau Password Salah');
       }

       if (!$token) {
        throw new Exception('Email atau password Salah');
       }

        /** @var User $user */
        $user = auth('api')->user();
        $this->storeSession($token, $user);

        return $this->buildPayload($token, $user);
    }

    public function refresh(): array
    {
        $oldToken = JWTAuth::getToken();
        $oldPayload = JWTAuth::setToken($oldToken)->getPayload();
        $oldJti = $oldPayload->get('jti');

        /** @var User $user */
        $user = auth('api')->user();

        $newToken = JWTAuth::refresh($oldToken);

        Redis::del("session:user:{$user->id}:{$oldJti}");
        $this->storeSession($newToken, $user);

        return $this->buildPayload($newToken, $user);
    }

    public function logout(): bool
    {
        $token = JWTAuth::getToken();
        $payload = JWTAuth::setToken($token)->getPayload();
        $jti = $payload->get('jti');
        $userId = $payload->get('sub');

        Redis::del("session:user:{$userId}:{$jti}");
        JWTAuth::invalidate($token);

        return true;
    }
}