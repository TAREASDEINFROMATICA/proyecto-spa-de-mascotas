<!DOCTYPE html>
<html>
<head>
    <title>Agregar Día No Laborable - Admin</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 40px 20px; }
        .container { max-width: 600px; margin: 0 auto; }
        .card { background: white; border-radius: 24px; overflow: hidden; box-shadow: 0 20px 35px rgba(0,0,0,0.1); }
        .card-header { background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; padding: 20px 30px; }
        .card-header h2 { display: flex; align-items: center; gap: 10px; }
        .content { padding: 30px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-weight: 600; margin-bottom: 8px; color: #1e293b; }
        input, select, textarea { width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 14px; transition: 0.3s; }
        input:focus, select:focus, textarea:focus { outline: none; border-color: #4CAF50; }
        .btn-save { background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); color: white; padding: 14px; border: none; border-radius: 12px; cursor: pointer; width: 100%; font-weight: 600; font-size: 16px; transition: 0.3s; }
        .btn-save:hover { transform: translateY(-2px); }
        .btn-back { background: #607d8b; color: white; padding: 10px 20px; border-radius: 40px; text-decoration: none; display: inline-block; margin-top: 15px; }
        .error { background: #ffebee; color: #c62828; padding: 12px; border-radius: 12px; margin-bottom: 20px; border-left: 4px solid #f44336; }
        small { display: block; margin-top: 5px; color: #64748b; font-size: 12px; }
    </style>
</head>
<body>
    @php $token = request()->query('token'); @endphp
    
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-plus-circle"></i> Agregar Día No Laborable</h2>
            </div>
            
            <div class="content">
                @if($errors->any())
                    <div class="error">
                        @foreach($errors->all() as $error)
                            {{ $error }}<br>
                        @endforeach
                    </div>
                @endif
                
                <form method="POST" action="/admin/dias-no-laborables?token={{ $token }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    
                    <div class="form-group">
                        <label><i class="fas fa-calendar"></i> Fecha *</label>
                        <input type="date" name="fecha" value="{{ old('fecha') }}" required>
                        <small>Selecciona el día que no será laborable</small>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-tag"></i> Tipo *</label>
                        <select name="tipo" required>
                            <option value="">Seleccionar tipo</option>
                            @foreach($tipos as $valor => $nombre)
                                <option value="{{ $valor }}">{{ $nombre }}</option>
                            @endforeach
                        </select>
                        <small>Clasifica el motivo del bloqueo</small>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-comment"></i> Motivo</label>
                        <textarea name="motivo" rows="3" placeholder="Ej: Día festivo nacional, mantenimiento eléctrico, etc.">{{ old('motivo') }}</textarea>
                        <small>Descripción opcional del motivo</small>
                    </div>
                    
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Guardar Día No Laborable
                    </button>
                </form>
                
                <div style="text-align: center; margin-top: 20px;">
                    <a href="/admin/dias-no-laborables?token={{ $token }}" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>