<?php

namespace Jsadways\Operationrecord\Exceptions;

use Exception;
use Log;
use Illuminate\Http\Request;
use Response;
use Illuminate\Http\JsonResponse;

class BasicException extends Exception
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
        return response()->fail($this->getMessage());
    }
}
