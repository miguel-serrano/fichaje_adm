<?php

declare(strict_types=1);

namespace App\DDD\Backoffice\Employee\Infrastructure\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $employeeId = $this->route('id');

        return [
            'name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:employees,email,' . $employeeId],
            'phone' => ['nullable', 'string', 'max:20'],
            'nid' => ['nullable', 'string', 'max:20', 'unique:employees,nid,' . $employeeId],
            'code' => ['nullable', 'string', 'max:50', 'unique:employees,code,' . $employeeId],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'last_name.required' => 'Los apellidos son obligatorios.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El email no es válido.',
            'email.unique' => 'Este email ya está registrado.',
            'nid.unique' => 'Este DNI/NIF ya está registrado.',
            'code.unique' => 'Este código ya está en uso.',
        ];
    }
}
