<?php

declare(strict_types=1);

namespace App\DDD\Shared\Infrastructure\Http\Exception;

use App\DDD\Authorization\Domain\Exception\AccessDeniedException;
use App\DDD\Backoffice\ClockIn\Domain\Exception\ClockInNotFoundException;
use App\DDD\Backoffice\Employee\Domain\Exception\EmployeeNotFoundException;
use App\DDD\Backoffice\Employee\Domain\Exception\InvalidEmployeeEmailException;
use App\DDD\Backoffice\Employee\Domain\Exception\InvalidEmployeeNameException;
use App\DDD\Backoffice\Employee\Domain\Exception\InvalidEmployeeNidException;
use App\DDD\Backoffice\Employee\Domain\Exception\InvalidEmployeePhoneException;
use App\DDD\Backoffice\Workplace\Domain\Exception\GeofenceViolationException;
use App\DDD\Backoffice\Workplace\Domain\Exception\InvalidCoordinatesException;
use App\DDD\Backoffice\Workplace\Domain\Exception\InvalidWorkplaceRadiusException;
use App\DDD\Backoffice\Workplace\Domain\Exception\WorkplaceNotFoundException;
use App\DDD\Shared\Domain\Exception\DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

/**
 * Convierte excepciones de dominio a respuestas HTTP apropiadas
 */
final class DomainExceptionHandler
{
    /**
     * Mapa de excepciones a códigos HTTP
     */
    private const HTTP_CODES = [

        ClockInNotFoundException::class => 404,
        EmployeeNotFoundException::class => 404,
        WorkplaceNotFoundException::class => 404,

        AccessDeniedException::class => 403,

        InvalidEmployeeEmailException::class => 422,
        InvalidEmployeeNameException::class => 422,
        InvalidEmployeeNidException::class => 422,
        InvalidEmployeePhoneException::class => 422,
        InvalidCoordinatesException::class => 422,
        InvalidWorkplaceRadiusException::class => 422,

        GeofenceViolationException::class => 409,
    ];

    /**
     * Mapa de excepciones a tipos de error
     */
    private const ERROR_TYPES = [
        ClockInNotFoundException::class => 'clock_in_not_found',
        EmployeeNotFoundException::class => 'employee_not_found',
        WorkplaceNotFoundException::class => 'workplace_not_found',
        AccessDeniedException::class => 'access_denied',
        InvalidEmployeeEmailException::class => 'invalid_email',
        InvalidEmployeeNameException::class => 'invalid_name',
        InvalidEmployeeNidException::class => 'invalid_nid',
        InvalidEmployeePhoneException::class => 'invalid_phone',
        InvalidCoordinatesException::class => 'invalid_coordinates',
        InvalidWorkplaceRadiusException::class => 'invalid_radius',
        GeofenceViolationException::class => 'geofence_violation',
    ];

    public function handle(Throwable $exception, Request $request): ?JsonResponse
    {
        if (!$exception instanceof DomainException) {
            return null;
        }

        $exceptionClass = get_class($exception);
        $httpCode = self::HTTP_CODES[$exceptionClass] ?? 400;
        $errorType = self::ERROR_TYPES[$exceptionClass] ?? 'domain_error';

        return new JsonResponse([
            'error' => $errorType,
            'message' => $exception->getMessage(),
            'code' => $httpCode,
        ], $httpCode);
    }

    /**
     * Registra el handler en el Exception Handler de Laravel
     */
    public static function register(\Illuminate\Foundation\Exceptions\Handler $handler): void
    {
        $handler->renderable(function (DomainException $e, Request $request) {
            if ($request->expectsJson()) {
                return (new self())->handle($e, $request);
            }

            return null;
        });
    }
}
