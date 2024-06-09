<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php');
    require_once(DIR_PUJOL."/clases/conexion/conexion.php");
    require_once(DIR_PUJOL."/clases/conexion/respuestas_http.php");
    // Obtener la carga útil JSON
    $jsonPayload = file_get_contents('php://input');

    // Decodificar la carga útil JSON en un array asociativo

    
    if ($_GET !== null) {
        $new_get = [];
        foreach ($_GET as $key => $value) {
            $new_get[$key] = Conexion::escaparCadena($value);
        }
        $_GET = $new_get;
    }

    abstract class UtilesGet {

        public static function verificar_encabezado($string){
            return isset($_GET[$string]);
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
                return $_GET[$string];
            }
            return null;
        }
        public static function obtener($string, $mensaje=null){
            if(self::verificar_encabezado($string)){
                return $_GET[$string];
            }
            if($mensaje == null){
                RespuestasHttp::error_400();
            }
            RespuestasHttp::error_400($mensaje);
        }
    }