<?php

/**
 * Archivo: funciones/conexion.php (línea 3)
 * Propósito: Crear y devolver un enlace activo hacia la base de datos del sistema.
 * Descripción: Recibe los parámetros de conexión, invoca mysqli_connect para abrir la
 * conexión y corta la ejecución con un mensaje claro si algo falla. Al finalizar, fija
 * el conjunto de caracteres a UTF-8 para evitar problemas con tildes o caracteres
 * especiales.
 * Retorna: mysqli\_connect — Devuelve el identificador de conexión para que otras
 * funciones puedan reutilizarlo y operar contra la base de datos.
 */
function ConexionBD($Host = 'localhost', $User = 'root', $Password = '', $BaseDeDatos = 'tdcsa') {
    $linkConexion = mysqli_connect($Host, $User, $Password, $BaseDeDatos);

    if ($linkConexion == false) {
        die('No se pudo establecer la conexión.');
    }

    mysqli_set_charset($linkConexion, 'utf8');

    return $linkConexion;
}

?>
