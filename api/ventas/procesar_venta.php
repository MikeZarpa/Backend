<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php'); //DIR_PUJOL
    require_once (DIR_PUJOL."/utiles/utilidades_request.php");
    require_once (DIR_PUJOL."/clases/base_de_datos/producto.php");
    require_once (DIR_PUJOL."/utiles/utilidades_get.php");
    require_once (DIR_PUJOL."/clases/conexion/respuestas_http.php");

    //POST crear uno nuevo
    if(UtilesRequest::es_post()){
        $factura = UtilesPost::obtener('factura');
        $carrito = $factura -> carrito;
        $factura_venta = new FacturaVenta();
        $factura_venta->save();
        $factura_venta->agregar_detalles_venta_desde_carrito($carrito);
    } else {
        RespuestasHttp::error_405();
    }