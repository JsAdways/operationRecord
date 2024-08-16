<?php

namespace Jsadways\Operationrecord\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class RecordException extends Exception
{
    /**
     * Report the exception.
     */
    public function report(): void
    {
        Log::error("message: {$this->getMessage()} file: {$this->getFile()} line:  {$this->getLine()}");
    }

    /**
     * Render the exception into an HTTP response.
     */
    public function render(Request $request): JsonResponse
    {
        return Response::json([
            'status_code' => 400,
            'message' => $this->getMessage()
        ],400);
    }
}
