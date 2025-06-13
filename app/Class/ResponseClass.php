<?php

namespace App\Class;

class ResponseClass
{
    public static function success(string $message = 'Success', $data = [], int $statusCode = 200)
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    public static function error(string $message = 'Terjadi kesalahan server!', array $data = [], int $statusCode = 500)
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }
}
