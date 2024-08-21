<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php');
    require_once(DIR_PUJOL.'/utiles/obtener_post.php');
    require_once(DIR_PUJOL."/clases/conexion/conexion.php");
    require_once(DIR_PUJOL.'/utiles/utilidades_get.php');
    require_once(DIR_PUJOL.'/utiles/utilidades_header.php');

    class TipoDeComparacion {
        const CONTIENE = '1';
        const LITERAL = '2';
        const DESDE = '3';
        const HASTA = '4';
        const DESDE_Y_HASTA = '5';
        const POR_ID_CAMPO = '6';
        const POR_NULIDAD='7';
        const MAYOR_QUE='8';

        public static function getAllTypes() {
            return [
                self::CONTIENE,
                self::LITERAL,
                self::DESDE,
                self::HASTA,
                self::DESDE_Y_HASTA,
                self::POR_ID_CAMPO,
                self::POR_NULIDAD,
                self::MAYOR_QUE,
            ];
        }
    }
    class Filtro {
        public $enabled = false;
        public $filters = [];
        public $campos = [];

        public function __construct($array_campos){
            $this -> campos = $array_campos;
            if(self::hay_filtros()){
                $filtro_json = UtilesGet::obtener_opcional("filtro");
                if($filtro_json == null){
                    $filtro_json = UtilesPost::obtener_opcional("filtro");
                }
                if($filtro_json == null){
                    $filtro_json =  UtilesHeader::obtener_opcional("filtro");
                }
                // Eliminar las barras invertidas que pudo haberle agregado el "Escapar cadena" del servidor
                $filtro_json_sin_barras = str_replace("\\", "", $filtro_json);
                $filtro = json_decode($filtro_json_sin_barras);
                $this -> enabled = $filtro -> enabled;
                foreach ($filtro -> filters as $array_asoc_filtro) {
                    $this -> filters[] = new FiltroDetalle($array_asoc_filtro);
                }
            }   
        }

        public function generar_condiciones(){
            if(!($this -> enabled)){
                return "";
            }
            $condicion = "";
            foreach ($this -> filters as $filtroDetalle) {
                $condicion = $condicion.($filtroDetalle -> crear_condicion_filtro($this -> campos));
            }
            return $condicion;
        }

        public static function hay_filtros(){
            return UtilesPost::verificar_encabezado("filtro") || UtilesGet::verificar_encabezado("filtro") || UtilesHeader::verificar_encabezado("filtro");
        }
        

        public function agregar_filtro(FiltroDetalle $filter){
            $this -> filters[] = $filter;
        }
        public static function quitar_todo_filtro(){
            if(isset($_GET["filtro"]))
                unset($_GET["filtro"]);
            if(isset($_POST["filtro"]))
                unset($_POST["filtro"]);
        }
    }
    class FiltroDetalle {
        public $enabled;
        public $campo; //Numero
        public $terminosDeBusqueda = [""];
        public $tipoBusqueda;

        public function __construct($array_asoc_filtro){
            if (is_object($array_asoc_filtro)) {
                $array_asoc_filtro = (array) $array_asoc_filtro;
            }

            $this -> enabled = $array_asoc_filtro['enabled'];
            $this -> campo = $array_asoc_filtro['campo'];
            $this -> terminosDeBusqueda = $array_asoc_filtro['terminosDeBusqueda'];
            $this -> tipoBusqueda = $array_asoc_filtro['tipoBusqueda'];
        }

        public function crear_condicion_filtro($array_campos){
            if(!($this -> enabled) || $this -> campo >= count($array_campos)){
                return '';
            }
            $campo_a_usar = $array_campos[($this -> campo)];

            $terminoBusqueda1 = Conexion::escaparCadena($this -> terminosDeBusqueda[0]);
            switch ($this -> tipoBusqueda) {
                case TipoDeComparacion::CONTIENE:
                    return " AND $campo_a_usar LIKE '%$terminoBusqueda1%' ";
                    break;
                case TipoDeComparacion::LITERAL:
                    return " AND $campo_a_usar = '$terminoBusqueda1' ";
                    break;
                case TipoDeComparacion::DESDE_Y_HASTA:
                    $terminoBusqueda2 = Conexion::escaparCadena($this -> terminosDeBusqueda[1]);
                    return " AND $campo_a_usar BETWEEN DATE('$terminoBusqueda1') AND DATE('$terminoBusqueda2')+1 ";
                    break;
                case TipoDeComparacion::POR_NULIDAD:
                    $not_opcional = $terminoBusqueda1=='1' ? "NOT" : "";
                    return " AND $campo_a_usar IS $not_opcional NULL ";
                    break;
                case TipoDeComparacion::MAYOR_QUE:
                    return " AND $campo_a_usar > $terminoBusqueda1 ";
                    break;
                default:
                    return 'No definido';
                    break;
            }

        }
    }

    