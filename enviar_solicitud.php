<?php
session_start();

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    $idUsuarioActual = $_SESSION["id_usuario"];

    $host = "localhost";
    $user = "practica";
    $password = "practica";
    $dbname = "clase_pw";

    $conn = mysqli_connect($host, $user, $password, $dbname);

    // Verificar si la conexión fue exitosa
    if (!$conn) {
    die("Conexión fallida: " . mysqli_connect_error());
    }

    $nombre = $_POST['nombre'] ?? '';
    $apellidos = $_POST['apellidos'] ?? '';

    // Construir la consulta SQL
    $sql = "SELECT * FROM usuarios WHERE 1=1";

    if (!empty($nombre)) {
        $sql .= " AND nombre LIKE '%$nombre%'";
    }

    if (!empty($apellidos)) {
        $sql .= " AND apellidos LIKE '%$apellidos%'";
    }

    $resultado = mysqli_query($conn, $sql);

    if (mysqli_num_rows($resultado) > 0) {
        echo "<table>";

        while ($fila = mysqli_fetch_assoc($resultado)) {
            $idUsuarioEncontrado = $fila['id'];
            $nombreUsuario = $fila['nombre'];
            $apellidosUsuario = $fila['apellidos'];
        
            $sqlAmistad = "SELECT * FROM amigos WHERE (id_usuario = $idUsuarioActual AND id_amigo = $idUsuarioEncontrado) OR (id_usuario = $idUsuarioEncontrado AND id_amigo = $idUsuarioActual)";
            $resultAmistad = mysqli_query($conn, $sqlAmistad);
        
            if (mysqli_num_rows($resultAmistad) > 0) {
                $filaAmistad = mysqli_fetch_assoc($resultAmistad);
                $estadoAmistad = $filaAmistad['estado'];
        
                if ($estadoAmistad === 'aceptada') {
                    echo "<tr>";
                    echo "<td>" . $nombreUsuario . " " . $apellidosUsuario . "</td>";
                    echo "<td><a href='perfil.php?id=$idUsuarioEncontrado'>Ver perfil</a></td>";
                    echo "</tr>";
                } elseif ($estadoAmistad === 'pendiente') {
                    if ($idUsuarioEncontrado === $idUsuarioActual) {
                        echo "<tr>";
                        echo "<td>" . $nombreUsuario . " " . $apellidosUsuario . "</td>";
                        echo "<td>Solicitud de amistad pendiente</td>";
                        echo "</tr>";
                    } else {
                        echo "<tr>";
                        echo "<td>" . $nombreUsuario . " " . $apellidosUsuario . "</td>";
                        echo "<td>";
                        echo "<form action='procesar_solicitud.php' method='post'>";
                        echo "<input type='hidden' name='idUsuarioDestino' value='$idUsuarioEncontrado'>";
                        echo "<input type='hidden' name='accion' value='aceptar'>";
                        echo "<button type='submit'>Aceptar solicitud</button>";
                        echo "</form>";
                        echo "<form action='procesar_solicitud.php' method='post'>";
                        echo "<input type='hidden' name='idUsuarioDestino' value='$idUsuarioEncontrado'>";
                        echo "<input type='hidden' name='accion' value='rechazar'>";
                        echo "<button type='submit'>Rechazar solicitud</button>";
                        echo "</form>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr>";
                    echo "<td>" . $nombreUsuario . " " . $apellidosUsuario . "</td>";
                    echo "<td><form action='procesar_solicitud.php' method='post'><input type='hidden' name='idUsuarioDestino' value='$idUsuarioEncontrado'><button type='submit'>Enviar solicitud de amistad</button></form></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr>";
                    echo "<td>" . $nombreUsuario . " " . $apellidosUsuario . "</td>";
                    echo "<td>";
                    echo "<form action='procesar_solicitud.php' method='post'>";
                    echo "<input type='hidden' name='idUsuarioDestino' value='$idUsuarioEncontrado'>";
                    echo "<input type='hidden' name='accion' value='enviar'>";
                    echo "<button type='submit'>Enviar solicitud de amistad</button>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
            }
        }

        echo "</table>";
    }
} else {
    header("Location: login.php");
    exit;
}
?>



