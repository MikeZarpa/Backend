<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php'); //DIR_PUJOL
    require_once (DIR_PUJOL."/utiles/utilidades_request.php");
    require_once (DIR_PUJOL."/clases/base_de_datos/producto.php");
    require_once (DIR_PUJOL."/utiles/utilidades_get.php");
    require_once (DIR_PUJOL."/clases/conexion/respuestas_http.php");    

    //Consulta el stock apartir de un producto
    if(UtilesRequest::es_get()){
        //1 Solo elemento
        $id_producto = UtilesGet::obtener('id_producto', "Falta identificar el producto");
        $stock_lote = StockLote::recuperar_todos_por_id_producto($id_producto);
        header('Content-Type: application/json');
        echo json_encode($stock_lote);
    }

    //Permite el ingreso de un nuevo stock
    if(UtilesRequest::es_post()){
        $id_stock = UtilesPost::obtener("id_stock","Falta identificar el stock a modificar");
        $id_producto = UtilesPost::obtener("id_producto", "Falta identificar el producto del stock");
        $cantidad = UtilesPost::obtener("cantidad","Falta la cantidad");
        $coste = UtilesPost::obtener("coste", "Falta el coste");
        $fecha_vto = UtilesPost::obtener_opcional("fecha_vto");

        $stock = new StockLote(null, $id_producto, $cantidad, $coste, $fecha_vto);
        $stock -> save();
    }

    //Modificar la cantidad o fecha de vencimiento en el stock
    if(UtilesRequest::es_put()){
        $id_stock = UtilesPost::obtener("id_stock","Falta identificar el stock a modificar");
        // $id_producto = UtilesPost::obtener("id_producto");
        $cantidad = UtilesPost::obtener("cantidad","Falta la cantidad");
        $coste = UtilesPost::obtener("coste", "Falta el coste");
        $fecha_vto = UtilesPost::obtener_opcional("fecha_vto");
        
        $stock = StockLote::recuperar_sin_producto_por_id($id_stock);
        $stock -> cantidad = $cantidad;
        $stock -> coste = $coste;
        $stock -> fecha_vto = $fecha_vto;

        $stock -> actualizar();
    }

    //Reducir a 0 un stock
    if(UtilesRequest::es_delete()){
        $id_stock = UtilesPost::obtener("id_stock","Falta identificar el stock a reducir");
        $stock = StockLote::recuperar_por_id($id_stock);
        $stock -> delete();
    }