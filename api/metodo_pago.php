<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php'); //DIR_PUJOL
    require_once (DIR_PUJOL."/utiles/utilidades_request.php");
    require_once (DIR_PUJOL."/clases/base_de_datos/metodo_pago.php");
    require_once (DIR_PUJOL."/utiles/utilidades_get.php");
    require_once (DIR_PUJOL."/clases/conexion/respuestas_http.php");
    

    GestorDePermisos::ExigirRol(["ADMIN","CAJERO"]);
    //GET, POST, PUT
    //GET obtener información
    //Consultar por 1 elemento
    //Consultar por muchos elementos
    if(UtilesRequest::es_get()){
        //1 Solo elemento
        if(UtilesGet::verificar_encabezado("id_metpago")){
            $id_metpago = UtilesGet::obtener('id_metpago');
            $metodo_pago = MetodoPago::recuperar_por_id($id_metpago);
            if($metodo_pago == null)
                RespuestasHttp::error_404();
            header('Content-Type: application/json');
            echo json_encode($metodo_pago);
        } else {
            //Consultar por muchos elementos
            $no_paginar = UtilesGet::obtener_opcional('no_paginar');
            if($no_paginar == null){
                $resultados = MetodoPago::consultarTodos();
            } else {
                $resultados = MetodoPago::consultarTodos(false);
            }
            header('Content-Type: application/json');
            echo json_encode($resultados);
        }
    } else {
        RespuestasHttp::error_405();
    }