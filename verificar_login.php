<?php 
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php');
    require_once (DIR_PUJOL.'/utiles/obtener_post.php');
    require_once (DIR_PUJOL.'/clases/conexion/conexion.php');
    require_once (DIR_PUJOL.'/clases/conexion/respuestas_http.php');
    require_once (DIR_PUJOL.'/clases/base_de_datos/usuario.php');
    require_once (DIR_PUJOL.'/utiles/utilidades_request.php');


    header('Content-Type: application/json');
    header("Access-Control-Expose-Headers: Content-Type, Authorization, X-Custom-Header");

    if(UtilesRequest::es_post()){

        $password = UtilesPost::obtener("password");
        $email = UtilesPost::obtener_opcional("email");
        $username = UtilesPost::obtener_opcional("username");

        $usuario = new Usuario($username, $email, $password);
        $usuario -> iniciar_sesion();

        echo json_encode($usuario);
    }

    RespuestasHttp::error_405();
    ?>