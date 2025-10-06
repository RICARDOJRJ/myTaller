<?php
// repuestos.php
include 'db_config.php';

// --- Lógica para ELIMINAR un repuesto ---
if (isset($_GET['delete_id']) && !empty($_GET['delete_id'])) {
    $id_to_delete = $conn->real_escape_string($_GET['delete_id']);
    $sql_delete = "DELETE FROM repuestos WHERE id = '$id_to_delete'";

    if ($conn->query($sql_delete) === TRUE) {
        header("Location: repuestos.php?status=deleted");
        exit();
    } else {
        echo "<p class='status-message error'>Error al eliminar el repuesto: " . $conn->error . "</p>";
    }
}

// --- Lógica para AÑADIR un nuevo repuesto ---
if (isset($_POST['submit_add'])) {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $cantidad = $conn->real_escape_string($_POST['cantidad']);
    $precio = $conn->real_escape_string($_POST['precio']);

    if (empty($nombre) || !is_numeric($cantidad) || $cantidad < 0 || !is_numeric($precio) || $precio < 0) {
        echo "<p class='status-message error'>Nombre es obligatorio. Cantidad y Precio deben ser números positivos.</p>";
    } else {
        $sql_insert = "INSERT INTO repuestos (nombre, cantidad, precio) VALUES ('$nombre', '$cantidad', '$precio')";

        if ($conn->query($sql_insert) === TRUE) {
            header("Location: repuestos.php?status=added");
            exit();
        } else {
            echo "<p class='status-message error'>Error al añadir el repuesto: " . $conn->error . "</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Repuestos</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Gestión de Repuestos</h1>

        <?php
        if (isset($_GET['status'])) {
            if ($_GET['status'] == 'deleted') {
                echo '<div class="status-message success">Repuesto eliminado exitosamente.</div>';
            } elseif ($_GET['status'] == 'added') {
                echo '<div class="status-message success">Repuesto añadido exitosamente.</div>';
            } elseif ($_GET['status'] == 'edited') {
                echo '<div class="status-message success">Repuesto editado exitosamente.</div>';
            }
        }
        ?>

        <div class="top-buttons">
            <a href="pdf_repuestos.php" class="action-button print-button" target="_blank">Imprimir en PDF</a>
            <a href="index.php" class="action-button back-button">Volver al Menú Principal</a>
        </div>

        <h2>Lista de Repuestos</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql_select = "SELECT id, nombre, cantidad, precio FROM repuestos ORDER BY id DESC";
                $result = $conn->query($sql_select);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . htmlspecialchars($row["nombre"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["cantidad"]) . "</td>";
                        echo "<td>" . htmlspecialchars(number_format($row["precio"], 2)) . "</td>"; // Formatear precio
                        echo "<td>";
                        echo "<a href='edit_repuesto.php?id=" . $row["id"] . "' class='action-button edit-button'>Editar</a>";
                        echo "<a href='repuestos.php?delete_id=" . $row["id"] . "' class='action-button delete-button' onclick=\"return confirm('¿Estás seguro de que quieres eliminar el repuesto " . htmlspecialchars($row["nombre"]) . "?');\">Eliminar</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No hay repuestos registrados.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <h2>Añadir Nuevo Repuesto</h2>
        <div class="form-container">
            <form action="repuestos.php" method="POST">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>

                <label for="cantidad">Cantidad:</label>
                <input type="number" id="cantidad" name="cantidad" min="0" value="0" required>

                <label for="precio">Precio:</label>
                <input type="number" id="precio" name="precio" step="0.01" min="0" value="0.00" required>

                <input type="submit" name="submit_add" value="Añadir Repuesto" class="create-button">
            </form>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>