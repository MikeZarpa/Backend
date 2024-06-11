<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php');
    require_once (DIR_PUJOL."/clases/conexion/conexion.php");
    require_once (DIR_PUJOL."/clases/utiles/PaginableClass.php");
    require_once (DIR_PUJOL."/utiles/utilidades_get.php");
    require_once (DIR_PUJOL."/clases/utiles/FiltroClass.php");
    require_once (DIR_PUJOL."/clases/conexion/respuestas_http.php");
    require_once (DIR_PUJOL."/clases/base_de_datos/producto.php");
    require_once (DIR_PUJOL."/clases/base_de_datos/stock_lote.php");


    class HistorialPrecio {
        public $id_histprecio = null;
        public $precio;
        public $fecha_vigencia;
        public $id_stock;
        
        public $stock = null;

        public function __construct($id_histprecio, $precio,$fecha_vigencia,$id_stock)
        {
            $this -> id_histprecio = $id_histprecio;
            $this -> precio = $precio;
            $this -> fecha_vigencia = $fecha_vigencia;
            $this -> id_stock = $id_stock;
        }

        public static function consultarTodos(){
            RespuestasHttp::error_500("No implementado aun...");
        }
        
        public static function recuperar_por_id($id_histprecio){
            RespuestasHttp::error_500("No implementado aun...");
        }

        public static function inicializar_desde_array($array){
            $historial_precio = new HistorialPrecio($array['id_histprecio'], $array['precio'], $array['fecha_vigencia'], $array['id_stock']);
            return $historial_precio;
        }
        public static function recuperar_actual_por_id_producto($id_producto){            
            $consultaSQL = "SELECT historial_precio.id_histprecio, historial_precio.precio, historial_precio.fecha_vigencia, historial_precio.id_stock FROM historial_precio JOIN stock_lote ON historial_precio.id_stock = stock_lote.id_stock WHERE stock_lote.id_producto = $id_producto AND historial_precio.fecha_vigencia = (SELECT MAX(historial_precio.fecha_vigencia) FROM historial_precio JOIN stock_lote ON historial_precio.id_stock = stock_lote.id_stock WHERE stock_lote.id_producto=$id_producto AND historial_precio.fecha_vigencia <= NOW())";
            $resultado = Conexion::obtenerDatos($consultaSQL);
            if($resultado){
                $historial_precio = self::inicializar_desde_array($resultado[0]);
                $historial_precio -> stock = StockLote::recuperar_sin_producto_por_id($historial_precio -> id_stock);
                return $historial_precio;
            } else return null;
        }

        public function save(){
            $precio = $this -> precio;
            $precio = Conexion::escaparCadena($precio);

            $id_stock = $this -> id_stock;
            $id_stock = Conexion::escaparCadena($id_stock);

            
            $consultaSQL = "INSERT INTO historial_precio(precio, id_stock) VALUE ($precio,$id_stock)";

            $this -> id_histprecio = Conexion::nonQueryId($consultaSQL);
        }

        public static function actualizar_precio_por_id_producto($id_producto, $precio){
            $ultimo_registro_precio = self::recuperar_actual_por_id_producto($id_producto);
            if($ultimo_registro_precio!=null){
                $ultimo_registro_precio -> precio = $precio;
                $ultimo_registro_precio -> save();
            }
        }
    }