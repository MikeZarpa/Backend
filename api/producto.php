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
        $habilitado = UtilesPost::obtener_opcional('habilitado');

        $producto = new Producto(null, $descripcion, $cantidad_minima, $id_marca, $habilitado);
        $producto -> save();
    }
    //PUT actualizar
    if(UtilesRequest::es_put()){
        $id_producto = UtilesPost::obtener('id_producto');
        $descripcion = UtilesPost::obtener('descripcion');
        $cantidad_minima = UtilesPost::obtener('cantidad_minima');
        $id_marca = UtilesPost::obtener('id_marca');
        $habilitado = UtilesPost::obtener('habilitado');

        $producto = new Producto($id_producto, $descripcion, $cantidad_minima, $id_marca, $habilitado);
        $producto -> actualizar();
    }
    if(UtilesRequest::es_delete()){
        $id_producto = UtilesGet::obtener('id_producto');
        $producto = Producto::recuperar_por_id($id_producto);
        if($producto == null)
            RespuestasHttp::error_404("Producto no encontrado");
        $producto -> delete();
    }