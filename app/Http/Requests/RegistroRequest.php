<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegistroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Todos pueden registrarse
    }

    public function rules(): array
    {
        return [
            'nombres' => 'required|string|max:80',
            'apellidos' => 'required|string|max:80',
            'correo' => 'required|email|unique:usuarios,correo', // Email único
               'ci' => 'nullable|string|max:20|unique:usuarios,ci', // CI único pero opcional
            'telefono' => 'required|string|max:20',
            'direccion' => 'nullable|string|max:200',
            'contrasena' => [
                'required',
                'string',
                Password::min(8)      // Mínimo 8 caracteres
                    ->mixedCase()     // Mayúsculas y minúsculas
                    ->letters()       // Letras
                    ->numbers()       // Números
                    ->symbols(),      // Símbolos
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'correo.unique' => 'Este correo ya está registrado',
            'ci.unique' => 'Esta cédula ya está registrada',
            'contrasena.min' => 'La contraseña debe tener al menos 8 caracteres',
            'contrasena.mixed' => 'La contraseña debe tener mayúsculas y minúsculas',
            'contrasena.numbers' => 'La contraseña debe tener al menos un número',
            'contrasena.symbols' => 'La contraseña debe tener al menos un símbolo (@$!%*#?&)',
        ];
    }
}