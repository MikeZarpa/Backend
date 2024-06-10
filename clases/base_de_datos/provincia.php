<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php');
    require_once (DIR_PUJOL."/clases/conexion/conexion.php");
    require_once (DIR_PUJOL."/clases/utiles/PaginableClass.php");
    require_once (DIR_PUJOL."/utiles/utilidades_get.php");
    require_once (DIR_PUJOL."/clases/utiles/FiltroClass.php");
    require_once (DIR_PUJOL."/clases/conexion/respuestas_http.php");
    require_once (DIR_PUJOL. "/clases/base_de_datos/pais.php");


    class Provincia {
        public $id_provincia;
        public $descripcion;
        public $id_pais;
        public $pais = null;

        public function __construct($id_provincia, $descripcion, $id_pais)
        {
            $this -> id_provincia = $id_provincia;
            $this -> descripcion = $descripcion;
            $this -> id_pais = $id_pais;
        }

        public static function consultarTodos(){

            $consultaSQL = "SELECT id_provincia, descripcion, id_pais FROM provincia WHERE true ";
            //$pagina = new PaginableClass($consultaSQL, $numero_de_pagina);
            
            $numero_de_pagina = UtilesGet::obtener_opcional('nroPagina');
            $filtro = new Filtro(["provincia.descripcion"]);
            $consulta_con_filtro = $consultaSQL.($filtro -> generar_condiciones());
            
            $pagina = new PaginableClass($consulta_con_filtro, $numero_de_pagina);

            return $pagina;
        }

        public static function recuperar_por_id($id_provincia){
            $consultaSQL = "SELECT id_provincia, descripcion, id_pais FROM provincia WHERE id_provincia = $id_provincia";
            $resultado = Conexion::obtenerDatos($consultaSQL);
            if($resultado){
                $provincia = self::inicializar_desde_array($resultado[0]);
                return $provincia;
            } else return null;
        }

        public static function recuperar_por_id_pais($id_pais){
            $id_pais = Conexion::escaparCadena($id_pais);
            $consultaSQL = "SELECT id_provincia, descripcion, id_pais FROM provincia WHERE id_pais=$id_pais ";
            $resultado = Conexion::obtenerDatos($consultaSQL);
            return $resultado;
        }

        public static function inicializar_desde_array($array){
            $provincia = new Provincia($array['id_provincia'], $array['descripcion'], $array['id_pais']);
            $provincia -> pais = Pais::recuperar_por_id($array['id_pais']);
            return $provincia;
        }

        public function save(){
            $descripcion = Conexion::escaparCadena($this -> descripcion);
            $id_pais = Conexion::escaparCadena($this -> id_pais);
            $consultaSQL = "INSERT INTO provincia(descripcion, id_pais) VALUE ('$descripcion',$id_pais)";
            $cantidad_registros_afectados = Conexion::nonQuery($consultaSQL);
            if($cantidad_registros_afectados < 1){
                RespuestasHttp::error_500("Error al ingresar la nueva provincia.");
            }
        }

        public function actualizar(){
            $descripcion = Conexion::escaparCadena($this -> descripcion);   //Por motivos de seguridad
            $id_pais = Conexion::escaparCadena($this -> id_pais); //Por motivos de seguridad
            $id_provincia = Conexion::escaparCadena($this -> id_provincia); //Por motivos de seguridad

            $cantidad = Conexion::nonQuery("SELECT * FROM provincia WHERE provincia.id_provincia = $id_provincia");
            if($cantidad == 1){
                $consultaActualizacion = "UPDATE provincia SET provincia.descripcion = '$descripcion', provincia.id_pais = $id_pais WHERE provincia.id_provincia = $id_provincia";
                Conexion::nonQuery($consultaActualizacion);
            } else {
                RespuestasHttp::error_404("No se encuentra el registro a actualizar.");
            }
        }

        public function delete(){
            $id_provincia = Conexion::escaparCadena($this -> id_provincia); //Por motivos de seguridad
            $cantidad_de_localidades_asociadas = Conexion::nonQuery("SELECT * FROM localidad WHERE localidad.id_provincia = $id_provincia");

            if($cantidad_de_localidades_asociadas != 0){
                RespuestasHttp::error_400("El registro Provincia está en uso.");
            } else {
                $cantidad = Conexion::nonQuery("DELETE FROM provincia WHERE provincia.id_provincia = $id_provincia");
                if($cantidad != 1){
                    RespuestasHttp::error_404();
                }
            }

        }
    }