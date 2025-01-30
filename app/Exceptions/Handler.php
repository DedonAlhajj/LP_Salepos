<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
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
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        // تخصيص رسالة الخطأ في حالة AuthorizationException
        if ($exception instanceof AuthorizationException) {
            return redirect()->back()->with('not_permitted', __('Sorry! You are not allowed to access this module.'));
        }

        // استدعاء طريقة render الافتراضية لأي استثناء آخر
        return parent::render($request, $exception);
    }

}
