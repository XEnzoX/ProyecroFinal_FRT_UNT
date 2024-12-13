<?php
$mysqli = new mysqli("localhost", "root", "", "proyecto_turnos" );

if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

$turno_id = isset($_GET['id_turno']) ? $_GET['id_turno'] : null;

if ($turno_id !== null) {
    $stmt = $mysqli->prepare("DELETE FROM solicitantes WHERE id = ?");
    $stmt->bind_param("i", $turno_id);

    if ($stmt->execute()) {
        header("Location: nombre_de_tu_pagina.php?msg=TurnoEliminado");
        exit(); 
    } else {
        echo "Error al eliminar turno: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "ID del turno no especificado.";
}

$mysqli->close();
?>
