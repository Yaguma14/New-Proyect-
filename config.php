<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sistema_votaciones');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
} else {
    echo "";
}

session_start();

function verificar_sesion() {
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: login.php");
        exit();
    }
}

function es_superadministrador() {
    // Verifica si el usuario ha iniciado sesión y si su rol es 'superadmin'
    return isset($_SESSION['usuario_id']) && isset($_SESSION['rol']) && $_SESSION['rol'] == 'superadmin';
}

// Agrega una contraseña para acceder a la página de registro de administradores
define('ADMIN_REGISTER_PASSWORD', '101420');


function es_administrador() {
    return isset($_SESSION['rol']) && $_SESSION['rol'] == 'administrador';
}
?>