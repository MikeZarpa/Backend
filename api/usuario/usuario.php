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
            $usuario = Usuario::recuperar_usuario_por_id($id_usuario);
            if($usuario == null)
                RespuestasHttp::error_404();
            header('Content-Type: application/json');
            echo json_encode($usuario);
        } else {
            //Consultar por muchos elementos
            $paginar = !UtilesGet::verificar_encabezado('no_paginar');
            $resultados = Usuario::consultarTodos($paginar);
            header('Content-Type: application/json');
            echo json_encode($resultados);
        }
    }

    //POST crear uno nuevo
    if(UtilesRequest::es_post()){
        $username = UtilesPost::obtener("username", "Falta nombre de usuario");
        $email = UtilesPost::obtener("email", "Falta el correo");
        $password = UtilesPost::obtener("password", "Falta la contraseña");
        $nombre = UtilesPost::obtener("nombre", "Falta el Nombre");
        $apellido = UtilesPost::obtener("apellido", "Falta el apellido");
        $palabra_secreta = UtilesPost::obtener("palabra_secreta", "Falta la palabra secreta");
        $habilitado = UtilesPost::obtener_opcional("habilitado");

        $usuario = new Usuario(null, $username, $email, $password, $nombre, $apellido, $palabra_secreta, $habilitado);
        $usuario -> save();
    }
    //PUT actualizar datos del usuario, esta forma no puede cambiar contraseñas
    if(UtilesRequest::es_put()){
        $id_usuario = UtilesPost::obtener('id_usuario');
        $username = UtilesPost::obtener("username", "Falta nombre de usuario");
        $email = UtilesPost::obtener("email", "Falta el correo");
        // $password = UtilesPost::obtener("password", "Falta la contraseña");
        $nombre = UtilesPost::obtener("nombre", "Falta el Nombre");
        $apellido = UtilesPost::obtener("apellido", "Falta el apellido");
        // $palabra_secreta = UtilesPost::obtener("palabra_secreta", "Falta la palabra secreta");
        $habilitado = UtilesPost::obtener_opcional("habilitado");

        $usuario = new Usuario($id_usuario, $username, $email, null, $nombre, $apellido, null, $habilitado);
        $usuario -> actualizar();
    }
    if(UtilesRequest::es_delete()){
        $id_usuario = UtilesGet::obtener('id_usuario', "Falta identificar el usuario");

        $id_usuario_actual = GestorDePermisos::obtener_id_usuario_actual();
        if($id_usuario == $id_usuario_actual)
            RespuestasHttp::error_400("No puedes dar de baja el usuario que estas utilizando...");

        $usuario = Usuario::recuperar_usuario_por_id($id_usuario);
        $usuario -> alternar_habilitacion_del_usuario();
        http_response_code(200);
    }