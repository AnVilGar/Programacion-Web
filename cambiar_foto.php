<?php
session_start();
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
  $host = "localhost";
  $user = "practica";
  $password = "practica";
  $dbname = "clase_pw";

  $conn = mysqli_connect($host, $user, $password, $dbname);

  // Verificar si la conexión fue exitosa
  if (!$conn) {
    die("Conexión fallida: " . mysqli_connect_error());
  }

  $id_usuario = $_SESSION["id_usuario"];

  $sql1 = "SELECT * FROM usuarios WHERE id = $id_usuario";
  $resultado1 = mysqli_query($conn, $sql1);

  // Verificar si la consulta SQL fue exitosa
  if (!$resultado1) {
      echo "Error al obtener los detalles del usuario: " . mysqli_error($conn);
      exit;
  }

  // Obtener los detalles del usuario en un array asociativo
  $usuario = mysqli_fetch_assoc($resultado1);

  if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] == UPLOAD_ERR_OK) {
      // Obtener la ruta del archivo temporal donde se ha guardado la imagen subida
      $rutaImagenTemporal = $_FILES["imagen"]["tmp_name"];
    
      // Obtener la ruta de la carpeta de imágenes del usuario
      //$rutaDirectorioUsuario = 'C:\xampp\htdocs\clase_pw\Users_images\Usuarios' . $_SESSION["id_usuario"];
      $rutaDirectorioUsuario = '../Users_images\Usuarios' . $_SESSION["id_usuario"];
    
      // Mover la imagen a la carpeta del usuario y renombrarla como "imagen_perfil.jpg"
      move_uploaded_file($rutaImagenTemporal, $rutaDirectorioUsuario . "/imagen_perfil.jpg");
  }
}else {
  header("location: login.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="stylesheet"  href="index.css">
    <meta charset="UTF-8">
    <title>Cambiar foto</title>
    <style>
  /* Estilo para el formulario */
  form {
    max-width: 400px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f2f2f2;
    border-radius: 5px;
  }

  /* Estilo para las etiquetas */
  label {
    display: block;
    margin-bottom: 10px;
    font-weight: bold;
  }

  /* Estilo para el campo de selección de imagen */
  input[type="file"] {
    margin-bottom: 20px;
  }

  /* Estilo para el botón de envío */
  input[type="submit"] {
    background-color: #4CAF50;
    color: #fff;
    padding: 10px 20px;
    border: none;
    border-radius: 3px;
    cursor: pointer;
  }

  input[type="submit"]:hover {
    background-color: #45a049;
  }
</style>

</head>
<body>
    <div class="perfil">
		<div class="info-perfil">
    <img src="../Users_images/Usuarios<?php echo $id_usuario; ?>/imagen_perfil.jpg" alt="Foto de perfil"></div>
        <h1><?php echo $usuario["nombre"];?> <?php echo $usuario["apellidos"];?></h1>

      <form method="POST" action="cambiar_foto.php" enctype="multipart/form-data">
          <label for="imagen">Seleccionar imagen:</label>
          <input type="file" name="imagen" id="imagen">
          <input type="submit" value="Guardar">
      </form>


      <div class="boton">
        <a href="web.php"><input type="button" value="Volver a inicio"></a>
	</div>
</body>
</html>
    