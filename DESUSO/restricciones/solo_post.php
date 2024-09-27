<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php');
    require_once(DIR_PUJOL.'/clases/conexion/respuestas_http.php');

    if($_SERVER['REQUEST_METHOD'] != "POST"){
        RespuestasHttp::error_405();
        die();
    }
    
    require_once(DIR_PUJOL.'/utiles/obtener_post.php');