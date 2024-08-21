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
            $id_producto = UtilesGet::obtener('id_producto', "Falta identificar el producto");
            $stock = StockLote::recuperar_todos_por_id_producto($id_producto);
            header('Content-Type: application/json');
            echo json_encode($stock);
    }
    //Post crear nuevo
    if(UtilesRequest::es_post()){
        $id_producto = UtilesPost::obtener('id_producto', "Falta identificar el producto");
        $cantidad = UtilesPost::obtener('cantidad', "La cantidad es inválida");
        $coste = UtilesPost::obtener('coste', "El Coste no está presente");
        $fecha_vto = UtilesPost::obtener_opcional('fecha_vto');

        $stock = new StockLote(null, $id_producto,$cantidad,$coste,$fecha_vto);
        $stock -> save();
    }
    //PUT actualizar
    if(UtilesRequest::es_put()){
        $id_stock = UtilesPost::obtener('id_stock', "Falta identificar el stock a modificar");
        $id_producto = UtilesPost::obtener('id_producto', "Falta identificar el producto");
        $cantidad = UtilesPost::obtener('cantidad', "La cantidad es inválida");
        $coste = UtilesPost::obtener('coste', "El Coste no está presente");
        $fecha_vto = UtilesPost::obtener('fecha_vto');
        $stock = new StockLote($id_stock, $id_producto,$cantidad,$coste,$fecha_vto);
        $stock -> actualizar();
    }
    //Delete
    if(UtilesRequest::es_delete()){
        $id_stock = UtilesPost::obtener('id_stock', "Falta identificar el stock a modificar");
        $stock = StockLote::recuperar_por_id($id_stock);
        if($stock == null)
            RespuestasHttp::error_404();
        $stock -> delete();
    }