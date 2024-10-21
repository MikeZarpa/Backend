<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php'); //DIR_PUJOL
    require_once (DIR_PUJOL."/utiles/utilidades_request.php");
    require_once (DIR_PUJOL."/clases/base_de_datos/condicion_iva.php");
    require_once (DIR_PUJOL."/utiles/utilidades_get.php");
    require_once (DIR_PUJOL."/clases/conexion/respuestas_http.php");
    

    GestorDePermisos::ExigirRol(["ADMIN","CAJERO"]);

    //GET, POST, PUT
    //GET obtener información
    //Consultar por 1 elemento
    //Consultar por muchos elementos
    if(UtilesRequest::es_get()){
        //1 Solo elemento
        if(UtilesGet::verificar_encabezado("id_cond_iva")){
            $id_cond_iva = UtilesGet::obtener('id_cond_iva');
            $cond_iva = CondicionIva::recuperar_por_id($id_cond_iva);
            if($cond_iva == null)
                RespuestasHttp::error_404();
            header('Content-Type: application/json');
            echo json_encode($cond_iva);
        } else {
            //Consultar por muchos elementos
            $paginar = !UtilesGet::verificar_encabezado('no_paginar');
            $resultados = CondicionIva::consultarTodos($paginar);
            header('Content-Type: application/json');
            echo json_encode($resultados);
        }
    }
    if(UtilesRequest::es_post()){
        RespuestasHttp::error_400();
    }

    //PUT actualizar
    if(UtilesRequest::es_put()){
        GestorDePermisos::ExigirRol(["ADMIN"]);
        $id_cond_iva = UtilesPost::obtener('id_cond_iva');
        $descripcion = UtilesPost::obtener('descripcion');
        $cond_iva = CondicionIva::recuperar_por_id($id_cond_iva);
        $cond_iva -> descripcion = $descripcion;
        $cond_iva -> actualizar();
    }
    if(UtilesRequest::es_delete()){
        RespuestasHttp::error_400();
    }