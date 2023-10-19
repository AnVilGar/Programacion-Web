<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
  // Conexión a la base de datos
  $host = "localhost";
  $user = "practica";
  $password = "practica";
  $dbname = "clase_pw";

  $conn = mysqli_connect($host, $user, $password, $dbname);

  // Verificar si la conexión fue exitosa
  if (!$conn) {
    die("Conexión fallida: " . mysqli_connect_error());
  }
  
  // Validación de credenciales ingresadas
  $email = $_POST["email-login"];
  $contraseña = $_POST["contraseña-login"];

  $sql = "SELECT id FROM usuarios WHERE email='$email' AND contraseña='$contraseña'";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    // Las credenciales son correctas, inicia sesión y redirige al usuario a la página principal
    session_start();
    $fila = $result->fetch_assoc();
    $_SESSION["loggedin"] = true;
    $_SESSION["id"] = $fila["id"];
    $_SESSION["email"] = $email;
    header("location: web.php");
    exit;
  } else {
    // Las credenciales son incorrectas, muestra un mensaje de error
    $error = "Email o contraseña incorrectos";
  }
  
  $conn->close();
}

?>