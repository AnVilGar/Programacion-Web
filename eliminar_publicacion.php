<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Verificar si se proporcionó el ID de la publicación en $_POST
if (!isset($_POST["id_publicacion"]) || empty($_POST["id_publicacion"])) {
    echo "Error: ID de publicación no válido";
    exit;
}

$correo=$_SESSION["email"];
$id_publicacion = $_POST["id_publicacion"];
$id_usuario = $_SESSION["id_usuario"];

// Establecer la conexión a la base de datos
$host = "localhost";
$user = "practica";
$password = "practica";
$dbname = "clase_pw";
$conn = mysqli_connect($host, $user, $password, $dbname);

// Verificar si la conexión fue exitosa
if (!$conn) {
    die("Conexión fallida: " . mysqli_connect_error());
}

$sql = "SELECT ruta_gpx, ruta_imagenes FROM actividad WHERE id = $id_publicacion AND id_usuario = $id_usuario";
$resultado = mysqli_query($conn, $sql);

if ($resultado) {
    $row = mysqli_fetch_assoc($resultado);
    $rutaGPX = $row['ruta_gpx'];
    $carpetaImagenes = $row['ruta_imagenes'];
} else {
    echo "Error al obtener las rutas del archivo GPX y las imágenes: " . mysqli_error($conn);
    exit;
}

if (unlink($rutaGPX)) {
    echo "El archivo GPX ha sido eliminado exitosamente.";
} else {
    echo "Error al eliminar el archivo GPX";
}

$archivosImagenes = glob($carpetaImagenes . '/*');
foreach ($archivosImagenes as $archivo) {
    if (is_file($archivo)) {
        unlink($archivo);
    }
}

if (is_dir($carpetaImagenes)) {
    rmdir($carpetaImagenes);
}

// Eliminar la publicación de la base de datos
$sql = "DELETE FROM actividad WHERE id = '$id_publicacion' AND id_usuario = '$id_usuario'";
$resultado = mysqli_query($conn, $sql);
$consulta= "DELETE FROM imagenes WHERE ruta= '$carpetaImagenes'";
$resultadoimg=mysqli_query($conn, $consulta);
$consulta1= "DELETE FROM rutas WHERE ruta= '$rutaGPX'";
$resultadogpx=mysqli_query($conn, $consulta1);

if ($resultado) {
    echo "La publicación ha sido eliminada exitosamente.";
    if ($correo != 'admin@admin.com'){
        echo '<a href="perfil.php"><input type="button" value="Volver al perfil"></a>';
        echo '<a href="web.php"><input type="button" value="Volver a inicio"></a>';
    }else{
        echo '<a href="administrador.php"><input type="button" value="Volver a inicio"></a>';
    }
} else {
    echo "Error al eliminar la publicación: " . mysqli_error($conn);
}

// Cerrar la conexión a la base de datos
mysqli_close($conn);
?>
