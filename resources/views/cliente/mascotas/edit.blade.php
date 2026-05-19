<!DOCTYPE html>
<html>
<head>
    <title>Editar {{ $mascota->nombre }} - Pet Spa</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Inter', sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 100vh; 
            padding: 50px 20px; 
        }
        .container { 
            max-width: 700px; 
            margin: 0 auto; 
            background: white; 
            border-radius: 28px; 
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); 
            overflow: hidden; 
        }
        
        /* Header */
        .page-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 25px 30px;
        }
        .page-header h2 {
            font-size: 24px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0;
        }
        .page-header h2 i { font-size: 28px; color: #ffd700; }
        
        /* Contenido */
        .content { padding: 30px; }
        
        /* Campos */
        .form-group {
            margin-bottom: 18px;
        }
        label {
            display: block;
            font-weight: 600;
            font-size: 13px;
            color: #1e293b;
            margin-bottom: 5px;
        }
        label i {
            margin-right: 6px;
            color: #4CAF50;
            width: 18px;
        }
        input, select, textarea {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 14px;
            transition: all 0.3s;
            font-family: 'Inter', sans-serif;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 0 3px rgba(76,175,80,0.1);
        }
        textarea {
            resize: vertical;
            min-height: 80px;
        }
        
        /* Foto actual */
        .foto-actual {
            text-align: center;
            margin: 15px 0;
            padding: 15px;
            background: #f8fafc;
            border-radius: 16px;
        }
        .foto-actual label {
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 10px;
            display: block;
        }
        .foto-actual img {
            max-width: 150px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        
        /* Archivo */
        input[type="file"] {
            padding: 10px;
            background: #f8fafc;
        }
        
        /* Errores */
        .error {
            background: #ffebee;
            color: #c62828;
            padding: 14px;
            border-radius: 12px;
            margin-bottom: 20px;
            border-left: 4px solid #f44336;
            font-size: 14px;
        }
        
        /* Botón guardar */
        .btn-save {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            color: white;
            padding: 14px;
            border: none;
            border-radius: 14px;
            cursor: pointer;
            font-weight: 700;
            font-size: 16px;
            width: 100%;
            transition: all 0.3s;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(76,175,80,0.3);
        }
        
        /* Botón volver */
        .back-link {
            margin-top: 20px;
            text-align: center;
        }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #607d8b 0%, #455a64 100%);
            color: white;
            padding: 12px 24px;
            border-radius: 14px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            font-size: 14px;
        }
        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        /* Grid para campos pequeños */
        .row-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        @media (max-width: 600px) {
            .container { margin: 0 10px; border-radius: 20px; }
            .content { padding: 20px; }
            .row-2 { grid-template-columns: 1fr; gap: 0; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h2>
                <i class="fas fa-paw"></i>
                Editar {{ $mascota->nombre }}
            </h2>
        </div>
        
        <div class="content">
            @php $token = request()->query('token'); @endphp
            
            @if($errors->any())
                <div class="error">
                    <i class="fas fa-exclamation-circle"></i>
                    @foreach($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
            @endif
            
            <form method="POST" action="/cliente/mascotas/{{ $mascota->id_mascota }}?token={{ $token }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="token" value="{{ $token }}">
                
                <!-- Admin y Recepción pueden cambiar el dueño -->
                @if(isset($clientes) && $clientes)
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Dueño</label>
                    <select name="id_cliente" required>
                        <option value="">Seleccionar dueño</option>
                        @foreach($clientes as $c)
                            <option value="{{ $c->id_cliente }}" {{ $mascota->id_cliente == $c->id_cliente ? 'selected' : '' }}>
                                {{ $c->usuario->nombres }} {{ $c->usuario->apellidos }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
                
                <div class="form-group">
                    <label><i class="fas fa-tag"></i> Nombre *</label>
                    <input type="text" name="nombre" value="{{ old('nombre', $mascota->nombre) }}" placeholder="Nombre de la mascota" required>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-paw"></i> Especie *</label>
                    <select name="especie" required>
                        <option value="">Seleccionar especie</option>
                        @foreach($especies as $e)
                            <option value="{{ $e }}" {{ $mascota->especie == $e ? 'selected' : '' }}>{{ $e }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="row-2">
                    <div class="form-group">
                        <label><i class="fas fa-dna"></i> Raza</label>
                        <input type="text" name="raza" value="{{ old('raza', $mascota->raza) }}" placeholder="Raza">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-venus-mars"></i> Sexo</label>
                        <select name="sexo">
                            <option value="">Seleccionar sexo</option>
                            <option value="Macho" {{ $mascota->sexo == 'Macho' ? 'selected' : '' }}>🐕 Macho</option>
                            <option value="Hembra" {{ $mascota->sexo == 'Hembra' ? 'selected' : '' }}>🐕 Hembra</option>
                        </select>
                    </div>
                </div>
                
                <div class="row-2">
                    <div class="form-group">
                        <label><i class="fas fa-calendar"></i> Fecha nacimiento</label>
                        <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', $mascota->fecha_nacimiento) }}">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-weight-hanging"></i> Peso (kg)</label>
                        <input type="number" step="0.1" name="peso" value="{{ old('peso', $mascota->peso) }}" placeholder="Peso">
                    </div>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-palette"></i> Color</label>
                    <input type="text" name="color" value="{{ old('color', $mascota->color) }}" placeholder="Color">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-brain"></i> Temperamento</label>
                    <select name="temperamento_general">
                        <option value="">Seleccionar temperamento</option>
                        @foreach($temperamentos as $t)
                            <option value="{{ $t }}" {{ $mascota->temperamento_general == $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-allergies"></i> Alergias</label>
                    <textarea name="alergias" placeholder="Alergias">{{ old('alergias', $mascota->alergias) }}</textarea>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-heartbeat"></i> Cuidados especiales</label>
                    <textarea name="cuidados_especiales" placeholder="Cuidados especiales">{{ old('cuidados_especiales', $mascota->cuidados_especiales) }}</textarea>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-sticky-note"></i> Observaciones</label>
                    <textarea name="observaciones" placeholder="Observaciones">{{ old('observaciones', $mascota->observaciones) }}</textarea>
                </div>
                
                @if($mascota->foto)
                    <div class="foto-actual">
                        <label><i class="fas fa-image"></i> Foto actual</label>
                        <img src="{{ Storage::url($mascota->foto) }}" alt="{{ $mascota->nombre }}">
                    </div>
                @endif
                
                <div class="form-group">
                    <label><i class="fas fa-upload"></i> Nueva foto (opcional)</label>
                    <input type="file" name="foto" accept="image/*">
                </div>
                
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
            </form>
            
            <!-- Botón volver según el rol -->
            <div class="back-link">
                @if($rol == 'admin')
                    <a href="/admin/mascotas?token={{ $token }}" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Volver a Mascotas
                    </a>
                @elseif($rol == 'recepcion')
                    <a href="/recepcion/mascotas?token={{ $token }}" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Volver a Mascotas
                    </a>
                @else
                    <a href="/cliente/mascotas/{{ $mascota->id_mascota }}?token={{ $token }}" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Volver a Ficha
                    </a>
                @endif
            </div>
        </div>
    </div>
</body>
</html>