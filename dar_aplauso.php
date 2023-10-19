<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener el ID del amigo y realizar las validaciones necesarias

    $amigoId = $_POST["id_usuario"];

    // Realizar la actualización en la base de datos
    $host = "localhost";
    $user = "practica";
    $password = "practica";
    $dbname = "clase_pw";

    $conn = mysqli_connect($host, $user, $password, $dbname);

    // Verificar si la conexión fue exitosa
    if (!$conn) {
    die("Conexión fallida: " . mysqli_connect_error());
    }

    // Incrementar el número de aplausos en 1 para el amigo correspondiente
    $sql = "UPDATE actividad SET aplausos = aplausos + 1 WHERE id_usuario = $amigoId";
    $conn->query($sql);

    // Obtener el nuevo número de aplausos del amigo
    $sql = "SELECT aplausos FROM actividad WHERE id_usuario = $amigoId";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $aplausos = $row["aplausos"];

    // Enviar la respuesta con el nuevo número de aplausos
        echo $aplausos;
    } else {
        echo "No se encontró el amigo o no hay datos de aplausos";
    }

    $conn->close();
}
?>