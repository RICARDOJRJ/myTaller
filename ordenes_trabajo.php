<?php
// ordenes_trabajo.php
include 'db_config.php';

// --- Lógica para ELIMINAR una orden ---
if (isset($_GET['delete_id']) && !empty($_GET['delete_id'])) {
    $id_to_delete = $conn->real_escape_string($_GET['delete_id']);
    $sql_delete = "DELETE FROM ordenes_de_trabajo WHERE id = '$id_to_delete'";

    if ($conn->query($sql_delete) === TRUE) {
        header("Location: ordenes_trabajo.php?status=deleted");
        exit();
    } else {
        echo "<p class='status-message error'>Error al eliminar la orden: " . $conn->error . "</p>";
    }
}

// --- Lógica para AÑADIR una nueva orden ---
if (isset($_POST['submit_add'])) {
    $vehiculo_id = $conn->real_escape_string($_POST['vehiculo_id']);
    $fecha = $conn->real_escape_string($_POST['fecha']);
    $descripcion = $conn->real_escape_string($_POST['descripcion']);
    $estado = $conn->real_escape_string($_POST['estado']);
$costo_mano_obra = $conn->real_escape_string($_POST['costo_mano_obra']);
    $impuesto_porcentaje = $conn->real_escape_string($_POST['impuesto_porcentaje']);

    if (empty($vehiculo_id) || empty($fecha) || empty($descripcion) || empty($estado) || !is_numeric($costo_mano_obra) || !is_numeric($impuesto_porcentaje)) {
        echo "<p class='status-message error'>Todos los campos principales son obligatorios y numéricos donde corresponde.</p>";
    } else {
        // Asegurarse de que los valores se inserten correctamente como decimales en la DB
        $costo_mano_obra = (float)$costo_mano_obra;
        $impuesto_porcentaje = (float)$impuesto_porcentaje;

        $sql_insert = "INSERT INTO ordenes_de_trabajo (vehiculo_id, fecha, descripcion, estado, costo_mano_obra, impuesto_porcentaje)
                       VALUES ('$vehiculo_id', '$fecha', '$descripcion', '$estado', '$costo_mano_obra', '$impuesto_porcentaje')";

        if ($conn->query($sql_insert) === TRUE) {
            header("Location: ordenes_trabajo.php?status=added");
            exit();
        } else {
            echo "<p class='status-message error'>Error al añadir la orden: " . $conn->error . "</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Órdenes de Trabajo</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Gestión de Órdenes de Trabajo</h1>

        <?php
        if (isset($_GET['status'])) {
            if ($_GET['status'] == 'deleted') {
                echo '<div class="status-message success">Orden de trabajo eliminada exitosamente.</div>';
            } elseif ($_GET['status'] == 'added') {
                echo '<div class="status-message success">Orden de trabajo añadida exitosamente.</div>';
            } elseif ($_GET['status'] == 'edited') {
                echo '<div class="status-message success">Orden de trabajo editada exitosamente.</div>';
            }
        }
        ?>

        <div class="top-buttons">
            <a href="pdf_ordenes.php" class="action-button print-button" target="_blank">Imprimir en PDF</a>
            <a href="index.php" class="action-button back-button">Volver al Menú Principal</a>
        </div>

        <h2>Lista de Órdenes de Trabajo</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Vehículo (Placa)</th>
                    <th>Fecha</th>
                    <th>Descripción</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql_select = "SELECT ot.id, ot.fecha, ot.descripcion, ot.estado, v.placa AS vehiculo_placa 
                               FROM ordenes_de_trabajo ot 
                               JOIN vehiculos v ON ot.vehiculo_id = v.id 
                               ORDER BY ot.id DESC";
                $result = $conn->query($sql_select);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . htmlspecialchars($row["vehiculo_placa"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["fecha"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["descripcion"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["estado"]) . "</td>";
                        echo "<td>";
                        echo "<a href='edit_orden.php?id=" . $row["id"] . "' class='action-button edit-button'>Editar</a>";
                        echo "<a href='ordenes_trabajo.php?delete_id=" . $row["id"] . "' class='action-button delete-button' onclick=\"return confirm('¿Estás seguro de que quieres eliminar esta orden?');\">Eliminar</a>";
                        echo "<a href='generar_factura_pdf.php?id=" . $row["id"] . "' class='action-button print-button' target='_blank'>Factura PDF</a>"; // Nuevo botón
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No hay órdenes de trabajo registradas.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <h2>Añadir Nueva Orden de Trabajo</h2>
        <div class="form-container">
            <form action="ordenes_trabajo.php" method="POST">
                <label for="vehiculo_id">Vehículo:</label>
                <select id="vehiculo_id" name="vehiculo_id" required>
                    <option value="">Seleccione un vehículo</option>
                    <?php
                    $vehiculos_query = $conn->query("SELECT id, placa, marca, modelo FROM vehiculos ORDER BY placa ASC");
                    if ($vehiculos_query->num_rows > 0) {
                        while ($vehiculo_row = $vehiculos_query->fetch_assoc()) {
                            echo "<option value='" . $vehiculo_row['id'] . "'>" . htmlspecialchars($vehiculo_row['placa']) . " (" . htmlspecialchars($vehiculo_row['marca']) . " " . htmlspecialchars($vehiculo_row['modelo']) . ")</option>";
                        }
                    } else {
                        echo "<option value=''>No hay vehículos registrados</option>";
                    }
                    ?>
                </select>

                <label for="fecha">Fecha:</label>
                <input type="date" id="fecha" name="fecha" required value="<?php echo date('Y-m-d'); ?>">

                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" rows="4" required></textarea>

                <label for="estado">Estado:</label>
                <select id="estado" name="estado" required>
                    <option value="Pendiente">Pendiente</option>
                    <option value="En Proceso">En Proceso</option>
                    <option value="Completada">Completada</option>
                    <option value="Cancelada">Cancelada</option>
                </select>

                <label for="costo_mano_obra">Costo Mano de Obra ($):</label>
                <input type="number" id="costo_mano_obra" name="costo_mano_obra" step="0.01" min="0" value="0.00" required>

                <label for="impuesto_porcentaje">Porcentaje de Impuesto (ej. 0.16 para 16%):</label>
                <input type="number" id="impuesto_porcentaje" name="impuesto_porcentaje" step="0.01" min="0" max="1" value="0.16" required>

                <input type="submit" name="submit_add" value="Añadir Orden" class="create-button">
            </form>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>
