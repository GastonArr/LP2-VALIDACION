<?php

function ConexionBD($Host = 'localhost', $User = 'root', $Password = '', $BaseDeDatos = 'tdcsa') {
    $linkConexion = mysqli_connect($Host, $User, $Password, $BaseDeDatos);

    if ($linkConexion == false) {
        die('No se pudo establecer la conexión.');
    }

    mysqli_set_charset($linkConexion, 'utf8');

    return $linkConexion;
}

?>
