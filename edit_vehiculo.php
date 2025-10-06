<?php
// edit_vehiculo.php
include 'db_config.php';

$vehiculo = null;
$id_vehiculo = null;

// Recuperar datos del vehículo para editar
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_vehiculo = $conn->real_escape_string($_GET['id']);
    $sql_select = "SELECT id, cliente_id, marca, modelo, placa FROM vehiculos WHERE id = '$id_vehiculo'";
    $result = $conn->query($sql_select);

    if ($result->num_rows == 1) {
        $vehiculo = $result->fetch_assoc();
    } else {
        echo "<p class='status-message error'>Vehículo no encontrado.</p>";
        //header("Location: vehiculos.php"); exit();
    }
} else {
    echo "<p class='status-message error'>ID de vehículo no especificado.</p>";
    //header("Location: vehiculos.php"); exit();
}

// Lógica para ACTUALIZAR el vehículo
if (isset($_POST['submit_edit']) && $vehiculo) {
    $cliente_id = $conn->real_escape_string($_POST['cliente_id']);
    $marca = $conn->real_escape_string($_POST['marca']);
    $modelo = $conn->real_escape_string($_POST['modelo']);
    $placa = $conn->real_escape_string($_POST['placa']);
    $id_actualizar = $conn->real_escape_string($_POST['id_vehiculo']);

    if (empty($marca) || empty($placa) || empty($cliente_id)) {
        echo "<p class='status-message error'>Marca, Placa y Cliente son campos obligatorios.</p>";
    } else {
        $sql_update = "UPDATE vehiculos SET cliente_id='$cliente_id', marca='$marca', modelo='$modelo', placa='$placa' WHERE id='$id_actualizar'";

        if ($conn->query($sql_update) === TRUE) {
            header("Location: vehiculos.php?status=edited");
            exit();
        } else {
            echo "<p class='status-message error'>Error al actualizar el vehículo: " . $conn->error . "</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Vehículo</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Editar Vehículo</h1>

        <?php if ($vehiculo): ?>
        <div class="form-container">
            <form action="edit_vehiculo.php?id=<?php echo htmlspecialchars($vehiculo['id']); ?>" method="POST">
                <input type="hidden" name="id_vehiculo" value="<?php echo htmlspecialchars($vehiculo['id']); ?>">

                <label for="cliente_id">Cliente:</label>
                <select id="cliente_id" name="cliente_id" required>
                    <option value="">Seleccione un cliente</option>
                    <?php
                    $clientes_query = $conn->query("SELECT id, nombre FROM clientes ORDER BY nombre ASC");
                    if ($clientes_query->num_rows > 0) {
                        while ($cliente_row = $clientes_query->fetch_assoc()) {
                            $selected = ($cliente_row['id'] == $vehiculo['cliente_id']) ? 'selected' : '';
                            echo "<option value='" . $cliente_row['id'] . "' $selected>" . htmlspecialchars($cliente_row['nombre']) . "</option>";
                        }
                    } else {
                        echo "<option value=''>No hay clientes registrados</option>";
                    }
                    ?>
                </select>

                <label for="marca">Marca:</label>
                <input type="text" id="marca" name="marca" value="<?php echo htmlspecialchars($vehiculo['marca']); ?>" required>

                <label for="modelo">Modelo:</label>
                <input type="text" id="modelo" name="modelo" value="<?php echo htmlspecialchars($vehiculo['modelo']); ?>">

                <label for="placa">Placa:</label>
                <input type="text" id="placa" name="placa" value="<?php echo htmlspecialchars($vehiculo['placa']); ?>" required>

                <input type="submit" name="submit_edit" value="Actualizar Vehículo">
                <a href="vehiculos.php" class="action-button back-button">Cancelar y Volver</a>
            </form>
        </div>
        <?php else: ?>
            <p class="status-message error" style="text-align: center;">No se pudo cargar la información del vehículo para editar.</p>
            <p style="text-align: center;"><a href="vehiculos.php" class="action-button back-button">Volver a la lista</a></p>
        <?php endif; ?>
    </div>
</body>
</html>
<?php $conn->close(); ?>