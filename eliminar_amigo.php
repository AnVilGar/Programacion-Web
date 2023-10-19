<?php
session_start();
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    $id_usuario = $_SESSION["id_usuario"];
    $_SESSION["correo"] = $_SESSION["email"];

    $host = "localhost";
    $user = "practica";
    $password = "practica";
    $dbname = "clase_pw";

    $conn = mysqli_connect($host, $user, $password, $dbname);

    // Verificar si la conexión fue exitosa
    if (!$conn) {
    die("Conexión fallida: " . mysqli_connect_error());
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["id_amigo"])) {
            $id_amigo = $_POST["id_amigo"];

            // Obtener el ID de la amistad
            $sql = "SELECT id FROM amigos WHERE (id_usuario = $id_usuario AND id_amigo = $id_amigo) OR (id_usuario = $id_amigo AND id_amigo = $id_usuario) AND estado='aceptada'";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $id_amistad = $row["id"];

                // Eliminar la amistad
                $sql_delete = "DELETE FROM amigos WHERE id = $id_amistad";
                if ($conn->query($sql_delete) === true) {
                    echo "Se ha eliminado al amigo correctamente.";
                    echo '<a href="perfil.php"><input type="button" value="Volver al perfil"></a>';
                    echo '<a href="web.php"><input type="button" value="Volver a inicio"></a>';
                } else {
                    echo "No se pudo eliminar al amigo.";
                    echo '<a href="perfil.php"><input type="button" value="Volver al perfil"></a>';
                    echo '<a href="web.php"><input type="button" value="Volver a inicio"></a>';
                }
            } else {
                echo "No se encontró una amistad válida para eliminar.";
                echo '<a href="perfil.php"><input type="button" value="Volver al perfil"></a>';
                echo '<a href="web.php"><input type="button" value="Volver a inicio"></a>';
            }
        } else {
            echo "No se proporcionó un ID de amigo válido.";
            echo '<a href="perfil.php"><input type="button" value="Volver al perfil"></a>';
            echo '<a href="web.php"><input type="button" value="Volver a inicio"></a>';
        }
    } else {
        echo "Método de solicitud no válido.";
        echo '<a href="perfil.php"><input type="button" value="Volver al perfil"></a>';
        echo '<a href="web.php"><input type="button" value="Volver a inicio"></a>';
    }

    $conn->close();
} else {
    header("location: login.php");
    exit;
}
?>



