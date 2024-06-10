<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php'); //DIR_PUJOL
    require_once (DIR_PUJOL."/utiles/utilidades_request.php");
    require_once (DIR_PUJOL."/clases/base_de_datos/stock_lote.php");
    require_once (DIR_PUJOL."/utiles/utilidades_get.php");
    require_once (DIR_PUJOL."/clases/conexion/respuestas_http.php");

    //GET, POST, PUT
    //GET obtener información
    //Consultar por 1 elemento
    //Consultar por muchos elementos
    if(UtilesRequest::es_get()){
        //1 Solo elemento
        if(UtilesGet::verificar_encabezado("id_stock")){
            $id_stock = UtilesGet::obtener('id_stock');
            $stock = StockLote::recuperar_por_id($id_stock);
            if($stock == null)
                RespuestasHttp::error_404();
            header('Content-Type: application/json');
            echo json_encode($stock);
        } else {
            //Consultar por muchos elementos
            $resultados = StockLote::consultarTodos();
            header('Content-Type: application/json');
            echo json_encode($resultados);
        }
    }

    //PUT actualizar
    if(UtilesRequest::es_put()){
        $id_stock = UtilesPost::obtener('id_stock');
        $id_producto = UtilesPost::obtener('id_producto');
        $cantidad = UtilesPost::obtener('cantidad');
        $coste = UtilesPost::obtener('coste');
        $fecha_vto = UtilesPost::obtener('fecha_vto');

        $stock = new StockLote($id_stock, $id_producto,$cantidad,$coste,$fecha_vto);
        $stock -> actualizar();
    }
    //Delete
    if(UtilesRequest::es_delete()){
        $id_stock = UtilesGet::obtener('id_stock');
        $stock = StockLote::recuperar_por_id($id_stock);
        if($stock == null)
            RespuestasHttp::error_404();
        $stock -> delete();
    }