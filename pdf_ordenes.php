<?php
// pdf_ordenes.php
include 'db_config.php';
require_once('tcpdf/tcpdf/tcpdf.php');

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Tu Nombre');
$pdf->SetTitle('Listado de Órdenes de Trabajo');
$pdf->SetSubject('Reporte de Órdenes de Trabajo');
$pdf->SetKeywords('TCPDF, PDF, órdenes, reporte');
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 10); // Fuente un poco más pequeña para más columnas
$pdf->SetTextColor(0, 86, 179);
$pdf->Write(0, 'Listado de Órdenes de Trabajo', '', 0, 'C', true, 0, false, false, 0);
$pdf->Ln(5);
$pdf->SetTextColor(0, 0, 0);

$html = '<table border="1" cellpadding="5" cellspacing="0">';
$html .= '<thead>';
$html .= '<tr style="background-color:#007bff; color:#ffffff;">';
$html .= '<th width="8%">ID</th>';
$html .= '<th width="20%">Vehículo (Placa)</th>';
$html .= '<th width="15%">Fecha</th>';
$html .= '<th width="40%">Descripción</th>';
$html .= '<th width="17%">Estado</th>';
$html .= '</tr>';
$html .= '</thead>';
$html .= '<tbody>';

$sql_select = "SELECT ot.id, ot.fecha, ot.descripcion, ot.estado, v.placa AS vehiculo_placa 
               FROM ordenes_de_trabajo ot 
               JOIN vehiculos v ON ot.vehiculo_id = v.id 
               ORDER BY ot.id DESC";
$result = $conn->query($sql_select);

if ($result->num_rows > 0) {
    $row_count = 0;
    while($row = $result->fetch_assoc()) {
        $bg_color = ($row_count % 2 == 0) ? '#f2f2f2' : '#ffffff';
        $html .= '<tr style="background-color:'.$bg_color.';">';
        $html .= '<td width="8%">'. $row["id"] .'</td>';
        $html .= '<td width="20%">'. htmlspecialchars($row["vehiculo_placa"]) .'</td>';
        $html .= '<td width="15%">'. htmlspecialchars($row["fecha"]) .'</td>';
        $html .= '<td width="40%">'. htmlspecialchars($row["descripcion"]) .'</td>';
        $html .= '<td width="17%">'. htmlspecialchars($row["estado"]) .'</td>';
        $html .= '</tr>';
        $row_count++;
    }
} else {
    $html .= '<tr><td colspan="5" align="center">No hay órdenes de trabajo registradas.</td></tr>';
}

$html .= '</tbody>';
$html .= '</table>';

$pdf->writeHTML($html, true, false, true, false, '');
$conn->close();
$pdf->Output('listado_ordenes_trabajo.pdf', 'I');
?>