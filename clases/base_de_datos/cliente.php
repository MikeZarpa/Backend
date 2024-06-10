<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php');
    require_once (DIR_PUJOL."/clases/conexion/conexion.php");
    require_once (DIR_PUJOL."/clases/utiles/PaginableClass.php");
    require_once (DIR_PUJOL."/utiles/utilidades_get.php");
    require_once (DIR_PUJOL."/clases/utiles/FiltroClass.php");
    require_once (DIR_PUJOL."/clases/conexion/respuestas_http.php");
    require_once (DIR_PUJOL. "/clases/base_de_datos/direccion.php");
    require_once (DIR_PUJOL. "/clases/base_de_datos/condicion_iva.php");

    class Cliente {
        public $id_cliente = null;
        public $nombre;
        public $apellido;
        public $dni;
        public $cuil_cuit;
        public $id_cond_iva;
        public $id_direccion;
        public $id_pais;

        public $pais = null;
        public $direccion = null;
        public $cond_iva = null;

        public function __construct($id_cliente, $nombre, $apellido, $dni, $cuil_cuit, $id_cond_iva, $id_direccion, $id_pais)
        {
            $this -> id_cliente = $id_cliente;
            $this -> nombre = $nombre;
            $this -> apellido = $apellido;
            $this -> dni = $dni;
            $this -> cuil_cuit = $cuil_cuit;
            $this -> id_cond_iva = $id_cond_iva;
            $this -> id_direccion = $id_direccion;
            $this -> id_pais = $id_pais;
        }

        public static function consultarTodos(){
            $consultaSQL = "SELECT cliente.id_cliente, cliente.nombre, cliente.apellido, cliente.dni, cliente.cuil_cuit, cliente.id_cond_iva, cliente.id_direccion, cliente.id_pais FROM cliente WHERE true ";
            //$pagina = new PaginableClass($consultaSQL, $numero_de_pagina);
            
            $numero_de_pagina = UtilesGet::obtener_opcional('nroPagina');
            $filtro = new Filtro(["cliente.nombre", "cliente.apellido", "cliente.dni"]);
            $consulta_con_filtro = $consultaSQL.($filtro -> generar_condiciones());
            $pagina = new PaginableClass($consulta_con_filtro, $numero_de_pagina);

            foreach ($pagina -> datos as $key => $value) {
                $pagina -> datos[$key]['pais'] = Pais::recuperar_por_id($value['id_pais']);
                //$pagina -> datos[$key]['direccion'] = Direccion::recuperar_por_id($value['id_direccion']);
                //$pagina -> datos[$key]['cond_iva'] = CondicionIva::recuperar_por_id($value['id_cond_iva']);
            }

            return $pagina;
        }

        public static function recuperar_por_id($id_cliente){
            $id_cliente = Conexion::escaparCadena($id_cliente);
            $consultaSQL = "SELECT cliente.id_cliente, cliente.nombre, cliente.apellido, cliente.dni, cliente.cuil_cuit, cliente.id_cond_iva, cliente.id_direccion, cliente.id_pais FROM cliente WHERE cliente.id_cliente = $id_cliente";
            $resultado = Conexion::obtenerDatos($consultaSQL);
            if($resultado){
                $cliente = self::inicializar_desde_array($resultado[0]);
                return $cliente;
            } else return null;
        }

        public static function inicializar_desde_array($array){
            $cliente = new Cliente($array['id_cliente'],$array['nombre'],$array['apellido'],$array['dni'],$array['cuil_cuit'],$array['id_cond_iva'],$array['id_direccion'],$array['id_pais']);
            $cliente -> pais = Pais::recuperar_por_id($array['id_pais']);
            $cliente -> direccion = Direccion::recuperar_por_id($array['id_direccion']);
            $cliente -> cond_iva = CondicionIva::recuperar_por_id($array['id_cond_iva']);

            return $cliente;
        }

        public function save(){

            //$id_cliente = Conexion::escaparCadena($this->id_cliente);
            $nombre = Conexion::escaparCadena($this->nombre);
            $apellido = Conexion::escaparCadena($this->apellido);
            $dni = Conexion::escaparCadena($this->dni);
            $cuil_cuit = Conexion::escaparCadena($this->cuil_cuit);
            $id_cond_iva = $this -> id_cond_iva ?? "null";
            $id_cond_iva = Conexion::escaparCadena($id_cond_iva);
            $id_direccion = $this -> id_direccion ?? "null";
            $id_direccion = Conexion::escaparCadena($id_direccion);
            $id_pais = $this -> id_pais ?? "null";
            $id_pais = Conexion::escaparCadena($id_pais);            


            $consultaSQL = "INSERT INTO cliente(nombre, apellido, dni, cuil_cuit, id_cond_iva, id_direccion,id_pais) VALUE ('$nombre','$apellido','$dni','$cuil_cuit',$id_cond_iva, $id_direccion, $id_pais)";

            $cantidad_registros_afectados = Conexion::nonQuery($consultaSQL);
            if($cantidad_registros_afectados < 1){
                RespuestasHttp::error_500("Error al ingresar nuevo cliente");
            }
        }

        public function actualizar(){
            $id_cliente = Conexion::escaparCadena($this->id_cliente);
            $nombre = Conexion::escaparCadena($this->nombre);
            $apellido = Conexion::escaparCadena($this->apellido);
            $dni = Conexion::escaparCadena($this->dni);
            $cuil_cuit = Conexion::escaparCadena($this->cuil_cuit);
            $id_cond_iva = $this -> id_cond_iva ?? "null";
            $id_cond_iva = Conexion::escaparCadena($id_cond_iva);
            $id_direccion = $this -> id_direccion ?? "null";
            $id_direccion = Conexion::escaparCadena($id_direccion);
            $id_pais = $this -> id_pais ?? "null";
            $id_pais = Conexion::escaparCadena($id_pais);

            $cantidad = Conexion::nonQuery("SELECT * FROM cliente WHERE cliente.id_cliente = $id_cliente");
            if($cantidad == 1){
                $consultaActualizacion = "UPDATE cliente SET nombre='$nombre', apellido='$apellido', dni='$dni', cuil_cuit='$cuil_cuit', id_cond_iva=$id_cond_iva, id_direccion=$id_direccion,id_pais=$id_pais WHERE cliente.id_cliente = $id_cliente";
                Conexion::nonQuery($consultaActualizacion);
            } else {
                RespuestasHttp::error_404("No se encuentra el registro a actualizar.");
            }
        }
    }