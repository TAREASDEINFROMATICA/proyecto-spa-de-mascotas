<?php
namespace App\Http\Controllers;

use App\Models\CategoriaProducto;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class CategoriaProductoController extends Controller
{
    private function getUserFromToken(Request $request)
    {
        $token = $request->query('token') ?? $request->input('token');
        if (!$token) return null;
        $token = trim($token, "'\"");
        $tokenRecord = PersonalAccessToken::findToken($token);
        if (!$tokenRecord) return null;
        return \App\Models\Usuario::find($tokenRecord->tokenable_id);
    }

    private function checkAdmin($user)
    {
        return $user && $user->rol->nombre === 'Administrador';
    }

    // =========================================================
    // LISTAR CATEGORÍAS
    // =========================================================
    public function index(Request $request)
    {
        $user = $this->getUserFromToken($request);
        if (!$this->checkAdmin($user)) {
            return redirect('/');
        }
        
        $categorias = CategoriaProducto::orderBy('nombre')->paginate(15);
        $token = $request->query('token');
        
        return view('admin.categorias.index', compact('categorias', 'token'));
    }

    // =========================================================
    // CREAR CATEGORÍA
    // =========================================================
    public function create(Request $request)
    {
        $user = $this->getUserFromToken($request);
        if (!$this->checkAdmin($user)) {
            return redirect('/');
        }
        
        $token = $request->query('token');
        return view('admin.categorias.create', compact('token'));
    }

    public function store(Request $request)
    {
        $user = $this->getUserFromToken($request);
        if (!$this->checkAdmin($user)) {
            return redirect('/');
        }
        
        $request->validate([
            'nombre' => 'required|max:80|unique:categorias_producto,nombre',
            'descripcion' => 'nullable|max:150'
        ]);
        
        CategoriaProducto::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion
        ]);
        
        return redirect()->route('admin.categorias.index', ['token' => $request->query('token')])
            ->with('success', '✅ Categoría creada correctamente');
    }

    // =========================================================
    // EDITAR CATEGORÍA
    // =========================================================
    public function edit(Request $request, $id)
    {
        $user = $this->getUserFromToken($request);
        if (!$this->checkAdmin($user)) {
            return redirect('/');
        }
        
        $categoria = CategoriaProducto::findOrFail($id);
        $token = $request->query('token');
        
        return view('admin.categorias.edit', compact('categoria', 'token'));
    }

    public function update(Request $request, $id)
    {
        $user = $this->getUserFromToken($request);
        if (!$this->checkAdmin($user)) {
            return redirect('/');
        }
        
        $categoria = CategoriaProducto::findOrFail($id);
        
        $request->validate([
            'nombre' => 'required|max:80|unique:categorias_producto,nombre,' . $id . ',id_categoria',
            'descripcion' => 'nullable|max:150'
        ]);
        
        $categoria->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion
        ]);
        
        return redirect()->route('admin.categorias.index', ['token' => $request->query('token')])
            ->with('success', '✅ Categoría actualizada correctamente');
    }

    // =========================================================
    // ELIMINAR CATEGORÍA
    // =========================================================
    public function destroy(Request $request, $id)
    {
        $user = $this->getUserFromToken($request);
        if (!$this->checkAdmin($user)) {
            return redirect('/');
        }
        
        $categoria = CategoriaProducto::findOrFail($id);
        
        // Verificar si tiene productos asociados
        if ($categoria->productos()->count() > 0) {
            return redirect()->route('admin.categorias.index', ['token' => $request->query('token')])
                ->with('error', '⚠️ No se puede eliminar la categoría porque tiene productos asociados');
        }
        
        $categoria->delete();
        
        return redirect()->route('admin.categorias.index', ['token' => $request->query('token')])
            ->with('success', '✅ Categoría eliminada correctamente');
    }
}