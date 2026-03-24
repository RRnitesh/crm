<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

abstract class BaseController extends Controller
{
    protected function logRequest($payload, string $context = 'API Request'): void
    {
        Log::channel('api')->info($context, [
            'payload' => $payload,
        ]);
    }

    /**
     * Log an exception in the API channel.
     */
    protected function logException(\Throwable $e, string $context = 'Operation failed'): void
    {
        Log::channel('api')->warning($context, [
            'message' => $e->getMessage(),
        ]);
    }

    /**
     * Return a standard error JSON response.
     */
    protected function errorResponse(string $message = 'Something went wrong', int $status = 500): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $status);
    }

    /**
     * Return a standard success JSON response.
     */
    protected function successResponse(string $message = 'Success', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
        ], $status);
    }

    /**
     * Return a standard success JSON response.
     */
    protected function executionResponse(bool $result): JsonResponse
    {
        $status = $result ? 200 : 401;
        $message = $result ? 'Operation successful' : 'Operation failed';

        return response()->json([
            'success' => $result,
            'message' => $message,
        ], $status);
    }

    protected function paginateResponse($records): JsonResponse
    {
        return response()->json([
            'data' => $records->items(),
            'current_page' => $records->currentPage(),
            'per_page' => $records->perPage(),
            'total' => $records->total(),
            'last_page' => $records->lastPage(),
            'next_page_url' => $records->nextPageUrl(),
            'prev_page_url' => $records->previousPageUrl(),
        ]);
    }
}
