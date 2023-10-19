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

    $idUsuarioDestino = $_POST['idUsuarioDestino'];
    $accion = $_POST['accion'];

    if ($accion === 'enviar') {
        // Enviar solicitud de amistad
        $sqlEnviarSolicitud = "INSERT INTO amigos (id_usuario, id_amigo, estado) VALUES ($idUsuarioActual, $idUsuarioDestino, 'pendiente')";
        $resultEnviarSolicitud = mysqli_query($conn, $sqlEnviarSolicitud);

        if ($resultEnviarSolicitud) {
            // Solicitud enviada exitosamente, redireccionar a la página de búsqueda
            header("Location: buscar.php");
            exit;
        } else {
            // Error al enviar la solicitud, mostrar mensaje de error o redireccionar a una página de error
            echo "Error al enviar la solicitud de amistad.";
        }
    }

    if ($accion === 'aceptar') {
        // Aceptar solicitud de amistad
        $sqlAceptarSolicitud = "UPDATE amigos SET estado = 'aceptada' WHERE id_usuario = $idUsuarioDestino AND id_amigo = $idUsuarioActual";
        $resultAceptarSolicitud = mysqli_query($conn, $sqlAceptarSolicitud);

        if ($resultAceptarSolicitud) {
            // Solicitud aceptada exitosamente
            header("Location: buscar.php");
            exit;
        } else {
            // Error al aceptar la solicitud, mostrar mensaje de error o redireccionar a una página de error
            echo "Error al aceptar la solicitud de amistad.";
        }
    } elseif ($accion === 'rechazar') {
        // Rechazar solicitud de amistad
        $sqlRechazarSolicitud = "DELETE FROM amigos WHERE id_usuario = $idUsuarioDestino AND id_amigo = $idUsuarioActual";
        $resultRechazarSolicitud = mysqli_query($conn, $sqlRechazarSolicitud);

        if ($resultRechazarSolicitud) {
            // Solicitud rechazada exitosamente
            header("Location: buscar.php");
            exit;
        } else {
            // Error al rechazar la solicitud, mostrar mensaje de error o redireccionar a una página de error
            echo "Error al rechazar la solicitud de amistad.";
        }
    }
} else {
    header("Location: login.php");
    exit;
}
?>


