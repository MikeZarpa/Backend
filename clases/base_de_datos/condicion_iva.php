<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php');
    require_once (DIR_PUJOL."/clases/conexion/conexion.php");
    require_once (DIR_PUJOL."/clases/utiles/PaginableClass.php");
    require_once (DIR_PUJOL."/utiles/utilidades_get.php");
    require_once (DIR_PUJOL."/clases/utiles/FiltroClass.php");
    require_once (DIR_PUJOL."/clases/conexion/respuestas_http.php");

    class CondicionIva {
        public $id_cond_iva = null;
        public $descripcion;

        public function __construct($id_cond_iva, $descripcion)
        {
            $this -> id_cond_iva = $id_cond_iva;
            $this -> descripcion = $descripcion;
        }

        public static function consultarTodos(){

            $consultaSQL = "SELECT id_cond_iva, descripcion FROM condicion_iva WHERE true ";
            //$pagina = new PaginableClass($consultaSQL, $numero_de_pagina);
            
            $numero_de_pagina = UtilesGet::obtener_opcional('nroPagina');
            $filtro = new Filtro([]);
            $consulta_con_filtro = $consultaSQL.($filtro -> generar_condiciones());
            
            $pagina = new PaginableClass($consulta_con_filtro, $numero_de_pagina);

            return $pagina;
        }

        public static function recuperar_por_id($id_cond_iva){
            $consultaSQL = "SELECT id_cond_iva, descripcion FROM condicion_iva WHERE id_cond_iva = $id_cond_iva";
            $resultado = Conexion::obtenerDatos($consultaSQL);
            if($resultado){
                $cond_iva = self::inicializar_desde_array($resultado[0]);
                return $cond_iva;
            } else return null;
        }

        public static function inicializar_desde_array($array){
            $cond_iva = new CondicionIva($array['id_cond_iva'], $array['descripcion']);
            return $cond_iva;
        }

        public function actualizar(){
            $descripcion = Conexion::escaparCadena($this -> descripcion);   //Por motivos de seguridad
            $id_cond_iva = Conexion::escaparCadena($this -> id_cond_iva); //Por motivos de seguridad
            $cantidad = Conexion::nonQuery("SELECT * FROM condicion_iva WHERE condicion_iva.id_cond_iva = $id_cond_iva");
            if($cantidad == 1){
                $consultaActualizacion = "UPDATE condicion_iva SET condicion_iva.descripcion = '$descripcion' WHERE condicion_iva.id_cond_iva = $id_cond_iva";
                Conexion::nonQuery($consultaActualizacion);
            } else {
                RespuestasHttp::error_404("No se encuentra el registro de condicion de iva a actualizar.");
            }
        }
    }