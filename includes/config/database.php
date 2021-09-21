<?php

function conectarDB() : mysqli{
    
    $db = mysqli_connect('localhost', 'root', '1234', 'bienes_raices');
    $db->set_charset('utf8');
    if(!$db){
        echo "Error no se pudo conectar";
        exit;
    }
    return $db;
}
