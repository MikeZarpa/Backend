<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php'); //DIR_PUJOL
    require_once (DIR_PUJOL."/utiles/utilidades_request.php");
    require_once (DIR_PUJOL."/clases/base_de_datos/factura_venta.php");
    require_once (DIR_PUJOL."/utiles/utilidades_get.php");
    require_once (DIR_PUJOL."/clases/conexion/respuestas_http.php");

    //GET, POST, PUT
    //GET obtener información
    //Consultar por 1 elemento
    //Consultar por muchos elementos
    if(UtilesRequest::es_get()){
        //1 Solo elemento
        if(UtilesGet::verificar_encabezado("id_factura_venta")){
            $id_factura_venta = UtilesGet::obtener('id_factura_venta');
            $factura_venta = FacturaVenta::recuperar_por_id($id_factura_venta);
            if($factura_venta == null)
                RespuestasHttp::error_404();
            header('Content-Type: application/json');
            echo json_encode($factura_venta);
        } else {
            //Consultar por muchos elementos
            $paginar = !UtilesGet::verificar_encabezado("no_paginar");
            $resultados = FacturaVenta::consultarTodos($paginar);
            header('Content-Type: application/json');
            echo json_encode($resultados);
        }
    }