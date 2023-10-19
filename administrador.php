<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="index.css">
    <title>Búsqueda de Usuarios</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: white;
        }

        .container {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.3);
        }

        h2 {
            text-align: center;
        }

        form {
            margin-top: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        select {
            width: 10%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="file"] {
            margin-top: 5px;
        }

        input[type="submit"] {
            width: 10%;
            padding: 10px;
            background-color: black;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .error {
            color: red;
            margin-top: 5px;
        }
    </style>
</head>
<body>
<header class="header">
    <h2>Página del administrador</h2>
    <div class="boton">
        <input type="button" value="Cerrar sesión" onclick="javascript:validar()" />
    </div>
</header>
<h1>Búsqueda de Usuarios</h1>
<form id="search-form" method="POST" action="administrador.php">
    <label for="nombre">Nombre:</label>
    <input type="text" id="nombre" name="nombre"><br>

    <label for="apellidos">Apellidos:</label>
    <input type="text" id="apellidos" name="apellidos"><br>

    <input type="submit" value="Buscar">
</form>

<div id="search-results"></div>

<form id="add-actividad-form" method="POST" action="administrador.php">
    <label for="nueva-actividad">Nueva actividad:</label>
    <input type="text" id="nueva-actividad" name="nueva-actividad" required>
    <input type="submit" value="Agregar">
</form>

<form id="delete-actividad-form" method="POST" action="administrador.php">
    <label for="eliminar-actividad">Eliminar actividad:</label>
    <input type="text" id="eliminar-actividad" name="eliminar-actividad" required>
    <input type="submit" value="Eliminar">
</form>

<script>
    function validar() {
        // Realizar una solicitud AJAX al servidor para cerrar la sesión
        $.ajax({
            url: 'cerrar_sesion.php',
            type: 'POST',
            success: function(response) {
                // Redirigir a la página de inicio de sesión después de cerrar sesión
                window.location = 'login.php';
            }
        });
    }
</script>
</body>
</html>

<?php
session_start();
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && $_SESSION["email"]==='admin@admin.com') {
    $id_usuario = $_SESSION["id"];
    $_SESSION["id_usuario"] = $id_usuario;
    $_SESSION["email"]=='admin@admin.com';

    $host = "localhost";
    $user = "practica";
    $password = "practica";
    $dbname = "clase_pw";

    $conn = mysqli_connect($host, $user, $password, $dbname);

    // Verificar si la conexión fue exitosa
    if (!$conn) {
    die("Conexión fallida: " . mysqli_connect_error());
    }
} else {
    header("location: login.php");
    exit;
}

$soapURL = "http://localhost/clase_pw/ws/servidor_soap.php";

// Namespace y nombre del servicio web
$namespace = "http://localhost/clase_pw/ws/servidor_soap.php";
$serviceName = "ServicioWeb";

// Crear una instancia del cliente SOAP
$client = new SoapClient(null, array(
    'location' => $soapURL,
    'uri' => $namespace
));
// Llamar al método del servicio web

if (!empty($_POST['nueva-actividad'])) {
    $nuevaActividad = $_POST['nueva-actividad'];
    $consulta_actividad= "INSERT INTO deportes (nombre) VALUES ('$nuevaActividad')";
    if (mysqli_query($conn, $consulta_actividad)) {
        echo "Añadido!";
    } else {
        echo "Error: " . $consulta_actividad . "<br>" . mysqli_error($conn);
    }
}

if (!empty($_POST['eliminar-actividad'])) {
    $antiguaActividad = $_POST['eliminar-actividad'];
    $consulta_actividad1= "DELETE FROM deportes WHERE nombre='$antiguaActividad'";
    if (mysqli_query($conn, $consulta_actividad1)) {
        echo "Eliminado!";
    } else {
        echo "Error: " . $consulta_actividad1 . "<br>" . mysqli_error($conn);
    }
}

if (!empty($_POST['nombre']) && !empty($_POST['apellidos'])) {
    $nombre = $_POST["nombre"];
    $apellidos = $_POST["apellidos"];
    $response = $client->__soapCall('getUserInfo', array($nombre, $apellidos));

    // Procesar la respuesta
    if ($response) {
        // Procesar los resultados y mostrarlos en la página
        $resultHTML = "<ul>";
        foreach ($response as $usuario) {
            $resultHTML .= "<li>Nombre: " . $usuario['nombre'] . "</li>";
            $resultHTML .= "<li>Apellidos: " . $usuario['apellidos'] . "</li>";
            $resultHTML .= "<li>Actividad preferida: " . $usuario['actividad_preferida'] . "</li>";
            $resultHTML .= "<li>ID: " . $usuario['id'] . "</li>";
            $resultHTML .= "<br>";
            $resultHTML .= "<form action=\"eliminar_usuario.php\" method=\"post\">";
            $resultHTML .= "<input type=\"hidden\" name=\"id\" value=\"" . $usuario['id'] . "\">";
            $resultHTML .= "<input type=\"submit\" value=\"Dar de baja\">";
            $resultHTML .= "</form>";
            $resultHTML .= "<form action=\"guardar_id_usuario_editar.php\" method=\"post\">";
            $resultHTML .= "<input type=\"hidden\" name=\"id_editar\" value=\"" . $usuario['id'] . "\">";
            $resultHTML .= "<input type=\"submit\" value=\"Editar perfil\">";
            $resultHTML .= "</form>";
            $resultHTML .= "<form action=\"publicaciones_administrador.php\" method=\"post\">";
            $resultHTML .= "<input type=\"hidden\" name=\"id_usuario\" value=\"" . $usuario['id'] . "\">";
            $resultHTML .= "<input type=\"submit\" value=\"Ver publicaciones\">";
            $resultHTML .= "</form>";
            $resultHTML .= "<form action=\"perfil_administrador.php\" method=\"post\">";
            $resultHTML .= "<input type=\"hidden\" name=\"id_usuario\" value=\"" . $usuario['id'] . "\">";
            $resultHTML .= "<input type=\"submit\" value=\"Ver perfil\">";
            $resultHTML .= "</form>";
            $resultHTML .= "<br>";
        }
        $resultHTML .= "</ul>";

        echo $resultHTML;
    } else {
        echo "No se encontraron resultados.";
    }
}
$conn->close();
?>