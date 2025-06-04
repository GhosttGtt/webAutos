<?php
// Función para obtener las citas
function obtenerCitas($token) {
    $url = 'https://alexcg.de/autozone/api/citas.php';
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    curl_close($ch);
    
    $data = json_decode($response, true);
    return $data['data'] ?? [];
}

// Obtener las citas
$citas = obtenerCitas($_SESSION['token']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Citas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .cita-row {
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .cita-row:hover {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Citas de Clientes</h5>
                <div class="d-flex gap-2">
                    <input type="date" id="filtroFecha" class="form-control form-control-sm" style="width: auto;">
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Número de Personas</th>
                                <th>Fecha y Hora</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($citas as $cita): ?>
                                <tr class="cita-row" 
                                    data-cita='<?php echo htmlspecialchars(json_encode($cita)); ?>'
                                    data-fecha="<?php echo htmlspecialchars($cita['datetime']); ?>"
                                    onclick="mostrarAcciones(this)">
                                    <td><?php echo htmlspecialchars($cita['id']); ?></td>
                                    <td><?php echo htmlspecialchars($cita['name']); ?></td>
                                    <td><?php echo htmlspecialchars($cita['email']); ?></td>
                                    <td><?php echo htmlspecialchars($cita['personas']); ?></td>
                                    <td><?php echo htmlspecialchars($cita['datetime']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Acciones -->
    <div class="modal fade" id="accionesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Acciones de Cita</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" onclick="mostrarEdicion()">
                            <i class="material-icons">edit</i> Editar Cita
                        </button>
                        <button class="btn btn-danger" onclick="confirmarEliminacion()">
                            <i class="material-icons">delete</i> Eliminar Cita
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Edición -->
    <div class="modal fade" id="edicionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Cita</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editarCitaForm">
                        <input type="hidden" id="editCitaId">
                        <div class="mb-3">
                            <label for="editName" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="editName" required>
                        </div>
                        <div class="mb-3">
                            <label for="editEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editEmail" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPersonas" class="form-label">Número de Personas</label>
                            <input type="number" class="form-control" id="editPersonas" required min="1">
                        </div>
                        <div class="mb-3">
                            <label for="editDatetime" class="form-label">Fecha y Hora</label>
                            <input type="datetime-local" class="form-control" id="editDatetime" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarEdicion()">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>

        // Filtro por fecha
        document.getElementById('filtroFecha').addEventListener('change', function() {
            const fecha = this.value;
            
            document.querySelectorAll('tbody tr').forEach(row => {
                const fechaCita = row.dataset.fecha.split(' ')[0];
                row.style.display = !fecha || fechaCita === fecha ? '' : 'none';
            });
        });
    </script>
</body>
</html>
