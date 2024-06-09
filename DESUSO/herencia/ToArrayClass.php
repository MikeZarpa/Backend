<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php');
    //require_once(DIR_PUJOL."/clases/conexion/conexion.php");

    abstract class ToArrayClass{
        protected $campos=[];

        public function to_array(){
            $array_asoc = [];
            foreach ($this -> campos as $value) {
                // Asegurarse de que la propiedad exista
                if (property_exists($this, $value)) {
                    $array_asoc[$value] = $this->{$value};
                }
            }
            return $array_asoc;
        }
    }