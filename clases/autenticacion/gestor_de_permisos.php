<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php'); //DIR_PUJOL
    require_once (DIR_PUJOL."/clases/autenticacion/token.php");
    require_once(DIR_PUJOL.'/clases/conexion/respuestas_http.php');

    class GestorDePermisos{
        private static $usuario = null;
        private static $codigo_roles_del_usuario = null;

        private static function RequerirTenerSesionIniciada(){
            if(self::$usuario != null)
                return;

            $usuario = Token::obtener_usuario_token_actual();
            self::$usuario = $usuario;
            
            $codigo_roles_del_usuario = [];
            foreach ($usuario -> roles as $rol){
                $codigo_roles_del_usuario[] = $rol -> codigo;
            }
            self::$codigo_roles_del_usuario = $codigo_roles_del_usuario;
        }

        public static function PoseeElRol($roles = []){
            self::RequerirTenerSesionIniciada();
            $bandera = false;  //Al menos 1 debe tener          
            foreach ($roles as $value) {
                if(in_array($value, self::$codigo_roles_del_usuario))
                    $bandera = true;
            }
            return $bandera;
        }
        public static function ExigirRol($roles = []){
            self::RequerirTenerSesionIniciada();
            if(!self::PoseeElRol($roles))
                RespuestasHttp::error_403();
        }

        private static function ComprobarPermisos($permiso){
            self::RequerirTenerSesionIniciada();
            throw new Exception("No implementado aún la comprobación de permisos...");
        }

        public static function obtener_id_usuario_actual(){
            self::RequerirTenerSesionIniciada();
            return self::$usuario -> id_usuario ;
        }
    }