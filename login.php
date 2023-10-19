<?php

if (!empty($_POST)) {
  
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

  if ($email == 'admin@admin.com' && $contraseña == 'admin123') {
    session_start();
    $_SESSION["loggedin"] = true;
    $sql1 = "SELECT id FROM usuarios WHERE email='$email' AND contraseña='$contraseña'";
    $result1 = $conn->query($sql1);
    $fila1 = $result1->fetch_assoc();
    $_SESSION["id"] = $fila1["id"];
    $_SESSION["email"] = 'admin@admin.com';
    // Credenciales válidas, redirigir a la página de administrador
    header("Location: administrador.php");
    exit();
    $conn->close();
  }

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

<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="stylesheet"  href="index.css">
    <meta charset="UTF-8">
    <title>INICIO SESION</title>
</head>
<body>
    <div class="pagina">
        <div class="caja">
            <div class="formulario">
                <form action="login.php" method="post"> <!--Para php-->
                    <h2>Inicio sesión</h2>
                    <div class="datos">
                        <ion-icon name="mail"></ion-icon>
                        <input type="email" name="email-login" required/>
                        <label for="email">Email</label>
                    </div>
                    <div class="datos">
                        <ion-icon name="lock-closed"></ion-icon>
                        <input type="password" name="contraseña-login" required/>
                        <label for="contraseña">Contraseña</label>
                    </div>
                    <div class="boton">
                        <input type="submit" value="Iniciar Sesión"/>
                    </div>
                    <div class="registrar">
                        <p>No tengo una cuenta <a href="register.php">Registar</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>