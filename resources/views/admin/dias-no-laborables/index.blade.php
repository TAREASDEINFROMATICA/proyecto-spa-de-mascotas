<!DOCTYPE html>
<html>
<head>
    <title>Días No Laborables - Admin</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Inter', sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 100vh; 
            padding: 40px 20px; 
        }
        .container { max-width: 1200px; margin: 0 auto; }
        .card { background: white; border-radius: 24px; overflow: hidden; box-shadow: 0 20px 35px rgba(0,0,0,0.1); }
        .card-header { background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; padding: 20px 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
        .card-header h2 { display: flex; align-items: center; gap: 10px; font-size: 22px; }
        .btn-add { background: #4CAF50; color: white; padding: 10px 20px; border-radius: 40px; text-decoration: none; font-weight: 500; transition: 0.3s; display: inline-flex; align-items: center; gap: 8px; }
        .btn-add:hover { background: #45a049; transform: translateY(-2px); }
        .btn-back { background: #607d8b; color: white; padding: 10px 20px; border-radius: 40px; text-decoration: none; }
        .content { padding: 30px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 14px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { background: #f8fafc; font-weight: 600; color: #1e293b; }
        .badge { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 500; display: inline-block; }
        .badge-feriado { background: #ffebee; color: #c62828; }
        .badge-mantenimiento { background: #fff3e0; color: #ef6c00; }
        .badge-ausencia { background: #e3f2fd; color: #1565c0; }
        .badge-descanso { background: #e8f5e9; color: #2e7d32; }
        .btn-icon { background: none; border: none; cursor: pointer; font-size: 18px; margin: 0 5px; transition: 0.2s; }
        .btn-icon:hover { transform: scale(1.1); }
        .btn-edit { color: #2196f3; }
        .btn-delete { color: #f44336; }
        .btn-toggle { color: #ff9800; }
        .pagination { margin-top: 20px; display: flex; justify-content: center; }
        .alert-success { background: #d4edda; color: #155724; padding: 12px 20px; border-radius: 12px; margin-bottom: 20px; border-left: 4px solid #28a745; }
        .empty-state { text-align: center; padding: 50px; color: #64748b; }
        .empty-state i { font-size: 60px; margin-bottom: 15px; opacity: 0.5; }
    </style>
</head>
<body>
    @php $token = request()->query('token'); @endphp
    
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-calendar-times"></i> Días No Laborables</h2>
                <div style="display: flex; gap: 10px;">
                    <a href="/admin/dias-no-laborables/create?token={{ $token }}" class="btn-add">
                        <i class="fas fa-plus"></i> Agregar Día
                    </a>
                    <a href="/admin/dashboard?token={{ $token }}" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
            
            <div class="content">
                @if(session('success'))
                    <div class="alert-success">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                    </div>
                @endif
                
                @if($dias->count() > 0)
                    <table>
                        <thead>
                            <tr>
                                <th>📅 Fecha</th>
                                <th>🏷️ Tipo</th>
                                <th>📝 Motivo</th>
                                <th>⚡ Estado</th>
                                <th style="width: 120px">🔧 Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dias as $dia)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($dia->fecha)->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge badge-{{ $dia->tipo }}">
                                        {{ $tipos[$dia->tipo] }}
                                    </span>
                                </td>
                                <td>{{ $dia->motivo ?? '—' }}</td>
                                <td>
                                    <span style="color: '{{ $dia->estado == 'activo' ? '#4CAF50' : '#f44336' }}'">
                                        {{ $dia->estado == 'activo' ? '✅ Activo' : '❌ Inactivo' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="/admin/dias-no-laborables/{{ $dia->id_dia_no_laborable }}/edit?token={{ $token }}" class="btn-icon btn-edit" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="eliminar('{{ $dia->id_dia_no_laborable }}')" class="btn-icon btn-delete" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <button onclick="toggleEstado('{{ $dia->id_dia_no_laborable }}')" class="btn-icon btn-toggle" title="Cambiar estado">
                                        <i class="fas fa-power-off"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    <div class="pagination">
                        {{ $dias->appends(['token' => $token])->links() }}
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-calendar-check"></i>
                        <h3>No hay días configurados</h3>
                        <p>Agrega feriados, mantenimientos o días de descanso</p>
                        <a href="/admin/dias-no-laborables/create?token={{ $token }}" class="btn-add" style="display: inline-block; margin-top: 15px;">
                            <i class="fas fa-plus"></i> Agregar primer día
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function eliminar(id) {
            if(confirm('¿Eliminar este día no laborable?')) {
                $.ajax({
                    url: '/admin/dias-no-laborables/' + id,
                    method: 'DELETE',
                    data: { token: '{{ $token }}', _token: '{{ csrf_token() }}' },
                    success: function(res) {
                        if(res.success) location.reload();
                        else alert('Error: ' + res.message);
                    }
                });
            }
        }
        
        function toggleEstado(id) {
            $.ajax({
                url: '/admin/dias-no-laborables/' + id + '/toggle',
                method: 'POST',
                data: { token: '{{ $token }}', _token: '{{ csrf_token() }}' },
                success: function(res) {
                    if(res.success) location.reload();
                    else alert('Error: ' + res.message);
                }
            });
        }
    </script>
</body>
</html>