<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombres' => 'required|string|max:80',
            'apellidos' => 'required|string|max:80',
            'correo' => 'required|email|unique:usuarios,correo',
            'telefono' => 'required|string|max:20',
            'direccion' => 'nullable|string|max:200',
            'contrasena' => [
                'required',
                'string',
                Password::min(8)
                    ->mixedCase()      // Mayúsculas y minúsculas
                    ->letters()        // Letras
                    ->numbers()        // Números
                    ->symbols()        // Símbolos
                    ->uncompromised(), // No ha sido vulnerada
            ],
            'ci' => 'nullable|string|max:20',
        ];
    }

    public function messages(): array
    {
        return [
            'contrasena.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'contrasena.mixed' => 'La contraseña debe combinar mayúsculas y minúsculas.',
            'contrasena.letters' => 'La contraseña debe contener letras.',
            'contrasena.numbers' => 'La contraseña debe contener al menos un número.',
            'contrasena.symbols' => 'La contraseña debe contener al menos un símbolo (@$!%*#?&).',
            'contrasena.uncompromised' => 'Esta contraseña ha sido filtrada en alguna brecha de seguridad, por favor elige otra.',
            'correo.unique' => 'Este correo ya está registrado.',
        ];
    }
}