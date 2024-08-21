<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php'); //DIR_PUJOL
    require_once (DIR_PUJOL."/utiles/utilidades_request.php");
    require_once (DIR_PUJOL."/clases/base_de_datos/roles_asignados.php");
    require_once (DIR_PUJOL."/utiles/utilidades_get.php");
    require_once (DIR_PUJOL."/clases/conexion/respuestas_http.php");
    require_once (DIR_PUJOL."/utiles/utilidades_get.php");
    

    //GET, POST, PUT
    //GET obtener información
    //Consultar por 1 elemento
    //Consultar por muchos elementos
    if(UtilesRequest::es_get()){
        $paginar = !UtilesGet::verificar_encabezado('no_paginar');
        $resultados = RolesAsignados::consultarTodos($paginar);
        header('Content-Type: application/json');
        echo json_encode($resultados);
    }
    
    //POST crear uno nuevo
    if(UtilesRequest::es_post()){
        $id_usuario = UtilesPost::obtener("id_usuario", "No se especificó el usuario objetivo.");
        $roles_id = UtilesPost::obtener("roles_id", "No se identificaron los roles");
        
        RolesAsignados::agregar_roles_al_usuario($id_usuario, $roles_id);
    }

    
    if(UtilesRequest::es_put()){
        RespuestasHttp::error_405();
    }

    if(UtilesRequest::es_delete()){
        $id_usuario = UtilesPost::obtener("id_usuario", "No se especificó el usuario objetivo.");
        $roles_id = UtilesPost::obtener("roles_id", "No se identificaron los roles");
        
        RolesAsignados::remover_roles_al_usuario($id_usuario, $roles_id);
    }