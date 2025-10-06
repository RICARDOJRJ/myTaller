<?php
// pdf_clientes.php
include 'db_config.php';
require_once('tcpdf/tcpdf/tcpdf.php'); // Asegúrate de que la ruta sea correcta

// Crear nuevo documento PDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Tu Nombre');
$pdf->SetTitle('Listado de Clientes');
$pdf->SetSubject('Reporte de Clientes');
$pdf->SetKeywords('TCPDF, PDF, clientes, reporte');
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);
$pdf->SetTextColor(0, 86, 179);
$pdf->Write(0, 'Listado de Clientes', '', 0, 'C', true, 0, false, false, 0);
$pdf->Ln(5);
$pdf->SetTextColor(0, 0, 0);

$html = '<table border="1" cellpadding="5" cellspacing="0">';
$html .= '<thead>';
$html .= '<tr style="background-color:#007bff; color:#ffffff;">';
$html .= '<th width="10%">ID</th>';
$html .= '<th width="30%">Nombre</th>';
$html .= '<th width="25%">Teléfono</th>';
$html .= '<th width="35%">Dirección</th>';
$html .= '</tr>';
$html .= '</thead>';
$html .= '<tbody>';

$sql_select = "SELECT id, nombre, telefono, direccion FROM clientes ORDER BY id DESC";
$result = $conn->query($sql_select);

if ($result->num_rows > 0) {
    $row_count = 0;
    while($row = $result->fetch_assoc()) {
        $bg_color = ($row_count % 2 == 0) ? '#f2f2f2' : '#ffffff';
        $html .= '<tr style="background-color:'.$bg_color.';">';
        $html .= '<td width="10%">'. $row["id"] .'</td>';
        $html .= '<td width="30%">'. htmlspecialchars($row["nombre"]) .'</td>';
        $html .= '<td width="25%">'. htmlspecialchars($row["telefono"]) .'</td>';
        $html .= '<td width="35%">'. htmlspecialchars($row["direccion"]) .'</td>';
        $html .= '</tr>';
        $row_count++;
    }
} else {
    $html .= '<tr><td colspan="4" align="center">No hay clientes registrados.</td></tr>';
}

$html .= '</tbody>';
$html .= '</table>';

$pdf->writeHTML($html, true, false, true, false, '');
$conn->close();
$pdf->Output('listado_clientes.pdf', 'I');
?>