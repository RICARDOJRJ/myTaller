<?php
// clientes.php
include 'db_config.php';

// --- Lógica para ELIMINAR un cliente ---
if (isset($_GET['delete_id']) && !empty($_GET['delete_id'])) {
    $id_to_delete = $conn->real_escape_string($_GET['delete_id']);
    $sql_delete = "DELETE FROM clientes WHERE id = '$id_to_delete'";

    if ($conn->query($sql_delete) === TRUE) {
        header("Location: clientes.php?status=deleted");
        exit();
    } else {
        echo "<p class='status-message error'>Error al eliminar el cliente: " . $conn->error . "</p>";
    }
}

// --- Lógica para AÑADIR un nuevo cliente ---
if (isset($_POST['submit_add'])) {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $telefono = $conn->real_escape_string($_POST['telefono']);
    $direccion = $conn->real_escape_string($_POST['direccion']);

    if (empty($nombre)) {
        echo "<p class='status-message error'>El nombre no puede estar vacío.</p>";
    } else {
        $sql_insert = "INSERT INTO clientes (nombre, telefono, direccion) VALUES ('$nombre', '$telefono', '$direccion')";

        if ($conn->query($sql_insert) === TRUE) {
            header("Location: clientes.php?status=added");
            exit();
        } else {
            echo "<p class='status-message error'>Error al añadir el cliente: " . $conn->error . "</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Clientes</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Gestión de Clientes</h1>

        <?php
        if (isset($_GET['status'])) {
            if ($_GET['status'] == 'deleted') {
                echo '<div class="status-message success">Cliente eliminado exitosamente.</div>';
            } elseif ($_GET['status'] == 'added') {
                echo '<div class="status-message success">Cliente añadido exitosamente.</div>';
            } elseif ($_GET['status'] == 'edited') {
                echo '<div class="status-message success">Cliente editado exitosamente.</div>';
            }
        }
        ?>

        <div class="top-buttons">
            <a href="pdf_clientes.php" class="action-button print-button" target="_blank">Imprimir en PDF</a>
            <a href="index.php" class="action-button back-button">Volver al Menú Principal</a>
        </div>

        <h2>Lista de Clientes</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Dirección</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql_select = "SELECT id, nombre, telefono, direccion FROM clientes ORDER BY id DESC";
                $result = $conn->query($sql_select);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . htmlspecialchars($row["nombre"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["telefono"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["direccion"]) . "</td>";
                        echo "<td>";
                        echo "<a href='edit_cliente.php?id=" . $row["id"] . "' class='action-button edit-button'>Editar</a>";
                        echo "<a href='clientes.php?delete_id=" . $row["id"] . "' class='action-button delete-button' onclick=\"return confirm('¿Estás seguro de que quieres eliminar a " . htmlspecialchars($row["nombre"]) . "?');\">Eliminar</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No hay clientes registrados.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <h2>Añadir Nuevo Cliente</h2>
        <div class="form-container">
            <form action="clientes.php" method="POST">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>

                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" name="telefono">

                <label for="direccion">Dirección:</label>
                <input type="text" id="direccion" name="direccion">

                <input type="submit" name="submit_add" value="Añadir Cliente" class="create-button">
            </form>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>