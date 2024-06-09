<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php');
    require_once(DIR_PUJOL."/clases/conexion/conexion.php");
    require_once(DIR_PUJOL."/clases/conexion/respuestas_http.php");
    require_once(DIR_PUJOL."/clases/base_de_datos/rol.php");
    require_once(DIR_PUJOL."/clases/autenticacion/token.php");
    require_once(DIR_PUJOL."/clases/utiles/PaginableClass.php");
    require_once(DIR_PUJOL."/clases/utiles/FiltroClass.php");

    
    class Usuario {
        public $id_usuario;
        public $username;
        public $email;
        public $password; //se oculta
        public $nombre;
        public $apellido;
        public $palabra_secreta;//se oculta
        public $habilitado;
        public $roles;

        public static function ver_usuarios($nro_pagina = 3, $cantidad = 1){
            $consulta = "SELECT * FROM usuarios WHERE true ";
            $filtro = new Filtro(["usuarios.nombre"]);
            $consulta_con_filtro = $consulta . $filtro ->generar_condiciones();
            $pagina = new PaginableClass($consulta_con_filtro, $nro_pagina, $cantidad);
            
            echo json_encode($pagina);
            exit();
        }

        public function __construct($username, $email, $password) {
            $this -> username = $username;
            $this -> email = $email;
            $this -> password = $password; 
        }

        public function iniciar_sesion(){
            self::ver_usuarios();
            $resultado =  Conexion::obtenerDatos("SELECT * FROM usuarios WHERE (username = '{$this -> username}' OR email = '{$this -> email}') AND password = '{$this -> password}'");
            
            if (count($resultado) > 0) {
                $primerResultado = $resultado[0];
                $this -> inicializar_desde_fila($primerResultado);
                Token::crear_token($primerResultado["id_usuario"]);
            } else {
                RespuestasHttp::error_404("Usuario no existe.");
            }
        }
        

        public function inicializar_desde_fila($registro_fila){
            $this -> id_usuario = $registro_fila["id_usuario"];
            $this -> username = $registro_fila["username"];
            $this -> email = $registro_fila["email"];
            $this -> password = $registro_fila["password"];
            $this -> nombre = $registro_fila["nombre"];
            $this -> apellido = $registro_fila["apellido"];
            $this -> palabra_secreta = $registro_fila["palabra_secreta"];
            $this -> habilitado = $registro_fila["habilitado"];
            $this -> roles = Rol::obtener_roles_usuario($registro_fila["id_usuario"]);
        }

        public static function recuperar_usuario_por_id($id_usuario){
            $resultado = Conexion::obtenerDatos("SELECT * FROM usuarios WHERE id_usuario='$id_usuario'");
            if(count($resultado) > 0){
                $usuario = new Usuario(null, null, null);
                $usuario -> inicializar_desde_fila($resultado[0]);
                return $usuario;
            } else return null;            
        }        
    }