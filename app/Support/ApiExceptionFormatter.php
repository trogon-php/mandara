<?php

namespace App\Support;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;

class ApiExceptionFormatter
{
    public static function format(Throwable $e): \Illuminate\Http\JsonResponse
    {
        $statusCode = 500;
        $message = 'Internal server error';
        $errors = [];

        if ($e instanceof ValidationException) {
            $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;
            $message = 'Validation error';
            $errors = $e->errors();
        } elseif ($e instanceof ModelNotFoundException) {
            $statusCode = Response::HTTP_NOT_FOUND;
            $message = 'Resource not found';
        } elseif ($e instanceof AuthenticationException) {
            $statusCode = Response::HTTP_UNAUTHORIZED;
            $message = 'Unauthenticated';
        } elseif ($e instanceof MethodNotAllowedHttpException) {
            $statusCode = Response::HTTP_METHOD_NOT_ALLOWED;
            $message = 'Method not allowed';
        } elseif ($e instanceof HttpExceptionInterface) {
            $statusCode = $e->getStatusCode();
            $message = self::defaultMessage($statusCode);
        } else {
            $message = config('app.debug')
                ? $e->getMessage()
                : 'Internal server error';
        }

        return response()->json([
            'status'      => false,
            'http_code' => $statusCode,
            'message'     => $message,
            'errors'      => $errors,
            'data'        => (object) [],
            'meta'        => (object) [],
        ], $statusCode);
    }

    protected static function defaultMessage(int $statusCode): string
    {
        return match ($statusCode) {
            400 => 'Bad request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not found',
            405 => 'Method not allowed',
            410 => 'Gone',
            422 => 'Validation error',
            429 => 'Too many requests',
            500 => 'Internal server error',
            default => 'Unexpected error',
        };
    }
}
