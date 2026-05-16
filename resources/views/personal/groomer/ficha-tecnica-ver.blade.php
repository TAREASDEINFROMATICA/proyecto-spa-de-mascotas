<!DOCTYPE html>
<html>
<head>
    <title>Ficha Técnica - {{ $cita->mascota->nombre }} (Ver)</title>
    <style>
        body { font-family: Arial; margin: 30px; background: #e8f5e9; }
        .container { max-width: 900px; margin: auto; background: white; padding: 20px; border-radius: 10px; }
        .seccion { background: #f5f5f5; padding: 15px; margin: 15px 0; border-radius: 8px; }
        h2 { color: #4CAF50; }
        h3 { color: #2196F3; }
        .fotos { display: flex; gap: 20px; flex-wrap: wrap; }
        .foto-card { text-align: center; }
        .foto-card img { width: 200px; height: 150px; object-fit: cover; border-radius: 8px; margin: 5px; }
        .btn { background: #607d8b; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 5px; text-decoration: none; display: inline-block; }
        .alerta-exito { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
        .info-linea { margin: 8px 0; }
        .info-label { font-weight: bold; display: inline-block; width: 180px; }
        .checklist-realizado { color: green; }
        .checklist-pendiente { color: #999; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📋 Ficha Técnica (Solo Lectura)</h1>
        
        @php $token = request()->query('token'); @endphp
        
        <div class="alerta-exito">
            ✅ Servicio finalizado - Esta ficha es de solo lectura
        </div>
        
        <!-- Datos de la mascota -->
        <div class="seccion">
            <h2>🐕 Datos de la Mascota</h2>
            <div class="info-linea"><span class="info-label">Nombre:</span> {{ $cita->mascota->nombre }}</div>
            <div class="info-linea"><span class="info-label">Especie:</span> {{ $cita->mascota->especie }}</div>
            <div class="info-linea"><span class="info-label">Raza:</span> {{ $cita->mascota->raza ?? '-' }}</div>
            <div class="info-linea"><span class="info-label">Edad:</span> {{ $cita->mascota->fecha_nacimiento ? \Carbon\Carbon::parse($cita->mascota->fecha_nacimiento)->age . ' años' : '-' }}</div>
            <div class="info-linea"><span class="info-label">Temperamento:</span> {{ ucfirst($cita->mascota->temperamento_general ?? '-') }}</div>
            <div class="info-linea"><span class="info-label">Alergias:</span> {{ $cita->mascota->alergias ?? '-' }}</div>
            <div class="info-linea"><span class="info-label">Cuidados especiales:</span> {{ $cita->mascota->cuidados_especiales ?? '-' }}</div>
        </div>
        
        <!-- Datos del servicio -->
        <div class="seccion">
            <h2>✂️ Datos del Servicio</h2>
            <div class="info-linea"><span class="info-label">Servicio:</span> {{ $cita->servicio->nombre }}</div>
            <div class="info-linea"><span class="info-label">Duración:</span> {{ $cita->servicio->duracion_minutos }} minutos</div>
            <div class="info-linea"><span class="info-label">Hora:</span> {{ $cita->hora_inicio }} - {{ $cita->hora_fin }}</div>
        </div>
        
        <!-- Estado de ingreso -->
        <div class="seccion">
            <h2>📝 Estado de Ingreso</h2>
            <p>{{ $estadoIngreso ?? 'No registrado' }}</p>
        </div>
        
        <!-- Checklist realizado -->
        <div class="seccion">
            <h2>✅ Checklist Realizado</h2>
            @php
                $checklistItems = ['Baño', 'Corte de pelo', 'Corte de uñas', 'Limpieza de oídos', 'Cepillado dental', 'Perfume'];
            @endphp
            @foreach($checklistItems as $item)
                <div class="{{ in_array($item, $checklistRealizado) ? 'checklist-realizado' : 'checklist-pendiente' }}">
                    {{ in_array($item, $checklistRealizado) ? '✅' : '❌' }} {{ $item }}
                </div>
            @endforeach
        </div>
        
        <!-- Observaciones -->
        @if($observacionesExtra)
        <div class="seccion">
            <h2>📝 Observaciones</h2>
            <p>{{ $observacionesExtra }}</p>
        </div>
        @endif
        
        <!-- Recomendaciones -->
        @if($recomendaciones)
        <div class="seccion">
            <h2>💡 Recomendaciones para el Dueño</h2>
            <p>{{ $recomendaciones }}</p>
        </div>
        @endif
        
        <!-- Fotos -->
        <div class="seccion">
            <h2>📸 Galería de Fotos</h2>
            <div class="fotos">
                <div class="foto-card">
                    <h3>📷 Antes</h3>
                    @foreach($fotosAntes as $foto)
                        <img src="{{ Storage::url($foto->url) }}" style="max-width: 150px; margin: 5px;">
                    @endforeach
                    @if($fotosAntes->count() == 0)
                        <p>No hay fotos "Antes"</p>
                    @endif
                </div>
                <div class="foto-card">
                    <h3>✨ Después</h3>
                    @foreach($fotosDespues as $foto)
                        <img src="{{ Storage::url($foto->url) }}" style="max-width: 150px; margin: 5px;">
                    @endforeach
                    @if($fotosDespues->count() == 0)
                        <p>No hay fotos "Después"</p>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Fecha de cierre -->
        <div class="seccion">
            <h2>📅 Información de Cierre</h2>
            <div class="info-linea"><span class="info-label">Fecha de cierre:</span> {{ $cita->updated_at ? \Carbon\Carbon::parse($cita->updated_at)->format('d/m/Y H:i:s') : '-' }}</div>
        </div>
        
        <button class="btn" onclick="window.location.href='/groomer/mis-citas?token={{ $token }}'">← Volver a Mis Citas</button>
    </div>
</body>
</html>