<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php');
    require_once (DIR_PUJOL."/clases/conexion/conexion.php");
    require_once (DIR_PUJOL."/clases/utiles/PaginableClass.php");
    require_once (DIR_PUJOL."/utiles/utilidades_get.php");
    require_once (DIR_PUJOL."/clases/utiles/FiltroClass.php");
    require_once (DIR_PUJOL."/clases/conexion/respuestas_http.php");

    class Pais {
        public $id_pais = null;
        public $descripcion;

        public function __construct($id_pais, $descripcion)
        {
            $this -> id_pais = $id_pais;
            $this -> descripcion = $descripcion;
        }

        public static function consultarTodos(){

            $consultaSQL = "SELECT id_pais, descripcion FROM pais WHERE true ";
            //$pagina = new PaginableClass($consultaSQL, $numero_de_pagina);
            
            /*
            $numero_de_pagina = UtilesGet::obtener_opcional('nroPagina');
            $filtro = new Filtro(["pais.descripcion"]);
            $consulta_con_filtro = $consultaSQL.($filtro -> generar_condiciones());
            $pagina = new PaginableClass($consulta_con_filtro, $numero_de_pagina);
            return $pagina;*/
            return Conexion::obtenerDatos($consultaSQL);

        }

        public static function recuperar_por_id($id_pais){
            if($id_pais == null) return null;
            
            $consultaSQL = "SELECT id_pais, descripcion FROM pais WHERE id_pais = $id_pais";
            $resultado = Conexion::obtenerDatos($consultaSQL);
            if($resultado){
                $pais = self::inicializar_desde_array($resultado[0]);
                return $pais;
            } else return null;
        }

        public static function inicializar_desde_array($array){
            $pais = new Pais($array['id_pais'], $array['descripcion']);
            return $pais;
        }

        public function save(){
            $descripcion = Conexion::escaparCadena($this -> descripcion);
            $consultaSQL = "INSERT INTO pais(descripcion) VALUE ('$descripcion')";
            $cantidad_registros_afectados = Conexion::nonQuery($consultaSQL);
            if($cantidad_registros_afectados < 1){
                RespuestasHttp::error_500("Error al ingresar el nuevo pais.");
            }
        }

        public function actualizar(){
            $descripcion = Conexion::escaparCadena($this -> descripcion);   //Por motivos de seguridad
            $id_pais = Conexion::escaparCadena($this -> id_pais); //Por motivos de seguridad
            $cantidad = Conexion::nonQuery("SELECT count(*) FROM pais WHERE pais.id_pais = $id_pais");
            if($cantidad == 1){
                $consultaActualizacion = "UPDATE pais SET pais.descripcion = '$descripcion' WHERE pais.id_pais = $id_pais";
                Conexion::nonQuery($consultaActualizacion);
            } else {
                RespuestasHttp::error_404("No se encuentra el registro a actualizar.");
            }
        }
    }