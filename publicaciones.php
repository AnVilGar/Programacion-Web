<?php
session_start();
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    $id_usuario = $_SESSION["id_usuario"];
} else {
    header("location: login.php");
    exit;
}

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

// Consultar las publicaciones del usuario por su ID
$sql = "SELECT * FROM actividad WHERE id_usuario = $id_usuario";
$resultado = mysqli_query($conn, $sql);

// Verificar si se encontraron resultados
if (mysqli_num_rows($resultado) > 0) {
    // Imprimir la tabla de publicaciones con estilos de espaciado
    echo '<table style="border-collapse: collapse;">';
    echo '<tr><th style="padding: 10px;">Título</th><th style="padding: 10px;">Tipo de Actividad</th><th style="padding: 10px;">Compañero de Actividad</th><th style="padding: 10px;">Aplausos</th><th style="padding: 10px;">Imagen</th></tr>';

    // Recorrer los resultados y mostrar cada publicación en una fila de la tabla
    while ($row = mysqli_fetch_assoc($resultado)) {
        echo '<tr>';
        echo '<td style="padding: 8px;">' . $row['titulo'] . '</td>';
        echo '<td style="padding: 8px;">' . $row['tipo_actividad'] . '</td>';
        echo '<td style="padding: 8px;">' . $row['companeros_actividad'] . '</td>';
        echo '<td style="padding: 8px;">' . $row['aplausos'] . '</td>';
        echo '<td style="padding: 8px;">';

        $rutaImagenes = $row['ruta_imagenes'];
        $carpetaImagenes = "{$rutaImagenes}";

        if (is_dir($carpetaImagenes)) {
            $imagenes = glob($carpetaImagenes . '/*.jpg');
            if (!empty($imagenes)) {
                echo "<div class='imagenes-web'>";
                foreach ($imagenes as $imagen) {
                    $rutaImagen = str_replace('\\', '/', $imagen);
                    echo "<div class='imagen-item'>";
                    echo "<img src='{$rutaImagen}' alt='Imagen' class='publicacion-imagen-web' width='200' height='150'>";
                    echo "</div>";
                }
                echo "</div>";
            }
        }

        echo '<td style="padding: 8px;">';
        echo '<form action="eliminar_publicacion.php" method="post"><input type="hidden" name="id_publicacion" value="' . $row['id'] . '"><input type="submit" value="Eliminar"></form>';
        echo '<form action="mostrar_ruta.php" method="post"><input type="hidden" name="ruta_gpx" value="' . $row['ruta_gpx'] . '"><input type="submit" value="Mostrar ruta"></form>';
        echo '</td>';
        echo '</tr>';
    }

    echo '</table>';

    echo '<a href="perfil.php"><input type="button" value="Volver al perfil"></a>';
} else {
    echo 'No se encontraron publicaciones para el usuario';
    echo '<a href="perfil.php"><input type="button" value="Volver al perfil"></a>';
}

// Cerrar la conexión a la base de datos
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Mis Publicaciones</title>
    <style>
        body {
            background-color: #f0f2f5;
            font-family: Arial, sans-serif;
        }

        table {
            width: 100%;
            background-color: #fff;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 20px;
        }

        th,
        td {
            padding: 10px;
        }

        th {
            background-color: #f0f2f5;
            text-align: left;
        }

        td {
            background-color: #fff;
        }

        td img {
            width: 200px;
            height: 150px;
        }

        input[type="button"],
        input[type="submit"] {
            background-color: greenyellow;
            color: black;
            border: none;
            padding: 8px 16px;
            font-size: 14px;
            cursor: pointer;
        }

        a {
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
</body>

</html>