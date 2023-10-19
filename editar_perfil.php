<?php
session_start();
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    // Obtener el ID del usuario
    $id_usuario = $_SESSION["id_usuario"];
    $correo=$_SESSION["email"];
} else {
    header("location: login.php");
    exit;
}
header('Content-Type: text/html; charset=utf-8');
require_once 'validad_registro.php';
$errores = array();

$id_usuario = $_SESSION["id_usuario"];

$host = "localhost";
$user = "practica";
$password = "practica";
$dbname = "clase_pw";

$conn = mysqli_connect($host, $user, $password, $dbname);

$sql = "SELECT * FROM usuarios WHERE id = $id_usuario";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

if (!empty($_POST)) {
    $host = "localhost";
    $user = "practica";
    $password = "practica";
    $dbname = "clase_pw";

    $conn = mysqli_connect($host, $user, $password, $dbname);

    // Verificar si la conexión fue exitosa
    if (!$conn) {
        die("Conexión fallida: " . mysqli_connect_error());
    }

    // Recuperar datos del formulario
    $nombre = $_POST["nombre-registro"];
    $apellidos = $_POST["apellidos-registro"];
    $usuario = $_POST["usuario-registro"];
    $email = $_POST["email-registro"];
    $password = $_POST["contraseña-registro"];
    $fecha_nacimiento = $_POST["nacimiento-registro"];
    $actividad_preferida = $_POST["act-preferida-registro"];
    $pais = $_POST["pais-registro1"];

    if ($pais === "espana") {
        $provincia = $_POST["provinciaList1"];
        $localidad = $_POST["localidadList1"];
        $pais = "España";
        if ($provincia == "Seleccione su provincia...") {
            $errores[] = 'El campo provincia es requerido.';
        }
    } else {
        $provincia = $_POST["provincia"];
        $localidad = $_POST["localidad"];
        $pais = $_POST["pais-otro-pais"];
        if (!validaRequerido($provincia)) {
            $errores[] = 'El campo provincia es requerido.';
        }
        if (!validaRequerido($localidad)) {
            $errores[] = 'El campo localidad es requerido.';
        }

        if (!validaRequerido($pais)) {
            $errores[] = 'El campo pais es requerido.';
        }
    }

    if (!validaRequerido($nombre)) {
        $errores[] = 'El campo nombre es requerido.';
    }
    if (!validaRequerido($apellidos)) {
        $errores[] = 'El campo apellidos es requerido.';
    }

    // Valida que el usuario tenga más de 8 caracteres y menos de 15
    $min = 8;
    $max = 15;
    if (!validaRequerido($usuario)) {
        $errores[] = 'El campo usuario es requerido.';
    } else if (!validaLetras($usuario)) {
        $errores[] = "El campo usuario solo puede incluir caracteres y números.";
    } else if (!validaLongitud($usuario, $min, $max)) {
        $errores[] = "El campo usuario debe tener entre $min y $max caracteres.";
    }
    $min = 6;
    $max = 10;
    if (!validaRequerido($password)) {
        $errores[] = 'El campo contraseña es requerido.';
    } else if (!validaLongitud($password, $min, $max)) {
        $errores[] = "El campo contraseña debe tener entre $min y $max caracteres.";
    }

    // Valida el formato de email
    if (!validaRequerido($email)) {
        $errores[] = 'El campo email es requerido.';
    } else if (!validaEmail($email)) {
        $errores[] = 'El campo email no tiene un formato válido.';
    }
    // Si no hay errores, actualiza el perfil
    if (empty($errores)) {
        $sql = "UPDATE usuarios SET nombre='$nombre', apellidos='$apellidos', usuario='$usuario', email='$email', contraseña='$password', fecha_nacimiento='$fecha_nacimiento', actividad_preferida='$actividad_preferida', pais='$pais', provincia='$provincia', localidad='$localidad' WHERE id=$id_usuario";
        if (mysqli_query($conn, $sql)) {
            if($correo!='admin@admin.com'){
                header('Location: perfil.php');
                exit();
            }else{
                header('Location: administrador.php');
            }
        } else {
            $errores[] = 'Error al actualizar el perfil: ' . mysqli_error($conn);
        }
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <link rel="stylesheet" href="index.css">
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Modificar perfil</title>
    <script src="js/AjaxCode.js"></script>
    <style>
        .error {
            color: #FF0000;
        }
    </style>
</head>

<body>
    <div class="perfil">
            <div class="formulario">
                <form method="post" action="editar_perfil.php">
                    <h2>Modificar perfil</h2>
                    <div class="campo">
                        <label for="nombre-registro">Nombre:</label>
                        <input type="text" id="nombre-registro" name="nombre-registro" value="<?php echo $row['nombre']; ?>" />
                    </div>
                    <div class="campo">
                        <label for="apellidos-registro">Apellidos:</label>
                        <input type="text" id="apellidos-registro" name="apellidos-registro" value="<?php echo $row['apellidos']; ?>"  />
                    </div>
                    <div class="campo">
                        <label for="usuario-registro">Usuario:</label>
                        <input type="text" id="usuario-registro" name="usuario-registro" value="<?php echo $row['usuario']; ?>"/>
                    </div>
                    <div class="campo">
                        <label for="email-registro">Email:</label>
                        <input type="email" id="email-registro" name="email-registro"  value="<?php echo $row['email']; ?>"/>
                    </div>
                    <div class="campo">
                        <label for="contraseña-registro">Contraseña:</label>
                        <input type="password" id="contraseña-registro" name="contraseña-registro"  value="<?php echo $row['contraseña']; ?>"/>
                    </div>
                    <div class="campo">
                        <label for="nacimiento-registro">Fecha de Nacimiento:</label>
                        <input type="date" id="nacimiento-registro" name="nacimiento-registro"  value="<?php echo $row['fecha_nacimiento']; ?>"/>
                    </div>
                    <div class="datos">
                        <ion-icon name="bicycle"></ion-icon>
                        Actividad preferida:
                        <select name="act-preferida-registro" required>
                        <option value="" disabled selected><?php echo $row['actividad_preferida']; ?></option>
                            <?php
                            // Conexión a la base de datos
                            $servername = "localhost";
                            $username = "practica";
                            $password = "practica";
                            $dbname = "clase_pw";

                            // Crear la conexión
                            $conn = new mysqli($servername, $username, $password, $dbname);

                            // Verificar la conexión
                            if ($conn->connect_error) {
                                die("Error de conexión: " . $conn->connect_error);
                            }

                            // Consulta para obtener las actividades desde la tabla "deportes"
                            $sql = "SELECT nombre FROM deportes";
                            $result = $conn->query($sql);

                            // Generar las opciones del campo de selección
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $nombreActividad = $row['nombre'];
                                    echo "<option value=\"" . htmlspecialchars($nombreActividad) . "\">" . htmlspecialchars($nombreActividad) . "</option>";
                                }
                            }

                            // Cerrar la conexión
                            $conn->close();
                            ?>
                        </select>
                        <span class="error">*</span>
                    </div>

                    <div class="datos">
                        Seleccione su país:
                        <select name="pais-registro1" id="pais-registro" onChange="return mostrarFormulario()" required>
                            <option value="" disabled selected>Selecciona el país...</option>
                            <option value="espana">España</option>
                            <option value="otro">Otro país</option>
                        </select>
                    </div>

                    <div id="formulario-espana" style="display: none;">
                        <div class="datos">
                            Seleccione su provincia:
                            <select name="provinciaList1" id="provinciaList" onChange="return provinciaListOnChange()">
                                <option>Seleccione su provincia...</option>
                                <?php
                                $xml = simplexml_load_file('provinciasypoblaciones.xml');
                                $result = $xml->xpath("/lista/provincia/nombre | /lista/provincia/@id");
                                for ($i = 0; $i < count($result); $i += 2) {
                                    $e = $i + 1;
                                    $provincia = utf8_decode($result[$e]);
                                    $idProvincia = $result[$i]; // Obtener el ID de la provincia
                                    echo ("<option value='$idProvincia'>$provincia</option>");
                                }
                                ?>
                            </select>
                            <br>
                            <br>
                            Seleccione su localidad:
                            <select name="localidadList1" id="localidadList">
                                <option>Seleccione antes una provincia</option>
                            </select>
                            <span id="advice"> </span>
                        </div>
                    </div>

                    <div id="formulario-otro-pais" style="display: none;">
                        <div class="datos">
                            <input type="text" name="provincia"/>
                            <label for="provincia">Provincia</label>
                            <span class="error">*</span>
                        </div>
                        <div class="datos">
                            <input type="text" name="localidad"/>
                            <label for="localidad">Localidad</label>
                            <span class="error">*</span>
                        </div>
                        <div class="datos">
                            <input type="text" name="pais-otro-pais"/>
                            <label for="pais">País</label>
                            <span class="error">*</span>
                        </div>
                    </div>
                    <div class="boton">
                        <input type="submit" value="Modificar"/>
                    </div>
                </form>
            <div class="boton">
            <?php if ($correo != 'admin@admin.com'){
                echo '<a href="perfil.php"><input type="button" value="Volver al perfil"></a>';
                echo '<a href="web.php"><input type="button" value="Volver a inicio"></a>';
            }else{
                echo '<a href="administrador.php"><input type="button" value="Volver a inicio"></a>';
            }
                ?>
	</div>
        </div>
    </div>
    <script src="js/provincias.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script>
        function mostrarFormulario() {
            var pais = document.getElementById("pais-registro").value;
            var formularioEspana = document.getElementById("formulario-espana");
            var formularioOtroPais = document.getElementById("formulario-otro-pais");

            if (pais === "espana") {
                formularioEspana.style.display = "block";
                formularioOtroPais.style.display = "none";
            } else if (pais === "otro") {
                formularioEspana.style.display = "none";
                formularioOtroPais.style.display = "block";
            }
        }
    </script>
    <?php if ($errores): ?>
    <ul style="color: #f00;">
        <?php foreach ($errores as $error): ?>
        <li> <?php echo $error ?> </li>
        <?php endforeach; ?>
    </ul>
    <?php endif; ?>
</body>

</html>
