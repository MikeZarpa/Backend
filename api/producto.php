<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php'); //DIR_PUJOL
    require_once (DIR_PUJOL."/utiles/utilidades_request.php");
    require_once (DIR_PUJOL."/clases/base_de_datos/producto.php");
    require_once (DIR_PUJOL."/utiles/utilidades_get.php");
    require_once (DIR_PUJOL."/clases/conexion/respuestas_http.php");

    //GET, POST, PUT
    //GET obtener información
    //Consultar por 1 elemento
    //Consultar por muchos elementos
    if(UtilesRequest::es_get()){
        //1 Solo elemento
        if(UtilesGet::verificar_encabezado("id_producto")){
            $id_producto = UtilesGet::obtener('id_producto');
            $producto = Producto::recuperar_por_id($id_producto);
            if($producto == null)
                RespuestasHttp::error_404();
            header('Content-Type: application/json');
            echo json_encode($producto);
        } else {
            //Consultar por muchos elementos
            $no_paginar = UtilesGet::obtener_opcional('no_paginar');
            if($no_paginar == null){
                $resultados = Producto::consultarTodos();
            } else {
                $resultados = Producto::consultarTodos(false);
            }
            header('Content-Type: application/json');
            echo json_encode($resultados);
        }
    }

    //POST crear uno nuevo
    if(UtilesRequest::es_post()){
        $descripcion = UtilesPost::obtener('descripcion', "Faltan campos");
        $cantidad_minima = UtilesPost::obtener_opcional('cantidad_minima');
        $id_marca = UtilesPost::obtener_opcional('id_marca');
        $id_categoria = UtilesPost::obtener_opcional('id_categoria');
        $habilitado = UtilesPost::obtener_opcional('habilitado');

        //Para el historial de precios
        $historial_precio_post = UtilesPost::obtener("historial_precio", "Falta el precio del producto");
        if(!isset($historial_precio_post["precio"]))
            RespuestasHttp::error_400("No está presente el precio del producto");
        $precio = $historial_precio_post["precio"];

        if(!isset($historial_precio_post['stock']))
            RespuestasHttp::error_400("No esta presente datos del stock");
        $stock_post = $historial_precio_post['stock'];

        if(!isset($stock_post["cantidad"]) || !isset($stock_post["coste"]))
            RespuestasHttp::error_400("Error con el registro de Stock del nuevo producto");

        $cantidad = $stock_post["cantidad"];
        $coste = $stock_post["coste"];
            
        //Guardamos el producto
        $producto = new Producto(null, $descripcion, $cantidad_minima, $id_marca, $habilitado, $id_categoria);
        $producto -> save();

        //Generamos el registro de Stock
        $stock = new StockLote(null,$producto->id_producto,$cantidad,$coste,null);
        $stock -> save();
        //Generamos el historial de precio
        $historial_precio = new HistorialPrecio(null, $precio,null,$stock->id_stock);
        $historial_precio -> save();
    }
    //PUT actualizar
    if(UtilesRequest::es_put()){
        $id_producto = UtilesPost::obtener('id_producto',"Falta la id del producto.");
        $descripcion = UtilesPost::obtener('descripcion', "Falta el nombre del producto.");
        $cantidad_minima = UtilesPost::obtener('cantidad_minima', "Falta la cantidad minima del producto.");
        $id_marca = UtilesPost::obtener_opcional('id_marca');
        $habilitado = UtilesPost::obtener('habilitado', "Falta la habilitación del producto");
        $id_categoria = UtilesPost::obtener_opcional('id_categoria');

        $producto = new Producto($id_producto, $descripcion, $cantidad_minima, $id_marca, $habilitado,$id_categoria);
        $producto -> actualizar();
    }
    if(UtilesRequest::es_delete()){
        $id_producto = UtilesGet::obtener('id_producto');
        $producto = Producto::recuperar_por_id($id_producto);
        if($producto == null)
            RespuestasHttp::error_404("Producto no encontrado");
        $producto -> delete();
    }