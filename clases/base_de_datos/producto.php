<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php');
    require_once (DIR_PUJOL."/clases/conexion/conexion.php");
    require_once (DIR_PUJOL."/clases/utiles/PaginableClass.php");
    require_once (DIR_PUJOL."/utiles/utilidades_get.php");
    require_once (DIR_PUJOL."/clases/utiles/FiltroClass.php");
    require_once (DIR_PUJOL."/clases/conexion/respuestas_http.php");
    require_once (DIR_PUJOL. "/clases/base_de_datos/marca.php");
    require_once (DIR_PUJOL. "/clases/base_de_datos/categoria.php");
    require_once (DIR_PUJOL."/clases/base_de_datos/historial_precio.php");


    class Producto {
        public $id_producto = null;
        public $descripcion;
        public $cantidad_minima;
        public $id_marca = null;
        public $id_categoria = null;
        public $habilitado;

        public $total_cantidad = null;
        public $marca = null;
        public $categoria=null;
        public $historial_precio = null;
        public $ultimo_stock = null;

        public function __construct($id_producto, $descripcion, $cantidad_minima, $id_marca, $habilitado, $id_categoria)
        {
            $this -> id_producto = $id_producto;
            $this -> descripcion = $descripcion;
            $this -> cantidad_minima = $cantidad_minima;
            if($id_marca == null){
                $id_marca = "null";
            }
            $this -> id_marca = $id_marca;
            $this -> habilitado = $habilitado;
            $this -> id_categoria = $id_categoria;
        }

        public static function consultarTodos($paginar = true){

            $consultaSQL = "SELECT producto.*, SUM(stock_lote.cantidad) AS total_cantidad FROM producto LEFT JOIN marca ON producto.id_marca = marca.id_marca LEFT JOIN stock_lote ON stock_lote.id_producto = producto.id_producto WHERE true ";
            //$pagina = new PaginableClass($consultaSQL, $numero_de_pagina);
            
            $filtro = new Filtro(["producto.descripcion", "producto.habilitado", "marca.descripcion"]);
            $consulta_con_filtro = $consultaSQL.($filtro -> generar_condiciones());
            $consulta_con_filtro = $consulta_con_filtro." GROUP BY producto.id_producto ";
            if($paginar){
                $numero_de_pagina = UtilesGet::obtener_opcional('nroPagina');
                $consulta_con_filtro = $consulta_con_filtro ." ORDER BY producto.id_producto DESC";
                $pagina = new PaginableClass($consulta_con_filtro, $numero_de_pagina);

                foreach ($pagina -> datos as $key => $value) {
                    $pagina -> datos[$key]['marca'] = Marca::recuperar_por_id($value['id_marca']);
                    $pagina -> datos[$key]['categoria'] = Categoria::recuperar_por_id($value['id_categoria']);
                    $pagina -> datos[$key]['historial_precio'] = HistorialPrecio::recuperar_actual_por_id_producto($value['id_producto']);
                    $pagina -> datos[$key]['ultimo_stock'] = StockLote::recuperar_ultimo_stock_por_producto_id_sin_producto($value['id_producto']);
                }

                return $pagina;
            } else {
                $resultado = Conexion::obtenerDatos($consulta_con_filtro);
                $resultado_array_obj = [];
                foreach ($resultado as $value) {
                    $obj = self::inicializar_desde_array($value);
                    $obj -> total_cantidad = $value['total_cantidad'];
                    $obj -> historial_precio = HistorialPrecio::recuperar_actual_por_id_producto($value['id_producto']);
                    $obj -> ultimo_stock = StockLote::recuperar_ultimo_stock_por_producto_id_sin_producto($value['id_producto']);
                    $resultado_array_obj[] = $obj;
                }
                return $resultado_array_obj;
            }
        }

        public static function recuperar_por_id($id_producto){
            $consultaSQL = "SELECT  producto.*, SUM(stock_lote.cantidad) AS total_cantidad FROM producto LEFT JOIN stock_lote ON stock_lote.id_producto = producto.id_producto WHERE producto.id_producto = $id_producto GROUP BY producto.id_producto, producto.descripcion;";
            $resultado = Conexion::obtenerDatos($consultaSQL);
            if($resultado){
                $producto = self::inicializar_desde_array($resultado[0]);
                $producto -> historial_precio = HistorialPrecio::recuperar_actual_por_id_producto($producto -> id_producto);
                return $producto;
            } else return null;
        }
        public static function recuperar_incompleto_por_id($id_producto){
            $consultaSQL = "SELECT  producto.*, SUM(stock_lote.cantidad) AS total_cantidad FROM producto LEFT JOIN stock_lote ON stock_lote.id_producto = producto.id_producto WHERE producto.id_producto = $id_producto GROUP BY producto.id_producto, producto.descripcion;";
            $resultado = Conexion::obtenerDatos($consultaSQL);
            if($resultado){
                $producto = self::inicializar_desde_array($resultado[0]);
                return $producto;
            } else return null;
        }

        public static function inicializar_desde_array($array){
            $producto = new Producto($array['id_producto'], $array['descripcion'], $array['cantidad_minima'], $array['id_marca'], $array['habilitado'],$array['id_categoria']);
            $producto -> total_cantidad = $array['total_cantidad'] ?? null;
            $producto -> marca = Marca::recuperar_por_id($array['id_marca']);
            $producto -> categoria = Categoria::recuperar_por_id($array['id_categoria']);
            return $producto;
        }

        public function save(){
            $descripcion = Conexion::escaparCadena($this -> descripcion);
            $cantidad_minima = $this -> cantidad_minima ?? "0";
            $cantidad_minima = Conexion::escaparCadena($cantidad_minima);
            $habilitado = $this -> habilitado ?? "null";
            $habilitado = Conexion::escaparCadena($habilitado);
            $id_marca = $this -> id_marca ?? "null";
            $id_marca = Conexion::escaparCadena($id_marca);
            $id_categoria = $this -> id_categoria ?? "null";
            $id_categoria = Conexion::escaparCadena($id_categoria);

            $consultaSQL = "INSERT INTO producto(descripcion, cantidad_minima, habilitado, id_marca, id_categoria) VALUE ('$descripcion', $cantidad_minima, $habilitado, $id_marca, $id_categoria)";
            $this -> id_producto = Conexion::nonQueryId($consultaSQL);
        }

        public function actualizar(){
            $id_producto = Conexion::escaparCadena($this -> id_producto); //Por motivos de seguridad
            $descripcion = Conexion::escaparCadena($this -> descripcion);
            $cantidad_minima = $this -> cantidad_minima ?? 0;
            $cantidad_minima = Conexion::escaparCadena($cantidad_minima);
            $habilitado = $this -> habilitado ?? "null";
            $habilitado = Conexion::escaparCadena($habilitado);
            $id_marca = $this -> id_marca ?? "null";
            $id_marca = Conexion::escaparCadena($id_marca);
            $id_categoria = $this -> id_categoria ?? "null";
            $id_categoria = Conexion::escaparCadena($id_categoria);

            $cantidad = Conexion::nonQuery("SELECT * FROM producto WHERE producto.id_producto = $id_producto");
            if($cantidad == 1){
                $consultaActualizacion = "UPDATE producto SET producto.descripcion = '$descripcion', producto.cantidad_minima = $cantidad_minima, producto.habilitado=$habilitado, producto.id_marca = $id_marca, producto.id_categoria = $id_categoria WHERE producto.id_producto = $id_producto";
                Conexion::nonQuery($consultaActualizacion);
            } else {
                RespuestasHttp::error_404("No se encuentra el registro a actualizar.");
            }
        }
        public function delete(){
            $id_producto = Conexion::escaparCadena($this -> id_producto);
            if($this -> habilitado==1){
                $consultaSQL = "UPDATE producto SET producto.habilitado=0 WHERE producto.id_producto = $id_producto";
            } else {
                $consultaSQL = "UPDATE producto SET producto.habilitado=1 WHERE producto.id_producto = $id_producto";
            }
            $affected_rows =  Conexion::nonQuery($consultaSQL);
            if($affected_rows != 1){
                return RespuestasHttp::error_500("Error al intentar deshabilitar/activar el producto");
            }
        }
    }