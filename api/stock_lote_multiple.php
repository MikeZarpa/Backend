<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php'); //DIR_PUJOL
    require_once (DIR_PUJOL."/utiles/utilidades_request.php");
    require_once (DIR_PUJOL."/clases/base_de_datos/stock_lote.php");
    require_once (DIR_PUJOL."/utiles/utilidades_get.php");
    require_once (DIR_PUJOL."/clases/conexion/respuestas_http.php");
    

    GestorDePermisos::ExigirRol(["ADMIN"]);

    //Post crear nuevo
    if(UtilesRequest::es_post()){

        $stocks = UtilesPost::obtener('stocks', "No se recibió el array de stocks");

        foreach ( $stocks as $stock){
            if(!isset($stock['id_producto']))
                RespuestasHttp::error_400("Falta identificar producto");
            if(!isset($stock['cantidad']))
                RespuestasHttp::error_400("Falta identificar cantidad");
            if(!isset($stock['coste']))
                RespuestasHttp::error_400("Falta identificar coste");
        }

        foreach ($stocks as $stock){
            $id_producto = $stock['id_producto'];
            $cantidad = $stock['cantidad'];
            $coste = $stock['coste'];
            if(isset($stock['fecha_vto']))
                $fecha_vto = $stock['fecha_vto'];
            else
                $fecha_vto = null;
            $stock = new StockLote(null, $id_producto,$cantidad,$coste,$fecha_vto);
            $stock -> save();
        }
    }