<?php
session_start();
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    $id_usuario = $_SESSION["id_usuario"];

    $host = "localhost";
    $user = "practica";
    $password = "practica";
    $dbname = "clase_pw";

    $conn = mysqli_connect($host, $user, $password, $dbname);

    // Verificar si la conexión fue exitosa
    if (!$conn) {
    die("Conexión fallida: " . mysqli_connect_error());
    }

    $sql_amigos = "SELECT u.id, u.nombre, u.apellidos, u.usuario FROM usuarios u INNER JOIN amigos a ON (u.id = a.id_amigo OR u.id = a.id_usuario) WHERE (a.id_usuario = $id_usuario OR a.id_amigo = $id_usuario) AND a.estado = 'aceptada' AND u.id <> $id_usuario";
    $resultado_amigos = mysqli_query($conn, $sql_amigos);

    // Almacenar los amigos en un arreglo para su uso posterior
    $amigos_disponibles = array();
    while ($row_amigo = mysqli_fetch_assoc($resultado_amigos)) {
        $amigos_disponibles[] = $row_amigo;
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Recuperar los datos del formulario
        $titulo = $_POST["titulo"];
        $tipoActividad = $_POST["actividad"];
        $companeros = isset($_POST["companeros"]) ? $_POST["companeros"] : "";
        $rutaTmpName = $_FILES["ruta"]["tmp_name"];
        $rutaName = $_FILES["ruta"]["name"];
        $imagenesTmpNames = $_FILES["imagenes"]["tmp_name"];
        $imagenesNames = $_FILES["imagenes"]["name"];
        $companeroSeleccionado = $_POST["companeros"];
        $nombreCompanero = "";
        $apellidoCompanero = "";

        if (!empty($companeroSeleccionado)) {
            $companeroData = explode(':', $companeroSeleccionado);
            $nombreCompanero = $companeroData[1];
            $apellidoCompanero = isset($companeroData[2]) ? $companeroData[2] : '';
        }

        // Validar los datos
        $errores = array();

        // Validar título
        if (empty($titulo)) {
            $errores[] = "El título es requerido.";
        }

        // Validar ruta GPX
        if (empty($rutaTmpName)) {
            $errores[] = "Se requiere una ruta GPX.";
        } else {
            // Directorio donde se guardarán las rutas
            $carpetaRutas = '../Users_images/Usuarios' . $_SESSION["id_usuario"] . '/rutas';

            // Verificar si la carpeta existe, y si no, crearla
            if (!is_dir($carpetaRutas)) {
                mkdir($carpetaRutas, 0777, true);
            }

            // Obtener el último número de ruta guardado
            $ultimoNumeroRuta = 0;
            $archivosRuta = glob($carpetaRutas . '/ruta*.gpx');
            if ($archivosRuta !== false) {
                $numerosRuta = array_map(function($archivo) {
                    return intval(str_replace('.gpx', '', substr($archivo, strrpos($archivo, '/') + 5)));
                }, $archivosRuta);
                if (!empty($numerosRuta)) {
                    $ultimoNumeroRuta = max($numerosRuta);
                } else {
                    $ultimoNumeroRuta = 0;
                }
            }

            // Generar un nombre único para el archivo GPX
            $rutaDestino = $carpetaRutas . '/ruta' . ($ultimoNumeroRuta + 1) . '.gpx';

            // Mover el archivo de la ruta GPX a la carpeta en el servidor
            move_uploaded_file($rutaTmpName, $rutaDestino);
        }

        $carpetaUsuario = '../Users_images/Usuarios' . $_SESSION["id_usuario"];
        $rutaGPX = $carpetaUsuario . "/rutas/ruta.gpx";

        // Obtener el número de la ruta actual
        $numeroRutaActual = $ultimoNumeroRuta + 1;

        $carpetaImagenes = '../Users_images/Usuarios' . $_SESSION["id_usuario"] . '/imagenes' . $numeroRutaActual;

        // Verificar si la carpeta existe, y si no, crearla
        if (!is_dir($carpetaImagenes)) {
            mkdir($carpetaImagenes, 0777, true);
        }

        // Mover las imágenes a la carpeta en el servidor
        $imagenURLs = array();
        foreach ($imagenesTmpNames as $key => $imagenTmpName) {
            if (!empty($imagenTmpName)) {
                $nombreImagen = $imagenesNames[$key];
                $imagenDestino = $carpetaImagenes . '/' . $nombreImagen;
                move_uploaded_file($imagenTmpName, $imagenDestino);
                $imagenURLs[] = $carpetaImagenes . '/' . $nombreImagen;

                $tamaño = filesize($imagenDestino);
                $dimensiones = getimagesize($imagenDestino);
                $alto = $dimensiones[1];
                $ancho = $dimensiones[0];
            }
        }

        // Convertir las URLs de las imágenes en una cadena separada por comas
        $imagenesURL = implode(',', $imagenURLs);

        $sql = "INSERT INTO actividad (id_usuario, titulo, tipo_actividad, ruta_gpx, ruta_imagenes, companeros_actividad) VALUES ('$id_usuario', '$titulo', '$tipoActividad', '$rutaDestino', '$carpetaImagenes', '$nombreCompanero $apellidoCompanero')";
        if (mysqli_query($conn, $sql)) {
            echo "Publicación añadida con éxito!.";
        } else {
            echo "Error al añadir la publicación: " . mysqli_error($conn);
        }
        
        if (!empty($imagenTmpName)) {
            $sql_imagenes = "INSERT INTO imagenes (id_usuario, nombre, tamaño, alto, ancho, ruta) VALUES ('$id_usuario', '$nombreImagen', '$tamaño', '$alto', '$ancho', '$carpetaImagenes')";
            if (mysqli_query($conn, $sql_imagenes)) {
                echo "Datos de la imagen guardados correctamente.";
            } else {
                echo "Error al guardar los datos de la imagen: " . mysqli_error($conn);
            }
        }   
        $sql_rutas = "INSERT INTO rutas (ruta, id_usuario) VALUES ('$rutaDestino', '$id_usuario')";
        if (mysqli_query($conn, $sql_rutas)) {
            echo "Datos de la ruta guardados correctamente.";
        } else {
            echo "Error al guardar los datos de la ruta: " . mysqli_error($conn);
        }

        mysqli_close($conn);
    }
} else {
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <link rel="stylesheet" href="index.css">
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
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
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="file"] {
            margin-top: 5px;
        }

        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
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
    <title>Nueva publicación</title>
    <h2>Añada su publicación</h2>
</head>

<body>
    <div class="container">
        <form method="post" action="anadir_publicacion.php" enctype="multipart/form-data">
            <label for="titulo">Título:</label>
            <input type="text" name="titulo" required>

            <label for="tipo_actividad">Tipo de actividad:</label>
            <input type="text" name="actividad" required/>

            <label for="ruta">Ruta GPX:</label>
            <input type="file" name="ruta" required accept=".gpx">

            <label for="companeros">Compañero de actividad (amigo):</label>
            <select name="companeros" onchange="updateCompaneros(this)">
                <option value="">Seleccionar amigo...</option>
                <?php foreach ($amigos_disponibles as $amigo) : ?>
                    <option value="<?php echo $amigo['id']; ?>:<?php echo $amigo['nombre']; ?> <?php echo $amigo['apellidos']; ?>"><?php echo $amigo['nombre']; ?> <?php echo $amigo['apellidos']; ?></option>
                <?php endforeach; ?>
            </select>
            <input type="hidden" name="companeros_act" id="companeros_act" value="">

            <label for="imagenes">Imágenes:</label>
            <input type="file" name="imagenes[]" multiple accept="image/jpeg,image/png">

            <input type="submit" value="Publicar actividad">

            <a href="web.php" class="boton">Volver</a>
        </form>

        <script>
            function updateCompaneros(selectElement) {
                document.getElementById("companeros_act").value = selectElement.value;
            }
        </script>
</body>

</html>