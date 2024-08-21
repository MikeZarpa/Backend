<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php');
    require_once(DIR_PUJOL.'/clases/utiles/FiltroClass.php');

    class BuildFiltroProducto{
        public $filtro;

        public function __construct() {
            $this -> filtro  = new Filtro(["producto.descripcion", "producto.habilitado", "marca.descripcion"]);
            $this -> filtro -> enabled = true;
        }

        public function descripcion_producto($descripcion, $tipoDeComparacion){
            $array_asoc = [
                "enabled" => true,
                "campo" => 0,
                "terminosDeBusqueda" => [$descripcion],
                "tipoBusqueda" => $tipoDeComparacion
            ];
            $filtro_detalle = new FiltroDetalle($array_asoc);
            $this -> filtro -> agregar_filtro($filtro_detalle);
            return $this;
        }
        public function habilitado($estado, $tipoDeComparacion){
            $array_asoc = [
                "enabled" => true,
                "campo" => 1,
                "terminosDeBusqueda" => [$estado],
                "tipoBusqueda" => $tipoDeComparacion
            ];
            $filtro_detalle = new FiltroDetalle($array_asoc);
            $this -> filtro -> agregar_filtro($filtro_detalle);
            return $this;
        }
        public function descripcion_marca($descripcion, $tipoDeComparacion){
            $array_asoc = [
                "enabled" => true,
                "campo" => 2,
                "terminosDeBusqueda" => [$descripcion],
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