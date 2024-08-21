<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php');
    require_once (DIR_PUJOL."/clases/conexion/conexion.php");
    require_once (DIR_PUJOL."/clases/utiles/PaginableClass.php");
    require_once (DIR_PUJOL."/utiles/utilidades_get.php");
    require_once (DIR_PUJOL."/clases/utiles/FiltroClass.php");
    require_once (DIR_PUJOL."/clases/conexion/respuestas_http.php");
    require_once (DIR_PUJOL. "/clases/base_de_datos/provincia.php");


    class Localidad {
        public $id_localidad;
        public $descripcion;
        public $codigo_postal;
        public $id_provincia;
        public $provincia = null;

        public function __construct($id_localidad, $descripcion, $codigo_postal, $id_provincia)
        {
            $this -> id_localidad = $id_localidad;
            $this -> descripcion = $descripcion;
            $this -> codigo_postal = $codigo_postal;
            $this -> id_provincia = $id_provincia;
        }

        public static function consultarTodos(){

            $consultaSQL = "SELECT id_localidad, descripcion, codigo_postal, id_provincia FROM localidad WHERE true ";
            
            $numero_de_pagina = UtilesGet::obtener_opcional('nroPagina');
            $filtro = new Filtro(["localidad.descripcion"]);
            $consulta_con_filtro = $consultaSQL.($filtro -> generar_condiciones());            
            $consulta_con_filtro = $consulta_con_filtro." ORDER BY localidad.descripcion ASC";
            $pagina = new PaginableClass($consulta_con_filtro, $numero_de_pagina);
            foreach ($pagina->datos as $key => $value) {
                $pagina -> datos[$key]['provincia'] = Provincia::recuperar_por_id($value['id_provincia']);
            }
            return $pagina;
        }

        public static function recuperar_por_id($id_localidad){
            if($id_localidad == null) return null;
            $id_localidad = Conexion::escaparCadena($id_localidad);
            $consultaSQL = "SELECT id_localidad, descripcion, codigo_postal, id_provincia FROM localidad WHERE id_localidad = $id_localidad";
            $resultado = Conexion::obtenerDatos($consultaSQL);
            if($resultado){
                $localidad = self::inicializar_desde_array($resultado[0]);
                return $localidad;
            } else return null;
        }

        public static function recuperar_por_provincia($id_provincia){
            $id_provincia = Conexion::escaparCadena($id_provincia);
            $consultaSQL = "SELECT id_localidad, descripcion, codigo_postal, id_provincia FROM localidad WHERE id_provincia=$id_provincia ";
            $consultaSQL = $consultaSQL." ORDER BY localidad.descripcion ASC";
            $resultado = Conexion::obtenerDatos($consultaSQL);
            return $resultado;
        }

        public static function inicializar_desde_array($array){
            $localidad = new Localidad($array['id_localidad'], $array['descripcion'], $array['codigo_postal'], $array['id_provincia']);
            $localidad -> provincia = Provincia::recuperar_por_id($array['id_provincia']);
            return $localidad;
        }

        public function save(){
            $descripcion = Conexion::escaparCadena($this -> descripcion);
            $codigo_postal = Conexion::escaparCadena($this -> codigo_postal);
            $id_provincia = Conexion::escaparCadena($this -> id_provincia);
            
            $consultaSQL = "INSERT INTO localidad(descripcion, codigo_postal, id_provincia) VALUE ('$descripcion','$codigo_postal',$id_provincia)";
            $cantidad_registros_afectados = Conexion::nonQuery($consultaSQL);
            if($cantidad_registros_afectados < 1){
                RespuestasHttp::error_500("Error al ingresar la nueva localidad.");
            }
        }

        public function actualizar(){
            $id_localidad = Conexion::escaparCadena($this -> id_localidad);
            $descripcion = Conexion::escaparCadena($this -> descripcion);
            $codigo_postal = Conexion::escaparCadena($this -> codigo_postal);
            $id_provincia = Conexion::escaparCadena($this -> id_provincia);

            $cantidad = Conexion::nonQuery("SELECT * FROM localidad WHERE localidad.id_localidad = $id_localidad");
            if($cantidad == 1){
                $consultaActualizacion = "UPDATE localidad SET localidad.descripcion = '$descripcion', localidad.id_provincia = $id_provincia, localidad.codigo_postal = '$codigo_postal' WHERE localidad.id_localidad = $id_localidad";
                Conexion::nonQuery($consultaActualizacion);
            } else {
                RespuestasHttp::error_404("No se encuentra el registro a actualizar.");
            }
        }

        public function delete(){
            $id_localidad = Conexion::escaparCadena($this -> id_localidad); //Por motivos de seguridad
            $cantidad_de_localidades_asociadas = Conexion::nonQuery("SELECT * FROM direccion WHERE direccion.id_localidad = $id_localidad");

            if($cantidad_de_localidades_asociadas != 0){
                RespuestasHttp::error_400("El registro de Localidad está en uso.");
            } else {
                $cantidad = Conexion::nonQuery("DELETE FROM localidad WHERE localidad.id_localidad = $id_localidad");
                if($cantidad != 1){
                    RespuestasHttp::error_404();
                }
            }

        }
    }