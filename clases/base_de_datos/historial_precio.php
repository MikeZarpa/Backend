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
            RespuestasHttp::error_500("ConsultarTodos HistorialPrecio No implementado aun...");
        }
        
        public static function recuperar_por_id($id_histprecio){
            if($id_histprecio == null) return null;

            $consultaSQL = "SELECT * FROM historial_precio WHERE historial_precio.id_histprecio = $id_histprecio;";
            $resultados = Conexion::obtenerDatos($consultaSQL);
            if(!$resultados){
                return RespuestasHttp::error_404("No se encontro el historial de precio");
            }
            $registro_historial = HistorialPrecio::inicializar_desde_array($resultados[0]);
            $registro_historial -> stock = StockLote::recuperar_por_id($registro_historial -> id_stock);
            return $registro_historial;
        }

        public static function inicializar_desde_array($array){
            $historial_precio = new HistorialPrecio($array['id_histprecio'], $array['precio'], $array['fecha_vigencia'], $array['id_stock']);
            return $historial_precio;
        }
        public static function recuperar_actual_por_id_producto($id_producto){            
            $consultaSQL = "SELECT historial_precio.* FROM historial_precio JOIN stock_lote ON historial_precio.id_stock = stock_lote.id_stock WHERE stock_lote.id_producto = $id_producto AND historial_precio.fecha_vigencia = (SELECT MAX(historial_precio.fecha_vigencia) FROM historial_precio JOIN stock_lote ON historial_precio.id_stock = stock_lote.id_stock WHERE stock_lote.id_producto=$id_producto AND historial_precio.fecha_vigencia <= NOW()) ORDER BY historial_precio.id_histprecio DESC";
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

        public function intentar_recargar_stock_producto(){
            
            $historial_precio_actual = self::recuperar_actual_por_id_producto($this -> stock -> id_producto);
            //Si el historial de precio no es el más reciente, se informa que es necesario recargar, porque se está usando uno viejo
            if($historial_precio_actual -> id_histprecio != $this -> id_histprecio)
                return true;

            $cantidad_actual = $this -> stock -> cantidad;
            //Si aún hay stock, no necesitamos recargar...
            if($cantidad_actual > 0) return false;

            $nuevo_stock = StockLote::obtener_stock_mas_viejo_por_id_producto($this -> stock -> id_producto);

            //Si ya estamos usando el stock más viejo, no necesitamos recargar...
            if($nuevo_stock -> id_stock == $this -> id_stock) return false;

            $this -> id_stock = $nuevo_stock -> id_stock;
            $this -> save();
            return true;
        }
    }