<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    /**
     * Send success response
     */
    public function sendResponse($result, $message = 'Success', $code = 200)
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $result,
        ];

        return response()->json($response, $code);
    }

    /**
     * Send error response
     */
    public function sendError($error, $errorMessages = [], $code = 400)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['errors'] = $errorMessages;
        }

        return response()->json($response, $code);
    }

    /**
     * Send validation error response
     */
    public function sendValidationError($validator, $code = 422)
    {
        return $this->sendError('Validation Error', $validator->errors()->all(), $code);
    }

    /**
     * Send paginated response
     */
    public function sendPaginatedResponse($paginator, $message = 'Success')
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $paginator->items(),
            'pagination' => [
                'total' => $paginator->total(),
                'count' => $paginator->count(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'total_pages' => $paginator->lastPage(),
                'links' => [
                    'next' => $paginator->nextPageUrl(),
                    'previous' => $paginator->previousPageUrl(),
                ],
            ],
        ];

        return response()->json($response, 200);
    }
}