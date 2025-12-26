<?php
// debug-session.php - Script temporal para verificar la sesión
session_start();

echo "<h2>Información de Sesión</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h3>Usuario Logueado:</h3>";
require_once 'includes/Class-usuario.php';
$Usuario_class = new Usuario();

if ($Usuario_class->usuarioLogueado()) {
    echo "✅ Usuario está logueado<br>";
    echo "ID: " . ($_SESSION['user_id'] ?? 'No definido') . "<br>";
    echo "Nombre: " . ($_SESSION['user_nombre'] ?? 'No definido') . "<br>";
    echo "Email: " . ($_SESSION['user_email'] ?? 'No definido') . "<br>";
    echo "Rol: " . ($_SESSION['user_rol'] ?? 'No definido') . "<br>";
    echo "Rol en minúsculas: " . strtolower($_SESSION['user_rol'] ?? '') . "<br>";
    
    echo "<h3>Verificación de Permisos:</h3>";
    $es_admin_metodo = $Usuario_class->esAdministrador();
    echo "¿Es administrador? (método): " . ($es_admin_metodo ? "✅ Sí" : "❌ No") . "<br>";
    
    $verifica_admin = $Usuario_class->verificarPermisos('Administrador');
    echo "¿Tiene permisos de Administrador? " . ($verifica_admin ? "✅ Sí" : "❌ No") . "<br>";
    
    $verifica_admin_lower = $Usuario_class->verificarPermisos('administrador');
    echo "¿Tiene permisos de administrador? " . ($verifica_admin_lower ? "✅ Sí" : "❌ No") . "<br>";
} else {
    echo "❌ Usuario NO está logueado<br>";
}

echo "<br><hr><br>";
echo "<a href='index.php'>Ir al Dashboard</a> | ";
echo "<a href='usuarios.php'>Ir a Usuarios</a> | ";
echo "<a href='roles.php'>Ir a Roles</a> | ";
echo "<a href='login.php'>Ir a Login</a>";
?>
