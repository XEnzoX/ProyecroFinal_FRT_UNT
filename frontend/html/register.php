<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Conexión a la base de datos
    $mysqli = new mysqli("localhost", "root", "", "proyecto_turnos");

    if ($mysqli->connect_error) {
        die("Error de conexión: " . $mysqli->connect_error);
    }

    // Validar que el nombre de usuario no exista
    $query_check = "SELECT * FROM usuarios WHERE username = ?";
    $stmt_check = $mysqli->prepare($query_check);
    $stmt_check->bind_param("s", $username);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows > 0) {
        echo "El nombre de usuario ya existe. Por favor, elige otro.";
    } else {
        // Encriptar la contraseña
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Insertar el nuevo usuario
        $query_insert = "INSERT INTO usuarios (username, password) VALUES (?, ?)";
        $stmt_insert = $mysqli->prepare($query_insert);
        $stmt_insert->bind_param("ss", $username, $hashed_password);

        if ($stmt_insert->execute()) {
            echo "Usuario registrado correctamente.";
        } else {
            echo "Error al registrar el usuario: " . $mysqli->error;
        }

        $stmt_insert->close();
    }

    $stmt_check->close();
    $mysqli->close();
}
?>
