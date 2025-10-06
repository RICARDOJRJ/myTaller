<?php
// vehiculos.php
include 'db_config.php';

// --- Lógica para ELIMINAR un vehículo ---
if (isset($_GET['delete_id']) && !empty($_GET['delete_id'])) {
    $id_to_delete = $conn->real_escape_string($_GET['delete_id']);
    $sql_delete = "DELETE FROM vehiculos WHERE id = '$id_to_delete'";

    if ($conn->query($sql_delete) === TRUE) {
        header("Location: vehiculos.php?status=deleted");
        exit();
    } else {
        echo "<p class='status-message error'>Error al eliminar el vehículo: " . $conn->error . "</p>";
    }
}

// --- Lógica para AÑADIR un nuevo vehículo ---
if (isset($_POST['submit_add'])) {
    $cliente_id = $conn->real_escape_string($_POST['cliente_id']);
    $marca = $conn->real_escape_string($_POST['marca']);
    $modelo = $conn->real_escape_string($_POST['modelo']);
    $placa = $conn->real_escape_string($_POST['placa']);

    if (empty($marca) || empty($placa) || empty($cliente_id)) {
        echo "<p class='status-message error'>Marca, Placa y Cliente son campos obligatorios.</p>";
    } else {
        $sql_insert = "INSERT INTO vehiculos (cliente_id, marca, modelo, placa) VALUES ('$cliente_id', '$marca', '$modelo', '$placa')";

        if ($conn->query($sql_insert) === TRUE) {
            header("Location: vehiculos.php?status=added");
            exit();
        } else {
            echo "<p class='status-message error'>Error al añadir el vehículo: " . $conn->error . "</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Vehículos</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Gestión de Vehículos</h1>

        <?php
        if (isset($_GET['status'])) {
            if ($_GET['status'] == 'deleted') {
                echo '<div class="status-message success">Vehículo eliminado exitosamente.</div>';
            } elseif ($_GET['status'] == 'added') {
                echo '<div class="status-message success">Vehículo añadido exitosamente.</div>';
            } elseif ($_GET['status'] == 'edited') {
                echo '<div class="status-message success">Vehículo editado exitosamente.</div>';
            }
        }
        ?>

        <div class="top-buttons">
            <a href="pdf_vehiculos.php" class="action-button print-button" target="_blank">Imprimir en PDF</a>
            <a href="index.php" class="action-button back-button">Volver al Menú Principal</a>
        </div>

        <h2>Lista de Vehículos</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Placa</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Modificación: Unir con la tabla clientes para mostrar el nombre del cliente
                $sql_select = "SELECT v.id, v.marca, v.modelo, v.placa, c.nombre AS cliente_nombre 
                               FROM vehiculos v 
                               JOIN clientes c ON v.cliente_id = c.id 
                               ORDER BY v.id DESC";
                $result = $conn->query($sql_select);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . htmlspecialchars($row["cliente_nombre"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["marca"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["modelo"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["placa"]) . "</td>";
                        echo "<td>";
                        echo "<a href='edit_vehiculo.php?id=" . $row["id"] . "' class='action-button edit-button'>Editar</a>";
                        echo "<a href='vehiculos.php?delete_id=" . $row["id"] . "' class='action-button delete-button' onclick=\"return confirm('¿Estás seguro de que quieres eliminar el vehículo con placa " . htmlspecialchars($row["placa"]) . "?');\">Eliminar</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No hay vehículos registrados.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <h2>Añadir Nuevo Vehículo</h2>
        <div class="form-container">
            <form action="vehiculos.php" method="POST">
                <label for="cliente_id">Cliente:</label>
                <select id="cliente_id" name="cliente_id" required>
                    <option value="">Seleccione un cliente</option>
                    <?php
                    // Obtener clientes para el select
                    $clientes_query = $conn->query("SELECT id, nombre FROM clientes ORDER BY nombre ASC");
                    if ($clientes_query->num_rows > 0) {
                        while ($cliente_row = $clientes_query->fetch_assoc()) {
                            echo "<option value='" . $cliente_row['id'] . "'>" . htmlspecialchars($cliente_row['nombre']) . "</option>";
                        }
                    } else {
                        echo "<option value=''>No hay clientes registrados</option>";
                    }
                    ?>
                </select>

                <label for="marca">Marca:</label>
                <input type="text" id="marca" name="marca" required>

                <label for="modelo">Modelo:</label>
                <input type="text" id="modelo" name="modelo">

                <label for="placa">Placa:</label>
                <input type="text" id="placa" name="placa" required>

                <input type="submit" name="submit_add" value="Añadir Vehículo" class="create-button">
            </form>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>