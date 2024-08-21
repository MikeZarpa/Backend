<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php');
    require_once (DIR_PUJOL."/clases/conexion/conexion.php");
    require_once (DIR_PUJOL."/clases/utiles/PaginableClass.php");
    require_once (DIR_PUJOL."/utiles/utilidades_get.php");
    require_once (DIR_PUJOL."/clases/utiles/FiltroClass.php");
    require_once (DIR_PUJOL."/clases/conexion/respuestas_http.php");
    require_once (DIR_PUJOL. "/clases/base_de_datos/localidad.php");


    class Direccion {
        public $id_direccion = null;
        public $calle = null;
        public $altura = null;
        public $piso = null;
        public $departamento = null;
        public $id_localidad;

        public $localidad = null;

        public function __construct($id_direccion, $calle, $altura, $piso, $departamento, $id_localidad)
        {
            $this -> id_direccion = $id_direccion;
            $this -> calle = $calle;
            $this -> altura = $altura;
            $this -> piso = $piso;
            $this -> departamento = $departamento;
            $this -> id_localidad = $id_localidad;
        }
        
        public static function recuperar_por_id($id_direccion){
            if($id_direccion == null) return null;

            $id_direccion = Conexion::escaparCadena($id_direccion);
            $consultaSQL = "SELECT direccion.id_direccion, direccion.calle, direccion.altura, direccion.piso, direccion.departamento, direccion.id_localidad FROM direccion WHERE id_direccion = $id_direccion";
            $resultado = Conexion::obtenerDatos($consultaSQL);
            if($resultado){
                $direccion = self::inicializar_desde_array($resultado[0]);
                return $direccion;
            } else return null;
        }

        public static function inicializar_desde_array($array){
            foreach (['id_direccion','calle','altura','piso','departamento'] as $value) {
                if(!isset($array[$value])){
                    $array[$value] = null;
                }
            }
            if(!isset($array['id_localidad'])){
                RespuestasHttp::error_400("Faltan la localidad en la direccion");
            }
            
            $direccion = new Direccion($array['id_direccion'], $array['calle'], $array['altura'], $array['piso'], $array['departamento'], $array['id_localidad']);
            
            $direccion -> localidad = Localidad::recuperar_por_id($array['id_localidad']);
            return $direccion;
        }

        public function save(){
            $calle = $this -> calle ?? "";
            $calle = Conexion::escaparCadena($calle);
            $altura = $this -> altura ?? "";
            $altura = Conexion::escaparCadena($altura);
            $piso = $this -> piso ?? "";
            $piso = Conexion::escaparCadena($piso);
            $departamento = $this -> departamento ?? "";
            $departamento = Conexion::escaparCadena($departamento);
            $id_localidad = Conexion::escaparCadena($this -> id_localidad);
            
            $consultaSQL = "INSERT INTO direccion(calle, altura, piso, departamento, id_localidad) VALUE ('$calle','$altura','$piso','$departamento',$id_localidad)";

            $this -> id_direccion = Conexion::nonQueryId($consultaSQL);
        }

        public function actualizar(){
            $id_direccion = Conexion::escaparCadena($this -> id_direccion);
            $calle = $this -> calle ?? "null";
            $calle = Conexion::escaparCadena($calle);
            $altura = $this -> altura ?? "null";
            $altura = Conexion::escaparCadena($altura);
            $piso = $this -> piso ?? "null";
            $piso = Conexion::escaparCadena($piso);
            $departamento = $this -> departamento ?? "null";
            $departamento = Conexion::escaparCadena($departamento);
            $id_localidad = Conexion::escaparCadena($this -> id_localidad);

            $cantidad = Conexion::nonQuery("SELECT * FROM direccion WHERE direccion.id_direccion = $id_direccion");
            if($cantidad == 1){
                $consultaActualizacion = "UPDATE direccion SET direccion.calle='$calle', direccion.altura='$altura', direccion.piso='$piso', direccion.departamento='$departamento', direccion.id_localidad=$id_localidad WHERE direccion.id_direccion = $id_direccion";
                Conexion::nonQuery($consultaActualizacion);
            } else {
                RespuestasHttp::error_404("No se encuentra el registro a actualizar.");
            }
        }
    }