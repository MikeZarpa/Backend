<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php');
    require_once (DIR_PUJOL."/clases/conexion/conexion.php");
    require_once (DIR_PUJOL."/clases/utiles/PaginableClass.php");
    require_once (DIR_PUJOL."/utiles/utilidades_get.php");
    require_once (DIR_PUJOL."/clases/utiles/FiltroClass.php");
    require_once (DIR_PUJOL."/clases/conexion/respuestas_http.php");

    class Producto {
        public $id_producto = null;
        public $descripcion;

        public function __construct($id_producto, $descripcion)
        {
            $this -> id_producto = $id_producto;
            $this -> descripcion = $descripcion;
        }

        public static function consultarTodos(){

            $consultaSQL = "SELECT id_producto, descripcion FROM producto WHERE true ";
            //$pagina = new PaginableClass($consultaSQL, $numero_de_pagina);
            
            $numero_de_pagina = UtilesGet::obtener_opcional('nroPagina');
            $filtro = new Filtro(["producto.descripcion"]);
            $consulta_con_filtro = $consultaSQL.($filtro -> generar_condiciones());
            
            $pagina = new PaginableClass($consulta_con_filtro, $numero_de_pagina);

            return $pagina;
        }

        public static function recuperar_por_id($id_producto){
            $consultaSQL = "SELECT id_producto, descripcion FROM producto WHERE id_producto = $id_producto";
            $resultado = Conexion::obtenerDatos($consultaSQL);
            if($resultado){
                $producto = self::inicializar_desde_array($resultado[0]);
                return $producto;
            } else return null;
        }

        public static function inicializar_desde_array($array){
            $producto = new Producto($array['id_producto'], $array['descripcion']);
            return $producto;
        }

        public function save(){
            $descripcion = Conexion::escaparCadena($this -> descripcion);
            $consultaSQL = "INSERT INTO producto(descripcion) VALUE ('$descripcion')";
            $cantidad_registros_afectados = Conexion::nonQuery($consultaSQL);
            if($cantidad_registros_afectados < 1){
                RespuestasHttp::error_500("Error al ingresar nueva producto");
            }
        }

        public function actualizar(){
            $descripcion = Conexion::escaparCadena($this -> descripcion);   //Por motivos de seguridad
            $id_producto = Conexion::escaparCadena($this -> id_producto); //Por motivos de seguridad
            $cantidad = Conexion::nonQuery("SELECT count(*) FROM producto WHERE producto.id_producto = $id_producto");
            if($cantidad == 1){
                $consultaActualizacion = "UPDATE producto SET producto.descripcion = '$descripcion' WHERE producto.id_producto = $id_producto";
                Conexion::nonQuery($consultaActualizacion);
            } else {
                RespuestasHttp::error_404("No se encuentra el registro a actualizar.");
            }
        }
    }