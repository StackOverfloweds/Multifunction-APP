<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Menolak request jika token JWT valid secara signature tapi sesinya
 * sudah di-revoke (dihapus dari Redis lewat logout / paksa logout).
 * Pasang middleware ini setelah auth:api di config/lighthouse.php.
 */
class EnsureSessionActive
{
    public function handle(Request $request, Closure $next)
    {
        $token = JWTAuth::getToken();

        if (! $token) {
            return $next($request);
        }

        try {
            $payload = JWTAuth::setToken($token)->getPayload();
            $jti = $payload->get('jti');
            $userId = $payload->get('sub');

            if (! Redis::exists("session:user:{$userId}:{$jti}")) {
                return response()->json([
                    'errors' => [['message' => 'Sesi sudah berakhir, silakan login kembali.']],
                ], 401);
            }
        } catch (\Throwable $e) {
            // Token tidak valid akan ditangani oleh guard auth:api sebelumnya.
        }

        return $next($request);
    }
}