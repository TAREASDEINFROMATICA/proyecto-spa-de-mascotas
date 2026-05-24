<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Mail;
use App\Mail\CredencialesEmpleadoMail;
use App\Services\AuditLogService;
use App\Models\Usuario;
use App\Models\Empleado;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class EmpleadoController extends Controller
{
    // Obtener usuario desde token
    private function getUserFromToken(Request $request)
    {
        $token = $request->query('token') ?? $request->input('token');
        if (!$token) return null;
        $token = trim($token, "'\"");
        $tokenRecord = PersonalAccessToken::findToken($token);
        if (!$tokenRecord) return null;
        return Usuario::find($tokenRecord->tokenable_id);
    }

    // Listar empleados
    public function index(Request $request)
    {
        $empleados = Empleado::with('usuario')->get();
        $token = $request->query('token');
        return view('admin.empleados.index', compact('empleados', 'token'));
    }

    // Formulario de creación
    public function create(Request $request)
    {
        $token = $request->query('token');
        return view('admin.empleados.create', compact('token'));
    }

    // Guardar empleado
    public function store(Request $request)
    {
        $user = $this->getUserFromToken($request);
        
        // Validación base
        $rules = [
            'nombres' => [
                'required', 
                'string', 
                'max:80', 
                'regex:/^[\pL\sáéíóúüñÁÉÍÓÚÜÑ]+$/u'
            ],
            'apellidos' => [
                'required', 
                'string', 
                'max:80', 
                'regex:/^[\pL\sáéíóúüñÁÉÍÓÚÜÑ]+$/u'
            ],
            'correo' => 'required|email|unique:usuarios,correo',
            'telefono' => [
                'required', 
                'string', 
                'regex:/^[0-9]{8,15}$/'
            ],
            'ci' => [
                'nullable', 
                'string', 
                'regex:/^[0-9]{6,12}$/',
                'unique:usuarios,ci'
            ],
            'cargo' => 'required|in:Recepcion,Groomer',
            'contrasena' => 'required|string|min:8|confirmed',
        ];
        
        // Si es Groomer, validar campos adicionales
        if ($request->cargo === 'Groomer') {
            $rules['especialidad'] = [
                'nullable', 
                'string', 
                'max:80', 
                'regex:/^[\pL\s0-9]+$/u'
            ];
            $rules['capacidad_simultanea'] = 'nullable|integer|min:1|max:10';
            $rules['capacidad_diaria'] = 'required|integer|min:1|max:20';
            $rules['turno'] = 'nullable|string|in:Mañana,Tarde,Noche,Completo';
        }
        
        $request->validate($rules, [
            'nombres.regex' => 'El nombre solo puede contener letras y espacios.',
            'apellidos.regex' => 'Los apellidos solo pueden contener letras y espacios.',
            'telefono.regex' => 'El teléfono solo puede contener números (8 a 15 dígitos).',
            'ci.regex' => 'La cédula solo puede contener números (6 a 12 dígitos).',
            'especialidad.regex' => 'La especialidad solo puede contener letras, números y espacios.',
            'contrasena.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'contrasena.confirmed' => 'La confirmación de contraseña no coincide.',
            'correo.unique' => 'Este correo ya está registrado.',
            'capacidad_diaria.min' => 'La capacidad diaria debe ser al menos 1.',
            'capacidad_diaria.max' => 'La capacidad diaria no puede ser mayor a 20.',
        ]);

        DB::beginTransaction();
        
        try {
            $rol = Rol::where('nombre', $request->cargo)->first();
            
            if (!$rol) {
                return back()->with('error', 'Rol no encontrado');
            }
            
            $usuario = Usuario::create([
                'id_rol' => $rol->id_rol,
                'nombres' => $request->nombres,
                'apellidos' => $request->apellidos,
                'correo' => $request->correo,
                'ci' => $request->ci, 
                'contrasena_hash' => Hash::make($request->contrasena),
                'telefono' => $request->telefono,
                'estado' => 'activo',
                'email_verified_at' => Carbon::now(),
                'fecha_registro' => Carbon::now(),
            ]);
            
            // Datos base del empleado
            $empleadoData = [
                'id_usuario' => $usuario->id_usuario,
                'cargo' => $request->cargo,
                'fecha_ingreso' => Carbon::now(),
            ];
            
            // Si es Groomer, agregar campos adicionales
            if ($request->cargo === 'Groomer') {
                $empleadoData['especialidad'] = $request->especialidad;
                $empleadoData['capacidad_simultanea'] = $request->capacidad_simultanea ?? 1;
                $empleadoData['capacidad_diaria'] = $request->capacidad_diaria;
                $empleadoData['turno'] = $request->turno;
            } else {
                // Valores por defecto para Recepción
                $empleadoData['especialidad'] = null;
                $empleadoData['capacidad_simultanea'] = 1;
                $empleadoData['capacidad_diaria'] = 8;
                $empleadoData['turno'] = null;
            }
            
            Empleado::create($empleadoData);
            
            // Enviar email con credenciales
            Mail::to($usuario->correo)->send(new CredencialesEmpleadoMail($usuario, $request->contrasena));
            
            // Log
            AuditLogService::registrar(
                $user ? $user->id_usuario : null,
                'Creó empleado: ' . $request->nombres . ' ' . $request->apellidos . ' (Cargo: ' . $request->cargo . ')',
                $request
            );
            
            DB::commit();
            
            return redirect()->route('empleados.index', ['token' => $request->query('token')])
                ->with('success', 'Empleado creado. Se enviaron las credenciales a su correo.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // Formulario de edición
    public function edit(Request $request, $id)
    {
        $empleado = Empleado::with('usuario')->findOrFail($id);
        $token = $request->query('token');
        return view('admin.empleados.edit', compact('empleado', 'token'));
    }

    // Actualizar empleado
    public function update(Request $request, $id)
    {
        $empleado = Empleado::findOrFail($id);
        $usuario = $empleado->usuario;
        
        // Validación base
        $rules = [
            'nombres' => 'required|string|max:80',
            'apellidos' => 'required|string|max:80',
            'telefono' => 'required|string|max:20',
            'ci' => 'nullable|string|max:20|unique:usuarios,ci,' . $usuario->id_usuario . ',id_usuario',
        ];
        
        // Si es Groomer, validar campos adicionales
        if ($empleado->cargo === 'Groomer') {
            $rules['especialidad'] = 'nullable|string|max:80';
            $rules['capacidad_simultanea'] = 'nullable|integer|min:1|max:10';
            $rules['capacidad_diaria'] = 'required|integer|min:1|max:20';
            $rules['turno'] = 'nullable|string|in:Mañana,Tarde,Noche,Completo';
        }
        
        $request->validate($rules);
        
        DB::beginTransaction();
        
        try {
            $usuario->update([
                'nombres' => $request->nombres,
                'apellidos' => $request->apellidos,
                'ci' => $request->ci,
                'telefono' => $request->telefono,
            ]);
            
            // Datos a actualizar
            $empleadoData = [];
            
            // Si es Groomer, actualizar campos adicionales
            if ($empleado->cargo === 'Groomer') {
                $empleadoData['especialidad'] = $request->especialidad;
                $empleadoData['capacidad_simultanea'] = $request->capacidad_simultanea ?? 1;
                $empleadoData['capacidad_diaria'] = $request->capacidad_diaria;
                $empleadoData['turno'] = $request->turno;
            }
            
            if (!empty($empleadoData)) {
                $empleado->update($empleadoData);
            }
            
            // LOG: Editar empleado
            $user = $request->user();
            AuditLogService::registrar(
                $user ? $user->id_usuario : null,
                'Editó empleado ID: ' . $empleado->id_empleado . ' - ' . $usuario->nombres . ' ' . $usuario->apellidos,
                $request
            );
            
            DB::commit();
            
            return redirect()->route('empleados.index', ['token' => $request->query('token')])
                ->with('success', 'Empleado actualizado');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // Desactivar empleado (cambiar estado a inactivo)
    public function destroy(Request $request, $id)
    {
        $empleado = Empleado::findOrFail($id);
        $usuario = $empleado->usuario;
        
        DB::beginTransaction();
        
        try {
            $usuario->update(['estado' => 'inactivo']);
            
            // LOG: Desactivar empleado
            $user = $this->getUserFromToken($request);
            AuditLogService::registrar(
                $user ? $user->id_usuario : null,
                'Desactivó empleado ID: ' . $empleado->id_empleado . ' - ' . $usuario->nombres . ' ' . $usuario->apellidos,
                $request
            );
            
            DB::commit();
            
            return redirect()->route('empleados.index', ['token' => $request->query('token')])
                ->with('success', 'Empleado desactivado');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
    // Activar empleado (cambiar estado a activo)
    public function activate(Request $request, $id)
    {
        $empleado = Empleado::findOrFail($id);
        $usuario = $empleado->usuario;
        
        DB::beginTransaction();
        
        try {
            $usuario->update(['estado' => 'activo']);
            
            // LOG: Activar empleado
            $user = $this->getUserFromToken($request);
            AuditLogService::registrar(
                $user ? $user->id_usuario : null,
                'Activó empleado ID: ' . $empleado->id_empleado . ' - ' . $usuario->nombres . ' ' . $usuario->apellidos,
                $request
            );
            
            DB::commit();
            
            return redirect()->route('empleados.index', ['token' => $request->query('token')])
                ->with('success', 'Empleado activado correctamente');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}