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

        public static function recuperar_todos($paginar=true){

            $consultaSQL = "SELECT stock_lote.* FROM stock_lote, producto WHERE stock_lote.id_producto = producto.id_producto ";
            //$pagina = new PaginableClass($consultaSQL, $numero_de_pagina);
            $filtro = new Filtro(["producto.habilitado","stock_lote.fecha_vto", "stock_lote.cantidad"]);

            $consulta_con_filtro = $consultaSQL.($filtro -> generar_condiciones());

            //Agregamos un order by
            $consulta_con_filtro = $consulta_con_filtro." ORDER BY stock_lote.id_stock DESC";

            if($paginar){
                $numero_de_pagina = UtilesGet::obtener_opcional('nroPagina');
                
                $pagina = new PaginableClass($consulta_con_filtro, $numero_de_pagina);

                return $pagina;
            } else {
                $resultadosConsulta = Conexion::obtenerDatos($consulta_con_filtro);
                $arreglo_obj_stock = [];

                foreach ($resultadosConsulta as $registro) {
                    $obj_stock = self::inicializar_desde_array($registro);
                    $arreglo_obj_stock[] = $obj_stock;
                }
                return $arreglo_obj_stock;
            }
        }

        public static function recuperar_todos_por_id_producto($id_producto){
            $id_producto = Conexion::escaparCadena($id_producto);

            $consultaSQL = "SELECT stock_lote.id_stock, stock_lote.id_producto, stock_lote.cantidad, stock_lote.coste, stock_lote.fecha_vto FROM stock_lote, producto WHERE stock_lote.id_producto = producto.id_producto AND producto.id_producto = $id_producto ";
            //$pagina = new PaginableClass($consultaSQL, $numero_de_pagina);
            
            $numero_de_pagina = UtilesGet::obtener_opcional('nroPagina');
            $filtro = new Filtro(["producto.habilitado","stock_lote.fecha_vto", "stock_lote.cantidad"]);
            $consulta_con_filtro = $consultaSQL.($filtro -> generar_condiciones());
            $consulta_con_filtro = $consulta_con_filtro." ORDER BY stock_lote.id_stock DESC";
            
            $pagina = new PaginableClass($consulta_con_filtro, $numero_de_pagina);
            
            return $pagina;
        }
        
        public static function recuperar_por_id($id_stock){
            if($id_stock == null) return null;

            $consultaSQL = "SELECT stock_lote.id_stock, stock_lote.id_producto, stock_lote.cantidad, stock_lote.coste, stock_lote.fecha_vto FROM stock_lote WHERE id_stock = $id_stock";
            $resultado = Conexion::obtenerDatos($consultaSQL);
            if($resultado){
                $stock = self::inicializar_desde_array($resultado[0]);
                $stock -> producto = Producto::recuperar_incompleto_por_id($stock -> id_producto);
                return $stock;
            } else return null;
        }

        public static function recuperar_ultimo_stock_por_producto_id_sin_producto($id_producto){
            if($id_producto == null) return null;
            $consultaSQL = "SELECT stock_lote.id_stock, stock_lote.id_producto, stock_lote.cantidad, stock_lote.coste, stock_lote.fecha_vto FROM stock_lote WHERE id_stock = (SELECT MAX(stock_lote.id_stock) FROM stock_lote WHERE stock_lote.id_producto = $id_producto);";
            $resultado = Conexion::obtenerDatos($consultaSQL);
            if($resultado){
                $stock = self::inicializar_desde_array($resultado[0]);
                $stock -> producto = Producto::recuperar_incompleto_por_id($stock -> id_producto);
                return $stock;
            } else return null;
        }

        public static function recuperar_sin_producto_por_id($id_stock){
            $consultaSQL = "SELECT stock_lote.id_stock, stock_lote.id_producto, stock_lote.cantidad, stock_lote.coste, stock_lote.fecha_vto FROM stock_lote WHERE id_stock = $id_stock";
            $resultado = Conexion::obtenerDatos($consultaSQL);
            if($resultado){
                $stock = self::inicializar_desde_array($resultado[0]);                
                return $stock;
            } else return null;
        }

        public static function inicializar_desde_array($array){
            $stock = new StockLote($array['id_stock'], $array['id_producto'],$array['cantidad'],$array['coste'],$array['fecha_vto']);
            return $stock;
        }
        
        public function actualizar(){
            $id_stock = Conexion::escaparCadena($this -> id_stock);
            $id_producto = Conexion::escaparCadena($this -> id_producto);
            $cantidad = (float) Conexion::escaparCadena($this -> cantidad);
            $coste =(float) Conexion::escaparCadena($this -> coste);
            $fecha_vto = $this -> fecha_vto;
            $fecha_vto = $fecha_vto ? "Date('$fecha_vto')": "null";
            $cantidad_encontrada = Conexion::nonQuery("SELECT * FROM stock_lote WHERE stock_lote.id_stock = $id_stock");
            if($cantidad_encontrada == 1){
                $consultaActualizacion = "UPDATE stock_lote SET stock_lote.id_producto = $id_producto, stock_lote.cantidad = $cantidad, stock_lote.coste = $coste, stock_lote.fecha_vto = $fecha_vto WHERE stock_lote.id_stock = $id_stock";
                Conexion::nonQuery($consultaActualizacion);
            } else {
                RespuestasHttp::error_404("No se encuentra el registro a actualizar.");
            }
        }

        public function delete(){
            $id_stock = Conexion::escaparCadena($this -> id_stock);
            //Verificamos que existe;
            $existenciaQuerry = "SELECT * FROM stock_lote WHERE stock_lote.id_stock = $id_stock";
            $existe = Conexion::nonQuery($existenciaQuerry) == 1;
            if(!$existe)
                RespuestasHttp::error_404("No se encuentra el registro de Stock a vaciar");
            //Reducimos la cantidad a 0;
            $this -> cantidad = 0;
            $this -> actualizar();
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
                //Si estan todos vacios, tomamos el último
                $consultaSQL = "SELECT MAX(stock_lote.id_stock) AS 'id' FROM stock_lote WHERE id_producto = $id_producto";
            }
            $resultado = Conexion::obtenerDatos($consultaSQL);
            if(count($resultado)>0){
                if($resultado[0]['id']!=null){
                    return self::recuperar_por_id($resultado[0]['id']);
                }
            } else return null;
        }
        public function save(){
            if($this -> id_stock != null)
                RespuestasHttp::error_500("Se intentó crear una copia de un registro ya existente.");

            $id_producto = Conexion::escaparCadena($this->id_producto);
            $cantidad = Conexion::escaparCadena($this->cantidad);
            $coste = Conexion::escaparCadena($this->coste);
            str_replace(",",".",$coste);
            $fecha_vto = Conexion::escaparCadena($this->fecha_vto);
            $fecha_vto = $fecha_vto ? "Date('$fecha_vto')": "null";

            $consultaSQL = "INSERT INTO stock_lote(id_producto, cantidad, coste, fecha_vto) VALUE ($id_producto, $cantidad, $coste, $fecha_vto);";

            $this -> id_stock = Conexion::nonQueryId($consultaSQL);
        }
        public function reducir_el_stock_y_obtener_ingresado($cantidad){
            if($this -> id_stock == null) RespuestasHttp::error_500("Se utilizó incorrectamente la reducción de stock");
            $cantidad_de_este_stock = $this -> cantidad;
            if(!($cantidad > 0))
                return 0;
            $cantidad_a_reducir  = min($cantidad, $cantidad_de_este_stock);
            //Reducimos la cantidad de este stock
            $this -> cantidad = $cantidad_de_este_stock - $cantidad_a_reducir;
            $this -> actualizar();
            //Devolvemos lo que falta reducir;
            return $cantidad_a_reducir;
        }
    }