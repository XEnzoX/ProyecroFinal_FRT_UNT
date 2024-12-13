<?php
require_once('./api/pdf/fpdf.php');

$mysqli = new mysqli("localhost", "root", "", "proyecto_turnos" );

if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

$query_turnos = "SELECT DAYNAME(fecha_turno) AS dia_semana, COUNT(*) AS cantidad_turnos 
              FROM solicitantes 
              WHERE YEAR(fecha_turno) = YEAR(CURDATE()) 
              GROUP BY dia_semana";

$result_turnos = $mysqli->query($query_turnos);

$data = array();
while ($row = $result_turnos->fetch_assoc()) {
    switch ($row['dia_semana']) {
        case 'Monday':
            $dia_espanol = 'Lunes';
            break;
        case 'Tuesday':
            $dia_espanol = 'Martes';
            break;
        case 'Wednesday':
            $dia_espanol = 'Miércoles';
            break;
        case 'Thursday':
            $dia_espanol = 'Jueves';
            break;
        case 'Friday':
            $dia_espanol = 'Viernes';
            break;
        default:
            $dia_espanol = 'Otro';
            break;
    }
    $data[$dia_espanol] = intval($row['cantidad_turnos']);
}

$colores = array(
    'Lunes' => array(42, 52, 132),
    'Martes' => array(43, 148, 190),
    'Miércoles' => array(43, 190, 132),
    'Jueves' => array(250, 170, 10),
    'Viernes' => array(123, 10, 250)
);

$image = imagecreatetruecolor(500, 200);
$white = imagecolorallocate($image, 255, 255, 255);
imagefilledrectangle($image, 0, 0, 499, 199, $white);

$start = 0;
$total = array_sum($data);
foreach ($data as $dia => $turnos) {
    list($r, $g, $b) = $colores[$dia];
    $color = imagecolorallocate($image, $r, $g, $b);
    $porcentaje = ($turnos / $total) * 360;
    $end = $start + $porcentaje;
    imagefilledarc($image, 250, 100, 180, 180, $start, $end, $color, IMG_ARC_PIE);
    $start = $end;
}

imagepng($image, 'grafico_circular.png');
imagedestroy($image);

class PDF extends FPDF
{
    function Footer()
    { 
        $this->Image('../frontend/img/logoMunicipalidadPdf.png', 30, 250, 150);
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, utf8_decode('Reporte de Turnos'), 0, 1, 'C');

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, utf8_decode('Gráfico de turnos almacenados hasta el dia de la fecha.'), 0, 1, 'C');
$pdf->Ln(5);
$pdf->Image('grafico_circular.png', null, null, 180);
$pdf->Ln(10);

$pdf->SetFont('Arial', 'B', 12);
foreach ($data as $dia => $turnos) {
    list($r, $g, $b) = $colores[$dia];
    $pdf->SetFillColor($r, $g, $b);
    $pdf->Cell(50, 10, utf8_decode($dia), 1, 0, 'C', true);
    $pdf->Cell(50, 10, $turnos, 1, 1, 'C');
}

$pdf->Ln(10);
$pdf->Cell(0, 10, utf8_decode('Total de Turnos: ') . $total, 0, 1, 'C');

$pdf->Output();
?>
