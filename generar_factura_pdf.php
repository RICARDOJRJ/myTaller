<?php
// generar_factura_pdf.php

include 'db_config.php';
require_once('tcpdf/tcpdf/tcpdf.php');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID de orden de trabajo no especificado.");
}

$id_orden = $conn->real_escape_string($_GET['id']);

// --- 1. Obtener datos de la Orden de Trabajo, Vehículo y Cliente ---
$sql = "SELECT 
            ot.id AS orden_id, 
            ot.fecha, 
            ot.descripcion, 
            ot.estado,
            ot.costo_mano_obra,
            ot.impuesto_porcentaje,
            v.marca AS vehiculo_marca,
            v.modelo AS vehiculo_modelo,
            v.placa AS vehiculo_placa,
            c.nombre AS cliente_nombre,
            c.telefono AS cliente_telefono,
            c.direccion AS cliente_direccion
        FROM 
            ordenes_de_trabajo ot
        JOIN 
            vehiculos v ON ot.vehiculo_id = v.id
        JOIN 
            clientes c ON v.cliente_id = c.id
        WHERE 
            ot.id = '$id_orden'";

$result = $conn->query($sql);

if ($result->num_rows === 0) {
    die("Orden de trabajo no encontrada.");
}

$orden = $result->fetch_assoc();

// --- 2. Obtener información de la empresa (configuración global) ---
$sql_config = "SELECT * FROM configuracion_empresa WHERE id = 1";
$result_config = $conn->query($sql_config);
$config_empresa = $result_config->fetch_assoc();

// --- 3. Lógica para calcular totales ---
$costo_mano_obra = (float)$orden['costo_mano_obra'];
$impuesto_porcentaje = (float)$orden['impuesto_porcentaje']; // Usar el porcentaje guardado en la orden
$total_repuestos = 0;
$repuestos_utilizados = [];

// Consulta para obtener los repuestos asociados a esta orden
$sql_repuestos = "SELECT 
                    orp.cantidad_utilizada, 
                    orp.precio_unitario_venta,
                    r.nombre AS repuesto_nombre
                  FROM 
                    ordenes_repuestos orp
                  JOIN
                    repuestos r ON orp.repuesto_id = r.id 
                  WHERE 
                    orp.orden_id = '$id_orden'";
$result_repuestos = $conn->query($sql_repuestos);

if ($result_repuestos && $result_repuestos->num_rows > 0) {
    while($repuesto_row = $result_repuestos->fetch_assoc()) {
        $subtotal_repuesto_item = $repuesto_row['cantidad_utilizada'] * $repuesto_row['precio_unitario_venta'];
        $total_repuestos += $subtotal_repuesto_item;
        $repuestos_utilizados[] = [
            'nombre' => $repuesto_row['repuesto_nombre'],
            'cantidad' => $repuesto_row['cantidad_utilizada'],
            'precio_unitario' => $repuesto_row['precio_unitario_venta'],
            'subtotal' => $subtotal_repuesto_item
        ];
    }
}

$subtotal_antes_impuestos = $costo_mano_obra + $total_repuestos;
$impuesto_monto = $subtotal_antes_impuestos * $impuesto_porcentaje;
$total_a_pagar = $subtotal_antes_impuestos + $impuesto_monto;

// Formatear a moneda para la salida en PDF
$costo_mano_obra_f = '$' . number_format($costo_mano_obra, 2, '.', ',');
$total_repuestos_f = '$' . number_format($total_repuestos, 2, '.', ',');
$subtotal_antes_impuestos_f = '$' . number_format($subtotal_antes_impuestos, 2, '.', ',');
$impuesto_monto_f = '$' . number_format($impuesto_monto, 2, '.', ',');
$total_a_pagar_f = '$' . number_format($total_a_pagar, 2, '.', ',');

// --- 4. Crear nuevo documento PDF con TCPDF ---
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Información del documento
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor($config_empresa['nombre_empresa']);
$pdf->SetTitle('Factura Orden de Trabajo #' . $orden['orden_id']);
$pdf->SetSubject('Factura de Servicio Automotriz');
$pdf->SetKeywords('Factura, Orden de Trabajo, Taller, PDF');

// No header/footer por defecto
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Márgenes (top, left, right)
$pdf->SetMargins(20, 20, 20);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->AddPage();

// --- 5. Contenido de la Factura ---

// Título de la Factura
$pdf->SetFont('helvetica', 'B', 24);
$pdf->SetTextColor(0, 86, 179); // Azul oscuro
$pdf->Cell(0, 15, 'FACTURA DE SERVICIO', 0, 1, 'C', 0, '', 0, false, 'T', 'M');
$pdf->SetTextColor(0, 0, 0); // Restaurar color a negro
$pdf->Ln(5);

// Información del Taller (Tu Empresa) - DINÁMICO
$pdf->SetFont('helvetica', '', 10);
$company_info_html = '<strong>' . htmlspecialchars($config_empresa['nombre_empresa']) . '</strong><br>';
if (!empty($config_empresa['direccion'])) $company_info_html .= htmlspecialchars($config_empresa['direccion']) . '<br>';
if (!empty($config_empresa['telefono'])) $company_info_html .= 'Teléfono: ' . htmlspecialchars($config_empresa['telefono']) . ' | ';
if (!empty($config_empresa['email'])) $company_info_html .= 'Email: ' . htmlspecialchars($config_empresa['email']) . '<br>';
if (!empty($config_empresa['numero_fiscal'])) $company_info_html .= 'RFC/CIF: ' . htmlspecialchars($config_empresa['numero_fiscal']) . '<br>';

$pdf->WriteHTMLCell(0, 0, '', '', $company_info_html, 0, 1, false, true, 'R', true);
$pdf->Ln(5);

// Número de Factura y Fecha
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 0, 'Factura No: ' . sprintf('%06d', $orden['orden_id']), 0, 0, 'L');
$pdf->Cell(0, 0, 'Fecha: ' . date('d/m/Y', strtotime($orden['fecha'])), 0, 1, 'R');
$pdf->Ln(5);

// Información del Cliente y Vehículo
$pdf->SetFont('helvetica', '', 10);
$html_info = '
<table cellpadding="3" cellspacing="0" border="0">
    <tr>
        <td width="50%" style="background-color:#E0E0E0;"><strong>Datos del Cliente:</strong></td>
        <td width="50%" style="background-color:#E0E0E0;"><strong>Datos del Vehículo:</strong></td>
    </tr>
    <tr>
        <td width="50%">
            <strong>Nombre:</strong> ' . htmlspecialchars($orden['cliente_nombre']) . '<br>
            <strong>Teléfono:</strong> ' . htmlspecialchars($orden['cliente_telefono']) . '<br>
            <strong>Dirección:</strong> ' . htmlspecialchars($orden['cliente_direccion']) . '
        </td>
        <td width="50%">
            <strong>Marca:</strong> ' . htmlspecialchars($orden['vehiculo_marca']) . '<br>
            <strong>Modelo:</strong> ' . htmlspecialchars($orden['vehiculo_modelo']) . '<br>
            <strong>Placa:</strong> ' . htmlspecialchars($orden['vehiculo_placa']) . '
        </td>
    </tr>
</table>
';
$pdf->writeHTML($html_info, true, false, true, false, '');
$pdf->Ln(10);

// Detalles del Servicio
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 0, 'DETALLES DEL SERVICIO:', 0, 1, 'L');
$pdf->Ln(3);

$html_description = '
<table border="1" cellpadding="5" cellspacing="0" style="width:100%;">
    <tr style="background-color:#007bff; color:#ffffff;">
        <th width="70%">Descripción del Trabajo</th>
        <th width="15%">Estado</th>
        <th width="15%">Costo Servicio</th>
    </tr>
    <tr>
        <td>' . nl2br(htmlspecialchars($orden['descripcion'])) . '</td>
        <td>' . htmlspecialchars($orden['estado']) . '</td>
        <td align="right">' . $costo_mano_obra_f . '</td>
    </tr>
</table>
';
$pdf->writeHTML($html_description, true, false, true, false, '');
$pdf->Ln(10);

// --- Sección de Repuestos (DINÁMICA) ---
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 0, 'REPUESTOS UTILIZADOS:', 0, 1, 'L');
$pdf->Ln(3);

$html_repuestos = '
<table border="1" cellpadding="5" cellspacing="0" style="width:100%;">
    <tr style="background-color:#007bff; color:#ffffff;">
        <th width="50%">Repuesto</th>
        <th width="15%">Cantidad</th>
        <th width="15%">P. Unitario</th>
        <th width="20%">Subtotal</th>
    </tr>';

if (!empty($repuestos_utilizados)) {
    foreach ($repuestos_utilizados as $repuesto) {
        $html_repuestos .= '
        <tr>
            <td>' . htmlspecialchars($repuesto['nombre']) . '</td>
            <td align="center">' . $repuesto['cantidad'] . '</td>
            <td align="right">$' . number_format($repuesto['precio_unitario'], 2, '.', ',') . '</td>
            <td align="right">$' . number_format($repuesto['subtotal'], 2, '.', ',') . '</td>
        </tr>';
    }
} else {
    $html_repuestos .= '<tr><td colspan="4" align="center">No se utilizaron repuestos para esta orden.</td></tr>';
}

$html_repuestos .= '
    <tr>
        <td colspan="3" align="right"><strong>Total Repuestos:</strong></td>
        <td align="right"><strong>' . $total_repuestos_f . '</strong></td>
    </tr>
</table>
';
$pdf->writeHTML($html_repuestos, true, false, true, false, '');
$pdf->Ln(10);

// Totales (Servicios + Repuestos + Impuestos) - DINÁMICOS
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 0, 'RESUMEN DE COSTOS:', 0, 1, 'R');
$pdf->Ln(3);

$html_totales = '
<table border="0" cellpadding="3" cellspacing="0" style="width:100%;">
    <tr>
        <td width="70%" align="right">Costo del Servicio:</td>
        <td width="30%" align="right">' . $costo_mano_obra_f . '</td>
    </tr>
    <tr>
        <td width="70%" align="right">Total Repuestos:</td>
        <td width="30%" align="right">' . $total_repuestos_f . '</td>
    </tr>
    <tr>
        <td width="70%" align="right">Subtotal:</td>
        <td width="30%" align="right">' . $subtotal_antes_impuestos_f . '</td>
    </tr>
    <tr>
        <td width="70%" align="right">IVA (' . ($impuesto_porcentaje * 100) . '%):</td>
        <td width="30%" align="right">' . $impuesto_monto_f . '</td>
    </tr>
    <tr style="background-color:#E0E0E0;">
        <td width="70%" align="right"><strong>TOTAL A PAGAR:</strong></td>
        <td width="30%" align="right"><strong>' . $total_a_pagar_f . '</strong></td>
    </tr>
</table>
';
$pdf->writeHTML($html_totales, true, false, true, false, '');
$pdf->Ln(15);

// Mensaje de Agradecimiento
$pdf->SetFont('helvetica', 'I', 10);
$pdf->Cell(0, 0, 'Gracias por su confianza en nuestros servicios!', 0, 1, 'C');

// Cerrar la conexión
$conn->close();

// Salida del PDF
$pdf->Output('factura_orden_' . $orden['orden_id'] . '.pdf', 'I');