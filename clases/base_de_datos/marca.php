<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php');
    require_once (DIR_PUJOL."/clases/conexion/conexion.php");
    require_once (DIR_PUJOL."/clases/utiles/PaginableClass.php");
    require_once (DIR_PUJOL."/utiles/utilidades_get.php");
    require_once (DIR_PUJOL."/clases/utiles/FiltroClass.php");
    require_once (DIR_PUJOL."/clases/conexion/respuestas_http.php");

    class Marca {
        public $id_marca = null;
        public $descripcion;

        public function __construct($id_marca, $descripcion)
        {
            $this -> id_marca = $id_marca;
            $this -> descripcion = $descripcion;
        }

        public static function consultarTodos($paginar = true){

            $consultaSQL = "SELECT id_marca, descripcion FROM marca WHERE true ";
            //$pagina = new PaginableClass($consultaSQL, $numero_de_pagina);
            if($paginar){
                $numero_de_pagina = UtilesGet::obtener_opcional('nroPagina');
                $filtro = new Filtro(["marca.descripcion"]);
                $consulta_con_filtro = $consultaSQL.($filtro -> generar_condiciones());
            
                $pagina = new PaginableClass($consulta_con_filtro, $numero_de_pagina);
                return $pagina;
            } else {
                $resultado = Conexion::obtenerDatos($consultaSQL);
                return $resultado;
            }
        }

        public static function recuperar_por_id($id_marca){
            $consultaSQL = "SELECT id_marca, descripcion FROM marca WHERE id_marca = $id_marca";
            $resultado = Conexion::obtenerDatos($consultaSQL);
            if($resultado){
                $marca = self::inicializar_desde_array($resultado[0]);
                return $marca;
            } else return null;
        }

        public static function inicializar_desde_array($array){
            $marca = new Marca($array['id_marca'], $array['descripcion']);
            return $marca;
        }

        public function save(){
            $descripcion = Conexion::escaparCadena($this -> descripcion);
            $consultaSQL = "INSERT INTO marca(descripcion) VALUE ('$descripcion')";
            $cantidad_registros_afectados = Conexion::nonQuery($consultaSQL);
            if($cantidad_registros_afectados < 1){
                RespuestasHttp::error_500("Error al ingresar nueva marca");
            }
        }

        public function actualizar(){
            $descripcion = Conexion::escaparCadena($this -> descripcion);   //Por motivos de seguridad
            $id_marca = Conexion::escaparCadena($this -> id_marca); //Por motivos de seguridad
            $cantidad = Conexion::nonQuery("SELECT count(*) FROM marca WHERE marca.id_marca = $id_marca");
            if($cantidad == 1){
                $consultaActualizacion = "UPDATE marca SET marca.descripcion = '$descripcion' WHERE marca.id_marca = $id_marca";
                Conexion::nonQuery($consultaActualizacion);
            } else {
                RespuestasHttp::error_404("No se encuentra el registro a actualizar.");
            }
        }
    }