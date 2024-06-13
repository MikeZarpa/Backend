<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php');
require_once(DIR_PUJOL.'/clases/conexion/respuestas_http.php');

class Conexion {

    private static $server;
    private static $user;
    private static $password;
    private static $database;
    private static $port;
    private static $conexion;

    // No hay constructor en una clase estática

    //Para cargar la configuracion del archivo config
    private static function datosConexion(){
        $direccion = dirname(__FILE__);
        $jsondata = file_get_contents($direccion . "/" . "config");
        return json_decode($jsondata, true);
    }

    //Prevenimos problemas con acentos y otros signos
    private static function convertirUTF8($array){
        // array_walk_recursive($array,function(&$item,$key){
        //     if(!mb_detect_encoding($item,'utf-8',true)){
        //         $item = utf8_encode($item);
        //     }
        // });
        return $array;
    }

    //Para todas las selects
    public static function obtenerDatos($sqlstr){
        self::iniciarConexion();
        $results = self::$conexion->query($sqlstr);
        $resultArray = array();
        if($results){
            foreach ($results as $key) {
                $resultArray[] = $key;
            }
        }
        return self::convertirUTF8($resultArray);
    }

    //Para los insert, delete, update... que no esperemos datos de respuesta
    public static function nonQuery($sqlstr){
        self::iniciarConexion();
        $results = self::$conexion->query($sqlstr);
        return self::$conexion->affected_rows;
    }

    //Para casos que necesitemos el id del insert que acabamos de usar, para claves foráneas
    public static function nonQueryId($sqlstr){
        self::iniciarConexion();
        $results = self::$conexion->query($sqlstr);
        if(!$results){
            $error_message = self::$conexion->error; // Obtenemos el mensaje de error
            echo "Error en la consulta: $error_message";
        }
        
        $filas = self::$conexion->affected_rows;
        if($filas >= 1){
            return self::$conexion->insert_id;
        } else {
            return 0;
        }
    }
     
    //encriptar, para contraseñas
    protected static function encriptar($string){
        return md5($string);
    }

    // Método para inicializar la conexión, se ejecuta cada vez que usa la conexion
    private static function iniciarConexion(){
        if (!isset(self::$conexion)) {  //Pero si ya se conectó no se inicializa 2 veces
            $listadatos = self::datosConexion();
            foreach ($listadatos as $key => $value) {
                self::$server = $value['server'];
                self::$user = $value['user'];
                self::$password = $value['password'];
                self::$database = $value['database'];
                self::$port = $value['port'];
            }
            self::$conexion = new mysqli(self::$server, self::$user, self::$password, self::$database, self::$port);
            if (self::$conexion->connect_errno) {
                RespuestasHttp::error_500();
            }
        }
    }
    /*
    //Metodos para seguridad:

        // Método para preparar una consulta SQL preparada
        public static function prepararQuery($sqlstr) {
            self::iniciarConexion();
            return self::$conexion->prepare($sqlstr);
        }
    
        // Método para ejecutar una consulta SQL preparada
        public static function ejecutarQueryPreparada($stmt) {
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }

            //Para casos que necesitemos el id del insert que acabamos de usar, para claves foráneas
        public static function ejecutarNonQueryIdPreparada($stmt){
            $stmt->execute();
            $filas = self::$conexion->affected_rows;
            if($filas >= 1){
                return self::$conexion->insert_id;
            } else {
                return 0;
            }
        }*/

        public static function escaparCadena($cadena) {
            self::iniciarConexion();
            // Escapar caracteres especiales para prevenir inyección SQL
            return self::$conexion->real_escape_string($cadena);
        } 
}


?>
