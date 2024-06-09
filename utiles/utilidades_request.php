<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php');
    require_once(DIR_PUJOL."/clases/conexion/respuestas_http.php");
    // Obtener la carga útil JSON

    abstract class UtilesRequest {

        public static function es_get(){
            return $_SERVER['REQUEST_METHOD'] == "GET";
        }

        public static function es_post(){
            return $_SERVER['REQUEST_METHOD'] == "POST";
        }
        public static function es_delete(){
            return $_SERVER['REQUEST_METHOD'] == "DELETE";
        }
        public static function es_put(){
            return $_SERVER['REQUEST_METHOD'] == "PUT";
        }
        public static function es_patch(){
            return $_SERVER['REQUEST_METHOD'] == "PATCH";
        }
    }