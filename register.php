<?php

header('Content-Type: text/html; charset=utf-8');
require_once 'validad_registro.php';

$errores = array();

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
    /*$provincia = $_POST["provinciaList1"];
    $localidad = $_POST["localidadList1"];
    $provincia = $_POST["provincia"];
    $localidad = $_POST["localidad"];
    $pais = $_POST["pais-otro-pais"];*/
    
    $pais = $_POST["pais-registro"];
    if ($pais === "espana") {
        $provincia = $_POST["provinciaList1"];
        $localidad = $_POST["localidadList1"];
        $pais="España";
        if ($provincia=="Seleccione su provincia...") {
            $errores[] = 'El campo provincia es requerido.';
        }
    } else {
        $provincia = $_POST["provincia"];
        $localidad = $_POST["localidad"];
        $pais= $_POST["pais-otro-pais"];
        if (!validaRequerido($provincia)) {
            $errores[] = 'El campo provincia es requerido.';
        }
        if (!validaRequerido($localidad)) {
            $errores[] = 'El campo localidad es requerido.';
        }

        if (!validaRequerido($pais)) {
            $errores[] = 'El campo localidad es requerido.';
        }
    }

    if (!validaRequerido($nombre)) {
        $errores[] = 'El campo nombre es requerido.';
    }
    if (!validaRequerido($apellidos)) {
        $errores[] = 'El campo apellidos es requerido.';
    }
    //Valida que el usuario tenga más de 8 caracteres y menos de 15
    $min = 8;
    $max = 15;
    if (!validaRequerido($usuario)) {
        $errores[] = 'El campo usuario es requerido.';
    } else if (!validaLetras($usuario) ) {
        $errores[] = "El campo usuario solo puede incluir caracteres y números.";
    } else if (!validaLongitud($usuario, $min, $max) ) {
        $errores[] = "El campo usuario debe tener entre $min y $max caracteres.";
    }
    //Valida que la contraseña tenga más de 6 y menos de 10 caracteres
    $min = 6;
    $max = 10;
    if (!validaRequerido($password)) {
        $errores[] = 'El campo contraseña es requerido.';
    } else if (!validaLongitud($password, $min, $max) ) {
        $errores[] = "El campo contraseña debe tener entre $min y $max caracteres.";
    }

    if (!validaRequerido($actividad_preferida)){
        $errores[] = 'El campo actividad_preferida es requerido.';
    }
    //Valida que el campo email sea correcto.
    if (!validaRequerido($email)) 
        $errores[] = 'El campo email es requerido.';
    else if (!validaEmail($email)) {
        $errores[] = 'El campo email es incorrecto.';
    }

    $sql1 = "SELECT * FROM usuarios WHERE email = '$email'";
    $result1 = mysqli_query($conn, $sql1);
    if (mysqli_num_rows($result1) > 0) {
        $errores[] = 'El correo electrónico ya está registrado.';
    }

    //Valida que el campo fecha contenga una fecha correcta dd/mm/yyyy.
    /*if (!validaRequerido($fecha_nacimiento)) 
        $errores[] = 'El campo fecha es requerido.';
    else if (!validaFecha($fecha_nacimiento)) {
        $errores[] = 'El campo fecha es incorrecto.';
    }*/
    //Verifica si ha encontrado errores y de no haber redirige a la página con el mensaje de que pasó la validación.
    if(!$errores){
        $sql = "INSERT INTO usuarios (nombre, apellidos, usuario, email, contraseña, fecha_nacimiento, actividad_preferida, provincia, localidad, pais)
            VALUES ('$nombre', '$apellidos', '$usuario', '$email', '$password', '$fecha_nacimiento', '$actividad_preferida', '$provincia', '$localidad', '$pais')";

        if (mysqli_query($conn, $sql)) {
        echo "Registro exitoso!";
        $idUsuario = mysqli_insert_id($conn);
        header("Location: login.php");
        } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }

        $rutaImagenPorDefecto = '../Users_images/sinfoto.jpg';
        $rutaDirectorioUsuario = '../Users_images/Usuarios' . $idUsuario;

        // Copia la imagen por defecto al directorio del usuario
        if (!file_exists($rutaDirectorioUsuario)) {
            mkdir($rutaDirectorioUsuario, 0777, true); // Crea el directorio si no existe
        }

        $rutaDestinoImagen = $rutaDirectorioUsuario . '/imagen_perfil.jpg';
        copy($rutaImagenPorDefecto, $rutaDestinoImagen);

        // Cerrar la conexión a la base de datos
        mysqli_close($conn);
        
         exit;
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="stylesheet"  href="index.css">
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>REGISTRAR</title>
    <script src="js/AjaxCode.js"></script>
    <style>
        .error {color: #FF0000;}
    </style>
</head>
<body>
    <div class="pagina">
        <div class="caja-register">
            <div class="formulario">
                <p><span class="error">* Campo requerido</span></p>
                <form method="post" action="register.php">
                    <h2>Registrar</h2>
                    <div class="datos">
                        <input type="text" name="nombre-registro" required />
                        <label for="nombre">Nombre</label>
                        <span class="error">*</span>
                    </div>
                    <div class="datos">
                        <input type="text" name="apellidos-registro" required />
                        <label for="apellidos">Apellidos</label>
                        <span class="error">*</span>
                    </div>
                    <div class="datos">
                        <ion-icon name="person"></ion-icon>
                        <input type="text" name="usuario-registro" required />
                        <label for="usuario">Usuario</label>
                        <span class="error">*</span>
                    </div>
                    <div class="datos">
                        <ion-icon name="mail"></ion-icon>
                        <input type="text" name="email-registro" required />
                        <label for="email">Email</label>
                        <span class="error">*</span>
                    </div>
                    <div class="datos">
                        <ion-icon name="lock-closed"></ion-icon>
                        <input type="password" name="contraseña-registro" required />
                        <label for="contraseña">Contraseña</label>
                        <span class="error">*</span>
                    </div>
                    <div class="datos">
                        Fecha de nacimiento:
                        <input type="date" name="nacimiento-registro" required />
                        <span class="error">*</span>
                    </div>
                    <div class="datos">
                        <ion-icon name="bicycle"></ion-icon>
                        Actividad preferida:
                        <select name="act-preferida-registro" required>
                            <option value="" disabled selected>Seleccionar actividad preferida...</option>
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
                        <select name="pais-registro" id="pais-registro" onChange="return mostrarFormulario()">
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
                        <input type="submit" value="Registrar" />
                    </div>
                    <div class="registrar">
                        <p>¿Ya eres miembro? <a href="login.php">Iniciar Sesión</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
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