<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php');
    require_once(DIR_PUJOL."/clases/conexion/conexion.php");
    require_once(DIR_PUJOL."/clases/conexion/respuestas_http.php");
    require_once(DIR_PUJOL."/clases/base_de_datos/rol.php");
    require_once(DIR_PUJOL."/clases/base_de_datos/usuario.php");
    require_once (DIR_PUJOL."/clases/utiles/FiltroClass.php");

    class RolesAsignados {
        public $id_rol_asignado;
        public $id_rol;
        public $id_usuario;
        public $rol;
        public $usuario;

        public function __construct($id_rol_asignado, $id_rol, $id_usuario)
        {
            $this -> id_rol_asignado = $id_rol_asignado;
            $this -> id_rol = $id_rol;
            $this -> id_usuario = $id_usuario;
        }

        public static function consultarTodos($paginar = true){
            $consultaSQL = "SELECT roles_asignados.* FROM roles_asignados INNER JOIN usuarios ON usuarios.id_usuario = roles_asignados.id_usuario INNER JOIN roles ON roles.id_rol = roles_asignados.id_rol WHERE true";
            
            if($paginar){            
                $numero_de_pagina = UtilesGet::obtener_opcional('nroPagina');
                $filtro = new Filtro(["usuarios.apellido", "usuarios.username", "roles.descripcion"]);
                $consulta_con_filtro = $consultaSQL.($filtro -> generar_condiciones());
                $pagina = new PaginableClass($consulta_con_filtro, $numero_de_pagina);

                return $pagina;
            } else {
                $resultado = Conexion::obtenerDatos($consultaSQL);
                $resultadoObj = [];
                foreach ($resultado as $value) {                    
                    $obj = self::inicializar_desde_array($value);
                    $resultadoObj[] = $obj;
                }
                return $resultadoObj;
            }
        }

        public static function recuperar_por_id($id_rol_asignado){
            if($id_rol_asignado == null) return null;

            $id_rol_asignado = Conexion::escaparCadena($id_rol_asignado);
            $consultaSQL = "SELECT * FROM roles_asignados WHERE id_rol_asignado = $id_rol_asignado";
            $resultado = Conexion::obtenerDatos($consultaSQL);
            if($resultado){
                $rol_asignado = self::inicializar_desde_array($resultado[0]);
                return $rol_asignado;
            } else return null;
        }
        public static function recuperar_por_id_usuario_y_id_rol($id_usuario, $id_rol){
            if(!self::verificar_si_el_usuario_tiene_el_rol_asignado($id_usuario, $id_rol))
                return null;

            $id_usuario = Conexion::escaparCadena($id_usuario);
            $id_rol = Conexion::escaparCadena($id_rol);

            $consultaSQL_Existencia = "SELECT * FROM roles_asignados WHERE id_rol = $id_rol AND id_usuario=$id_usuario;";
            $resultado = Conexion::obtenerDatos($consultaSQL_Existencia);

            $rol_asignado = self::inicializar_desde_array($resultado[0]);
            return $rol_asignado;
        }

        public static function inicializar_desde_array($array){
            $id_rol_asignado = $array["id_rol_asignado"];
            $id_rol = $array["id_rol"];
            $id_usuario = $array["id_usuario"];
            $rol = Rol::recuperar_por_id($id_rol);
            $usuario = Usuario::recuperar_usuario_por_id($id_usuario);
            
            $rol_asignado = new RolesAsignados($id_rol_asignado, $id_rol, $id_usuario);
            $rol_asignado -> rol = $rol;
            $rol_asignado -> usuario = $usuario;
            return $rol_asignado;
        }

        public function save(){
            $id_rol_asignado = Conexion::escaparCadena($this -> id_rol_asignado);
            $id_rol = Conexion::escaparCadena($this -> id_rol);
            $id_usuario = Conexion::escaparCadena($this -> id_usuario);
            

            if($id_rol_asignado != null)
                RespuestasHttp::error_500("Se intentó crear un duplicado de un registro ya existente");

            if(Rol::recuperar_por_id($id_rol) == null)
                RespuestasHttp::error_400("Ese rol no existe");
            if(Usuario::recuperar_usuario_por_id($id_usuario) == null)
                RespuestasHttp::error_400("Ese usuario no existe");
            if(self::verificar_si_el_usuario_tiene_el_rol_asignado($id_usuario,$id_rol)){
                RespuestasHttp::error_400("El usuario ya tiene ese rol");
            }

            $consultaSQL = "INSERT INTO roles_asignados(id_rol, id_usuario) VALUE ($id_rol, $id_usuario)";
            $this->id_rol = Conexion::nonQueryId($consultaSQL);
        }

        public function delete(){
            $id_rol_asignado = Conexion::escaparCadena($this -> id_rol_asignado);

            if($id_rol_asignado == null)
                RespuestasHttp::error_500("No se puede borrar el registro ya que no tiene identificador");

            $consultaSQL = "DELETE FROM roles_asignados WHERE id_rol_asignado = $id_rol_asignado;";
            Conexion::nonQuery($consultaSQL);
        }

        public static function verificar_si_el_usuario_tiene_el_rol_asignado($id_usuario, $id_rol){

            $id_usuario = Conexion::escaparCadena($id_usuario);
            $id_rol = Conexion::escaparCadena($id_rol);

            $consultaSQL_Existencia = "SELECT * FROM roles_asignados WHERE id_rol = $id_rol AND id_usuario=$id_usuario;";
            $cantidad_de_resultados = Conexion::nonQuery($consultaSQL_Existencia);
            return $cantidad_de_resultados > 0;
        }

        public static function agregar_roles_al_usuario($id_usuario, $array_roles_id){
            //Verificaciones
            if(Usuario::recuperar_usuario_por_id($id_usuario) == null)
                RespuestasHttp::error_400("El usuario objetivo no existe.");

            foreach ($array_roles_id as $id_rol) {
                $rol = Rol::recuperar_por_id($id_rol);
                if($rol == null)
                    RespuestasHttp::error_400("El rol que se quiere asignar no existe.");
            }

            //Concretamos la acción
            foreach ($array_roles_id as $id_rol) {
                if(!self::verificar_si_el_usuario_tiene_el_rol_asignado($id_usuario, $id_rol)){
                    $asignacion = new RolesAsignados(null, $id_rol, $id_usuario);
                    $asignacion -> save();
                }
            }
        }

        public static function remover_roles_al_usuario($id_usuario, $array_roles_id){
            //Verificaciones
            if(Usuario::recuperar_usuario_por_id($id_usuario) == null)
                RespuestasHttp::error_400("El usuario objetivo no existe.");

            foreach ($array_roles_id as $id_rol) {
                $rol = Rol::recuperar_por_id($id_rol);
                if($rol == null)
                    RespuestasHttp::error_400("El rol que se quiere remover no existe.");
            }
            
            //Concretamos la acción
            foreach ($array_roles_id as $id_rol) {
                if(self::verificar_si_el_usuario_tiene_el_rol_asignado($id_usuario, $id_rol)){
                    $asignacion = RolesAsignados::recuperar_por_id_usuario_y_id_rol($id_usuario, $id_rol);
                    $asignacion -> delete();
                }
            }
        }
    }