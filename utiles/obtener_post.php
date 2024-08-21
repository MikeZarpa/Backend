<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php');
    require_once(DIR_PUJOL."/clases/conexion/conexion.php");
    require_once(DIR_PUJOL."/clases/conexion/respuestas_http.php");
    // Obtener la carga útil JSON
    $jsonPayload = file_get_contents('php://input');

    // Decodificar la carga útil JSON en un array asociativo

    
    $data = json_decode($jsonPayload, true);
    // Iterar sobre los datos decodificados y agregarlos a $_POST
    // Verificar si la decodificación fue exitosa y $data no es null
    if ($data !== null) {
        foreach ($data as $key => $value) {
            if (gettype($value)!="array" && gettype($value)!= "integer"&& gettype($value)!= "double") {
                $_POST[$key] = Conexion::escaparCadena($value);
            } else {
                $_POST[$key] = $value;
            }
        }
    }

    abstract class UtilesPost {

        public static function verificar_encabezado($string){
            return isset($_POST[$string]);
        }

        public static function verificar_encabezadoLista($array){
            $bandera = true;
            foreach ($array as $string) {
                if(!self::verificar_encabezado($string)){
                    $bandera = false;
                }
            }
            return $bandera;
        }

        public static function obtener_opcional($string){
            if(self::verificar_encabezado($string)){
                return $_POST[$string];
            }
            return null;
        }
        public static function obtener($string, $mensaje=null){
            if(self::verificar_encabezado($string)){
                return $_POST[$string];
            }
            if($mensaje == null){
                RespuestasHttp::error_400();
            }
            RespuestasHttp::error_400($mensaje);

        }
    }