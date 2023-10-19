<?php
session_start();
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    // Obtener el ID del usuario
    $id_usuario = $_SESSION["id"];
    $_SESSION["id_usuario"] = $id_usuario;
    $_SESSION["correo"]=$_SESSION["email"];
} else {
    header("location: login.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "clase_pw";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consulta para obtener los amigos del usuario
$query = "SELECT u.id, u.nombre, u.apellidos, u.usuario FROM usuarios u INNER JOIN amigos a ON (u.id = a.id_amigo OR u.id = a.id_usuario) WHERE (a.id_usuario = $id_usuario OR a.id_amigo = $id_usuario) AND a.estado = 'aceptada' AND u.id <> $id_usuario";
$result = mysqli_query($conn, $query);

// Array para almacenar las rutas de los amigos del usuario
$rutasAmigos = array();

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $rutasAmigos[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <link rel="stylesheet" href="index.css">
    <meta charset="UTF-8">
    <title>BIENVENIDOS</title>
    <script src="jquery-3.6.3.min.js"></script>
    <link rel="stylesheet" href="leaflet/leaflet.css" />
    <script src="leaflet/leaflet.js"></script>
    <script src="leaflet/gpx/gpx.js"></script>
    <style type="text/css">
        .mapa {
            height: 500px;
            width: 500px;
        }

        .datos {
            height: 500px;
            width: 500px;
        }
    </style>
    <script type="text/javascript">
        function msToTime(milliseconds) {
            //Get hours from milliseconds
            var hours = milliseconds / (1000 * 60 * 60);
            var absoluteHours = Math.floor(hours);
            var h = absoluteHours > 9 ? absoluteHours : '0' + absoluteHours;

            //Get remainder from hours and convert to minutes
            var minutes = (hours - absoluteHours) * 60;
            var absoluteMinutes = Math.floor(minutes);
            var m = absoluteMinutes > 9 ? absoluteMinutes : '0' + absoluteMinutes;

            //Get remainder from minutes and convert to seconds
            var seconds = (minutes - absoluteMinutes) * 60;
            var absoluteSeconds = Math.floor(seconds);
            var s = absoluteSeconds > 9 ? absoluteSeconds : '0' + absoluteSeconds;

            return h == "00" ? m + ':' + s : h + ':' + m + ':' + s;
        }

        $(document).ready(function () {
            <?php foreach ($rutasAmigos as $amigo): ?>
                <?php
                $nombre = $amigo['nombre'];
                $apellidos = $amigo['apellidos'];
                $usuario = $amigo['usuario'];

                $carpetaUsuario = '../Users_images/Usuarios' . $amigo['id'];
                $carpetaRutas = $carpetaUsuario . '/rutas';

                //$rutaAmigo = '';
                //$rutaMasReciente = '';

                if (is_dir($carpetaRutas)) {
                    $archivosRutas = glob($carpetaRutas . '/*.gpx');
                    if (!empty($archivosRutas)) {
                        $archivoMasReciente = max($archivosRutas);
                        $rutaMasReciente = str_replace($carpetaUsuario, '..\\Users_images\\Usuarios' . $amigo['id'], $archivoMasReciente);
                        $rutaAmigo = str_replace('\\', '/', $rutaMasReciente);
                    }
                }
                ?>

                <?php if (!empty($rutaAmigo)) : ?>
                    var map<?php echo $amigo['id']; ?> = L.map('mapa<?php echo $amigo['id']; ?>').setView([35.896, -5.29], 15);
                    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: 'Programación Web'
                    }).addTo(map<?php echo $amigo['id']; ?>);
                    var gpxData<?php echo $amigo['id']; ?> = '<?php echo $rutaAmigo; ?>';
                    var gpx<?php echo $amigo['id']; ?> = new L.GPX(gpxData<?php echo $amigo['id']; ?>, {
                        async: true,
                        marker_options: {
                            startIconUrl: 'leaflet/gpx/images/pin-icon-start.png',
                            endIconUrl: 'leaflet/gpx/images/pin-icon-end.png',
                            shadowUrl: 'leaflet/gpx/images/pin-shadow.png'
                        },
                        polyline_options: {
                            color: 'red',
                            opacity: 0.75,
                            weight: 3,
                            lineCap: 'round'
                        }
                    }).on('loaded', function (e) {
                        map<?php echo $amigo['id']; ?>.fitBounds(e.target.getBounds());
                        const inicio = new Date(gpx<?php echo $amigo['id']; ?>.get_start_time()).toLocaleString();
                        const fin = new Date(gpx<?php echo $amigo['id']; ?>.get_end_time()).toLocaleString();
                        const kms = (gpx<?php echo $amigo['id']; ?>.get_distance() / 1000).toFixed(2);
                        const tiempoTotal = msToTime(gpx<?php echo $amigo['id']; ?>.get_total_time());
                        const tiempoMovimiento = msToTime(gpx<?php echo $amigo['id']; ?>.get_moving_time());
                    }).addTo(map<?php echo $amigo['id']; ?>);
                <?php endif; ?>

            <?php endforeach; ?>
        });
    </script>
</head>

<body>
    <header class="header">
        <div class="logo">
            <a href="cambiar_foto.php"><img src="../Users_images/Usuarios<?php echo $id_usuario; ?>/imagen_perfil.jpg" style="border-radius: 50%;"></a>
        </div>
        <nav>
            <ul class="nav-links">
                <li><a href="perfil.php">Mi Perfil</a></li>
                <li><a href="anadir_publicacion.php">Añadir Publicación</a></li>
                <li><a href="../ws/cliente_soap.php">Buscar usuarios</a></li>
            </ul>
        </nav>
        <!--<div class="buscador">
            <i class="uil-search"></i>
            <form method="POST" action="buscar.php">
                <label>Nombre:</label>
                <input type="text" name="nombre"><br>
                <label>Apellidos:</label>
                <input type="text" name="apellidos"><br>
                <input type="submit" value="Buscar">
            </form>
        </div>-->
        <div class="boton">
            <input type="button" value="Cerrar sesión" onclick="javascript:validar()" />
        </div>
    </header>

    <h2>Publicaciones</h2>
<!-- Aquí se mostrará el mapa con la ruta y las imágenes -->
<?php foreach ($rutasAmigos as $amigo) : ?>
    <?php
    $carpetaUsuario = '../Users_images/Usuarios' . $amigo['id'];
    $carpetaRutas = $carpetaUsuario . '/rutas';

    $rutaAmigo = '';
    $rutaMasReciente = '';

    if (is_dir($carpetaRutas)) {
        $archivosRutas = glob($carpetaRutas . '/*.gpx');
        if (!empty($archivosRutas)) {
            $archivoMasReciente = max($archivosRutas);
            $rutaMasReciente = str_replace($carpetaUsuario, '..\\Users_images\\Usuarios' . $amigo['id'], $archivoMasReciente);
            $rutaAmigo = str_replace('\\', '/', $rutaMasReciente);
        }
    }
    ?>

    <?php if (!empty($rutaAmigo)) : ?>
        <div class="amigo-info">
            <img src="../Users_images/Usuarios<?php echo $amigo['id']; ?>/imagen_perfil.jpg" class="amigo-foto">
            <span class="amigo-nombre"><?php echo $amigo['nombre']; ?></span>
            <span class="amigo-apellidos"><?php echo $amigo['apellidos']; ?></span>
            <span class="amigo-usuario"><?php echo $amigo['usuario']; ?></span>
        </div>
        <div id="mapa<?php echo $amigo['id']; ?>" class="mapa"></div>
        <div id="datos<?php echo $amigo['id']; ?>" class="datos">
            <?php
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "clase_pw";
            $conn1 = new mysqli($servername, $username, $password, $dbname);
            $query1 = "SELECT titulo, tipo_actividad, companeros_actividad, ruta_imagenes, aplausos FROM actividad WHERE id_usuario = {$amigo['id']} AND ruta_gpx='{$rutaAmigo}'";
            $result1 = mysqli_query($conn1, $query1);

            if (mysqli_num_rows($result1) > 0) {
                while ($row = mysqli_fetch_assoc($result1)) {
                    echo "<h3>{$row['titulo']}</h3>";
                    echo "<p>Tipo de Actividad: {$row['tipo_actividad']}</p>";
                    echo "<p>Compañeros de Actividad: {$row['companeros_actividad']}</p>";
                    echo "<p>Aplausos: {$row['aplausos']}</p>";

                    // Obtener la ruta de las imágenes
                    $rutaImagenes = $row['ruta_imagenes'];
                    $carpetaImagenes = "{$rutaImagenes}";

                    if (is_dir($carpetaImagenes)) {
                        $imagenes = glob($carpetaImagenes . '/*.jpg');
                        if (!empty($imagenes)) {
                            foreach ($imagenes as $imagen) {
                                $rutaImagen = str_replace('\\', '/', $imagen);
                                echo "<img src='{$rutaImagen}' alt='Imagen' class='publicacion-imagen-web'>";
                            }
                        }
                    }
                }
            } else {
                echo 'No se encontraron publicaciones';
            }
            ?>
        </div>

        <div>
            <span id="aplausos<?php echo $amigo['id']; ?>"></span>
            <button onclick="aplauDir(<?php echo $amigo['id']; ?>)">Aplaudir</button>
        </div>
        <hr>
    <?php endif; ?>
<?php endforeach; ?>


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

        function aplauDir(id_usuario) {
            // Realizar una solicitud AJAX al servidor para aplaudir a un usuario
            $.ajax({
                url: 'dar_aplauso.php',
                type: 'POST',
                data: {
                    id_usuario: id_usuario
                },
                success: function(response) {
                    // Actualizar el contador de aplausos
                    $('#aplausos' + id_usuario).html(response);
                }
            });
        }
    </script>
</body>

</html>