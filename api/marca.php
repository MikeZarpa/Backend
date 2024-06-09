<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php'); //DIR_PUJOL
    require_once (DIR_PUJOL."/utiles/utilidades_request.php");
    require_once (DIR_PUJOL."/clases/base_de_datos/marca.php");
    require_once (DIR_PUJOL."/utiles/utilidades_get.php");
    require_once (DIR_PUJOL."/clases/conexion/respuestas_http.php");

    //GET, POST, PUT
    //GET obtener información
    //Consultar por 1 elemento
    //Consultar por muchos elementos
    if(UtilesRequest::es_get()){
        //1 Solo elemento
        if(UtilesGet::verificar_encabezado("id_marca")){
            $id_marca = UtilesGet::obtener('id_marca');
            $marca = Marca::recuperar_por_id($id_marca);
            if($marca == null)
                RespuestasHttp::error_404();
            header('Content-Type: application/json');
            echo json_encode($marca);
        } else {
            //Consultar por muchos elementos
            $resultados = Marca::consultarTodos();
            header('Content-Type: application/json');
            echo json_encode($resultados);
        }
    }

    //POST crear uno nuevo
    //PUT actualizar