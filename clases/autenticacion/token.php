<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php');
    require_once(DIR_PUJOL."/clases/conexion/conexion.php");
    require_once(DIR_PUJOL.'/clases/conexion/respuestas_http.php');


    class Token{

        public static function crear_token($id_usuario){
            $val = true;
            $token = bin2hex(openssl_random_pseudo_bytes(16,$val));
            Conexion::nonQuery("UPDATE tokens_de_sesion SET habilitado=0 WHERE id_usuario='$id_usuario' AND habilitado=1");
            Conexion::nonQuery("INSERT INTO tokens_de_sesion (id_usuario,token)VALUES('$id_usuario','$token')");
            header("Authorization: Bearer $token");
        }

        public static function obtener_usuario_token(){
            $token = self::obtener_token();
            $resultado = Conexion::obtenerDatos("SELECT id_usuario FROM tokens_de_sesion WHERE token='$token' AND habilitado=1");
            if(count($resultado)>0){
                $id_usuario = $resultado[0];
                $usuario = Usuario::recuperar_usuario_por_id($id_usuario);
                if($usuario == null){
                    RespuestasHttp::error_401();
                } else return $usuario;
            }
        }

        public static function obtener_token(){
            // Obtener todos los encabezados de la solicitud
            $headers = getallheaders();

            // Verificar si existe el encabezado de autorización
            if (isset($headers['Authorization'])) {
                // Obtener el valor del encabezado de autorización
                $authorizationHeader = $headers['Authorization'];

                // Verificar si el encabezado tiene el formato correcto "Bearer token"
                if (preg_match('/Bearer\s+(.*)/', $authorizationHeader, $matches)) {
                    $token = $matches[1]; // Extraer el token de la expresión regular
                    return $token;
                } else {
                    RespuestasHttp::error_403("Error de Token.");
                }
            } else {
                RespuestasHttp::error_401("Falta inicio de sesión.");
            }
        }
    }