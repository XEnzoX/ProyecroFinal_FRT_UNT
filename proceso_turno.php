<?php
$mysqli = new mysqli("localhost", "root", "", "proyecto_turnos");

if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

$query_eliminar_turnos = "DELETE FROM solicitantes WHERE fecha_turno < CURDATE() AND eliminado = 0";
$mysqli->query($query_eliminar_turnos);

$query_turnos = "SELECT * FROM solicitantes ORDER BY fecha_turno ASC";
$result_turnos = $mysqli->query($query_turnos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabla de Turnos con DataTables</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css">

<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>

<style>
    body{
        background-color: #11121a;
        text-decoration: none !important; 
    }

    .pagination {
        display: -ms-flexbox;
        display: flex;
        padding-left: 0;
        list-style: none;
        border-radius: 0.25rem;
        gap: 10px; 
    }

    .pagination li {
        margin: 0; 
    }

    .pagination a,
    .pagination span {
        display: inline-block;
        padding: 0.5rem 0.75rem;
        color: #007bff;
        text-decoration: none;
        background-color: #fff;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        transition: background-color 0.2s ease, color 0.2s ease;
    }

    .pagination a:hover {
        color: #0056b3;
        background-color: #e9ecef;
        border-color: #dee2e6;
    }

    .pagination .active span {
        color: #fff;
        background-color: #007bff;
        border-color: #007bff;
    }

    .pagination .disabled span {
        color: #6c757d;
        pointer-events: none;
        background-color: #fff;
        border-color: #dee2e6;
    }

    .table-container {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 10px;
        box-shadow: 5px 5px 8px rgba(0, 0, 0, 0.2);
        margin-top: 20px;
    }

    
    .table {
        width: 100%; 
        margin-bottom: 0; 
        border-collapse: separate; 
        border-spacing: 0; 
    }

    
    .table th {
        position: sticky;
        top: 0;
        background-color: #f8f9fa;
        z-index: 2;
        text-align: center;
        border-bottom: 2px solid #ddd; 
        padding: 8px;
    }
    .table td {
        padding: 8px;
        text-align: center;
        border: 1px solid #ddd;
    }

    .table tbody tr:nth-child(odd) {
        background-color: #f9f9f9;
    }

    .table tbody tr:hover {
        background-color: #e9ecef;
    }

    @media (max-width: 600px) {
        .table {
            font-size: 14px;
        }

        .table-container {
            padding: 5px;
        }
    }
</style>

</head>
<body>
    <div class="container">
        <h2 class="mt-4">Tabla de Turnos</h2>

        <div class="table-container">
            <table id="tablaTurnos" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>DNI</th>
                        <th>Motivo</th>
                        <th>Fecha del turno</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result_turnos) {
                        while ($row = $result_turnos->fetch_assoc()) {
                            $fecha_turno = date('d/m/Y', strtotime($row['fecha_turno']));
                            echo '<tr>
                                    <td>' . htmlspecialchars($row['nombre']) . '</td>
                                    <td>' . htmlspecialchars($row['apellido']) . '</td>
                                    <td>' . htmlspecialchars($row['dni']) . '</td>
                                    <td>' . htmlspecialchars($row['motivo']) . '</td>
                                    <td>' . $fecha_turno . '</td>
                                    <td>
                                        <button onclick="confirmarEliminar(' . htmlspecialchars($row['id']) . ')">Eliminar</button>
                                    </td>
                                  </tr>';
                        }
                    } else {
                        echo '<tr><td colspan="6">No hay turnos disponibles</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>
    
    <script>
    $(document).ready(function () {
        $('#tablaTurnos').DataTable({
            language: {
                processing: "Tratamiento en curso...",
                search: "Buscar&nbsp;:",
                lengthMenu: "Mostrar _MENU_ elementos",
                info: "Mostrando _START_ a _END_ de _TOTAL_ elementos",
                infoEmpty: "No existen datos.",
                infoFiltered: "(filtrado de _MAX_ elementos en total)",
                loadingRecords: "Cargando...",
                zeroRecords: "No se encontraron datos",
                emptyTable: "No hay datos disponibles en la tabla.",
                paginate: {
                    first: "Primero",
                    previous: "Anterior",
                    next: "Siguiente",
                    last: "Último"
                }
            },
            scrollY: 400, 
            scrollX: true, 
            scrollCollapse: true, 
            lengthMenu: [[10, 25, -1], [10, 25, "Todos"]],
            fixedHeader: true, 
            columnDefs: [
                { width: '20%', targets: 0 }, 
                { width: '20%', targets: 1 }, 
                { width: '20%', targets: 2 }, 
                { width: '20%', targets: 3 }, 
                { width: '20%', targets: 4 }  
            ]
        });
    });

    function confirmarEliminar(id) {
        if (confirm("¿Estás seguro de que deseas eliminar este turno?")) {
            console.log("Turno con ID " + id + " eliminado.");
        }
    }
</script>

</body>
</html>

<?php
$mysqli->close();
?>
