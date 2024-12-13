<?php
session_start();

$mensaje_turno_guardado = '';
$mensaje_error = ''; 

$mysqli = new mysqli("localhost", "root", "", "proyecto_turnos");

if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["guardar_datos"])) {
    $nombre = $_POST["nombre"];
    $apellido = $_POST["apellido"];
    $dni = $_POST["dni"];
    $motivo = $_POST["motivo"];
    $agendar_turno = $_POST["agendar_turno"];

    
    $query_verificar_dni = "SELECT * FROM solicitantes WHERE dni = '$dni'";
    $result_verificar_dni = $mysqli->query($query_verificar_dni);

    if ($result_verificar_dni && $result_verificar_dni->num_rows > 0) {
        $mensaje_error = "El DNI ingresado ya existe.";
    } else {
        $query_verificar_turno = "SELECT * FROM solicitantes WHERE dni = '$dni' AND fecha_turno = '$agendar_turno'";
        $result_verificar_turno = $mysqli->query($query_verificar_turno);

        if ($result_verificar_turno && $result_verificar_turno->num_rows > 0) {
            $mensaje_error = "Ya existe un turno registrado para este usuario en la misma fecha.";
        } else {
            $query_count_turnos = "SELECT COUNT(*) AS cantidad_turnos FROM solicitantes WHERE fecha_turno = '$agendar_turno' AND eliminado = 0";
            $result_count_turnos = $mysqli->query($query_count_turnos);

            if ($result_count_turnos) {
                $row_count_turnos = $result_count_turnos->fetch_assoc();
                $cantidad_turnos = $row_count_turnos['cantidad_turnos'];

                if ($cantidad_turnos >= 5) {
                
                    $mensaje_error = "No se pueden programar más de 5 turnos para este día. Seleccione otra fecha.";
                } else {
                
                    try {
                        $query_insert_turno = "INSERT INTO solicitantes (nombre, apellido, dni, motivo, fecha_turno) VALUES ('$nombre', '$apellido', '$dni', '$motivo', STR_TO_DATE('$agendar_turno', '%d/%m/%Y'))";
                        $result_insert_turno = $mysqli->query($query_insert_turno);

                        if ($result_insert_turno) {
                            $_SESSION['turno_guardado'] = true;
                            $mensaje_turno_guardado = "Turno guardado exitosamente.";
                        } else {
                            throw new Exception("Error al guardar el turno.");
                        }
                    } catch (mysqli_sql_exception $e) {
                        $mensaje_error = "No se pueden programar más de 5 turnos para un mismo día. Modifica el día para tu consulta.";
                    } catch (Exception $e) {
                        $mensaje_error = $e->getMessage();
                    }
                }
            } else {
                $mensaje_error = "Error al contar los turnos existentes.";
            }
        }
    }
}
$mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../frontend/css/style_usuario.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
    <title>Retirar Turno</title>
</head>
<body>
<div class="main--content">
    <div class="header--wrapper">
        <div class="header--title">
            <span><strong>Municipalidad de La Banda</strong></span>
            <h2>Sistema de Turnos</h2>
        </div>
        <div class="user--info">
            <img class="logo_municipio" src="../frontend/img/Escudo_de_La_Banda,_Santiago_del_Estero.png"></img>
        </div>           
    </div>

    <div class="card--container">
        <h3 class="main--title">Agendar Turno</h3>
        <form class="datos_turnos" name="turnos" method="post" onsubmit="return validarFormulario()">
            <p> Nombre:
                <input type="text" name="nombre" required>
            </p>
            <p> Apellido:
                <input type="text" name="apellido" required>
            </p>
            <p> Dni:
                <input type="text" name="dni" required>
            </p>
            <p> Motivo del turno:
                <input type="text" name="motivo">
                <span id="motivoCounter">0/50</span>
            </p>
            <p> Seleccionar la fecha de turno: 
                <input id="agendar_turno" name="agendar_turno" readonly required>
            </p>
            <p class="contenedor_guardar"><input class="guardar_turno" type="submit" name="guardar_datos" value="Enviar"></p>
        </form>
    </div>

    <?php
            if ($mensaje_turno_guardado) {
                echo "<div class='mensaje_turno_guardado'>$mensaje_turno_guardado</div>";
            }
            if ($mensaje_error) {
                echo "<div class='error_mensaje'>$mensaje_error</div>";
            }
        ?>
</div>

<script>
    function validarFormulario() {
        var nombre = document.forms["turnos"]["nombre"].value;
        var apellido = document.forms["turnos"]["apellido"].value;
        var dni = document.forms["turnos"]["dni"].value;
        var fechaTurno = document.forms["turnos"]["agendar_turno"].value;

        if (nombre == "" || apellido == "" || dni == "" || fechaTurno == "") {
            alert("Por favor, complete todos los campos.");
            return false;
        }

        return true;
    }

    document.addEventListener('DOMContentLoaded', function () {
        var motivoInput = document.querySelector('input[name="motivo"]');
        var motivoCounter = document.getElementById('motivoCounter');

        motivoInput.addEventListener('input', function() {
            var motivoValue = motivoInput.value;
            var currentLength = motivoValue.length;

            motivoCounter.innerText = currentLength + "/50";            

            if (currentLength > 50) {
                motivoInput.value = motivoValue.slice(0, 50);
                motivoCounter.innerText = "50/50";
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        var inputFecha = document.getElementById('agendar_turno');

        flatpickr(inputFecha, {
            dateFormat: 'd/m/Y',
            minDate: 'today',
            disableMobile: true,
            locale: 'es', 
            disable: [
                function(date) {
                    return (date.getDay() === 6 || date.getDay() === 0);
                }
            ]
        });
    });
</script>
</body>
</html>
