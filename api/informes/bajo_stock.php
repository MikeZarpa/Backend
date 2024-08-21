<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php'); //DIR_PUJOL
    require_once (DIR_PUJOL."/utiles/utilidades_request.php");
    require_once (DIR_PUJOL."/clases/informes/informes.php");
    require_once (DIR_PUJOL."/utiles/utilidades_get.php");
    require_once (DIR_PUJOL."/clases/conexion/respuestas_http.php");

    //GET
    //GET obtener información
    if(UtilesRequest::es_get()){
        $productos_con_bajo_stock = Informes::productos_debajo_de_la_cantidad_minima();
        header('Content-Type: application/json');
        echo json_encode($productos_con_bajo_stock);
    } else {
        RespuestasHttp::error_405("No se permite ese método");
    }