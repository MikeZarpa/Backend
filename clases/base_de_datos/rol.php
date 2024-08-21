<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php');
    require_once(DIR_PUJOL."/clases/conexion/conexion.php");
    require_once(DIR_PUJOL."/clases/conexion/respuestas_http.php");
    require_once (DIR_PUJOL."/clases/utiles/FiltroClass.php");
    
    class Rol {
        public $id_rol;
        public $descripcion;
        public $codigo;

        public function __construct($id_rol, $descripcion, $codigo)
        {
            $this -> id_rol = $id_rol;
            $this -> descripcion = $descripcion;
            $this -> codigo = $codigo;
        }

        public static function consultarTodos($paginar = true){
            $consultaSQL = "SELECT * FROM roles WHERE true ";
            //$pagina = new PaginableClass($consultaSQL, $numero_de_pagina);
            if($paginar){            
                $numero_de_pagina = UtilesGet::obtener_opcional('nroPagina');
                $filtro = new Filtro(["descripcion", "codigo"]);
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

        public static function obtener_roles_usuario($id_usuario){
            $resultado = Conexion::ObtenerDatos("SELECT roles.* FROM roles, roles_asignados WHERE roles.id_rol = roles_asignados.id_rol AND roles_asignados.id_usuario = $id_usuario");

            $roles = [];
            foreach ($resultado as $rol) {
                $obj_rol = self::inicializar_desde_array($rol);
                array_push($roles, $obj_rol);
            }
            return $roles;
        }

        public static function recuperar_por_id($id_rol){
            if($id_rol == null) return null;

            $id_rol = Conexion::escaparCadena($id_rol);
            $consultaSQL = "SELECT * FROM roles WHERE id_rol = $id_rol";
            $resultado = Conexion::obtenerDatos($consultaSQL);
            if($resultado){
                $cliente = self::inicializar_desde_array($resultado[0]);
                return $cliente;
            } else return null;
        }

        public static function inicializar_desde_array($array){
            $id_rol = $array["id_rol"];
            $descripcion = $array["descripcion"];
            $codigo = $array["codigo"];

            $rol = new Rol($id_rol, $descripcion, $codigo);

            return $rol;
        }

        public function save(){
            $id_rol = $this -> id_rol;
            if($id_rol != null)
                RespuestasHttp::error_500("Se intentó crear un duplicado de un registro ya existente");
            $descripcion = Conexion::escaparCadena( $this -> descripcion );
            $codigo = Conexion::escaparCadena($this -> codigo);

            //Verificamos existencia
            $consultaSQL_Existencia = "SELECT * FROM roles WHERE codigo = $codigo";
            $cantidad_de_resultados = Conexion::nonQuery($consultaSQL_Existencia);
            if($cantidad_de_resultados!=0)
                RespuestasHttp::error_400("ya existe rol con ese código");
            

            $consultaSQL = "INSERT INTO roles(descripcion, codigo) VALUE ('$descripcion', '$codigo')";

            $this->id_rol = Conexion::nonQueryId($consultaSQL);
        }

        public function actualizar(){
            if($this -> id_rol == null)
                RespuestasHttp::error_500("Fallo al intentar");

            $id_rol = Conexion::escaparCadena($this->id_rol);
            $descripcion = Conexion::escaparCadena($this->descripcion);

            $cantidad = Conexion::nonQuery("SELECT * FROM roles WHERE id_rol = $id_rol");
            if($cantidad == 1){
                $consultaActualizacion = "UPDATE roles SET descripcion='$descripcion' WHERE id_rol = $id_rol";
                Conexion::nonQuery($consultaActualizacion);
            } else {
                RespuestasHttp::error_404("No se encuentra el registro a actualizar.");
            }
        }
    }