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

        // public static function ver_usuarios($nro_pagina = 3, $cantidad = 1){
        //     $consulta = "SELECT * FROM usuarios WHERE true ";
        //     $filtro = new Filtro(["usuarios.nombre"]);
        //     $consulta_con_filtro = $consulta . $filtro ->generar_condiciones();
        //     $pagina = new PaginableClass($consulta_con_filtro, $nro_pagina, $cantidad);
            
        //     echo json_encode($pagina);
        //     exit();
        // }

        public static function consultarTodos($paginar = true){
            $consultaSQL = "SELECT * FROM usuarios WHERE true ";
            //$pagina = new PaginableClass($consultaSQL, $numero_de_pagina);
            if($paginar){            
                $numero_de_pagina = UtilesGet::obtener_opcional('nroPagina');
                $filtro = new Filtro(["usuarios.nombre", "usuarios.apellido", "usuarios.username", "usuarios.email", "usuarios.habilitado"]);
                $consulta_con_filtro = $consultaSQL.($filtro -> generar_condiciones());
                $pagina = new PaginableClass($consulta_con_filtro, $numero_de_pagina);

                foreach ($pagina -> datos as $key => $value) {
                    $pagina -> datos[$key]['roles'] = Rol::obtener_roles_usuario($value['id_usuario']);
                }

                return $pagina;
            } else {
                $resultado = Conexion::obtenerDatos($consultaSQL);
                $resultadoObj = [];
                foreach ($resultado as $value) {                    
                    $obj = self::inicializar_desde_fila($value);
                    $resultadoObj[] = $obj;
                }
                return $resultadoObj;
            }
        }

        public function __construct($id_usuario, $username, $email, $password, $nombre, $apellido, $palabra_secreta, $habilitado) {
            $this -> id_usuario = $id_usuario;
            $this -> username = $username;
            $this -> email = $email;
            $this -> password = $password;
            $this -> nombre = $nombre;
            $this -> apellido = $apellido;
            $this -> palabra_secreta = $palabra_secreta;
            $this -> habilitado = $habilitado;
        }

        public static function iniciar_sesion($username, $email, $password){
            $username = Conexion::escaparCadena($username);
            $email = Conexion::escaparCadena($email);
            $password = Conexion::escaparCadena($password);

            $resultado =  Conexion::obtenerDatos("SELECT * FROM usuarios WHERE (username = '$username' OR email = '$email') AND password = '$password'");
            
            if (count($resultado) > 0) {
                $primerResultado = $resultado[0];
                $usuario = self::inicializar_desde_fila($primerResultado);

                if($usuario -> habilitado != 1)
                    RespuestasHttp::error_403("El usuario está deshabilitado");

                Token::crear_token($primerResultado["id_usuario"]);
                return $usuario;
            } else {
                RespuestasHttp::error_404("Credenciales Inválidas.");
            }
        }
        

        public static function inicializar_desde_fila($registro_fila){
            $id_usuario = $registro_fila["id_usuario"];
            $username = $registro_fila["username"];
            $email = $registro_fila["email"];
            $password = null;
            $nombre = $registro_fila["nombre"];
            $apellido = $registro_fila["apellido"];
            $palabra_secreta = null;
            $habilitado = $registro_fila["habilitado"] ? 1 : 0;

            $usuario = new Usuario($id_usuario, $username, $email, $password, $nombre, $apellido, $palabra_secreta, $habilitado);

            $usuario -> roles = Rol::obtener_roles_usuario($registro_fila["id_usuario"]);
            return $usuario;
        }

        public static function recuperar_usuario_por_id($id_usuario){
            $resultado = Conexion::obtenerDatos("SELECT * FROM usuarios WHERE id_usuario='$id_usuario'");
            if(count($resultado) > 0){
                $usuario = self::inicializar_desde_fila($resultado[0]);
                return $usuario;
            } else return null;
        }

        public function save(){            
            if($this -> id_usuario != null){
                RespuestasHttp::error_500("Se intentó guardar un usuario ya existente, prevensión de duplicación accidental");
            }

            $username = Conexion::escaparCadena($this -> username);
            $password = Conexion::escaparCadena($this -> password);
            $email = Conexion::escaparCadena($this -> email);
            $nombre = Conexion::escaparCadena($this->nombre);
            $apellido = Conexion::escaparCadena($this->apellido);
            $palabra_secreta = Conexion::escaparCadena($this->palabra_secreta);
            $habilitado = $this -> habilitado ? 1 : 0;

            //Verificamos si es que existe ya un usuario con ese nombre de usuario o correo
            $consultaSQL_VerificacionExistencia = "SELECT * FROM usuarios WHERE username = '$username' OR email = '$email'";
            $cantidad_de_coincidencias = Conexion::nonQuery($consultaSQL_VerificacionExistencia);
            if($cantidad_de_coincidencias > 0)
                RespuestasHttp::error_400("Ya existe usuario con ese username o email");

            $consultaSQL = "INSERT INTO usuarios(username, password, email, nombre, apellido, palabra_secreta,habilitado) VALUE ('$username','$password','$email','$nombre','$apellido', '$palabra_secreta', $habilitado)";

            $this -> id_usuario = Conexion::nonQueryId($consultaSQL);
        }

        public function alternar_habilitacion_del_usuario(){
            $id_usuario = Conexion::escaparCadena($this -> id_usuario);

            //Verificamos si el usuario existe...
            $consultaSQL_VerificacionExistencia = "SELECT * FROM usuarios WHERE id_usuario = $id_usuario";
            $cantidad_de_coincidencias = Conexion::nonQuery($consultaSQL_VerificacionExistencia);
            if($cantidad_de_coincidencias != 1)
                RespuestasHttp::error_404("No se encuentra el registro del usuario a Habilitar o Deshabilitar");
            
            //Efectuamos la actualización
            $habilitado = !($this -> habilitado) ? 1 : 0;
            $consultaActualizacion = "UPDATE usuarios SET habilitado=$habilitado WHERE id_usuario = $id_usuario;";
            Conexion::nonQuery($consultaActualizacion);
        }

        public function actualizar(){
            $id_usuario = Conexion::escaparCadena($this->id_usuario);
            $username = Conexion::escaparCadena($this -> username);
            // $password = Conexion::escaparCadena($this -> password);
            $email = Conexion::escaparCadena($this -> email);
            $nombre = Conexion::escaparCadena($this->nombre);
            $apellido = Conexion::escaparCadena($this->apellido);
            // $palabra_secreta = Conexion::escaparCadena($this->palabra_secreta);
            $habilitado = $this -> habilitado ? 1 : 0;

            $cantidad = Conexion::nonQuery("SELECT * FROM usuarios WHERE id_usuario = $id_usuario;");
            if($cantidad != 1)
                RespuestasHttp::error_404("No se encuentra el registro a actualizar.");

            //Verificamos si va a haber cambios respecto a nombre de usuario o correo
            $usuario_a_actualizar = self::recuperar_usuario_por_id($id_usuario);
            $username_anterior = Conexion::escaparCadena($usuario_a_actualizar -> username);
            $email_anterior = Conexion::escaparCadena($usuario_a_actualizar -> email);
            if(!(($username_anterior == $username) && ($email_anterior == $email))){
                if($username_anterior != $username){
                    $consulta_prueba_1 = "SELECT * FROM usuarios WHERE username = '$username' AND id_usuario != $id_usuario;";
                    if(Conexion::nonQuery($consulta_prueba_1) > 0)
                        RespuestasHttp::error_400("El nuevo nombre de usuario ya está en uso");
                }
                if($email_anterior != $email){
                    $consulta_prueba_2 = "SELECT * FROM usuarios WHERE email = '$email' AND id_usuario != $id_usuario;";
                    if(Conexion::nonQuery($consulta_prueba_2) > 0)
                        RespuestasHttp::error_400("El nuevo email de usuario ya está en uso");
                }
            }

            //Efectuamos la actualización
            $consultaActualizacion = "UPDATE usuarios SET username='$username', email='$email', nombre='$nombre', apellido='$apellido', habilitado=$habilitado WHERE id_usuario = $id_usuario;";
            Conexion::nonQuery($consultaActualizacion);
        }

        public static function cambiar_contrasena($id_usuario_objetivo, $nueva_contrasena, $id_usuario_solicitante, $contrasena_actual){
            $id_usuario_objetivo = Conexion::escaparCadena($id_usuario_objetivo);
            $nueva_contrasena = Conexion::escaparCadena($nueva_contrasena);
            $id_usuario_solicitante = Conexion::escaparCadena($id_usuario_solicitante);
            $contrasena_actual = Conexion::escaparCadena($contrasena_actual);

            //Verificamos si el usuario existe...
            $consultaSQL_VerificacionExistencia = "SELECT * FROM usuarios WHERE id_usuario = $id_usuario_solicitante AND password = '$contrasena_actual';";
            $cantidad_de_coincidencias = Conexion::nonQuery($consultaSQL_VerificacionExistencia);
            if($cantidad_de_coincidencias != 1)
                RespuestasHttp::error_404("Error al verificar las credenciales del usuario");

            $consultaSQL_VerificacionExistencia2 = "SELECT * FROM usuarios WHERE id_usuario = $id_usuario_objetivo;";
            $cantidad_de_coincidencias = Conexion::nonQuery($consultaSQL_VerificacionExistencia2);
            if($cantidad_de_coincidencias != 1)
                RespuestasHttp::error_404("No se encuentra el registro del usuario objetivo");

            //Efectuamos la actualización
            $consultaActualizacion = "UPDATE usuarios SET password='$nueva_contrasena' WHERE id_usuario = $id_usuario_objetivo;";
            Conexion::nonQuery($consultaActualizacion);
        }
    }