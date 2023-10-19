<?php
session_start(); // Iniciar la sesión si aún no está iniciada
session_destroy(); // Destruir la sesión

// Enviar una respuesta al cliente (puede ser cualquier cosa, en este caso enviamos un mensaje de éxito)
echo "Sesión cerrada exitosamente";
?>