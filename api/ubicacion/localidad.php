<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php'); //DIR_PUJOL
    require_once (DIR_PUJOL."/utiles/utilidades_request.php");
    require_once (DIR_PUJOL."/clases/base_de_datos/localidad.php");
    require_once (DIR_PUJOL."/utiles/utilidades_get.php");
    require_once (DIR_PUJOL."/clases/conexion/respuestas_http.php");
    

    GestorDePermisos::ExigirRol(["ADMIN","CAJERO"]);

    //GET, POST, PUT
    //GET obtener información
    //Consultar por 1 elemento
    //Consultar por muchos elementos
    if(UtilesRequest::es_get()){
        //1 Solo elemento
        if(UtilesGet::verificar_encabezado("id_localidad")){
            $id_localidad = UtilesGet::obtener('id_localidad');
            $localidad = Localidad::recuperar_por_id($id_localidad);
            if($localidad == null)
                RespuestasHttp::error_404();
            header('Content-Type: application/json');
            echo json_encode($localidad);
        } else if(UtilesGet::verificar_encabezado("id_provincia")){
            $id_provincia = UtilesGet::obtener('id_provincia');
            $localidades = Localidad::recuperar_por_provincia($id_provincia);
            header('Content-Type: application/json');
            echo json_encode($localidades);
        } else {
            RespuestasHttp::error_400();
        }
    }

    //POST crear uno nuevo
    if(UtilesRequest::es_post()){
        GestorDePermisos::ExigirRol(["ADMIN"]);
        $descripcion = UtilesPost::obtener('descripcion', "Faltan campos");
        $id_provincia = UtilesPost::obtener('id_provincia', "Faltan campos");
        $codigo_postal = UtilesPost::obtener('codigo_postal', "Faltan campos");


        $localidad = new Localidad(null, $descripcion,$codigo_postal, $id_provincia);
        $localidad -> save();
    }
    //PUT actualizar
    if(UtilesRequest::es_put()){
        GestorDePermisos::ExigirRol(["ADMIN"]);
        $id_localidad = UtilesPost::obtener('id_localidad', "Faltan campos");
        $descripcion = UtilesPost::obtener('descripcion', "Faltan campos");
        $id_provincia = UtilesPost::obtener('id_provincia', "Faltan campos");
        $codigo_postal = UtilesPost::obtener('codigo_postal', "Faltan campos");

        $localidad = new Localidad($id_localidad, $descripcion,$codigo_postal, $id_provincia);
        $localidad -> actualizar();
    }
    //Delete
    if(UtilesRequest::es_delete()){
        GestorDePermisos::ExigirRol(["ADMIN"]);
        $id_localidad = UtilesGet::obtener('id_localidad');
        $localidad = Localidad::recuperar_por_id($id_localidad);
        if($localidad == null)
            RespuestasHttp::error_404();
        $localidad -> delete();
    }