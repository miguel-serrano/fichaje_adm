<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\ClockIn\Infrastructure\Http\Request;

use App\DDD\Backoffice\ClockIn\Domain\ValueObject\ClockInType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CreateClockInRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // La autorización se maneja en el Handler
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'type' => ['required', 'string', Rule::enum(ClockInType::class)],
            'timestamp' => ['required', 'date'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'workplace_id' => ['nullable', 'integer', 'exists:workplaces,id'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'employee_id.required' => 'El empleado es obligatorio.',
            'employee_id.exists' => 'El empleado no existe.',
            'type.required' => 'El tipo de fichaje es obligatorio.',
            'type.enum' => 'El tipo de fichaje no es válido.',
            'timestamp.required' => 'La fecha y hora son obligatorias.',
            'latitude.required' => 'La ubicación es obligatoria.',
            'longitude.required' => 'La ubicación es obligatoria.',
            'latitude.between' => 'La latitud no es válida.',
            'longitude.between' => 'La longitud no es válida.',
        ];
    }
}
