<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php');
    require_once (DIR_PUJOL."/clases/conexion/conexion.php");
    require_once (DIR_PUJOL."/clases/utiles/PaginableClass.php");
    require_once (DIR_PUJOL."/utiles/utilidades_get.php");
    require_once (DIR_PUJOL."/clases/utiles/FiltroClass.php");
    require_once (DIR_PUJOL."/clases/conexion/respuestas_http.php");

    class Categoria {
        public $id_categoria = null;
        public $descripcion;

        public function __construct($id_categoria, $descripcion)
        {
            $this -> id_categoria = $id_categoria;
            $this -> descripcion = $descripcion;
        }

        public static function consultarTodos($paginar = true){

            $consultaSQL = "SELECT categoria.* FROM categoria WHERE true ";
            //$pagina = new PaginableClass($consultaSQL, $numero_de_pagina);
            
            $numero_de_pagina = UtilesGet::obtener_opcional('nroPagina');
            $filtro = new Filtro(["categoria.descripcion"]);
            $consulta_con_filtro = $consultaSQL.($filtro -> generar_condiciones());
            $consulta_con_filtro = $consulta_con_filtro." ORDER BY categoria.id_categoria DESC";
            if($paginar){
                $pagina = new PaginableClass($consulta_con_filtro, $numero_de_pagina);

                return $pagina;
            } else {
                $resultado = Conexion::obtenerDatos($consultaSQL);
                return $resultado;
            }
        }

        public static function recuperar_por_id($id_categoria){
            if($id_categoria == null) return null;
            $consultaSQL = "SELECT categoria.* FROM categoria WHERE id_categoria = $id_categoria";
            $resultado = Conexion::obtenerDatos($consultaSQL);
            if($resultado){
                $categoria = self::inicializar_desde_array($resultado[0]);
                return $categoria;
            } else return null;
        }

        public static function inicializar_desde_array($array){
            $categoria = new Categoria($array['id_categoria'], $array['descripcion']);
            return $categoria;
        }

        public function save(){
            $descripcion = Conexion::escaparCadena($this -> descripcion);
            $consultaSQL = "INSERT INTO categoria(descripcion) VALUE ('$descripcion')";
            $cantidad_registros_afectados = Conexion::nonQuery($consultaSQL);
            if($cantidad_registros_afectados < 1){
                RespuestasHttp::error_500("Error al ingresar nueva categoria");
            }
        }

        public function actualizar(){
            $descripcion = Conexion::escaparCadena($this -> descripcion);   //Por motivos de seguridad
            $id_categoria = Conexion::escaparCadena($this -> id_categoria); //Por motivos de seguridad
            $cantidad = Conexion::nonQuery("SELECT count(*) FROM categoria WHERE categoria.id_categoria = $id_categoria");
            if($cantidad == 1){
                $consultaActualizacion = "UPDATE categoria SET categoria.descripcion = '$descripcion' WHERE categoria.id_categoria = $id_categoria";
                Conexion::nonQuery($consultaActualizacion);
            } else {
                RespuestasHttp::error_404("No se encuentra el registro a actualizar.");
            }
        }
    }