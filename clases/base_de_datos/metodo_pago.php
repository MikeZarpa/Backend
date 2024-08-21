<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php');
    require_once (DIR_PUJOL."/clases/conexion/conexion.php");
    require_once (DIR_PUJOL."/clases/utiles/PaginableClass.php");
    require_once (DIR_PUJOL."/utiles/utilidades_get.php");
    require_once (DIR_PUJOL."/clases/utiles/FiltroClass.php");
    require_once (DIR_PUJOL."/clases/conexion/respuestas_http.php");

    class MetodoPago {
        public $id_metpago = null;
        public $descripcion;
        public $habilitado = 1;

        public function __construct($id_metpago, $descripcion, $habilitado)
        {
            $this -> id_metpago = $id_metpago;
            $this -> descripcion = $descripcion;
            $this -> habilitado = $habilitado;
        }

        public static function consultarTodos($paginar = false){
            $consultaSQL = "SELECT metodo_pago.* FROM metodo_pago WHERE true ";
            //$pagina = new PaginableClass($consultaSQL, $numero_de_pagina);
            $resultado = Conexion::obtenerDatos($consultaSQL);
            return $resultado;
        }

        public static function recuperar_por_id($id_metpago){
            if($id_metpago == null) return null;
            $consultaSQL = "SELECT metodo_pago.* FROM metodo_pago WHERE id_metpago = $id_metpago";
            $resultado = Conexion::obtenerDatos($consultaSQL);
            if($resultado){
                $metodo_pago = self::inicializar_desde_array($resultado[0]);
                return $metodo_pago;
            } else return null;
        }

        public static function inicializar_desde_array($array){
            $metodo_pago = new MetodoPago($array['id_metpago'], $array['descripcion'], $array['habilitado']);
            return $metodo_pago;
        }

        public function alternar_habilitacion_de_la_metodo_de_pago(){
            $id_metpago = Conexion::escaparCadena($this -> id_metpago);

            //Verificamos si el usuario existe...
            if(self::recuperar_por_id($id_metpago) == null) RespuestasHttp::error_404("No se encontró el registro de metodo de pago a deshabilitar");
            
            //Efectuamos la actualización
            $habilitado = !($this -> habilitado) ? 1 : 0;
            $consultaActualizacion = "UPDATE metodo_pago SET habilitado=$habilitado WHERE id_metpago = $id_metpago;";
            Conexion::nonQuery($consultaActualizacion);
        }
    }