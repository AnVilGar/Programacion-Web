<?php
session_start();
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && $_SESSION["email"]==='admin@admin.com') {
  if (isset($_POST["id_editar"])) {
    $_SESSION["id_usuario"] = $_POST["id_editar"];
    $_SESSION["correo"]=$_SESSION["email"];
    header("Location: editar_perfil.php");
  }
}else {
    header("location: login.php");
    exit;
}
?>
