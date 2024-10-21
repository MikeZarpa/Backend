<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php');
    require_once (DIR_PUJOL."/clases/conexion/conexion.php");
    require_once (DIR_PUJOL."/clases/utiles/PaginableClass.php");
    require_once (DIR_PUJOL."/utiles/utilidades_get.php");
    require_once (DIR_PUJOL."/clases/utiles/FiltroClass.php");
    require_once (DIR_PUJOL."/clases/conexion/respuestas_http.php");
    require_once (DIR_PUJOL."/clases/base_de_datos/detalle_venta.php");
    require_once (DIR_PUJOL."/clases/base_de_datos/cliente.php");
    require_once (DIR_PUJOL."/clases/base_de_datos/metodo_pago.php");

    class FacturaVenta {
        public $id_factura_venta = null;
        public $fecha;
        public $total = 0;
        public $id_metodo_pago = 1;
        public $id_cond_iva = 1;
        public $id_cliente = null;
        public $id_usuario;

        public $habiltado = 1; //?
        
        public $usuario = null;
        public $cliente = null;
        public $cond_iva = null;
        public $metodo_pago = null;
        public $detalles_venta = null;
        public function __construct($id_factura_venta,$id_usuario, $id_cliente = null, $id_metodo_pago = 1)
        {
            $this -> id_factura_venta = $id_factura_venta;
            $this -> id_usuario = $id_usuario;
            $this -> id_cliente = $id_cliente;
            $this -> id_metodo_pago = $id_metodo_pago;
        }

        public static function consultarTodos($paginar = true){

            $consultaSQL = "SELECT factura_venta.* FROM factura_venta WHERE true ";
            //$pagina = new PaginableClass($consultaSQL, $numero_de_pagina);
            
            $filtro = new Filtro(["factura_venta.id_cliente", "factura_venta.fecha", "factura_venta.id_usuario"]);
            $consulta_con_filtro = $consultaSQL.($filtro -> generar_condiciones());

            if($paginar){
                $numero_de_pagina = UtilesGet::obtener_opcional('nroPagina');
                $consulta_con_filtro = $consulta_con_filtro ." ORDER BY factura_venta.id_factura_venta DESC";
                $pagina = new PaginableClass($consulta_con_filtro, $numero_de_pagina);

                foreach ($pagina -> datos as $key => $value) {
                    $pagina -> datos[$key]['usuario'] = Usuario::recuperar_usuario_por_id($value['id_usuario']);
                    $pagina -> datos[$key]['cond_iva'] = CondicionIva::recuperar_por_id($value['id_cond_iva']);
                    $pagina -> datos[$key]['cliente'] = Cliente::recuperar_por_id($value['id_cliente']);
                    $pagina -> datos[$key]['metodo_pago'] = MetodoPago::recuperar_por_id($value['id_metodo_pago']);
                }

                return $pagina;
            } else {
                $resultado = Conexion::obtenerDatos($consulta_con_filtro);
                $resultado_obj = [];
                foreach ($resultado as $registro) {
                    $obj = self::inicializar_desde_array($registro);
                    $obj -> usuario = Usuario::recuperar_usuario_por_id($registro['id_usuario']);
                    $obj -> cond_iva = CondicionIva::recuperar_por_id($registro['id_cond_iva']);
                    $obj -> cliente = Cliente::recuperar_por_id($registro['id_cliente']);
                    $obj -> metodo_pago = MetodoPago::recuperar_por_id($registro['id_metodo_pago']);
                    $resultado_obj[] = $obj;
                }
                return $resultado_obj;
            }
        }

        public static function recuperar_por_id($id_factura_venta){
            $consultaSQL = "SELECT factura_venta.* FROM factura_venta WHERE factura_venta.id_factura_Venta = $id_factura_venta;";
            $resultado = Conexion::obtenerDatos($consultaSQL);
            if($resultado){
                $factura = self::inicializar_desde_array($resultado[0]);
                $factura -> detalles_venta = DetalleVenta::recuperar_por_id_factura($factura -> id_factura_venta);
                $factura -> usuario = Usuario::recuperar_usuario_por_id($factura -> id_usuario );
                $factura -> cliente = Cliente::recuperar_por_id($factura -> id_cliente );
                $factura -> cond_iva = CondicionIva::recuperar_por_id($factura -> id_cond_iva );
                $factura -> metodo_pago = MetodoPago::recuperar_por_id($factura -> id_metodo_pago);
                return $factura;
            } else return null;
        }

        public static function inicializar_desde_array($array){
            $factura = new FacturaVenta($array['id_factura_venta'], $array['id_usuario'], $array['id_cliente'] ?? null);
            $factura -> total = $array['total'];
            $factura -> fecha = $array['fecha'];
            $factura -> id_metodo_pago = $array['id_metodo_pago'];
            $factura -> id_cond_iva = $array["id_cond_iva"];
            $factura -> id_cliente = $array["id_cliente"];
            return $factura;
        }
        
        public function save(){
            if($this -> id_factura_venta != null) RespuestasHttp::error_500("Se intentó duplicar un registro de factura ya existente");
            $id_metodo_pago = $this -> id_metodo_pago;
            $id_cond_iva = $this -> id_cond_iva;
            $id_cliente = $this -> id_cliente ?? "null";

            if($this -> id_factura_venta == null && $id_cliente != "null"){
                $cliente = Cliente::recuperar_por_id($id_cliente);
                $this -> id_cond_iva = $cliente -> id_cond_iva;
                $id_cond_iva = $cliente -> id_cond_iva;
            }

            $id_usuario = $this-> id_usuario;
            
            $consultaSQL = "INSERT INTO factura_venta(id_metodo_pago, id_cond_iva, id_cliente, id_usuario) VALUE ($id_metodo_pago, $id_cond_iva, $id_cliente, $id_usuario)";

            $id_factura_venta = Conexion::nonQueryId($consultaSQL);
            $this -> id_factura_venta = $id_factura_venta;                    
        }

        public function agregar_detalles_venta_desde_carrito($array_carrito){
            $id_factura_venta = $this -> id_factura_venta;

            for ($i = 0; $i < count($array_carrito); $i++){
                $elemento_carrito = $array_carrito[$i];
            
                //Vamos a procesar cada item del carrito
                $cantidad = $elemento_carrito['cantidad'];
                $id_histprecio = $elemento_carrito['id_histprecio'] ?? "null";
                $id_promocion = $elemento_carrito['id_promocion'] ?? "null";                
                $id_tipo_det_venta = $elemento_carrito['id_tipo_det_venta'];
                
                $detalle_venta = new DetalleVenta($id_histprecio, $cantidad, $id_factura_venta);
                $detalle_venta -> id_promocion = $id_promocion;
                $detalle_venta -> id_tipo_det_venta = $id_tipo_det_venta;
                $detalle_venta -> save();
            }

            $this -> actualizar_total();
        }

        public function actualizar_total(){            
            $id_factura_venta = $this-> id_factura_venta;
            $this->detalles_venta = DetalleVenta::recuperar_por_id_factura($id_factura_venta);
            $total = 0;
            foreach ($this->detalles_venta as $detalle_venta) {
                if($detalle_venta -> tipo_det_venta -> codigo == "VENTA"){
                    $total += $detalle_venta -> hist_precio -> precio * $detalle_venta -> cantidad;
                }
            }
            //Calculamos el descuento del iva
            $condicion_iva = CondicionIva::recuperar_por_id($this -> id_cond_iva);
            $porcentaje = 121 - $condicion_iva ->porcentaje;

            $descuento = $total*$porcentaje/100 - $total;

            $total_con_descuento = $total - $descuento;
            
            $this -> total = $total;
            $consultaSQL = "UPDATE factura_venta SET factura_venta.total = $total_con_descuento WHERE factura_venta.id_factura_venta = $id_factura_venta;";
            Conexion::nonQuery($consultaSQL);
        }
    }