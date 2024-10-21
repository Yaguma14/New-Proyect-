<?php
require_once 'config.php';

// Verificar si se ha enviado la contraseña correcta
if (!isset($_SESSION['admin_auth']) && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['admin_password'])) {
    if ($_POST['admin_password'] === ADMIN_REGISTER_PASSWORD) {
        $_SESSION['admin_auth'] = true;
    } else {
        $error = "Contraseña incorrecta";
    }
}

// Verificar si el usuario está autenticado como superadministrador o ha proporcionado la contraseña correcta
if (!es_superadministrador() && !isset($_SESSION['admin_auth'])) {
    if (!isset($error)) {
        $error = "Por favor, ingrese la contraseña para acceder a esta página.";
    }
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Autenticación para Registro de Administrador</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
        <h2>Autenticación Requerida</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="post">
            <label for="admin_password">Contraseña de Acceso:</label>
            <input type="password" id="admin_password" name="admin_password" required>
            <button type="submit">Acceder</button>
        </form>
    </body>
    </html>
    <?php
    exit();
}

// Procesar el registro del administrador
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['registrar'])) {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $correo = $conn->real_escape_string($_POST['correo']);
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
    $rol = 'administrador';

    $sql = "INSERT INTO usuarios (nombre, correo, contrasena, rol) VALUES ('$nombre', '$correo', '$contrasena', '$rol')";
    
    if ($conn->query($sql) === TRUE) {
        $mensaje = "Administrador registrado con éxito";
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
    <title>Registrar Administrador</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Registrar Nuevo Administrador</h2>
    <?php 
    if (isset($mensaje)) echo "<p class='success'>$mensaje</p>";
    if (isset($error)) echo "<p class='error'>$error</p>";
    ?>
    <form method="post">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required><br>

        <label for="correo">Correo electrónico:</label>
        <input type="email" id="correo" name="correo" required><br>

        <label for="contrasena">Contraseña:</label>
        <input type="password" id="contrasena" name="contrasena" required><br>

        <button type="submit" name="registrar">Registrar Administrador</button>
    </form>
    <p><a href="admin_panel.php">Volver al Panel de Administrador</a></p>
</body>
</html>