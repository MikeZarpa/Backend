<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php'); //DIR_PUJOL
    require_once (DIR_PUJOL."/utiles/utilidades_request.php");
    require_once (DIR_PUJOL."/clases/base_de_datos/producto.php");
    require_once (DIR_PUJOL."/utiles/utilidades_get.php");
    require_once (DIR_PUJOL."/clases/conexion/respuestas_http.php");

    //POST crear uno nuevo
    if(UtilesRequest::es_post()){
        //Por implementar
        RespuestasHttp::error_500("Por implementar");
    } else {
        RespuestasHttp::error_405();
    }