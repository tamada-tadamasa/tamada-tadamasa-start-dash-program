<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Consts\ErrorType;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                // Validation Exception
                if ($e instanceof ValidationException) {
                    return $this->getBasicResponse(ErrorType::CODE_INVALID_PARAMETERS, $e->errors(), ErrorType::STATUS_INVALID_PARAMETERS);
                }

                // ModelNotFoundException
                if ($e instanceof ModelNotFoundException) {
                    return $this->getBasicResponse(ErrorType::CODE_NOT_FOUND, __('errors.MSG_NOT_FOUND'), ErrorType::STATUS_NOT_FOUND);
                }

                // HTTP Exception
                if ($this->isHttpException($e)) {
                    return $this->getBasicResponse((string) $e->getStatusCode(), $e->getMessage(), $e->getStatusCode());
                }

                return $this->getBasicResponse(ErrorType::CODE_INTERNAL_ERROR, $e->getMessage(), ErrorType::STATUS_INTERNAL_ERROR);
            }
        });
    }

    private function getBasicResponse(string $code, string|array $message, int $status): JsonResponse
    {
        $response = [
            'error' => [
                'code' => $code,
                'message' => $message,
            ],
        ];

        return response()->json($response, $status);
    }
}
