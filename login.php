<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = $conn->real_escape_string($_POST['correo']);
    $contrasena = $_POST['contrasena'];

    $sql = "SELECT id, nombre, contrasena, rol FROM usuarios WHERE correo = '$correo'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $usuario = $result->fetch_assoc();
        if (password_verify($contrasena, $usuario['contrasena'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['rol'] = $usuario['rol'];

            if ($usuario['rol'] == 'administrador') {
                header("Location: admin_panel.php");
            } else {
                header("Location: foro.php");
            }
            exit();
        }
    }
    
    $error = "Correo o contraseña incorrectos";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Iniciar Sesión</h2>
    <?php if (isset($error)) echo "<p>$error</p>"; ?>
    <form method="post">
        <label for="correo">Correo electrónico:</label>
        <input type="email" id="correo" name="correo" required><br>

        <label for="contrasena">Contraseña:</label>
        <input type="password" id="contrasena" name="contrasena" required><br>

        <button type="submit">Iniciar Sesión</button>
    </form>
    <p><a href="registro.php">Registrarse</a></p>
</body>
</html>