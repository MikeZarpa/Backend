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
        public $habilitado = 1;

        public function __construct($id_marca, $descripcion, $habilitado)
        {
            $this -> id_marca = $id_marca;
            $this -> descripcion = $descripcion;
            $this -> habilitado = $habilitado;
        }

        public static function consultarTodos($paginar = true){

            $consultaSQL = "SELECT marca.* FROM marca WHERE true ";
            $filtro = new Filtro(["marca.descripcion", "marca.habilitado"]);
            $consulta_con_filtro = $consultaSQL.($filtro -> generar_condiciones());
            $consulta_con_filtro = $consulta_con_filtro ." ORDER BY marca.id_marca DESC";

            if($paginar){
                $numero_de_pagina = UtilesGet::obtener_opcional('nroPagina');
            
                $pagina = new PaginableClass($consulta_con_filtro, $numero_de_pagina);
                return $pagina;
            } else {
                $resultado = Conexion::obtenerDatos($consultaSQL);
                return $resultado;
            }
        }

        public static function recuperar_por_id($id_marca){
            if($id_marca == null) return null;
            $consultaSQL = "SELECT marca.* FROM marca WHERE id_marca = $id_marca";
            $resultado = Conexion::obtenerDatos($consultaSQL);
            if($resultado){
                $marca = self::inicializar_desde_array($resultado[0]);
                return $marca;
            } else return null;
        }

        public static function inicializar_desde_array($array){
            $marca = new Marca($array['id_marca'], $array['descripcion'], $array['habilitado']);
            return $marca;
        }

        public function save(){
            if($this -> id_marca != null)
                RespuestasHttp::error_400("Se intenta guardar un duplicado de una marca ya existente.");
            $descripcion = Conexion::escaparCadena($this -> descripcion);
            $consultaSQL = "INSERT INTO marca(descripcion) VALUE ('$descripcion')";
            $this -> id_marca = Conexion::nonQueryId($consultaSQL);
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

        public function alternar_habilitacion_de_la_marca(){
            $id_marca = Conexion::escaparCadena($this -> id_marca);

            //Verificamos si el usuario existe...
            $consultaSQL_VerificacionExistencia = "SELECT * FROM marca WHERE id_marca = $id_marca";
            $cantidad_de_coincidencias = Conexion::nonQuery($consultaSQL_VerificacionExistencia);
            if($cantidad_de_coincidencias != 1)
                RespuestasHttp::error_404("No se encuentra el registro de la Marca a Habilitar o Deshabilitar");
            
            //Efectuamos la actualización
            $habilitado = !($this -> habilitado) ? 1 : 0;
            $consultaActualizacion = "UPDATE marca SET habilitado=$habilitado WHERE id_marca = $id_marca;";
            Conexion::nonQuery($consultaActualizacion);
        }
    }