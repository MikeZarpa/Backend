<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php'); //DIR_PUJOL
    require_once (DIR_PUJOL."/utiles/utilidades_request.php");
    require_once (DIR_PUJOL."/clases/informes/informes.php");
    require_once (DIR_PUJOL."/utiles/utilidades_get.php");
    require_once (DIR_PUJOL."/clases/conexion/respuestas_http.php");
    

    GestorDePermisos::ExigirRol(["ADMIN"]);

    //GET
    //GET obtener información
    if(UtilesRequest::es_get()){
        $fecha1 = UtilesGet::obtener("fecha1", "Falta la primera fecha");
        $fecha2 = UtilesGet::obtener("fecha2", "Falta la segunda fecha");
        $fecha_min = min($fecha1, $fecha2);
        $fecha_max = max($fecha1, $fecha2);
        $ventas = Informes::ventas_categoria_entre_fechas($fecha_min,$fecha_max);
        header('Content-Type: application/json');
        echo json_encode($ventas);
    } else {
        RespuestasHttp::error_405("No se permite ese método");
    }