<?php

namespace App\Utils;

class ResponseUtil
{
    public static function noticeResponse($message, $httpCode = 200, $data = null) {
        return response()->json(["message" => $message, "data" => $data], $httpCode);
    }

    public static function errorResponse($message, $httpCode = 422, $data = null) {
        return response()->json(["message" => $message, "data" => $data], $httpCode);
    }
}