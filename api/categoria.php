<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php'); //DIR_PUJOL
    require_once (DIR_PUJOL."/utiles/utilidades_request.php");
    require_once (DIR_PUJOL."/clases/base_de_datos/categoria.php");
    require_once (DIR_PUJOL."/utiles/utilidades_get.php");
    require_once (DIR_PUJOL."/clases/conexion/respuestas_http.php");
    

    GestorDePermisos::ExigirRol(["ADMIN","CAJERO"]);

    //GET, POST, PUT
    //GET obtener información
    //Consultar por 1 elemento
    //Consultar por muchos elementos
    if(UtilesRequest::es_get()){
        //1 Solo elemento
        if(UtilesGet::verificar_encabezado("id_categoria")){
            $id_categoria = UtilesGet::obtener('id_categoria');
            $categoria = Categoria::recuperar_por_id($id_categoria);
            if($categoria == null)
                RespuestasHttp::error_404();
            header('Content-Type: application/json');
            echo json_encode($categoria);
        } else {
            //Consultar por muchos elementos
            $no_paginar = UtilesGet::obtener_opcional('no_paginar');
            if($no_paginar == null){
                $resultados = Categoria::consultarTodos();
            } else {
                $resultados = Categoria::consultarTodos(false);
            }
            header('Content-Type: application/json');
            echo json_encode($resultados);
        }
    }

    //POST crear uno nuevo
    if(UtilesRequest::es_post()){
        GestorDePermisos::ExigirRol(["ADMIN"]);
        $descripcion = UtilesPost::obtener('descripcion', "Faltan campos");
        $categoria = new Categoria(null, $descripcion);
        $categoria -> save();
    }
    //PUT actualizar
    if(UtilesRequest::es_put()){
        GestorDePermisos::ExigirRol(["ADMIN"]);
        $id_categoria = UtilesPost::obtener('id_categoria');
        $descripcion = UtilesPost::obtener('descripcion');
        $categoria = new Categoria($id_categoria, $descripcion);
        $categoria -> actualizar();
    }