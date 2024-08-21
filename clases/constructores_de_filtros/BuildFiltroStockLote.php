<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php');
    require_once(DIR_PUJOL.'/clases/utiles/FiltroClass.php');

    class BuildFiltroStockLote{
        public $filtro;

        public function __construct() {
            $this -> filtro  = new Filtro(["producto.habilitado","stock_lote.fecha_vto", "stock_lote.cantidad"]);
            $this -> filtro -> enabled = true;
        }

        public function producto_habilitado($estado, $tipoDeComparacion){
            $array_asoc = [
                "enabled" => true,
                "campo" => 0,
                "terminosDeBusqueda" => [$estado],
                "tipoBusqueda" => $tipoDeComparacion
            ];
            $filtro_detalle = new FiltroDetalle($array_asoc);
            $this -> filtro -> agregar_filtro($filtro_detalle);
            return $this;
        }
        public function stock_vencimiento($fecha1,$fecha2, $tipoDeComparacion){
            $array_asoc = [
                "enabled" => true,
                "campo" => 1,
                "terminosDeBusqueda" => [$fecha1,$fecha2],
                "tipoBusqueda" => $tipoDeComparacion
            ];
            $filtro_detalle = new FiltroDetalle($array_asoc);
            $this -> filtro -> agregar_filtro($filtro_detalle);
            return $this;
        }
        public function stock_cantidad($cantidad, $tipoDeComparacion){
            $array_asoc = [
                "enabled" => true,
                "campo" => 2,
                "terminosDeBusqueda" => [$cantidad],
                "tipoBusqueda" => $tipoDeComparacion
            ];
            $filtro_detalle = new FiltroDetalle($array_asoc);
            $this -> filtro -> agregar_filtro($filtro_detalle);
            return $this;
        }
        public function finalizar(){
            $_POST["filtro"] = json_encode($this -> filtro);
        }
    }