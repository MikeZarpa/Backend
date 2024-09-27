<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php'); //DIR_PUJOL
    require_once (DIR_PUJOL."/utiles/utilidades_request.php");
    require_once (DIR_PUJOL."/clases/base_de_datos/producto.php");
    require_once (DIR_PUJOL."/utiles/utilidades_get.php");
    require_once (DIR_PUJOL."/clases/conexion/respuestas_http.php");

    //POST crear uno nuevo
    if(UtilesRequest::es_post()){
        $id_producto = UtilesPost::obtener('id_producto');
        $precio = UtilesPost::obtener('precio');

        HistorialPrecio::actualizar_precio_por_id_producto($id_producto, $precio);
    } else {
        RespuestasHttp::error_405();
    }