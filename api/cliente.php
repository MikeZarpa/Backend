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
            $paginar = !UtilesGet::verificar_encabezado('no_paginar');
            $resultados = Cliente::consultarTodos($paginar);
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
        $id_cond_iva = UtilesPost::obtener_opcional('id_cond_iva');

        $direccion_data = UtilesPost::obtener('direccion');
        $direccion = Direccion::inicializar_desde_array($direccion_data);
        $direccion -> save();
        $id_direccion = $direccion -> id_direccion;

        $id_pais = UtilesPost::obtener_opcional('id_pais');
        $habilitado = UtilesPost::obtener_opcional('habilitado') ?? 1;

        $cliente = new Cliente(null, $nombre, $apellido, $dni, $cuil_cuit, $id_cond_iva, $id_direccion, $id_pais, $habilitado);
        $cliente -> save();
    }
    //PUT actualizar
    if(UtilesRequest::es_put()){
        $id_cliente = UtilesPost::obtener('id_cliente');
        $nombre = UtilesPost::obtener('nombre', "Faltan campos");
        $apellido = UtilesPost::obtener('apellido', "Faltan campos");
        $dni = UtilesPost::obtener('dni', "Faltan campos");
        $cuil_cuit = UtilesPost::obtener('cuil_cuit', "Faltan campos");
        $id_cond_iva = UtilesPost::obtener_opcional('id_cond_iva');
        $id_direccion = UtilesPost::obtener_opcional('id_direccion');
        $id_pais = UtilesPost::obtener_opcional('id_pais');
        $habilitado = UtilesPost::obtener_opcional('habilitado') ?? 1;

        $cliente = new Cliente($id_cliente, $nombre, $apellido, $dni, $cuil_cuit, $id_cond_iva, $id_direccion, $id_pais, $habilitado);
        $cliente -> actualizar();
        if(UtilesPost::verificar_encabezado("direccion")){
            $direccion = UtilesPost::obtener('direccion', "Faltan campos");
            $direccion_obj = Direccion::inicializar_desde_array($direccion);
            $direccion_obj -> actualizar();
        }
    }
    if(UtilesRequest::es_delete()){
        $id_cliente = UtilesPost::obtener('id_cliente', "No se identificó el cliente en el body");
        $cliente = Cliente::recuperar_por_id($id_cliente);
        $cliente -> alternar_habilitacion_del_cliente();
    }