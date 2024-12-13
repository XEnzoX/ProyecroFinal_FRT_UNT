<?php
require('./api/pdf/fpdf.php');

class PDF extends FPDF
{
    function Header()
    {
        $this->Image('../frontend/img/logoMunicipalidadPdf.png', 30, 10, 150);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(0, 50, 'Turnos Guardados', 0, 1, 'C');
        $this->Ln(-15);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $pagina_actual = $this->PageNo();
        $paginas_totales = '{nb}';
        $this->Cell(0, 10, utf8_decode("Página $pagina_actual de $paginas_totales"), 0, 0, 'C');
    }
}

function generarPDF()
{
    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Times', '', 12);

    $mysqli = new mysqli("localhost", "root", "", "proyecto_turnos");

    if ($mysqli->connect_error) {
        die("Error de conexión: " . $mysqli->connect_error);
    }

    $query_turnos = "SELECT * FROM solicitantes";
    $result_turnos = $mysqli->query($query_turnos);

    if ($result_turnos) {
        while ($row = $result_turnos->fetch_assoc()) {
            $pdf->Cell(0, 10, utf8_decode('Nombre: ' . $row['nombre']), 0, 1, 'C');
            $pdf->Cell(0, 10, utf8_decode('Apellido: ' . $row['apellido']), 0, 1, 'C');
            $pdf->Cell(0, 10, utf8_decode('DNI: ' . $row['dni']), 0, 1, 'C');
            $pdf->Cell(0, 10, utf8_decode('Motivo: ' . $row['motivo']), 0, 1, 'C');
            $fecha_turno = date('d/m/Y', strtotime($row['fecha_turno']));
            $pdf->Cell(0, 10, utf8_decode('Fecha del turno: ' . $fecha_turno), 0, 1, 'C');
            $pdf->Ln();
        }
    } else {
        $pdf->Cell(0, 10, "Error al obtener los turnos: " . $mysqli->error, 0, 1, 'C');
    }

    $mysqli->close();

    $pdf->Output();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["generar_pdf"])) {
    generarPDF();
}
?>
