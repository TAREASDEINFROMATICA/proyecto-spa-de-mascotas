<!DOCTYPE html>
<html>
<head>
    <title>Registrar Mascota - Pet Spa</title>
    <style>
        body { font-family: Arial; margin: 50px; background: #fff3e0; }
        .container { max-width: 500px; margin: auto; background: white; padding: 30px; border-radius: 10px; }
        input, select, textarea { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ddd; border-radius: 5px; }
        button { background: #4CAF50; color: white; padding: 10px; border: none; border-radius: 5px; cursor: pointer; }
        .error { color: red; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🐾 Registrar Nueva Mascota</h2>
        
        @php
            $token = request()->query('token');
        @endphp
        
        @if($errors->any())
            <div class="error">
                @foreach($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif
        
        <form method="POST" action="/cliente/mascotas?token={{ $token }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            
            @if(isset($clientes) && count($clientes) > 0 && $clientes->first() && $clientes->first() instanceof \App\Models\Cliente)
                @php
                    $primerCliente = $clientes->first();
                    $esAdmin = $primerCliente && $primerCliente->usuario && $primerCliente->usuario->esAdmin();
                @endphp
                
                @if($esAdmin)
                <select name="id_cliente" required>
                    <option value="">Seleccionar dueño</option>
                    @foreach($clientes as $c)
                        <option value="{{ $c->id_cliente }}">{{ $c->usuario->nombres }} {{ $c->usuario->apellidos }}</option>
                    @endforeach
                </select>
                @else
                <input type="hidden" name="id_cliente" value="{{ $clientes->first()->id_cliente }}">
                @endif
            @else
                <input type="hidden" name="id_cliente" value="">
            @endif
            
            <input type="text" name="nombre" placeholder="Nombre de la mascota *" required>
            
            <select name="especie" required>
                <option value="">Seleccionar especie</option>
                @foreach($especies as $e)
                    <option value="{{ $e }}">{{ $e }}</option>
                @endforeach
            </select>
            
            <input type="text" name="raza" placeholder="Raza">
            
            <select name="sexo">
                <option value="">Seleccionar sexo</option>
                <option value="Macho">Macho</option>
                <option value="Hembra">Hembra</option>
            </select>
            
            <input type="date" name="fecha_nacimiento" placeholder="Fecha de nacimiento">
            <input type="number" step="0.1" name="peso" placeholder="Peso (kg)">
            <input type="text" name="color" placeholder="Color">
            
            <select name="temperamento_general">
                <option value="">Seleccionar temperamento</option>
                @foreach($temperamentos as $t)
                    <option value="{{ $t }}">{{ ucfirst($t) }}</option>
                @endforeach
            </select>
            
            <textarea name="alergias" placeholder="Alergias"></textarea>
            <textarea name="cuidados_especiales" placeholder="Cuidados especiales"></textarea>
            <textarea name="observaciones" placeholder="Observaciones"></textarea>
            
            <input type="file" name="foto" accept="image/*">
            
            <button type="submit">✅ Registrar Mascota</button>
        </form>
        <div style="background: #eee; padding: 5px; margin-bottom: 10px;">
    Token: {{ request()->query('token') }}
</div>
        <br>
        <a href="/cliente/mascotas?token={{ $token }}">← Volver</a>
    </div>
</body>
</html>