<?php
// edit_cliente.php
include 'db_config.php';

$cliente = null;
$id_cliente = null;

// Recuperar datos del cliente para editar
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_cliente = $conn->real_escape_string($_GET['id']);
    $sql_select = "SELECT id, nombre, telefono, direccion FROM clientes WHERE id = '$id_cliente'";
    $result = $conn->query($sql_select);

    if ($result->num_rows == 1) {
        $cliente = $result->fetch_assoc();
    } else {
        echo "<p class='status-message error'>Cliente no encontrado.</p>";
        //header("Location: clientes.php"); exit();
    }
} else {
    echo "<p class='status-message error'>ID de cliente no especificado.</p>";
    //header("Location: clientes.php"); exit();
}

// Lógica para ACTUALIZAR el cliente
if (isset($_POST['submit_edit']) && $cliente) {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $telefono = $conn->real_escape_string($_POST['telefono']);
    $direccion = $conn->real_escape_string($_POST['direccion']);
    $id_actualizar = $conn->real_escape_string($_POST['id_cliente']);

    if (empty($nombre)) {
        echo "<p class='status-message error'>El nombre no puede estar vacío.</p>";
    } else {
        $sql_update = "UPDATE clientes SET nombre='$nombre', telefono='$telefono', direccion='$direccion' WHERE id='$id_actualizar'";

        if ($conn->query($sql_update) === TRUE) {
            header("Location: clientes.php?status=edited");
            exit();
        } else {
            echo "<p class='status-message error'>Error al actualizar el cliente: " . $conn->error . "</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cliente</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Editar Cliente</h1>

        <?php if ($cliente): ?>
        <div class="form-container">
            <form action="edit_cliente.php?id=<?php echo htmlspecialchars($cliente['id']); ?>" method="POST">
                <input type="hidden" name="id_cliente" value="<?php echo htmlspecialchars($cliente['id']); ?>">

                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($cliente['nombre']); ?>" required>

                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($cliente['telefono']); ?>">

                <label for="direccion">Dirección:</label>
                <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($cliente['direccion']); ?>">

                <input type="submit" name="submit_edit" value="Actualizar Cliente">
                <a href="clientes.php" class="action-button back-button">Cancelar y Volver</a>
            </form>
        </div>
        <?php else: ?>
            <p class="status-message error" style="text-align: center;">No se pudo cargar la información del cliente para editar.</p>
            <p style="text-align: center;"><a href="clientes.php" class="action-button back-button">Volver a la lista</a></p>
        <?php endif; ?>
    </div>
</body>
</html>
<?php $conn->close(); ?>