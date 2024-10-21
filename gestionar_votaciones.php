<?php
require_once 'config.php';
verificar_sesion();

if (!es_administrador()) {
    header("Location: foro.php");
    exit();
}

// Obtener el estado actual de las votaciones
$sql = "SELECT votaciones_abiertas FROM configuracion_sistema LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $votaciones_abiertas = $row['votaciones_abiertas'];
} else {
    // Si no hay configuración, crearla
    $sql = "INSERT INTO configuracion_sistema (votaciones_abiertas) VALUES (0)";
    $conn->query($sql);
    $votaciones_abiertas = 0;
}

// Manejar la actualización del estado de las votaciones
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['actualizar_estado'])) {
    $nuevo_estado = $_POST['estado'] == '1' ? 1 : 0;
    $sql = "UPDATE configuracion_sistema SET votaciones_abiertas = $nuevo_estado";
    
    if ($conn->query($sql) === TRUE) {
        $votaciones_abiertas = $nuevo_estado;
        $mensaje = "El estado de las votaciones ha sido actualizado.";
    } else {
        $error = "Error al actualizar el estado de las votaciones: " . $conn->error;
    }
}

// Obtener estadísticas de votación
$sql_estadisticas = "
    SELECT 
        (SELECT COUNT(*) FROM votos_candidatos) as total_votos_candidatos,
        (SELECT COUNT(*) FROM votos_actividades) as total_votos_actividades,
        (SELECT COUNT(*) FROM usuarios WHERE rol = 'estudiante') as total_estudiantes
";
$result_estadisticas = $conn->query($sql_estadisticas);
$estadisticas = $result_estadisticas->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Votaciones</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Gestionar Votaciones</h2>
    
    <?php if (isset($mensaje)): ?>
        <p class="success"><?php echo $mensaje; ?></p>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="post">
        <h3>Estado actual de las votaciones</h3>
        <p>Las votaciones están actualmente: <strong><?php echo $votaciones_abiertas ? 'Abiertas' : 'Cerradas'; ?></strong></p>
        
        <label for="estado">Cambiar estado de las votaciones:</label>
        <select name="estado" id="estado">
            <option value="1" <?php echo $votaciones_abiertas ? 'selected' : ''; ?>>Abiertas</option>
            <option value="0" <?php echo !$votaciones_abiertas ? 'selected' : ''; ?>>Cerradas</option>
        </select>
        
        <button type="submit" name="actualizar_estado">Actualizar Estado</button>
    </form>

    <h3>Estadísticas de Votación</h3>
    <table>
        <tr>
            <th>Métrica</th>
            <th>Valor</th>
        </tr>
        <tr>
            <td>Total de votos para candidatos</td>
            <td><?php echo $estadisticas['total_votos_candidatos']; ?></td>
        </tr>
        <tr>
            <td>Total de votos para actividades recreativas</td>
            <td><?php echo $estadisticas['total_votos_actividades']; ?></td>
        </tr>
        <tr>
            <td>Total de estudiantes registrados</td>
            <td><?php echo $estadisticas['total_estudiantes']; ?></td>
        </tr>
        <tr>
            <td>Porcentaje de participación (candidatos)</td>
            <td><?php echo $estadisticas['total_estudiantes'] > 0 ? round(($estadisticas['total_votos_candidatos'] / $estadisticas['total_estudiantes']) * 100, 2) : 0; ?>%</td>
        </tr>
        <tr>
            <td>Porcentaje de participación (actividades)</td>
            <td><?php echo $estadisticas['total_estudiantes'] > 0 ? round(($estadisticas['total_votos_actividades'] / $estadisticas['total_estudiantes']) * 100, 2) : 0; ?>%</td>
        </tr>
    </table>

    <p><a href="admin_panel.php">Volver al Panel de Administrador</a></p>
</body>
</html>