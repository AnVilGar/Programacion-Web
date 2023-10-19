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

$host = "localhost";
$user = "practica";
$password = "practica";
$dbname = "clase_pw";

$conn = mysqli_connect($host, $user, $password, $dbname);

// Verificar si la conexión fue exitosa
if (!$conn) {
    die("Conexión fallida: " . mysqli_connect_error());
}

function obtenerNumeroPublicaciones($id_usuario, $conn) {
    $query = "SELECT COUNT(*) as total FROM actividad WHERE id_usuario = $id_usuario";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }

    return 0;
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
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination .page-link {
            display: inline-block;
            padding: 8px 12px;
            margin: 0 5px;
            text-decoration: none;
            color: #333;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .pagination .page-link.current {
            background-color: greenyellow;
            color: black;
            border-color: greenyellow;
        }

        .pagination .page-link.disabled {
            opacity: 0.5;
            pointer-events: none;
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
                        attribution: 'FitConnect'
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
        <div class="boton">
            <input type="button" value="Cerrar sesión" onclick="javascript:validar()" />
        </div>
    </header>

    <h2>Publicaciones</h2>
<!-- Aquí se mostrará el mapa con la ruta y las imágenes -->
<?php
// Variables de paginación
$registrosPorPagina = 10;
$pag = (isset($_GET['pag'])) ? $_GET['pag'] : 1;

// Obtener el total de publicaciones
$totalPublicaciones = 0;
foreach ($rutasAmigos as $amigo) {
    $carpetaUsuario = '../Users_images/Usuarios' . $amigo['id'];
    $carpetaRutas = $carpetaUsuario . '/rutas';

    if (is_dir($carpetaRutas)) {
        $archivosRutas = glob($carpetaRutas . '/*.gpx');
        if (!empty($archivosRutas)) {
            $totalPublicaciones += count($archivosRutas);
        }
    }
}

// Calcular el número de páginas
$totalPaginas = ceil($totalPublicaciones / $registrosPorPagina);
$inicio = ($pag - 1) * $registrosPorPagina;
$fin = $inicio + $registrosPorPagina - 1;
$javascriptCode = '';
// Obtener las publicaciones a mostrar en la página actual
$publicacionesMostradas = 0;
$rutasMasRecientes = [];
foreach ($rutasAmigos as $amigo) {
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
                if ($publicacionesMostradas >= $inicio && $publicacionesMostradas <= $fin) {
                    // Mostrarpublicación
                    echo "<div class='amigo-info'>";
                    echo "<img src='../Users_images/Usuarios{$amigo['id']}/imagen_perfil.jpg' class='amigo-foto'>";
                    echo "<span class='amigo-nombre'>{$amigo['nombre']}</span>";
                    echo "<span class='amigo-apellidos'>{$amigo['apellidos']}</span>";
                    echo "<span class='amigo-usuario'>{$amigo['usuario']}</span>";
                    echo "</div>";
                    echo "<div id='mapa{$amigo['id']}' class='mapa'></div>";
                    echo "<div id='datos{$amigo['id']}' class='datos'>";
                    
                    $query1 = "SELECT titulo, tipo_actividad, companeros_actividad, ruta_imagenes, aplausos FROM actividad WHERE id_usuario = {$amigo['id']} AND ruta_gpx='{$rutaAmigo}'";
                    $result1 = mysqli_query($conn, $query1);

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
                                    echo "<div class='imagenes-web'>";
                                    foreach ($imagenes as $imagen) {
                                        $rutaImagen = str_replace('\\', '/', $imagen);
                                        echo "<div class='imagen-item'>";
                                        echo "<img src='{$rutaImagen}' alt='Imagen' class='publicacion-imagen-web'>";
                                        echo "</div>";
                                    }
                                    echo "</div>";
                                }
                            }
                        }
                    } else {
                        echo 'No se encontraron publicaciones';
                    }

                    echo "</div>";
                    echo "<div>";
                    echo "<span id='aplausos{$amigo['id']}'></span>";
                    echo "<button onclick='aplauDir({$amigo['id']})'>Aplaudir</button>";
                    echo "</div>";
                    echo "<hr>";

                    $publicacionesMostradas++;
                    // Almacenar el código JavaScript en una variable
                    $javascriptCode .= "var map{$amigo['id']} = L.map('mapa{$amigo['id']}').setView([35.896, -5.29], 15);";
                    $javascriptCode .= "L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {";
                    $javascriptCode .= "    maxZoom: 19,";
                    $javascriptCode .= "    attribution: 'FitConnect'";
                    $javascriptCode .= "}).addTo(map{$amigo['id']});";
                    $javascriptCode .= "var gpxData{$amigo['id']} = '{$rutaAmigo}';";
                    $javascriptCode .= "var gpx{$amigo['id']} = new L.GPX(gpxData{$amigo['id']}, {";
                    $javascriptCode .= "    async: true,";
                    $javascriptCode .= "    marker_options: {";
                    $javascriptCode .= "        startIconUrl: 'leaflet/gpx/images/pin-icon-start.png',";
                    $javascriptCode .= "        endIconUrl: 'leaflet/gpx/images/pin-icon-end.png',";
                    $javascriptCode .= "        shadowUrl: 'leaflet/gpx/images/pin-shadow.png'";
                    $javascriptCode .= "    },";
                    $javascriptCode .= "    polyline_options: {";
                    $javascriptCode .= "        color: 'red',";
                    $javascriptCode .= "        opacity: 0.75,";
                    $javascriptCode .= "        weight: 3,";
                    $javascriptCode .= "        lineCap: 'round'";
                    $javascriptCode .= "    }";
                    $javascriptCode .= "}).on('loaded', function (e) {";
                    $javascriptCode .= "    map{$amigo['id']}.fitBounds(e.target.getBounds());";
                    $javascriptCode .= "    const inicio = new Date(gpx{$amigo['id']}.get_start_time()).toLocaleString();";
                    $javascriptCode .= "    const fin = new Date(gpx{$amigo['id']}.get_end_time()).toLocaleString();";
                    $javascriptCode .= "    const kms = (gpx{$amigo['id']}.get_distance() / 1000).toFixed(2);";
                    $javascriptCode .= "    const tiempoTotal = msToTime(gpx{$amigo['id']}.get_total_time());";
                    $javascriptCode .= "    const tiempoMovimiento = msToTime(gpx{$amigo['id']}.get_moving_time());";
                    $javascriptCode .= "}).addTo(map{$amigo['id']});";
                } else {
                    $publicacionesMostradas++;
                }

                if ($publicacionesMostradas > $fin) {
                    break;
                }
        }
    }
}
echo "<script>{$javascriptCode}</script>";
?>

<!-- Paginación -->
<div class="pagination">
    <?php
    // Botón "Anterior"
    if ($pag > 1) {
        echo "<a href='?pag=" . ($pag - 1) . "' class='page-link'>&laquo; Anterior</a>";
    } else {
        echo "<span class='page-link disabled'>&laquo; No hay página anterior</span>";
    }

    // Números de página
    for ($i = 1; $i <= $totalPaginas; $i++) {
        if ($i == $pag) {
            echo "<span class='page-link current'> $i</span>";
        } else {
            echo "<a href='?pag=$i' class='page-link'> $i</a>";
        }
    }

    // Botón "Siguiente"
    if ($pag < $totalPaginas) {
        echo "<a href='?pag=" . ($pag + 1) . "' class='page-link'> Siguiente &raquo;</a>";
    } else {
        echo "<span class='page-link disabled'> No hay página siguiente &raquo;</span>";
    }
    ?>
</div>

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