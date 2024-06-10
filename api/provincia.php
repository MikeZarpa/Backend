<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php'); //DIR_PUJOL
    require_once (DIR_PUJOL."/utiles/utilidades_request.php");
    require_once (DIR_PUJOL."/clases/base_de_datos/provincia.php");
    require_once (DIR_PUJOL."/utiles/utilidades_get.php");
    require_once (DIR_PUJOL."/clases/conexion/respuestas_http.php");

    //GET, POST, PUT
    //GET obtener información
    //Consultar por 1 elemento
    //Consultar por muchos elementos
    if(UtilesRequest::es_get()){
        //1 Solo elemento
        if(UtilesGet::verificar_encabezado("id_provincia")){
            $id_provincia = UtilesGet::obtener('id_provincia');
            $provincia = Provincia::recuperar_por_id($id_provincia);
            if($provincia == null)
                RespuestasHttp::error_404();
            header('Content-Type: application/json');
            echo json_encode($provincia);
        } else if(UtilesGet::verificar_encabezado("id_pais")){
            $id_pais = UtilesGet::obtener('id_pais');
            $provincias = Provincia::recuperar_por_id_pais($id_pais);
            header('Content-Type: application/json');
            echo json_encode($provincias);

        } else {
            RespuestasHttp::error_400();
            //Consultar por muchos elementos
            /*$resultados = Provincia::consultarTodos();
            header('Content-Type: application/json');
            echo json_encode($resultados);*/
        }
    }

    //POST crear uno nuevo
    if(UtilesRequest::es_post()){
        $descripcion = UtilesPost::obtener('descripcion', "Faltan campos");
        $id_pais = UtilesPost::obtener('id_pais', "Faltan campos");
        $provincia = new Provincia(null, $descripcion, $id_pais);
        $provincia -> save();
    }
    //PUT actualizar
    if(UtilesRequest::es_put()){
        $id_provincia = UtilesPost::obtener('id_provincia');
        $descripcion = UtilesPost::obtener('descripcion');
        $id_pais = UtilesPost::obtener('id_pais', "Faltan campos");

        $provincia = new Provincia($id_provincia, $descripcion, $id_pais);
        $provincia -> actualizar();
    }
    //Delete
    if(UtilesRequest::es_delete()){
        $id_provincia = UtilesGet::obtener('id_provincia');
        $provincia = Provincia::recuperar_por_id($id_provincia);
        if($provincia == null)
            RespuestasHttp::error_404();
        $provincia -> delete();
    }