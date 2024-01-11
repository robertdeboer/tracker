<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

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
        $this->reportable(
            function (\Illuminate\Database\QueryException $e) {
                if(str_contains($e->getMessage(), '1062 Duplicate entry')) {
                    throw new DataException('Another item like this already exist.', $e->getMessage());
                }
                if (str_contains($e->getMessage(), 'SQLSTATE[23000]: Integrity constraint violation')) {
                    throw new DataException('This item is in use.', $e->getMessage());
                }
            }
        );

        $this->reportable(
            function (\Illuminate\Database\UniqueConstraintViolationException $e) {
                throw new DataException('Another item like this already exist.', $e->getMessage());
            }
        );
    }
}
