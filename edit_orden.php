<?php
// edit_orden.php
include 'db_config.php';

$orden = null;
$id_orden = null;

// Recuperar datos de la orden para editar
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_orden = $conn->real_escape_string($_GET['id']);
    $sql_select = "SELECT id, vehiculo_id, fecha, descripcion, estado FROM ordenes_de_trabajo WHERE id = '$id_orden'";
    $result = $conn->query($sql_select);

    if ($result->num_rows == 1) {
        $orden = $result->fetch_assoc();
    } else {
        echo "<p class='status-message error'>Orden de trabajo no encontrada.</p>";
        //header("Location: ordenes_trabajo.php"); exit();
    }
} else {
    echo "<p class='status-message error'>ID de orden de trabajo no especificado.</p>";
    //header("Location: ordenes_trabajo.php"); exit();
}

// Lógica para ACTUALIZAR la orden
if (isset($_POST['submit_edit']) && $orden) {
    $vehiculo_id = $conn->real_escape_string($_POST['vehiculo_id']);
    $fecha = $conn->real_escape_string($_POST['fecha']);
    $descripcion = $conn->real_escape_string($_POST['descripcion']);
    $estado = $conn->real_escape_string($_POST['estado']);
    $id_actualizar = $conn->real_escape_string($_POST['id_orden']);

    if (empty($vehiculo_id) || empty($fecha) || empty($descripcion) || empty($estado)) {
        echo "<p class='status-message error'>Todos los campos son obligatorios.</p>";
    } else {
        $sql_update = "UPDATE ordenes_de_trabajo SET vehiculo_id='$vehiculo_id', fecha='$fecha', descripcion='$descripcion', estado='$estado' WHERE id='$id_actualizar'";

        if ($conn->query($sql_update) === TRUE) {
            header("Location: ordenes_trabajo.php?status=edited");
            exit();
        } else {
            echo "<p class='status-message error'>Error al actualizar la orden: " . $conn->error . "</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Orden de Trabajo</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Editar Orden de Trabajo</h1>

        <?php if ($orden): ?>
        <div class="form-container">
            <form action="edit_orden.php?id=<?php echo htmlspecialchars($orden['id']); ?>" method="POST">
                <input type="hidden" name="id_orden" value="<?php echo htmlspecialchars($orden['id']); ?>">

                <label for="vehiculo_id">Vehículo:</label>
                <select id="vehiculo_id" name="vehiculo_id" required>
                    <option value="">Seleccione un vehículo</option>
                    <?php
                    $vehiculos_query = $conn->query("SELECT id, placa, marca, modelo FROM vehiculos ORDER BY placa ASC");
                    if ($vehiculos_query->num_rows > 0) {
                        while ($vehiculo_row = $vehiculos_query->fetch_assoc()) {
                            $selected = ($vehiculo_row['id'] == $orden['vehiculo_id']) ? 'selected' : '';
                            echo "<option value='" . $vehiculo_row['id'] . "' $selected>" . htmlspecialchars($vehiculo_row['placa']) . " (" . htmlspecialchars($vehiculo_row['marca']) . " " . htmlspecialchars($vehiculo_row['modelo']) . ")</option>";
                        }
                    } else {
                        echo "<option value=''>No hay vehículos registrados</option>";
                    }
                    ?>
                </select>

                <label for="fecha">Fecha:</label>
                <input type="date" id="fecha" name="fecha" value="<?php echo htmlspecialchars($orden['fecha']); ?>" required>

                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" rows="4" required><?php echo htmlspecialchars($orden['descripcion']); ?></textarea>

                <label for="estado">Estado:</label>
                <select id="estado" name="estado" required>
                    <option value="Pendiente" <?php echo ($orden['estado'] == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                    <option value="En Proceso" <?php echo ($orden['estado'] == 'En Proceso') ? 'selected' : ''; ?>>En Proceso</option>
                    <option value="Completada" <?php echo ($orden['estado'] == 'Completada') ? 'selected' : ''; ?>>Completada</option>
                    <option value="Cancelada" <?php echo ($orden['estado'] == 'Cancelada') ? 'selected' : ''; ?>>Cancelada</option>
                </select>

                <input type="submit" name="submit_edit" value="Actualizar Orden">
                <a href="ordenes_trabajo.php" class="action-button back-button">Cancelar y Volver</a>
            </form>
        </div>
        <?php else: ?>
            <p class="status-message error" style="text-align: center;">No se pudo cargar la información de la orden para editar.</p>
            <p style="text-align: center;"><a href="ordenes_trabajo.php" class="action-button back-button">Volver a la lista</a></p>
        <?php endif; ?>
    </div>
</body>
</html>
<?php $conn->close(); ?>


<?php
// edit_orden.php
include 'db_config.php';

$orden = null;
$id_orden = null;
$vehiculos = []; // Para el select de vehículos
$repuestos_disponibles = []; // Para el select de repuestos
$repuestos_en_orden = []; // Repuestos ya asociados a esta orden

// --- 1. Recuperar datos iniciales ---
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_orden = $conn->real_escape_string($_GET['id']);

    // Obtener datos de la orden
    $sql_select_orden = "SELECT id, vehiculo_id, fecha, descripcion, estado, costo_mano_obra, impuesto_porcentaje 
                         FROM ordenes_de_trabajo WHERE id = '$id_orden'";
    $result_orden = $conn->query($sql_select_orden);

    if ($result_orden->num_rows == 1) {
        $orden = $result_orden->fetch_assoc();
    } else {
        die("<p class='status-message error'>Orden de trabajo no encontrada.</p>");
    }

    // Obtener vehículos para el select
    $vehiculos_query = $conn->query("SELECT id, placa, marca, modelo FROM vehiculos ORDER BY placa ASC");
    if ($vehiculos_query->num_rows > 0) {
        while ($row = $vehiculos_query->fetch_assoc()) {
            $vehiculos[] = $row;
        }
    }

    // Obtener repuestos disponibles para el select de añadir
    $repuestos_query = $conn->query("SELECT id, nombre, precio FROM repuestos ORDER BY nombre ASC");
    if ($repuestos_query->num_rows > 0) {
        while ($row = $repuestos_query->fetch_assoc()) {
            $repuestos_disponibles[] = $row;
        }
    }

    // Obtener repuestos ya asociados a esta orden
    $sql_repuestos_orden = "SELECT orp.repuesto_id, orp.cantidad_utilizada, orp.precio_unitario_venta, r.nombre AS repuesto_nombre 
                            FROM ordenes_repuestos orp
                            JOIN repuestos r ON orp.repuesto_id = r.id
                            WHERE orp.orden_id = '$id_orden'";
    $result_repuestos_orden = $conn->query($sql_repuestos_orden);
    if ($result_repuestos_orden->num_rows > 0) {
        while ($row = $result_repuestos_orden->fetch_assoc()) {
            $repuestos_en_orden[] = $row;
        }
    }

} else {
    die("<p class='status-message error'>ID de orden de trabajo no especificado.</p>");
}

// --- 2. Lógica para ACTUALIZAR la orden de trabajo principal ---
if (isset($_POST['submit_edit_orden'])) {
    // ... (otras variables) ...
    $costo_mano_obra = $conn->real_escape_string($_POST['costo_mano_obra']);
    $impuesto_porcentaje = $conn->real_escape_string($_POST['impuesto_porcentaje']);

    // Validar que son numéricos ANTES de usarlos
    if (empty($vehiculo_id) || empty($fecha) || empty($descripcion) || empty($estado) || !is_numeric($costo_mano_obra) || !is_numeric($impuesto_porcentaje)) {
        echo "<p class='status-message error'>Todos los campos principales son obligatorios y numéricos donde corresponde.</p>";
    } else {
        // Asegurarse de que los valores se inserten correctamente como decimales en la DB
        $costo_mano_obra = (float)$costo_mano_obra;
        $impuesto_porcentaje = (float)$impuesto_porcentaje;

        $sql_update = "UPDATE ordenes_de_trabajo SET
                       vehiculo_id='$vehiculo_id',
                       fecha='$fecha',
                       descripcion='$descripcion',
                       estado='$estado',
                       costo_mano_obra='$costo_mano_obra',
                       impuesto_porcentaje='$impuesto_porcentaje'
                       WHERE id='$id_orden'";
        if ($conn->query($sql_update) === TRUE) {
            header("Location: edit_orden.php?id=$id_orden&status=edited_orden");
            exit();
        } else {
            echo "<p class='status-message error'>Error al actualizar la orden: " . $conn->error . "</p>";
        }
    }
}

// --- 3. Lógica para AÑADIR un repuesto a la orden ---
if (isset($_POST['submit_add_repuesto'])) {
    $repuesto_id = $conn->real_escape_string($_POST['repuesto_id']);
    $cantidad_utilizada = $conn->real_escape_string($_POST['cantidad_utilizada']);
    $precio_unitario_venta = $conn->real_escape_string($_POST['precio_unitario_venta']);

    if (empty($repuesto_id) || !is_numeric($cantidad_utilizada) || $cantidad_utilizada <= 0 || !is_numeric($precio_unitario_venta) || $precio_unitario_venta < 0) {
        echo "<p class='status-message error'>Repuesto, Cantidad y Precio unitario son obligatorios y deben ser valores válidos.</p>";
    } else {
        // Forzar a float para asegurar que se guardan como números en la DB
        $cantidad_utilizada = (int)$cantidad_utilizada; // Cantidad debe ser entero
        $precio_unitario_venta = (float)$precio_unitario_venta;

        if ($check_result->num_rows > 0) {
            // Si ya existe, actualiza la cantidad y precio
            $sql_update_repuesto = "UPDATE ordenes_repuestos SET 
                                    cantidad_utilizada = cantidad_utilizada + '$cantidad_utilizada', 
                                    precio_unitario_venta = '$precio_unitario_venta' 
                                    WHERE orden_id = '$id_orden' AND repuesto_id = '$repuesto_id'";
            $conn->query($sql_update_repuesto); // No es crítico si falla aquí, pero deberías manejarlo
            header("Location: edit_orden.php?id=$id_orden&status=updated_repuesto");
            exit();

        } else {
            // Si no existe, inserta uno nuevo
           $sql_insert_repuesto = "INSERT INTO ordenes_repuestos (orden_id, repuesto_id, cantidad_utilizada, precio_unitario_venta)
                                VALUES ('$id_orden', '$repuesto_id', '$cantidad_utilizada', '$precio_unitario_venta')";

            if ($conn->query($sql_insert_repuesto) === TRUE) {
                header("Location: edit_orden.php?id=$id_orden&status=added_repuesto");
                exit();
            } else {
                echo "<p class='status-message error'>Error al añadir el repuesto: " . $conn->error . "</p>";
            }
        }
    }
}

// --- 4. Lógica para ELIMINAR un repuesto de la orden ---
if (isset($_GET['delete_repuesto_id'])) {
    $repuesto_id_to_delete = $conn->real_escape_string($_GET['delete_repuesto_id']);
    $sql_delete_repuesto = "DELETE FROM ordenes_repuestos WHERE orden_id = '$id_orden' AND repuesto_id = '$repuesto_id_to_delete'";

    if ($conn->query($sql_delete_repuesto) === TRUE) {
        header("Location: edit_orden.php?id=$id_orden&status=deleted_repuesto");
        exit();
    } else {
        echo "<p class='status-message error'>Error al eliminar el repuesto: " . $conn->error . "</p>";
    }
}

// Volver a cargar los repuestos de la orden después de cualquier operación CRUD de repuestos
// Esto es importante para que la tabla se actualice sin recargar toda la página manualmente
$repuestos_en_orden = [];
$sql_repuestos_orden = "SELECT orp.repuesto_id, orp.cantidad_utilizada, orp.precio_unitario_venta, r.nombre AS repuesto_nombre 
                        FROM ordenes_repuestos orp
                        JOIN repuestos r ON orp.repuesto_id = r.id
                        WHERE orp.orden_id = '$id_orden'";
$result_repuestos_orden = $conn->query($sql_repuestos_orden);
if ($result_repuestos_orden->num_rows > 0) {
    while ($row = $result_repuestos_orden->fetch_assoc()) {
        $repuestos_en_orden[] = $row;
    }
}

// También volvemos a cargar la orden para reflejar los cambios de costo/impuesto si se actualizaron
$sql_select_orden = "SELECT id, vehiculo_id, fecha, descripcion, estado, costo_mano_obra, impuesto_porcentaje 
                     FROM ordenes_de_trabajo WHERE id = '$id_orden'";
$result_orden = $conn->query($sql_select_orden);
$orden = $result_orden->fetch_assoc(); // Recargamos el array $orden
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Orden de Trabajo #<?php echo htmlspecialchars($id_orden); ?></title>
    <link rel="stylesheet" href="styles.css">
    <script>
        // Función para autocompletar el precio del repuesto
        function setRepuestoPrice() {
            const repuestoSelect = document.getElementById('repuesto_id');
            const precioInput = document.getElementById('precio_unitario_venta');
            const selectedOption = repuestoSelect.options[repuestoSelect.selectedIndex];
            const price = selectedOption.dataset.precio; // Usamos dataset para guardar el precio

            if (price) {
                precioInput.value = parseFloat(price).toFixed(2);
            } else {
                precioInput.value = '0.00';
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Editar Orden de Trabajo #<?php echo htmlspecialchars($orden['id']); ?></h1>

        <?php
        if (isset($_GET['status'])) {
            if ($_GET['status'] == 'edited_orden') {
                echo '<div class="status-message success">Orden principal actualizada exitosamente.</div>';
            } elseif ($_GET['status'] == 'added_repuesto') {
                echo '<div class="status-message success">Repuesto añadido a la orden exitosamente.</div>';
            } elseif ($_GET['status'] == 'updated_repuesto') {
                echo '<div class="status-message success">Cantidad de repuesto actualizada en la orden.</div>';
            } elseif ($_GET['status'] == 'deleted_repuesto') {
                echo '<div class="status-message success">Repuesto eliminado de la orden.</div>';
            }
        }
        ?>

        <div class="top-buttons">
            <a href="ordenes_trabajo.php" class="action-button back-button">Volver a Órdenes</a>
            <a href="generar_factura_pdf.php?id=<?php echo htmlspecialchars($orden['id']); ?>" class="action-button print-button" target="_blank">Ver Factura PDF</a>
        </div>

        <h2>Detalles de la Orden</h2>
        <div class="form-container">
            <form action="edit_orden.php?id=<?php echo htmlspecialchars($orden['id']); ?>" method="POST">
                <label for="vehiculo_id">Vehículo:</label>
                <select id="vehiculo_id" name="vehiculo_id" required>
                    <option value="">Seleccione un vehículo</option>
                    <?php foreach ($vehiculos as $v): ?>
                        <option value="<?php echo htmlspecialchars($v['id']); ?>" <?php echo ($v['id'] == $orden['vehiculo_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($v['placa'] . " (" . $v['marca'] . " " . $v['modelo'] . ")"); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="fecha">Fecha:</label>
                <input type="date" id="fecha" name="fecha" value="<?php echo htmlspecialchars($orden['fecha']); ?>" required>

                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" rows="4" required><?php echo htmlspecialchars($orden['descripcion']); ?></textarea>

                <label for="estado">Estado:</label>
                <select id="estado" name="estado" required>
                    <option value="Pendiente" <?php echo ($orden['estado'] == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                    <option value="En Proceso" <?php echo ($orden['estado'] == 'En Proceso') ? 'selected' : ''; ?>>En Proceso</option>
                    <option value="Completada" <?php echo ($orden['estado'] == 'Completada') ? 'selected' : ''; ?>>Completada</option>
                    <option value="Cancelada" <?php echo ($orden['estado'] == 'Cancelada') ? 'selected' : ''; ?>>Cancelada</option>
                </select>

                <label for="costo_mano_obra">Costo Mano de Obra ($):</label>
                <input type="number" id="costo_mano_obra" name="costo_mano_obra" step="0.01" min="0" value="<?php echo htmlspecialchars($orden['costo_mano_obra']); ?>" required>

                <label for="impuesto_porcentaje">Porcentaje de Impuesto (ej. 0.16 para 16%):</label>
                <input type="number" id="impuesto_porcentaje" name="impuesto_porcentaje" step="0.01" min="0" max="1" value="<?php echo htmlspecialchars($orden['impuesto_porcentaje']); ?>" required>

                <input type="submit" name="submit_edit_orden" value="Actualizar Detalles de la Orden" class="create-button">
            </form>
        </div>

        <h2 style="margin-top: 30px;">Repuestos Utilizados en esta Orden</h2>
        <table>
            <thead>
                <tr>
                    <th>Repuesto</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Subtotal</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($repuestos_en_orden)): ?>
                    <?php $total_repuestos_orden = 0; ?>
                    <?php foreach ($repuestos_en_orden as $rep): ?>
                        <?php $subtotal_repuesto = $rep['cantidad_utilizada'] * $rep['precio_unitario_venta']; ?>
                        <?php $total_repuestos_orden += $subtotal_repuesto; ?>
                        <tr>
                            <td><?php echo htmlspecialchars($rep['repuesto_nombre']); ?></td>
                            <td align="center"><?php echo htmlspecialchars($rep['cantidad_utilizada']); ?></td>
                            <td align="right">$<?php echo number_format($rep['precio_unitario_venta'], 2); ?></td>
                            <td align="right">$<?php echo number_format($subtotal_repuesto, 2); ?></td>
                            <td>
                                <a href="edit_orden.php?id=<?php echo htmlspecialchars($orden['id']); ?>&delete_repuesto_id=<?php echo htmlspecialchars($rep['repuesto_id']); ?>" 
                                   class="action-button delete-button" 
                                   onclick="return confirm('¿Eliminar <?php echo htmlspecialchars($rep['repuesto_nombre']); ?> de esta orden?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="total-row">
                        <td colspan="3" align="right"><strong>Total Repuestos de la Orden:</strong></td>
                        <td align="right"><strong>$<?php echo number_format($total_repuestos_orden, 2); ?></strong></td>
                        <td></td>
                    </tr>
                <?php else: ?>
                    <tr><td colspan="5">No hay repuestos asociados a esta orden.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <h2 style="margin-top: 30px;">Añadir Repuesto a la Orden</h2>
        <div class="form-container">
            <form action="edit_orden.php?id=<?php echo htmlspecialchars($orden['id']); ?>" method="POST">
                <label for="repuesto_id">Repuesto:</label>
                <select id="repuesto_id" name="repuesto_id" onchange="setRepuestoPrice()" required>
                    <option value="">Seleccione un repuesto</option>
                    <?php foreach ($repuestos_disponibles as $rd): ?>
                        <option value="<?php echo htmlspecialchars($rd['id']); ?>" data-precio="<?php echo htmlspecialchars($rd['precio']); ?>">
                            <?php echo htmlspecialchars($rd['nombre'] . " ($" . number_format($rd['precio'], 2) . ")"); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="cantidad_utilizada">Cantidad:</label>
                <input type="number" id="cantidad_utilizada" name="cantidad_utilizada" min="1" value="1" required>

                <label for="precio_unitario_venta">Precio Unitario de Venta ($):</label>
                <input type="number" id="precio_unitario_venta" name="precio_unitario_venta" step="0.01" min="0" value="0.00" required>
                <small>Este es el precio al que se vende en esta orden. Se autocompleta con el precio actual del inventario.</small>

                <input type="submit" name="submit_add_repuesto" value="Añadir Repuesto" class="create-button">
            </form>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>