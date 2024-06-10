<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php');
    require_once(DIR_PUJOL."/clases/conexion/conexion.php");

    class PaginableClass {
        public $pagina_actual;  //Listo
        public $cantidad_por_pagina;    //Listo
        public $datos;          //Listo
        public $nro_desde;      //Listo
        public $nro_hasta;      //Listo
        public $ultima_pagina;  //Listo
        public $total_elementos;    //Listo

        function __construct($consulta, $pagina_actual=1, $cantidad_por_pagina = 20){
            /*
            $patron = '/SELECT .*? FROM/i';
            $reemplazo = 'SELECT COUNT(*) as "cantidad" FROM';*/

            if($pagina_actual == null){
                $pagina_actual = 1;
            }
            if($cantidad_por_pagina==null || $cantidad_por_pagina < 1){
                $cantidad_por_pagina = 20;
            }

            // Reemplazamos el patrón en la consulta
            //$nuevaConsulta = preg_replace($patron, $reemplazo, $consulta);
            //var_dump($nuevaConsulta);
            $cantidad_total = Conexion::nonQuery($consulta);

            $cantidad_total_paginas = ceil($cantidad_total / $cantidad_por_pagina);
            $pagina_actual = min($cantidad_total_paginas, $pagina_actual);
            $pagina_actual = max($pagina_actual, 1);
            $offset = ($pagina_actual -1 ) * $cantidad_por_pagina; //Paginas ya pasadas...

            //Ahora cargamos todo datos de los paginables
                //Pagina Actual
                $this -> pagina_actual = max(1,$pagina_actual);
                //Cantidad_por_pagina
                $this -> cantidad_por_pagina = $cantidad_por_pagina;
                //Datos
                $consulta_paginada = $consulta . " LIMIT $offset, $cantidad_por_pagina ";
                $this -> datos = Conexion::obtenerDatos($consulta_paginada);
                //Nro_desde
                $this -> nro_desde = min($offset + 1, $cantidad_total);
                //Nro_hasta
                $this -> nro_hasta = min($offset + $cantidad_por_pagina, $cantidad_total);
                //Ultima_pagina
                $this -> ultima_pagina = max(1,$cantidad_total_paginas);
                //Total_elementos
                $this -> total_elementos = $cantidad_total;

        }
    }