<?php

namespace App\Class;


class TokenClass
{
    public static function respondWithToken($token, $refreshToken = '')
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->guard('api')->factory()->getTTL(),
            'refresh_token' => $refreshToken ?: null
        ];
    }

    public static function refresh($user)
    {
        return TokenClass::respondWithToken($user);
    }
}
