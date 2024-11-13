<?php
require_once 'config.php';
verificar_sesion();

if (!es_administrador()) {
    header("Location: foro.php");
    exit();
}

// Manejar la adición de candidatos
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar'])) {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $cargo = $conn->real_escape_string($_POST['cargo']);
    $propuestas = $conn->real_escape_string($_POST['propuestas']);

    // Manejar la subida de la foto
    $foto = '';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $upload_dir = 'uploads/';
        
        // Crear el directorio si no existe
        if (!file_exists($upload_dir) && !is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $foto_nombre = basename($_FILES['foto']['name']);
        $foto_destino = $upload_dir . $foto_nombre;

        // Asegurarse de que el nombre del archivo sea único
        $i = 1;
        while (file_exists($foto_destino)) {
            $info = pathinfo($foto_nombre);
            $foto_nombre = $info['filename'] . '_' . $i . '.' . $info['extension'];
            $foto_destino = $upload_dir . $foto_nombre;
            $i++;
        }

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $foto_destino)) {
            $foto = $foto_destino;
        } else {
            $error = "Error al subir la imagen. Por favor, inténtalo de nuevo.";
        }
    }

    if (!isset($error)) {
        $sql = "INSERT INTO candidatos (nombre, cargo, foto, propuestas) VALUES ('$nombre', '$cargo', '$foto', '$propuestas')";
        if ($conn->query($sql) === TRUE) {
            $mensaje = "Candidato agregado con éxito.";
        } else {
            $error = "Error al agregar el candidato: " . $conn->error;
        }
    }
}

// Manejar la eliminación de candidatos
if (isset($_GET['eliminar'])) {
    $id = $conn->real_escape_string($_GET['eliminar']);
    $sql = "DELETE FROM candidatos WHERE id = '$id'";
    $conn->query($sql);
}

// Obtener lista de candidatos
$sql = "SELECT * FROM candidatos ORDER BY nombre";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Candidatos</title>
    <link rel="stylesheet" href="css/gestionar_candidatos.css">
    <link rel="icon" type="image/x-icon" href="./img/icono.jpg">
</head>
<body>

    <div class="video-container">
        <video autoplay muted loop class="background-video">
            <source src="./video/salon4.mp4" type="video/mp4">
        </video>
    </div>

    <h2>Gestionar Candidatos</h2>

    <?php if (isset($mensaje)): ?>
        <p class="success"><?php echo $mensaje; ?></p>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <h3>Agregar Nuevo Candidato</h3>
    <form method="post" enctype="multipart/form-data">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required><br>

        <label for="cargo">Cargo:</label>
        <select id="cargo" name="cargo" required>
            <option value="personero">Personero</option>
            <option value="cavildante">Cavildante</option>
            <option value="contralor">Contralor</option>
        </select><br>

        <label for="foto">Foto:</label>
        <input type="file" id="foto" name="foto" accept="image/*"><br>

        <label for="propuestas">Propuestas:</label>
        <textarea id="propuestas" name="propuestas" required></textarea><br>

        <button type="submit" name="agregar">Agregar Candidato</button>
    </form>

    <h3>Lista de Candidatos</h3>
    <table border="1">
        <tr>
            <th>Nombre</th>
            <th>Cargo</th>
            <th>Foto</th>
            <th>Propuestas</th>
            <th>Acciones</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                <td><?php echo htmlspecialchars($row['cargo']); ?></td>
                <td>
                    <?php if ($row['foto']): ?>
                        <img src="<?php echo htmlspecialchars($row['foto']); ?>" alt="Foto de <?php echo htmlspecialchars($row['nombre']); ?>" width="100">
                    <?php else: ?>
                        Sin foto
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($row['propuestas']); ?></td>
                <td>
                    <a href="?eliminar=<?php echo $row['id']; ?>" onclick="return confirm('¿Estás seguro de que quieres eliminar este candidato?');">Eliminar</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <p><a href="admin_panel.php">Volver al Panel de Administrador</a></p>
</body>
</html>