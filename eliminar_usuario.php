<?php
// Obtener el ID del usuario a eliminar (puedes obtenerlo de una variable POST, GET u otra fuente)
$id = $_POST["id"];

// Realizar la conexión a la base de datos
$host = "localhost";
$user = "practica";
$password = "practica";
$dbname = "clase_pw";

$conn = mysqli_connect($host, $user, $password, $dbname);

// Verificar la conexión
if (mysqli_connect_errno()) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Preparar y ejecutar la consulta para eliminar el usuario
$sql = "DELETE FROM usuarios WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

if (mysqli_stmt_affected_rows($stmt) > 0) {
    echo "Usuario eliminado con éxito";
    echo "<a href=\"administrador.php\">Volver</a></p>";
} else {
    echo "No se encontró ningún usuario con el ID proporcionado";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>







