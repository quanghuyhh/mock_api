<?php

namespace App\Repositories;

use App\Http\Resources\Api\V1\ExceptionResource;
use App\Models\ResponseData;

class ApiExceptionRepository
{
    public function throwException($code = 200, $message = '', $description = '')
    {
        $ex = new ResponseData();
        $ex->fill([
            'code' => (string) $code,
            'message' => $message,
            'description' => $description
        ]);
        return response()->json($ex, $code);
    }
}