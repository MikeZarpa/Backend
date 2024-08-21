<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php');
    require_once (DIR_PUJOL."/clases/conexion/conexion.php");
    require_once (DIR_PUJOL."/clases/conexion/respuestas_http.php");

    class TipoDetalleVenta {
        public $id_tipo_det_venta = null;
        public $descripcion;
        public $codigo;

        public function __construct()
        {

        }
        
        public static function recuperar_por_id($id_tipo_det_venta){
            $id_tipo_det_venta = Conexion::escaparCadena($id_tipo_det_venta);
            $consultaSQL = "SELECT * FROM tipo_detalle_venta WHERE tipo_detalle_venta.id_tipo_det_venta = $id_tipo_det_venta;";
            $resultados = Conexion::obtenerDatos($consultaSQL);

            if(!$resultados)
                RespuestasHttp::error_404("No se encontró el Tipo Detalle de Venta");
            $resultado = $resultados[0];
            $tipo_det_venta = new TipoDetalleVenta();
            $tipo_det_venta -> id_tipo_det_venta = $resultado["id_tipo_det_venta"];
            $tipo_det_venta -> descripcion = $resultado["descripcion"];
            $tipo_det_venta -> codigo = $resultado["codigo"];
            return $tipo_det_venta;
        }
    }