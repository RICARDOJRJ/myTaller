<?php
// edit_repuesto.php
include 'db_config.php';

$repuesto = null;
$id_repuesto = null;

// Recuperar datos del repuesto para editar
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_repuesto = $conn->real_escape_string($_GET['id']);
    $sql_select = "SELECT id, nombre, cantidad, precio FROM repuestos WHERE id = '$id_repuesto'";
    $result = $conn->query($sql_select);

    if ($result->num_rows == 1) {
        $repuesto = $result->fetch_assoc();
    } else {
        echo "<p class='status-message error'>Repuesto no encontrado.</p>";
        //header("Location: repuestos.php"); exit();
    }
} else {
    echo "<p class='status-message error'>ID de repuesto no especificado.</p>";
    //header("Location: repuestos.php"); exit();
}

// Lógica para ACTUALIZAR el repuesto
if (isset($_POST['submit_edit']) && $repuesto) {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $cantidad = $conn->real_escape_string($_POST['cantidad']);
    $precio = $conn->real_escape_string($_POST['precio']);
    $id_actualizar = $conn->real_escape_string($_POST['id_repuesto']);

    if (empty($nombre) || !is_numeric($cantidad) || $cantidad < 0 || !is_numeric($precio) || $precio < 0) {
        echo "<p class='status-message error'>Nombre es obligatorio. Cantidad y Precio deben ser números positivos.</p>";
    } else {
        $sql_update = "UPDATE repuestos SET nombre='$nombre', cantidad='$cantidad', precio='$precio' WHERE id='$id_actualizar'";

        if ($conn->query($sql_update) === TRUE) {
            header("Location: repuestos.php?status=edited");
            exit();
        } else {
            echo "<p class='status-message error'>Error al actualizar el repuesto: " . $conn->error . "</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Repuesto</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Editar Repuesto</h1>

        <?php if ($repuesto): ?>
        <div class="form-container">
            <form action="edit_repuesto.php?id=<?php echo htmlspecialchars($repuesto['id']); ?>" method="POST">
                <input type="hidden" name="id_repuesto" value="<?php echo htmlspecialchars($repuesto['id']); ?>">

                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($repuesto['nombre']); ?>" required>

                <label for="cantidad">Cantidad:</label>
                <input type="number" id="cantidad" name="cantidad" min="0" value="<?php echo htmlspecialchars($repuesto['cantidad']); ?>" required>

                <label for="precio">Precio:</label>
                <input type="number" id="precio" name="precio" step="0.01" min="0" value="<?php echo htmlspecialchars(number_format($repuesto['precio'], 2, '.', '')); ?>" required>

                <input type="submit" name="submit_edit" value="Actualizar Repuesto">
                <a href="repuestos.php" class="action-button back-button">Cancelar y Volver</a>
            </form>
        </div>
        <?php else: ?>
            <p class="status-message error" style="text-align: center;">No se pudo cargar la información del repuesto para editar.</p>
            <p style="text-align: center;"><a href="repuestos.php" class="action-button back-button">Volver a la lista</a></p>
        <?php endif; ?>
    </div>
</body>
</html>
<?php $conn->close(); ?>
