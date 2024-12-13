<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Conexión a la base de datos
    $mysqli = new mysqli("localhost", "root", "", "proyecto_turnos");

    if ($mysqli->connect_error) {
        die("Error de conexión: " . $mysqli->connect_error);
    }

    // Consultar el usuario por su nombre
    $query = "SELECT * FROM usuarios WHERE username=?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        // Verificar la contraseña encriptada
        if (password_verify($password, $user["password"])) {
            $_SESSION['logged_in'] = true;
            $_SESSION['user'] = $username;
            header("Location: /turnos_web_proyecto_final/backend/dashboard_user.php");
            exit();
        } else {
            $error_message = "Contraseña incorrecta. Inténtalo de nuevo.";
        }
    } else {
        $error_message = "Usuario no encontrado. Inténtalo de nuevo.";
    }

    $stmt->close();
    $mysqli->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error de Inicio de Sesión</title>
    <link rel="stylesheet" href="/turnos_web_proyecto_final/frontend/css/style_login.css">
</head>
<body>
    <div class="login-container">
        <h2>Error de Inicio de Sesión</h2>
        <?php if (isset($error_message)) : ?>
            <p><?php echo $error_message; ?></p>
        <?php endif; ?>
        <a href="/turnos_web_proyecto_final/frontend/html/login.html">Volver al Login</a>
    </div>
</body>
</html>
