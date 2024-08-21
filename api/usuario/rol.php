<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php'); //DIR_PUJOL
    require_once (DIR_PUJOL."/utiles/utilidades_request.php");
    require_once (DIR_PUJOL."/clases/base_de_datos/usuario.php");
    require_once (DIR_PUJOL."/utiles/utilidades_get.php");
    require_once (DIR_PUJOL."/clases/conexion/respuestas_http.php");

    //GET, POST, PUT
    //GET obtener información
    //Consultar por 1 elemento
    //Consultar por muchos elementos
    if(UtilesRequest::es_get()){
        //1 Solo elemento
        if(UtilesGet::verificar_encabezado("id_usuario")){
            $id_usuario = UtilesGet::obtener('id_usuario');
            $roles = Rol::obtener_roles_usuario($id_usuario);
            header('Content-Type: application/json');
            echo json_encode($roles);
        } else {
            //Consultar por muchos elementos
            $paginar = !UtilesGet::verificar_encabezado('no_paginar');
            $resultados = Rol::consultarTodos($paginar);
            header('Content-Type: application/json');
            echo json_encode($resultados);
        }
    }

    //POST crear uno nuevo
    if(UtilesRequest::es_post()){
        $descripcion = UtilesPost::obtener("descripcion", "Falta la descripción del rol.");
        $codigo = UtilesPost::obtener("codigo", "Falta el codigo del rol");
        $rol = new Rol(null, $descripcion, $codigo);
        $rol -> save();
    }
    //PUT actualizar datos del usuario, esta forma no puede cambiar contraseñas
    if(UtilesRequest::es_put()){
        $id_rol = UtilesPost::obtener('id_rol', "Falta identificar el rol a actualizar");
        $descripcion = UtilesPost::obtener("descripcion", "Falta la descripción del rol.");

        $rol = Rol::recuperar_por_id($id_rol);
        $rol -> descripcion = $descripcion;
        $rol -> actualizar();
    }
    if(UtilesRequest::es_delete()){
        RespuestasHttp::error_405("No se permite borrar roles, contacte con el administrador");
    }