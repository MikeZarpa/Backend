<?php
    abstract class UtilesHeader {

    public static function verificar_encabezado($headerName){
        $headers = getallheaders();
        return array_key_exists($headerName, $headers);
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

    public static function obtener_opcional($headerName){
        $headers = getallheaders();
        return $headers[$headerName] ?? null;
    }
}