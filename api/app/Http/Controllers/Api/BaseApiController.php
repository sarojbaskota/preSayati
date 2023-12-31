<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class BaseApiController extends Controller
{
    /**
     * Send a success response.
     *
     * @param  string|array|null  $data
     * @param  string  $message
     * @param  int  $statusCode
     * @return JsonResponse
     */
    protected function sendSuccess($data = null, string $message = 'Success', int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'status'  => 'success',
            'message' => $message,
            'data'    => $data,
        ], $statusCode);
    }
     /**
     * Send a success response.
     *
     * @param  string|array|null  $data
     * @param  string  $message
     * @param  int  $statusCode
     * @return JsonResponse
     */
    protected function sendCreateSuccess($data = null, string $message = 'Success', int $statusCode = 201): JsonResponse
    {
        return response()->json([
            'status'  => 'success',
            'message' => $message,
            'data'    => $data,
        ], $statusCode);
    }

    /**
     * Send an error response.
     *
     * @param  string  $message
     * @param  int  $statusCode
     * @param  array|null  $errors
     * @return JsonResponse
     */
    protected function sendError(string $message = 'Error', int $statusCode = 400, ?array $errors = null): JsonResponse
    {
        $response = [
            'status'  => 'error',
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Send a not found response.
     *
     * @param  string  $message
     * @return JsonResponse
     */
    protected function sendNotFound(string $message = 'Not Found'): JsonResponse
    {
        return $this->sendError($message, 404);
    }
    /**
     * Send validation fail response.
     *
     * @param  ValidationException  $exception
     * @return JsonResponse
     */
    protected function sendValidationFail(ValidationException $exception): JsonResponse
    {
        return $this->sendError('Validation Failed', 422, $exception->errors());
    }
}
