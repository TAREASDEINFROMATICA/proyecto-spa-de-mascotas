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

class EmpleadoController extends Controller
{
    // Listar empleados
    public function index()
    {
        $empleados = Empleado::with('usuario')->get();
        return view('admin.empleados.index', compact('empleados'));
    }

    // Formulario de creación
    public function create()
    {
        return view('admin.empleados.create');
    }

    // Guardar empleado
    public function store(Request $request)
{
    $request->validate([
        'nombres' => [
            'required', 
            'string', 
            'max:80', 
            'regex:/^[\pL\sáéíóúüñÁÉÍÓÚÜÑ]+$/u'  // Solo letras
        ],
        'apellidos' => [
            'required', 
            'string', 
            'max:80', 
            'regex:/^[\pL\sáéíóúüñÁÉÍÓÚÜÑ]+$/u'  // Solo letras
        ],
        'correo' => 'required|email|unique:usuarios,correo',
        'telefono' => [
            'required', 
            'string', 
            'regex:/^[0-9]{8,15}$/'  // Solo números, 8-15 dígitos
        ],
        'ci' => [
            'nullable', 
            'string', 
            'regex:/^[0-9]{6,12}$/',  // Solo números, 6-12 dígitos
            'unique:usuarios,ci'
        ],
        'cargo' => 'required|in:Recepcion,Groomer',
        'especialidad' => [
            'nullable', 
            'string', 
            'max:80', 
            'regex:/^[\pL\s0-9]+$/u'  // Letras, números y espacios
        ],
        'capacidad_simultanea' => 'nullable|integer|min:1|max:10',
        'turno' => 'nullable|string|in:Mañana,Tarde,Noche,Completo',
        'contrasena' => 'required|string|min:8|confirmed',
    ], [
        // Mensajes de error personalizados
        'nombres.regex' => 'El nombre solo puede contener letras y espacios.',
        'apellidos.regex' => 'Los apellidos solo pueden contener letras y espacios.',
        'telefono.regex' => 'El teléfono solo puede contener números (8 a 15 dígitos).',
        'ci.regex' => 'La cédula solo puede contener números (6 a 12 dígitos).',
        'especialidad.regex' => 'La especialidad solo puede contener letras, números y espacios.',
        'contrasena.min' => 'La contraseña debe tener al menos 8 caracteres.',
        'contrasena.confirmed' => 'La confirmación de contraseña no coincide.',
        'correo.unique' => 'Este correo ya está registrado.',
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
        
        Empleado::create([
            'id_usuario' => $usuario->id_usuario,
            'cargo' => $request->cargo,
            'especialidad' => $request->especialidad,
            'capacidad_simultanea' => $request->capacidad_simultanea ?? 1,
            'turno' => $request->turno,    
            'fecha_ingreso' => Carbon::now(),
        ]);
        
        // Enviar email con credenciales
        Mail::to($usuario->correo)->send(new \App\Mail\CredencialesEmpleadoMail($usuario, $request->contrasena));
        
      // Log
$user = Auth::user();
AuditLogService::registrar(
    $user ? $user->id_usuario : null,
    'Creó empleado: ' . $request->nombres . ' ' . $request->apellidos . ' (Cargo: ' . $request->cargo . ')',
    $request
);
        
        DB::commit();
        
        return redirect()->route('empleados.index')
            ->with('success', 'Empleado creado. Se enviaron las credenciales a su correo.');
            
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Error: ' . $e->getMessage());
    }
}

    // Formulario de edición
    public function edit($id)
    {
        $empleado = Empleado::with('usuario')->findOrFail($id);
        return view('admin.empleados.edit', compact('empleado'));
    }

    // Actualizar empleado
    public function update(Request $request, $id)
    {
        $empleado = Empleado::findOrFail($id);
        $usuario = $empleado->usuario;
        
        $request->validate([
            'nombres' => 'required|string|max:80',
            'apellidos' => 'required|string|max:80',
            'telefono' => 'required|string|max:20',
            'ci' => 'nullable|string|max:20|unique:usuarios,ci,' . $usuario->id_usuario . ',id_usuario',
            'especialidad' => 'nullable|string|max:80',
            'capacidad_simultanea' => 'nullable|integer|min:1|max:10',
            'turno' => 'nullable|string|in:Mañana,Tarde,Noche,Completo',
        ]);
        
        DB::beginTransaction();
        
        try {
            $usuario->update([
                'nombres' => $request->nombres,
                'apellidos' => $request->apellidos,
                'ci' => $request->ci,
                'telefono' => $request->telefono,
            ]);
            
            $empleado->update([
                'especialidad' => $request->especialidad,
                'capacidad_simultanea' => $request->capacidad_simultanea ?? 1,
                'turno' => $request->turno,
            ]);
            
            // LOG: Editar empleado
            $user = $request->user();
            AuditLogService::registrar(
                $user ? $user->id_usuario : null,
                'Editó empleado ID: ' . $empleado->id_empleado . ' - ' . $usuario->nombres . ' ' . $usuario->apellidos,
                $request
            );
            
            DB::commit();
            
            return redirect()->route('empleados.index')
                ->with('success', 'Empleado actualizado');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // Desactivar empleado (cambiar estado a inactivo)
    public function destroy($id)
    {
        $empleado = Empleado::findOrFail($id);
        $usuario = $empleado->usuario;
        
        DB::beginTransaction();
        
        try {
            $usuario->update(['estado' => 'inactivo']);
            
            // LOG: Desactivar empleado
            AuditLogService::registrar(
                auth()->id_usuario,
                'Desactivó empleado ID: ' . $empleado->id_empleado . ' - ' . $usuario->nombres . ' ' . $usuario->apellidos,
                request()
            );
            
            DB::commit();
            
            return redirect()->route('empleados.index')
                ->with('success', 'Empleado desactivado');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
    // Activar empleado (cambiar estado a activo)
    public function activate($id)
    {
        $empleado = Empleado::findOrFail($id);
        $usuario = $empleado->usuario;
        
        DB::beginTransaction();
        
        try {
            $usuario->update(['estado' => 'activo']);
            
            // LOG: Activar empleado
            AuditLogService::registrar(
                auth()->id_usuario,
                'Activó empleado ID: ' . $empleado->id_empleado . ' - ' . $usuario->nombres . ' ' . $usuario->apellidos,
                request()
            );
            
            DB::commit();
            
            return redirect()->route('empleados.index')
                ->with('success', 'Empleado activado correctamente');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}