<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php'); //DIR_PUJOL
    require_once (DIR_PUJOL."/utiles/utilidades_request.php");
    require_once (DIR_PUJOL."/clases/base_de_datos/pais.php");
    require_once (DIR_PUJOL."/utiles/utilidades_get.php");
    require_once (DIR_PUJOL."/clases/conexion/respuestas_http.php");
    

    GestorDePermisos::ExigirRol(["ADMIN","CAJERO"]);

    //GET, POST, PUT
    //GET obtener información
    //Consultar por 1 elemento
    //Consultar por muchos elementos
    if(UtilesRequest::es_get()){
        //1 Solo elemento
        if(UtilesGet::verificar_encabezado("id_pais")){
            $id_pais = UtilesGet::obtener('id_pais');
            $pais = Pais::recuperar_por_id($id_pais);
            if($pais == null)
                RespuestasHttp::error_404();
            header('Content-Type: application/json');
            echo json_encode($pais);
        } else {
            //Consultar por muchos elementos
            $resultados = Pais::consultarTodos();
            header('Content-Type: application/json');
            echo json_encode($resultados);
        }
    }

    //POST crear uno nuevo
    if(UtilesRequest::es_post()){
        GestorDePermisos::ExigirRol(["ADMIN"]);
        $descripcion = UtilesPost::obtener('descripcion', "Faltan campos");
        $pais = new Pais(null, $descripcion);
        $pais -> save();
    }
    //PUT actualizar
    if(UtilesRequest::es_put()){
        GestorDePermisos::ExigirRol(["ADMIN"]);
        $id_pais = UtilesPost::obtener('id_pais');
        $descripcion = UtilesPost::obtener('descripcion');
        $pais = new Pais($id_pais, $descripcion);
        $pais -> actualizar();
    }