<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php');
    require_once (DIR_PUJOL."/clases/conexion/conexion.php");
    require_once (DIR_PUJOL."/clases/utiles/PaginableClass.php");
    require_once (DIR_PUJOL."/utiles/utilidades_get.php");
    require_once (DIR_PUJOL."/clases/utiles/FiltroClass.php");
    require_once (DIR_PUJOL."/clases/conexion/respuestas_http.php");
    require_once (DIR_PUJOL."/clases/base_de_datos/tipo_detalle_venta.php");
    require_once (DIR_PUJOL."/clases/base_de_datos/historial_precio.php");

    class DetalleVenta {
        public $id_det_venta = null;
        public $cantidad;
        public $id_histprecio;
        public $hist_precio;
        public $id_promocion = null;
        public $id_factura_venta;
        public $id_tipo_det_venta = 1;
        public $tipo_det_venta;

        private $cantidad_total_a_registrar = null;

        public function __construct($id_histprecio, $cantidad, $id_factura_venta)
        {
            $this->id_histprecio = $id_histprecio;
            $this -> cantidad = $cantidad;
            $this -> cantidad_total_a_registrar = $cantidad;
            $this->id_factura_venta = $id_factura_venta;
            $this -> hist_precio = HistorialPrecio::recuperar_por_id($id_histprecio);
        }
        
        public function save(){
            $id_promocion = $this -> id_promocion ?? "null";
            $id_factura_venta = $this -> id_factura_venta;
            $id_tipo_det_venta = $this -> id_tipo_det_venta;
            $cantidad_a_ingresar = $this -> cantidad_total_a_registrar;

            $numero_de_bucles = 0;

            //Un bucle que guarda teniendo en cuenta los stock de los historiales de precio
            while(true){
                $numero_de_bucles++;

                //Comenzamos a tratar las cantidades
                $id_histprecio = $this -> id_histprecio;
                
                $this -> comprobar_la_valides_del_historial_de_precio($id_histprecio);
                $cantidad = $this -> hist_precio -> stock -> reducir_el_stock_y_obtener_ingresado($cantidad_a_ingresar);
                //El stock está vacio, por lo que devuelve 0, guardamos lo que queda, probablemente algo falló en el seguimiento del stock pero debemos seguir con la venta...
                if($cantidad == 0) $cantidad = $cantidad_a_ingresar;
                
                $consultaSQL = "INSERT INTO detalle_venta(cantidad, id_histprecio, id_promocion, id_factura_venta,id_tipo_det_venta) VALUE ($cantidad, $id_histprecio, $id_promocion, $id_factura_venta, $id_tipo_det_venta)";
                Conexion::nonQuery($consultaSQL);
                
                //Actualizamos para el bucle
                $cantidad_a_ingresar = $cantidad_a_ingresar - $cantidad;
                if($cantidad_a_ingresar == 0) break;
                if($numero_de_bucles > 25){
                    var_dump($cantidad_a_ingresar);
                    var_dump($this);
                    RespuestasHttp::error_500("Ocurrió un bucle muy largo cuando se guardaba un detalle de venta");
                }
            }

        }

        public function comprobar_la_valides_del_historial_de_precio($id_histprecio){
            //Verificamos que esté vigente el historial de precio utilizado
            $historial_de_precio =  HistorialPrecio::recuperar_por_id($id_histprecio);
            $hay_nuevo_historial = $historial_de_precio -> intentar_recargar_stock_producto();
            if($hay_nuevo_historial){
                //Actualizamos el historial de esta venta
                $historial_actualizado = HistorialPrecio::recuperar_actual_por_id_producto($historial_de_precio -> stock -> id_producto);
                $this -> id_histprecio = $historial_actualizado -> id_histprecio;
                $this -> hist_precio = $historial_actualizado;
            }
        }

        public static function recuperar_por_id_factura($id_factura_venta){
            $id_factura_venta = Conexion::escaparCadena($id_factura_venta);
            $consultaSQL = "SELECT * FROM detalle_venta WHERE detalle_venta.id_factura_venta = $id_factura_venta;";
            $resultados = Conexion::obtenerDatos($consultaSQL);
            $array_detalle_venta = [];
            foreach ($resultados as $registro_detalle) {
                $detalle = new DetalleVenta($registro_detalle["id_histprecio"],$registro_detalle["cantidad"],$registro_detalle["id_factura_venta"]);

                $detalle -> id_det_venta = $registro_detalle["id_det_venta"];
                $detalle -> hist_precio = HistorialPrecio::recuperar_por_id($detalle -> id_histprecio);
                $detalle -> id_tipo_det_venta = $registro_detalle["id_tipo_det_venta"];
                $detalle -> tipo_det_venta = TipoDetalleVenta::recuperar_por_id($detalle -> id_tipo_det_venta);

                $array_detalle_venta[] = $detalle;
            }
            return $array_detalle_venta;
        }
        public function obtener_importe_detalle_venta(){
            $cantidad = $this -> cantidad;
            $precio = $this-> hist_precio ->precio;
            return $cantidad * $precio;
        }
    }