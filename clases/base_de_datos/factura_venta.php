<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php');
    require_once (DIR_PUJOL."/clases/conexion/conexion.php");
    require_once (DIR_PUJOL."/clases/utiles/PaginableClass.php");
    require_once (DIR_PUJOL."/utiles/utilidades_get.php");
    require_once (DIR_PUJOL."/clases/utiles/FiltroClass.php");
    require_once (DIR_PUJOL."/clases/conexion/respuestas_http.php");

    class FacturaVenta {
        public $id_factura_venta = null;
        public $fecha;
        public $total = 0;
        public $id_metodo_pago = 1;
        public $id_cond_iva = 1;
        public $id_cliente = null;
        public $id_usuario = 1; //Provicional deberia undefined
        public $habiltado = 1; //?

        public $detalles_venta = null;
        public function __construct()
        {            
        }
        
        public function save(){
            $id_metodo_pago = $this -> id_metodo_pago;
            $id_cond_iva = $this -> id_cond_iva;
            $id_cliente = $this -> id_cliente ?? "null";
            $id_usuario = $this-> id_usuario;

            $consultaSQL = "INSERT INTO factura_venta(id_metodo_pago, id_cond_iva, id_cliente, id_usuario) VALUE ($id_metodo_pago, $id_cond_iva, $id_cliente, $id_usuario)";

            $id_factura_venta = Conexion::nonQueryId($consultaSQL);
            $this -> id_factura_venta = $id_factura_venta;                        
        }

        public function agregar_detalles_venta_desde_carrito($array_carrito){
            
        }

        public function actualizar(){
            
        }
    }