<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

/**
 * Exception handler.
 * All API exception rendering is handled inline in bootstrap/app.php
 * using Laravel 12's withExceptions() closure — this file is kept
 * for compatibility but adds no custom logic.
 */
class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
