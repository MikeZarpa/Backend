<?php 

class RespuestasHttp {

    public static $response = [
        'status' => "ok",
        "result" => array()
    ];

    public static function respuesta_200($contenido=""){
        http_response_code(200); //Podria no estar
        header('Content-Type: application/json');
        self::$response['result'] = $contenido;
        echo json_encode(self::$response);
        die();
    }

    public static function error_400($valor = "Datos enviados incompletos o con formato incorrecto") {
        http_response_code(400); //Bad request... 
        header('Content-Type: application/json');
        self::$response['status'] = "error";
        self::$response['result'] = array(
            "error_id" => "400",
            "error_msg" => $valor
        );
        echo json_encode(self::$response);
        die();
    }
    public static function error_401($valor = "Token inválido o caducado") {
        http_response_code(401);    //No autorizado
        header('Content-Type: application/json');
        self::$response['status'] = "error";
        self::$response['result'] = array(
            "error_id" => "401",
            "error_msg" => $valor
        );
        echo json_encode(self::$response);
        die();
    }

    public static function error_403($valor = "No tiene los permisos adecuados para realizar esta operación") {
        http_response_code(403);    //Prohibido o restringido
        header('Content-Type: application/json');
        self::$response['status'] = "error";
        self::$response['result'] = array(
            "error_id" => "403",
            "error_msg" => $valor
        );
        echo json_encode(self::$response);
        die();
    }

    public static function error_404($valor = "No encontrado") {
        http_response_code(404);    //No found
        header('Content-Type: application/json');
        self::$response['status'] = "error";
        self::$response['result'] = array(
            "error_id" => "404",
            "error_msg" => $valor
        );
        echo json_encode(self::$response);
        die();
    }

    public static function error_405($valor = "Metodo no permitido") {
        http_response_code(405);    //Método no permitido - GET, POST, DELETE...
        header('Content-Type: application/json');
        self::$response['status'] = "error";
        self::$response['result'] = array(
            "error_id" => "405",
            "error_msg" => $valor
        );
        echo json_encode(self::$response);
        die();
    }

    public static function error_406($valor = "Token inválido, expirado u otro error fatal.") {
        header('Content-Type: application/json');
        http_response_code(406);    //Método no permitido - GET, POST, DELETE...
        self::$response['status'] = "error";
        self::$response['result'] = array(
            "error_id" => "406",
            "error_msg" => $valor
        );
        echo json_encode(self::$response);
        die();
    } 


    public static function error_500($valor = "Error interno del servidor") {
        header('Content-Type: application/json');
        http_response_code(500);
        self::$response['status'] = "error";
        self::$response['result'] = array(
            "error_id" => "500",
            "error_msg" => $valor
        );
        echo json_encode(self::$response);
        die();
    }
}
