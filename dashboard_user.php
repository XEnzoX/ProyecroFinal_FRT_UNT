<?php
session_start();


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout'])) {
    session_unset();

    session_destroy();

    header("Location: /turnos_web_022/frontend/html/login.html");
    exit();
}

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: /turnos_web_022/frontend/html/login.html');
    exit();
}

$mysqli = new mysqli("localhost", "root", "", "proyecto_turnos" );

if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["guardar_datos"])) {
    $nombre = $_GET["nombre"];
    $apellido = $_GET["apellido"];
    $dni = $_GET["dni"];
    $motivo = $_GET["motivo"];
    $agendar_turno = $_GET["agendar_turno"];

    $query_count_turnos = "SELECT COUNT(*) AS cantidad_turnos FROM solicitantes WHERE fecha_turno = '$agendar_turno' AND eliminado = 0";
    $result_count_turnos = $mysqli->query($query_count_turnos);

    if ($result_count_turnos) {
        $row_count_turnos = $result_count_turnos->fetch_assoc();
        $cantidad_turnos = $row_count_turnos['cantidad_turnos'];

        if ($cantidad_turnos >= 5) {
            echo "No se pueden programar más de 5 turnos para este día.";
            exit();
        }
    } else {
        echo "Error al contar los turnos existentes.";
        exit();
    }

    $query_insert_turno = "INSERT INTO solicitantes 
    (nombre, apellido, dni, motivo, fecha_turno) 
    VALUES ('$nombre', '$apellido', '$dni', '$motivo', 
    STR_TO_DATE('$agendar_turno', '%d/%m/%Y'))";
    $result_insert_turno = $mysqli->query($query_insert_turno);

    if ($result_insert_turno) {
        echo "Turno guardado exitosamente.";
    } else {
        echo "Error al guardar el turno.";
    }
}

$mysqli->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/turnos_web_proyecto_final/frontend/css/style_dashboard.css">
    <script src="https://kit.fontawesome.com/b0bd3f177d.js" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

    <title>Dashboard</title>
</head>
<body>
    <div class="menu-horizontal">
    <ul class="sub-menu">
        <li class="active">
            <a href="/turnos_web_proyecto_final/backend/dashboard_user.php" class="hove_inicio">
                <i class="fa-solid fa-ticket"></i>
                <span>Turnos</span>
            </a>
        </li>
        <li>
            <form method="POST" action="plantillaPdf.php" target="_blank">
                <button class="custom-button" type="submit" name="generar_pdf">
                    <i class="fa-solid fa-file-pdf"></i>
                    <span class="cuadro_col">Generar PDF</span>
                </button>
            </form>
        </li>
        <li>
            <form method="GET" action="/turnos_web_proyecto_final/backend/estadisticas.php" target="_blank">
                <button class="custom-button" type="submit" name="ver_estadisticas">
                    <i class="fa-solid fa-chart-simple"></i>
                    <span class="cuadro_col">Estadísticas</span>
                </button>
            </form>
        </li>
    </ul>

    <div class="menu-salir">
    <form method="POST" action="">
        <button class="custom-button" type="submit" name="logout">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span class="cuadro_col">Salir</span>
        </button>
    </form>
</div>

</div>

<div class="overlay"></div>

<div class="hamburger-menu">
    <ul class="sub-menu">
        <li class="active">
            <a href="/turnos_web_proyecto_final/backend/dashboard_user.php" class="hove_inicio">
                <i class="fa-solid fa-ticket"></i>
                <span>Turnos</span>
            </a>
        </li>
        <li>
            <form method="POST" action="plantillaPdf.php" target="_blank">
                <button class="custom-button" type="submit" name="generar_pdf">
                    <i class="fa-solid fa-file-pdf"></i>
                    <span class="cuadro_col">Generar PDF</span>
                </button>
            </form>
        </li>
        <li>
            <form method="GET" action="/turnos_web_proyecto_final/backend/estadisticas.php" target="_blank">
                <button class="custom-button" type="submit" name="ver_estadisticas">
                    <i class="fa-solid fa-chart-simple"></i>
                    <span class="cuadro_col">Estadísticas</span>
                </button>
            </form>
        </li>
    </ul>

    <div class="menu-salir">
    <form method="POST" action="">
        <button class="custom-button" type="submit" name="logout">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span class="cuadro_col">Salir</span>
        </button>
    </form>
</div>


</div>

<button class="menu-hamburguesa">
    <i class="fa-solid fa-bars"></i>
</button>


    <div class="main--content">
        <div class="header--wrapper">
            <div class="header--title">
                <span><strong>Municipalidad de La Banda</strong></span>
                <h2>Sistema de Turnos</h2>
            </div>
            <div class="user--info">
        </div>           
    </div>

<div class="main-content">
  <div class="card--container_turnos_buscador">
      <div class="card--container_turnos">
          <?php include 'proceso_turno.php'; ?>
      </div>
  </div>
</div>

<script src="/frontend/js/sidebar.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const menuHamburguesa = document.querySelector(".menu-hamburguesa");
    const hamburgerMenu = document.querySelector(".hamburger-menu");
    const overlay = document.querySelector(".overlay");
    const body = document.body;
    const icon = menuHamburguesa.querySelector("i"); 

    const toggleMenu = () => {
        const isOpen = hamburgerMenu.classList.toggle("open"); 
        overlay.classList.toggle("open", isOpen); 
        body.classList.toggle("no-scroll", isOpen); 

        if (isOpen) {
            icon.classList.remove("fa-bars");
            icon.classList.add("fa-xmark");
        } else {
            icon.classList.remove("fa-xmark");
            icon.classList.add("fa-bars");
        }
    };

    menuHamburguesa.addEventListener("click", toggleMenu);

    overlay.addEventListener("click", toggleMenu);
});

    ocument.addEventListener('DOMContentLoaded', function () {
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
            dateFormat: 'dd/mm/YYYY', 
            minDate: 'today', 
            disableMobile: true, 
        });

        <?php unset($_SESSION['turno_guardado']); ?>
    });

        function confirmarEliminar(idTurno) {
            if (confirm("¿Estás seguro de que deseas eliminar este turno?")) {
                eliminarTurno(idTurno);
            }
        }

        function eliminarTurno(idTurno) {
            var xhr = new XMLHttpRequest();

            xhr.open("GET", "/turnos_web/backend/eliminar_turno.php?id_turno=" + idTurno, true);

            xhr.onload = function () {
                if (xhr.status === 200) {
                    alert(xhr.responseText);
                    location.reload();
                } else {
                    alert("Error al eliminar el turno");
                }
            };

            xhr.send();
        }

    window.addEventListener('DOMContentLoaded', function () {
        function ajustarDimensiones() {
            var screenWidth = window.innerWidth;
            var turnosContainer = document.querySelector('.turnos-container');

            if (screenWidth <= 768) {
                turnosContainer.classList.add('column-layout');
            } else {
                turnosContainer.classList.remove('column-layout');
            }
        }

        ajustarDimensiones();
        window.addEventListener('resize', ajustarDimensiones);
    });

    function clearFilters() {
        var searchForm = document.querySelector('.card--container_turnos_buscador form');
        searchForm.action = '<?php echo $_SERVER['PHP_SELF']; ?>';
        searchForm.querySelector('input[name="search_query"]').value = '';
        searchForm.submit();
    }

    </script>
</body>
</html>