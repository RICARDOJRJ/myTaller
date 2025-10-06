<?php
// configuracion.php
include 'db_config.php';

$config = null;

// Cargar configuración actual
$sql_select = "SELECT * FROM configuracion_empresa WHERE id = 1";
$result = $conn->query($sql_select);
if ($result && $result->num_rows > 0) {
    $config = $result->fetch_assoc();
} else {
    // Si no hay configuración, se puede insertar una por defecto aquí o en el SQL de arriba
    $conn->query("INSERT INTO configuracion_empresa (id, nombre_empresa) VALUES (1, 'Mi Taller') ON DUPLICATE KEY UPDATE nombre_empresa='Mi Taller'");
    $result = $conn->query($sql_select);
    $config = $result->fetch_assoc();
}

// Lógica para ACTUALIZAR la configuración
if (isset($_POST['submit_edit'])) {
    $nombre_empresa = $conn->real_escape_string($_POST['nombre_empresa']);
    $direccion = $conn->real_escape_string($_POST['direccion']);
    $telefono = $conn->real_escape_string($_POST['telefono']);
    $email = $conn->real_escape_string($_POST['email']);
    $numero_fiscal = $conn->real_escape_string($_POST['numero_fiscal']);
    // No manejaremos el logo por ahora para simplificar, pero aquí iría la lógica de subida de archivos

    $sql_update = "UPDATE configuracion_empresa SET 
                    nombre_empresa='$nombre_empresa', 
                    direccion='$direccion', 
                    telefono='$telefono', 
                    email='$email', 
                    numero_fiscal='$numero_fiscal' 
                    WHERE id=1";

    if ($conn->query($sql_update) === TRUE) {
        header("Location: configuracion.php?status=edited");
        exit();
    } else {
        echo "<p class='status-message error'>Error al actualizar la configuración: " . $conn->error . "</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración del Taller</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Configuración del Taller</h1>

        <?php
        if (isset($_GET['status'])) {
            if ($_GET['status'] == 'edited') {
                echo '<div class="status-message success">Configuración actualizada exitosamente.</div>';
            }
        }
        ?>

        <div class="top-buttons">
            <a href="index.php" class="action-button back-button">Volver al Menú Principal</a>
        </div>

        <div class="form-container">
            <form action="configuracion.php" method="POST">
                <label for="nombre_empresa">Nombre del Taller:</label>
                <input type="text" id="nombre_empresa" name="nombre_empresa" value="<?php echo htmlspecialchars($config['nombre_empresa'] ?? ''); ?>" required>

                <label for="direccion">Dirección:</label>
                <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($config['direccion'] ?? ''); ?>">

                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($config['telefono'] ?? ''); ?>">

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($config['email'] ?? ''); ?>">

                <label for="numero_fiscal">Número Fiscal (RFC/CIF/NIF):</label>
                <input type="text" id="numero_fiscal" name="numero_fiscal" value="<?php echo htmlspecialchars($config['numero_fiscal'] ?? ''); ?>">
                
                <input type="submit" name="submit_edit" value="Guardar Configuración">
            </form>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>