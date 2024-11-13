<?php
require_once 'config.php';
verificar_sesion();

// Obtener publicaciones del foro
$sql = "SELECT p.*, u.nombre as autor, COUNT(c.id) as num_comentarios 
        FROM publicaciones_foro p 
        JOIN usuarios u ON p.usuario_id = u.id 
        LEFT JOIN comentarios_foro c ON p.id = c.publicacion_id
        GROUP BY p.id
        ORDER BY p.fecha_publicacion DESC";
$result = $conn->query($sql);

// Verificar si las votaciones están abiertas
$sql_votaciones = "SELECT votaciones_abiertas FROM configuracion_sistema LIMIT 1";
$result_votaciones = $conn->query($sql_votaciones);
$votaciones_abiertas = $result_votaciones->fetch_assoc()['votaciones_abiertas'];

// Verificar si el estudiante ya ha votado
$usuario_id = $_SESSION['usuario_id'];
$sql_check_voto = "SELECT id FROM votos_candidatos WHERE usuario_id = $usuario_id LIMIT 1";
$result_check_voto = $conn->query($sql_check_voto);
$ya_voto = $result_check_voto->num_rows > 0;

// Manejar mensajes
$mensaje = '';
if (isset($_GET['mensaje'])) {
    switch ($_GET['mensaje']) {
        case 'voto_exitoso':
            $mensaje = "¡Gracias por votar! Tu voto ha sido registrado exitosamente.";
            break;
        case 'ya_votaste':
            $mensaje = "Ya has emitido tu voto. Solo se permite un voto por estudiante.";
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foro Estudiantil</title>
    <link rel="stylesheet" href="css/foro.css">
    <link rel="icon" type="image/x-icon" href="./img/icono.jpg">
</head>
<body>
    <!-- Video de fondo -->
    <video autoplay muted loop class="video-background">
        <source src="./video/salon3.mp4" type="video/mp4">
        Tu navegador no soporta el video de fondo.
    </video>

    <div class="container">
        <h2>Foro Estudiantil</h2>

        <!-- Mensaje de alerta -->
        <?php if ($mensaje): ?>
            <div class="mensaje">
                <p><?php echo $mensaje; ?></p>
            </div>
        <?php endif; ?>

        <!-- Sección de votaciones -->
        <div class="votaciones">
            <?php if ($votaciones_abiertas && !$ya_voto): ?>
                <p><a href="votaciones.php">¡Las votaciones están abiertas! Haz clic aquí para votar.</a></p>
            <?php elseif ($votaciones_abiertas && $ya_voto): ?>
                <p>Las votaciones están abiertas, pero ya has emitido tu voto.</p>
            <?php else: ?>
                <p>Las votaciones se abrirán pronto.</p>
            <?php endif; ?>
        </div>

        <!-- Sección de novedades y publicaciones -->
        <h3>Novedades y Publicaciones</h3>
        <div class="publicaciones">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="publicacion">
                    <h4><?php echo htmlspecialchars($row['titulo']); ?></h4>
                    <p><?php echo htmlspecialchars($row['contenido']); ?></p>
                    <small>
                        Publicado por <?php echo htmlspecialchars($row['autor']); ?> el <?php echo $row['fecha_publicacion']; ?>
                        | <a href="comentar.php?id=<?php echo $row['id']; ?>">
                            Comentarios (<?php echo $row['num_comentarios']; ?>)
                          </a>
                    </small>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Enlace de cerrar sesión -->
        <div class="cerrar-ses
