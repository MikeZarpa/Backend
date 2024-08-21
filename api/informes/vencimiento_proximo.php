<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php'); //DIR_PUJOL
    require_once (DIR_PUJOL."/utiles/utilidades_request.php");
    require_once (DIR_PUJOL."/clases/informes/informes.php");
    require_once (DIR_PUJOL."/utiles/utilidades_get.php");
    require_once (DIR_PUJOL."/clases/conexion/respuestas_http.php");

    //GET
    //GET obtener información
    if(UtilesRequest::es_get()){
        $dias_de_anticipacion = UtilesGet::obtener_opcional("dias_de_anticipacion") ?? 1;

        $informe = Informes::productos_vencidos_o_cerca($dias_de_anticipacion);

        header('Content-Type: application/json');
        echo json_encode($informe);
    } else {
        RespuestasHttp::error_405("No se permite ese método");
    }