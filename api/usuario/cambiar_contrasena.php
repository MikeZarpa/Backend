<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php'); //DIR_PUJOL
    require_once (DIR_PUJOL."/utiles/utilidades_request.php");
    require_once (DIR_PUJOL."/clases/base_de_datos/usuario.php");
    require_once (DIR_PUJOL."/clases/conexion/respuestas_http.php");

    //POST crear uno nuevo
    if(UtilesRequest::es_post()){
        $id_usuario_solicitante = GestorDePermisos::obtener_id_usuario_actual();
        $id_usuario_objetivo = UtilesPost::obtener("id_usuario_objetivo", "Falta identificar al usuario objetivo.");
        $nueva_contrasena = UtilesPost::obtener("nueva_contrasena", "La nueva contraseña no está presente.");
        $contrasena_actual_solicitante = UtilesPost::obtener("contrasena_del_solicitante", "No se confirmó la contraseña.");

        Usuario::cambiar_contrasena($id_usuario_objetivo, $nueva_contrasena, $id_usuario_solicitante, $contrasena_actual_solicitante);
    }