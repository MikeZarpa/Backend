<?php
    require_once ($_SERVER['DOCUMENT_ROOT'].'/DIR_PUJOL.php');
    require_once (DIR_PUJOL."/clases/base_de_datos/producto.php");
    require_once (DIR_PUJOL."/clases/base_de_datos/factura_venta.php");
    require_once(DIR_PUJOL.'/clases/utiles/FiltroClass.php');
    require_once(DIR_PUJOL.'/clases/constructores_de_filtros/BuildFiltroProducto.php');
    require_once(DIR_PUJOL.'/clases/constructores_de_filtros/BuildFiltroStockLote.php');


    abstract class Informes {
        
        public static function productos_debajo_de_la_cantidad_minima(){

            //Filtramos para tener solo los habilitados
            Filtro::quitar_todo_filtro();
            $build_filtro = new BuildFiltroProducto();
            $build_filtro -> habilitado('1',TipoDeComparacion::LITERAL);
            $build_filtro -> finalizar();

            $productos = Producto::consultarTodos(false);
            $productos_a_informar = [];

            //Recorremos todos los productos
            for ($index = count($productos)-1; $index >= 0; $index--) { 
                $producto = $productos[$index];

                //Descartamos todos los que tienen cantidad_minima que no sea mayor a 0
                if(!($producto -> cantidad_minima > 0)){
                    //unset($productos[$index]);
                    continue;
                }
                
                //Descartamos todos los que no esta por debajo de la cantidad minima
                if(!($producto -> total_cantidad < $producto -> cantidad_minima)){
                    //unset($productos[$index]);
                    continue;
                }
                $productos_a_informar[] = $producto;
            }

            return $productos_a_informar;
        }
        public static function ventas_productos_entre_fechas($fecha_min, $fecha_max){
            $sql_querry = "SELECT producto.*,
            SUM(detalle_venta.cantidad) AS CANTIDAD_VENDIDA,
            SUM(detalle_venta.cantidad * historial_precio.precio) AS TOTAL_VENDIDO,
            SUM(detalle_venta.cantidad * stock_lote.coste) AS TOTAL_COSTE,
            SUM(detalle_venta.cantidad * (historial_precio.precio-stock_lote.coste)) AS GANANCIA_GENERADA,
	        DATEDIFF(DATE('$fecha_max')+1, DATE('$fecha_min')) AS DIAS_TOTALES,
            ROUND(SUM(detalle_venta.cantidad) / (DATEDIFF(DATE('$fecha_max')+1, DATE('$fecha_min'))),2) AS CANT_PROM_DIARIO,
            ROUND(SUM(detalle_venta.cantidad * historial_precio.precio) / (DATEDIFF(DATE('$fecha_max')+1, DATE('$fecha_min'))),2) AS VENDIDO_PROM_DIARIO,
            ROUND(SUM(detalle_venta.cantidad * stock_lote.coste) / (DATEDIFF(DATE('$fecha_max')+1, DATE('$fecha_min'))),2) AS COSTE_PROM_DIARIO,
            ROUND(SUM(detalle_venta.cantidad * (historial_precio.precio-stock_lote.coste)) / (DATEDIFF(DATE('$fecha_max')+1, DATE('$fecha_min'))),2) AS GANANCIA_PROM_DIARIO
            FROM factura_venta
                INNER JOIN detalle_venta ON detalle_venta.id_factura_venta = factura_venta.id_factura_venta
                INNER JOIN historial_precio ON historial_precio.id_histprecio = detalle_venta.id_histprecio
                INNER JOIN stock_lote ON stock_lote.id_stock = historial_precio.id_stock
                INNER JOIN producto ON producto.id_producto = stock_lote.id_producto
            WHERE factura_venta.fecha BETWEEN DATE('$fecha_min') AND DATE('$fecha_max')+1
            GROUP BY producto.id_producto
            ORDER BY GANANCIA_GENERADA DESC";

            $resultados = Conexion::obtenerDatos($sql_querry);
            //Ponemos los datos de los productos
            foreach ($resultados as $index => $registro) {
                $resultados[$index]["producto"] = Producto::inicializar_desde_array($registro);
            }
            return $resultados;
        }
        public static function ventas_categoria_entre_fechas($fecha_min, $fecha_max){
            $sql_querry = "SELECT categoria.*,
            SUM(detalle_venta.cantidad) AS CANTIDAD_VENDIDA,
            SUM(detalle_venta.cantidad * historial_precio.precio) AS TOTAL_VENDIDO,
            SUM(detalle_venta.cantidad * stock_lote.coste) AS TOTAL_COSTE,
            SUM(detalle_venta.cantidad * (historial_precio.precio-stock_lote.coste)) AS GANANCIA_GENERADA,
	        DATEDIFF(DATE('$fecha_max')+1, DATE('$fecha_min')) AS DIAS_TOTALES,
            ROUND(SUM(detalle_venta.cantidad) / (DATEDIFF(DATE('$fecha_max')+1, DATE('$fecha_min'))),2) AS CANT_PROM_DIARIO,
            ROUND(SUM(detalle_venta.cantidad * historial_precio.precio) / (DATEDIFF(DATE('$fecha_max')+1, DATE('$fecha_min'))),2) AS VENDIDO_PROM_DIARIO,
            ROUND(SUM(detalle_venta.cantidad * stock_lote.coste) / (DATEDIFF(DATE('$fecha_max')+1, DATE('$fecha_min'))),2) AS COSTE_PROM_DIARIO,
            ROUND(SUM(detalle_venta.cantidad * (historial_precio.precio-stock_lote.coste)) / (DATEDIFF(DATE('$fecha_max')+1, DATE('$fecha_min'))),2) AS GANANCIA_PROM_DIARIO
            FROM factura_venta
                INNER JOIN detalle_venta ON detalle_venta.id_factura_venta = factura_venta.id_factura_venta
                INNER JOIN historial_precio ON historial_precio.id_histprecio = detalle_venta.id_histprecio
                INNER JOIN stock_lote ON stock_lote.id_stock = historial_precio.id_stock
                INNER JOIN producto ON producto.id_producto = stock_lote.id_producto
                LEFT JOIN categoria ON categoria.id_categoria = producto.id_categoria
            WHERE factura_venta.fecha BETWEEN DATE('$fecha_min') AND DATE('$fecha_max')+1
            GROUP BY categoria.id_categoria
            ORDER BY GANANCIA_GENERADA DESC";

            $resultados = Conexion::obtenerDatos($sql_querry);
            //Ponemos los datos de los productos
            foreach ($resultados as $index => $registro) {
                $resultados[$index]["categoria"] = Categoria::inicializar_desde_array($registro);
            }
            return $resultados;
        }
        public static function prueba(){
            Filtro::quitar_todo_filtro();
            $build_filtro = new BuildFiltroProducto();
            // $build_filtro -> descripcion_producto("ase",TipoDeComparacion::CONTIENE);
            // $build_filtro -> habilitado('1',TipoDeComparacion::LITERAL);
            // $build_filtro -> descripcion_marca('0', TipoDeComparacion::POR_NULIDAD);
            $build_filtro -> finalizar();
            return Producto::consultarTodos(true);
        }
        public static function productos_vencidos_o_cerca($cantidad_dias_anticipacion){

            Filtro::quitar_todo_filtro();
            $build_filtro = new BuildFiltroStockLote();
            $build_filtro -> producto_habilitado('1',TipoDeComparacion::LITERAL);
            $build_filtro -> stock_cantidad('0', TipoDeComparacion::MAYOR_QUE);
            $build_filtro -> stock_vencimiento('1',null, TipoDeComparacion::POR_NULIDAD);
            $build_filtro -> finalizar();

            $registros_de_stock = StockLote::recuperar_todos(false);

            $registros_a_informar = [];
            $hoy = new DateTime(); // Fecha actual

            for ($index = count($registros_de_stock)-1; $index >= 0; $index--) { 
                $registro = $registros_de_stock[$index];
        
                $fecha_vto = new DateTime($registro->fecha_vto); // Convertir la fecha de vencimiento a un objeto DateTime
        
                // Calcular la diferencia en días
                $diferencia_dias = $hoy->diff($fecha_vto)->days + 1;
        
                // Si la diferencia es mayor que la cantidad de días de anticipación y la fecha es futura, omitimos ese registro
                if($diferencia_dias > $cantidad_dias_anticipacion && $fecha_vto > $hoy) {
                    continue;
                }

                $registros_a_informar[] = $registro;
            }

            foreach ($registros_a_informar as $index => $registro) {
                $registro -> producto = Producto::recuperar_incompleto_por_id($registro -> id_producto);
            }

            return $registros_a_informar;
        }
    }