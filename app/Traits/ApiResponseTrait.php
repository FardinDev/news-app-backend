<?php

namespace App\Traits;

trait ApiResponseTrait
{
   
    public function apiSuccessResponse($message, $data = [], $statusCode = 200)
    {

        if ($message == 'Success' || $message == 'success') {
            $message = trans('messages.success');
        }
        $data = [
            'status' => 'success',
            'code' => $statusCode,
            'message' => $message,
            'data' => $data ? $data : null,
        ];

        return response()->json($data, $statusCode);
    }

    public function apiFailedResponse($message, $data = [], $statusCode = 412)
    {

        $data = [
            'status' => 'failed',
            'code' => $statusCode,
            'message' => $message,
            'data' => $data ? $data : null,
        ];

        return response()->json($data, $statusCode);
    }
}
