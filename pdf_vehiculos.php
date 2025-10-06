<?php
// pdf_vehiculos.php
include 'db_config.php';
require_once('tcpdf/tcpdf/tcpdf.php');

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Tu Nombre');
$pdf->SetTitle('Listado de Vehículos');
$pdf->SetSubject('Reporte de Vehículos');
$pdf->SetKeywords('TCPDF, PDF, vehículos, reporte');
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);
$pdf->SetTextColor(0, 86, 179);
$pdf->Write(0, 'Listado de Vehículos', '', 0, 'C', true, 0, false, false, 0);
$pdf->Ln(5);
$pdf->SetTextColor(0, 0, 0);

$html = '<table border="1" cellpadding="5" cellspacing="0">';
$html .= '<thead>';
$html .= '<tr style="background-color:#007bff; color:#ffffff;">';
$html .= '<th width="10%">ID</th>';
$html .= '<th width="25%">Cliente</th>';
$html .= '<th width="20%">Marca</th>';
$html .= '<th width="20%">Modelo</th>';
$html .= '<th width="25%">Placa</th>';
$html .= '</tr>';
$html .= '</thead>';
$html .= '<tbody>';

$sql_select = "SELECT v.id, v.marca, v.modelo, v.placa, c.nombre AS cliente_nombre 
               FROM vehiculos v 
               JOIN clientes c ON v.cliente_id = c.id 
               ORDER BY v.id DESC";
$result = $conn->query($sql_select);

if ($result->num_rows > 0) {
    $row_count = 0;
    while($row = $result->fetch_assoc()) {
        $bg_color = ($row_count % 2 == 0) ? '#f2f2f2' : '#ffffff';
        $html .= '<tr style="background-color:'.$bg_color.';">';
        $html .= '<td width="10%">'. $row["id"] .'</td>';
        $html .= '<td width="25%">'. htmlspecialchars($row["cliente_nombre"]) .'</td>';
        $html .= '<td width="20%">'. htmlspecialchars($row["marca"]) .'</td>';
        $html .= '<td width="20%">'. htmlspecialchars($row["modelo"]) .'</td>';
        $html .= '<td width="25%">'. htmlspecialchars($row["placa"]) .'</td>';
        $html .= '</tr>';
        $row_count++;
    }
} else {
    $html .= '<tr><td colspan="5" align="center">No hay vehículos registrados.</td></tr>';
}

$html .= '</tbody>';
$html .= '</table>';

$pdf->writeHTML($html, true, false, true, false, '');
$conn->close();
$pdf->Output('listado_vehiculos.pdf', 'I');
?>
