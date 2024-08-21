<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php'); //DIR_PUJOL
    require_once (DIR_PUJOL."/utiles/utilidades_request.php");
    require_once (DIR_PUJOL."/clases/base_de_datos/usuario.php");
    require_once (DIR_PUJOL."/utiles/utilidades_get.php");
    require_once (DIR_PUJOL."/clases/conexion/respuestas_http.php");

    //POST crear uno nuevo
    if(UtilesRequest::es_post()){
        $id_usuario_desde_token = GestorDePermisos::obtener_id_usuario_actual();
        $usuario = Usuario::recuperar_usuario_por_id($id_usuario_desde_token);
        header('Content-Type: application/json');
        echo json_encode($usuario);
    } else {
        RespuestasHttp::error_405();
    }