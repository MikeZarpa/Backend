<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php'); //DIR_PUJOL
    require_once (DIR_PUJOL."/utiles/utilidades_request.php");
    require_once (DIR_PUJOL."/clases/base_de_datos/direccion.php");
    require_once (DIR_PUJOL."/utiles/utilidades_get.php");
    require_once (DIR_PUJOL."/clases/conexion/respuestas_http.php");

    //GET, POST, PUT
    //GET obtener información
    //Consultar por 1 elemento
    //Consultar por muchos elementos
    if(UtilesRequest::es_get()){
        //1 Solo elemento
        if(UtilesGet::verificar_encabezado("id_direccion")){
            $id_direccion = UtilesGet::obtener('id_direccion');
            $direccion = Direccion::recuperar_por_id($id_direccion);
            if($direccion == null)
                RespuestasHttp::error_404();
            header('Content-Type: application/json');
            echo json_encode($direccion);
        } else {
            RespuestasHttp::error_400();
        }
    }

    //POST crear uno nuevo
    if(UtilesRequest::es_post()){
        RespuestasHttp::error_405();
    }
    //PUT actualizar
    if(UtilesRequest::es_put()){
        $id_direccion = UtilesPost::obtener('id_direccion');
        $calle = UtilesPost::obtener_opcional('calle', "Faltan campos");
        $altura = UtilesPost::obtener_opcional('altura', "Faltan campos");
        $piso = UtilesPost::obtener_opcional('piso', "Faltan campos");
        $departamento = UtilesPost::obtener_opcional('departamento', "Faltan campos");
        $id_localidad = UtilesPost::obtener('id_localidad', "Faltan campos");

        $direccion = new Direccion($id_direccion, $calle, $altura, $piso, $departamento, $id_localidad);
        $direccion -> actualizar();
    }
    if(UtilesRequest::es_delete()){
        RespuestasHttp::error_405(); // No se permite este método...
    }