<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $correo = $conn->real_escape_string($_POST['correo']);
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
    $rol = 'estudiante'; // Por defecto, todos los nuevos registros son estudiantes

    $sql = "INSERT INTO usuarios (nombre, correo, contrasena, rol) VALUES ('$nombre', '$correo', '$contrasena', '$rol')";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: login.php");
        exit();
    } else {
        $error = "Error al registrar: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Registro de Usuario</h2>
    <?php if (isset($error)) echo "<p>$error</p>"; ?>
    <form method="post">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required><br>

        <label for="correo">Correo electrónico:</label>
        <input type="email" id="correo" name="correo" required><br>

        <label for="contrasena">Contraseña:</label>
        <input type="password" id="contrasena" name="contrasena" required><br>

        <button type="submit">Registrarse</button>
    </form>
    <p><a href="login.php">Volver al inicio de sesión</a></p>
</body>
</html>