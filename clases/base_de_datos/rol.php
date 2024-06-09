<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php');
    require_once(DIR_PUJOL."/clases/conexion/conexion.php");
    require_once(DIR_PUJOL."/clases/conexion/respuestas_http.php");

    class Rol {
        private $id_rol;
        private $descripcion;
        private $codigo;

        public static function obtener_roles_usuario($id_usuario){
            $resultado = Conexion::ObtenerDatos("SELECT roles.id_rol, roles.descripcion, roles.codigo FROM roles, roles_asignados WHERE roles.id_rol = roles_asignados.id_rol AND roles_asignados.id_usuario = $id_usuario");

            $roles = [];
            foreach ($resultado as $rol) {
                array_push($roles, [
                    "id_rol" => $rol["id_rol"],
                    "descripcion" => $rol["descripcion"],
                    "codigo" => $rol["codigo"],
                ]);
            }
            return $roles;
        }
    }