<?php
// Este archivo se encarga de cerrar la sesión del usuario actual
session_start();

// Eliminamos todos los valores almacenados en la variable global de sesión
$_SESSION = array();
// Destruimos la sesión para invalidar el identificador y liberar recursos
session_destroy();

// Redirigimos al usuario al formulario de login para que pueda autenticarse nuevamente
header('Location: login.php');
// Detenemos la ejecución del script después de enviar la cabecera de redirección
exit;
