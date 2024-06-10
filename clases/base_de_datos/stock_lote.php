<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php');
    require_once (DIR_PUJOL."/clases/conexion/conexion.php");
    require_once (DIR_PUJOL."/clases/utiles/PaginableClass.php");
    require_once (DIR_PUJOL."/utiles/utilidades_get.php");
    require_once (DIR_PUJOL."/clases/utiles/FiltroClass.php");
    require_once (DIR_PUJOL."/clases/conexion/respuestas_http.php");
    require_once (DIR_PUJOL."/clases/base_de_datos/producto.php");

    class StockLote {
        public $id_stock = null;
        public $id_producto = null;
        public $cantidad = null;
        public $coste = null;
        public $fecha_vto = null;
        public $producto = null;

        public function __construct($id_stock, $id_producto,$cantidad,$coste,$fecha_vto)
        {
            $this -> id_stock = $id_stock;
            $this -> id_producto = $id_producto;
            $this -> cantidad = $cantidad;
            $this -> coste = $coste;
            $this -> fecha_vto = $fecha_vto;
        }

        public static function consultarTodos(){

            $consultaSQL = "SELECT stock_lote.id_stock, stock_lote.id_producto, stock_lote.cantidad, stock_lote.coste, stock_lote.fecha_vto FROM stock_lote, producto WHERE stock_lote.id_producto = producto.id_producto ";
            //$pagina = new PaginableClass($consultaSQL, $numero_de_pagina);
            
            $numero_de_pagina = UtilesGet::obtener_opcional('nroPagina');
            $filtro = new Filtro(["producto.descripcion"]);
            $consulta_con_filtro = $consultaSQL.($filtro -> generar_condiciones());

            
            $pagina = new PaginableClass($consulta_con_filtro, $numero_de_pagina);
            
            //Rellenamos la página con los productos
            foreach ($pagina -> datos as $key => $dato) {
                $pagina -> datos[$key]['producto'] = Producto::recuperar_por_id($dato['id_producto']);
            }
            
            return $pagina;
        }
        
        public static function recuperar_por_id($id_stock){
            $consultaSQL = "SELECT stock_lote.id_stock, stock_lote.id_producto, stock_lote.cantidad, stock_lote.coste, stock_lote.fecha_vto FROM stock_lote WHERE id_stock = $id_stock";
            $resultado = Conexion::obtenerDatos($consultaSQL);
            if($resultado){
                $marca = self::inicializar_desde_array($resultado[0]);
                return $marca;
            } else return null;
        }

        public static function inicializar_desde_array($array){
            $stock = new StockLote($array['id_stock'], $array['id_producto'],$array['cantidad'],$array['coste'],$array['fecha_vto']);
            $stock -> producto = Producto::recuperar_por_id($array['id_producto']);
            return $stock;
        }
        
        public function actualizar(){
            $id_stock = Conexion::escaparCadena($this -> id_stock);
            $id_producto = Conexion::escaparCadena($this -> id_producto);
            $cantidad = (float) Conexion::escaparCadena($this -> cantidad);
            $coste =(float) Conexion::escaparCadena($this -> coste);
            $fecha_vto_sql = isset($fecha_vto) ? "DATE('$fecha_vto')" : "NULL";
            $cantidad_encontrada = Conexion::nonQuery("SELECT * FROM stock_lote WHERE stock_lote.id_stock = $id_stock");
            if($cantidad_encontrada == 1){
                $consultaActualizacion = "UPDATE stock_lote SET stock_lote.id_producto = $id_producto, stock_lote.cantidad = $cantidad, stock_lote.coste = $coste, stock_lote.fecha_vto = DATE($fecha_vto_sql  ) WHERE stock_lote.id_stock = $id_stock";
                Conexion::nonQuery($consultaActualizacion);
            } else {
                RespuestasHttp::error_404("No se encuentra el registro a actualizar.");
            }
        }

        public function delete(){
            $id_stock = $this -> id_stock;
            $cantidad_encontrada = Conexion::nonQuery("SELECT * FROM historial_precio WHERE historial_precio.id_stock = $id_stock");
            if($cantidad_encontrada == 0){
                Conexion::nonQuery("DELETE FROM stock_lote WHERE stock_lote.id_stock = $id_stock");
            } else {
                $this -> cantidad = 0;
                $this -> actualizar();
            }
        }

        public static function obtener_stock_actual_por_id_producto($id_producto){
            $consultaSQL = "SELECT SUM(stock_lote.cantidad) AS 'CANTIDAD' FROM stock_lote WHERE id_stock = $id_producto GROUP BY stock_lote.id_producto" ;
            $resultado = Conexion::obtenerDatos($consultaSQL);
            if($resultado)
                $cantidad = $resultado[0]['CANTIDAD'];
            else
                $cantidad = 0;
            return $cantidad;
        }
        public static function obtener_stock_mas_viejo_por_id_producto($id_producto){
            $cantidadSQL = "SELECT * FROM stock_lote WHERE id_producto = $id_producto AND stock_lote.cantidad > 0";
            $cantidad = Conexion::nonQuery($cantidadSQL);
            if($cantidad > 0){                
                $consultaSQL = "SELECT MIN(stock_lote.id_stock) AS 'id' FROM stock_lote WHERE id_producto = $id_producto AND stock_lote.cantidad > 0";
            } else {
                $consultaSQL = "SELECT MAX(stock_lote.id_stock) AS 'id' FROM stock_lote WHERE id_producto = $id_producto";
            }
            $resultado = Conexion::obtenerDatos($consultaSQL);

            if($resultado['id']!=null){
                return self::recuperar_por_id($resultado['id']);
            } else return null;
        }
    }