<?php
session_start();
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    $id_usuario = $_POST["id_usuario"];

	$host = "localhost";
    $user = "practica";
    $password = "practica";
    $dbname = "clase_pw";

    $conn = mysqli_connect($host, $user, $password, $dbname);

    // Verificar si la conexión fue exitosa
    if (!$conn) {
    die("Conexión fallida: " . mysqli_connect_error());
    }

	$xml = simplexml_load_file('provinciasypoblaciones.xml');

	$sql = "SELECT * FROM usuarios WHERE id = '$id_usuario'";
	$resultado = mysqli_query($conn, $sql);

	if (!$resultado) {
		echo "Error al obtener los detalles del usuario: " . mysqli_error($conn);
		exit;
	}

	$usuario = mysqli_fetch_assoc($resultado);

	$idProvincia = $usuario['provincia'];

	$nombreProvincia = '';
    foreach ($xml->provincia as $provincia) {
        if ((string)$provincia['id'] === $idProvincia) {
            $nombreProvincia = (string)$provincia->nombre;
            break;
        }
    }

	// Obtener la lista de amigos excluyendo al usuario actual
	$sqlAmigos = "SELECT u.id, u.nombre, u.apellidos, u.usuario FROM usuarios u INNER JOIN amigos a ON (u.id = a.id_amigo OR u.id = a.id_usuario) WHERE (a.id_usuario = $id_usuario OR a.id_amigo = $id_usuario) AND a.estado = 'aceptada' AND u.id <> $id_usuario";
	$resultadoAmigos = mysqli_query($conn, $sqlAmigos);
	$numAmigos = mysqli_num_rows($resultadoAmigos);
} else {
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="stylesheet"  href="index.css">
    <meta charset="UTF-8">
    <title>Mi perfil</title>
</head>
<body>
	<div class="perfil">
		<div class="info-perfil">
			<img src="../Users_images/Usuarios<?php echo $id_usuario; ?>/imagen_perfil.jpg" alt="Foto de perfil">
		</div>
		<h1><?php echo $usuario["nombre"]; ?> <?php echo $usuario["apellidos"]; ?></h1>
		<h2><?php echo $usuario["usuario"]; ?></h2>

		<div class="informacion">
			<span>Localización:</span>
			<p><?php echo $usuario["localidad"]; ?>, <?php echo $nombreProvincia; ?>, <?php echo $usuario["pais"];?></p>
		</div>

		<div class="informacion">
			<span>Email:</span>
			<p><?php echo $usuario["email"]; ?></p>
		</div>

		<div class="informacion">
			<span>Actividad preferida:</span>
			<p><?php echo $usuario["actividad_preferida"]; ?></p>
		</div>

		<div class="amigos">
			<a href="#lista-amigos">Amigos: <?php echo $numAmigos; ?></a>
		</div>

		<div id="lista-amigos">
			<ul>
				<?php while ($amigo = mysqli_fetch_assoc($resultadoAmigos)) : ?>
					<li><?php echo $amigo['nombre']; ?> <?php echo $amigo['apellidos']; ?> (<?php echo $amigo['usuario']; ?>)
					</li>
					
				<?php endwhile; ?>
			</ul>
		</div>

	<div class="boton">
    	<a href="administrador.php"><input type="button" value="Volver a inicio"></a>
	</div>

</body>
</html>