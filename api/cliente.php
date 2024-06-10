<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php'); //DIR_PUJOL
    require_once (DIR_PUJOL."/utiles/utilidades_request.php");
    require_once (DIR_PUJOL."/clases/base_de_datos/cliente.php");
    require_once (DIR_PUJOL."/utiles/utilidades_get.php");
    require_once (DIR_PUJOL."/clases/conexion/respuestas_http.php");

    //GET, POST, PUT
    //GET obtener información
    //Consultar por 1 elemento
    //Consultar por muchos elementos
    if(UtilesRequest::es_get()){
        //1 Solo elemento
        if(UtilesGet::verificar_encabezado("id_cliente")){
            $id_cliente = UtilesGet::obtener('id_cliente');
            $cliente = Cliente::recuperar_por_id($id_cliente);
            if($cliente == null)
                RespuestasHttp::error_404();
            header('Content-Type: application/json');
            echo json_encode($cliente);
        } else {
            //Consultar por muchos elementos
            $resultados = Cliente::consultarTodos();
            header('Content-Type: application/json');
            echo json_encode($resultados);
        }
    }

    //POST crear uno nuevo
    if(UtilesRequest::es_post()){
        $nombre = UtilesPost::obtener('nombre', "Faltan campos");
        $apellido = UtilesPost::obtener('apellido', "Faltan campos");
        $dni = UtilesPost::obtener('dni', "Faltan campos");
        $cuil_cuit = UtilesPost::obtener('cuil_cuit', "Faltan campos");
        $id_cond_iva = UtilesPost::obtener_opcional('id_cond_iva', "Faltan campos");

        $direccion_data = UtilesPost::obtener('direccion');
        $direccion = Direccion::inicializar_desde_array($direccion_data);
        $direccion -> save();
        $id_direccion = $direccion -> id_direccion;

        $id_pais = UtilesPost::obtener_opcional('id_pais', "Faltan campos");

        $cliente = new Cliente(null, $nombre, $apellido, $dni, $cuil_cuit, $id_cond_iva, $id_direccion, $id_pais);
        $cliente -> save();
    }
    //PUT actualizar
    if(UtilesRequest::es_put()){
        $id_cliente = UtilesPost::obtener('id_cliente');
        $nombre = UtilesPost::obtener('nombre', "Faltan campos");
        $apellido = UtilesPost::obtener('apellido', "Faltan campos");
        $dni = UtilesPost::obtener('dni', "Faltan campos");
        $cuil_cuit = UtilesPost::obtener('cuil_cuit', "Faltan campos");
        $id_cond_iva = UtilesPost::obtener_opcional('id_cond_iva', "Faltan campos");
        $id_direccion = UtilesPost::obtener_opcional('id_direccion', "Faltan campos");
        $id_pais = UtilesPost::obtener_opcional('id_pais', "Faltan campos");

        $cliente = new Cliente($id_cliente, $nombre, $apellido, $dni, $cuil_cuit, $id_cond_iva, $id_direccion, $id_pais);
        $cliente -> actualizar();
    }
    if(UtilesRequest::es_delete()){
        RespuestasHttp::error_405(); // No se permite este método...
    }