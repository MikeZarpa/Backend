-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         8.0.27 - MySQL Community Server - GPL
-- SO del servidor:              Win64
-- HeidiSQL Versión:             11.3.0.6295
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para tp_pujol_db
DROP DATABASE IF EXISTS `tp_pujol_db`;
CREATE DATABASE IF NOT EXISTS `tp_pujol_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `tp_pujol_db`;

-- Volcando estructura para tabla tp_pujol_db.categoria
DROP TABLE IF EXISTS `categoria`;
CREATE TABLE IF NOT EXISTS `categoria` (
  `id_categoria` int NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla tp_pujol_db.categoria: ~0 rows (aproximadamente)
DELETE FROM `categoria`;
/*!40000 ALTER TABLE `categoria` DISABLE KEYS */;
INSERT INTO `categoria` (`id_categoria`, `descripcion`) VALUES
	(1, 'Bebidas'),
	(2, 'Gastronomía');
/*!40000 ALTER TABLE `categoria` ENABLE KEYS */;

-- Volcando estructura para tabla tp_pujol_db.categoria_asignada
DROP TABLE IF EXISTS `categoria_asignada`;
CREATE TABLE IF NOT EXISTS `categoria_asignada` (
  `id_categoria_asignada` int NOT NULL AUTO_INCREMENT,
  `id_producto` int NOT NULL,
  `id_categoria` int NOT NULL,
  PRIMARY KEY (`id_categoria_asignada`),
  KEY `FK_categoria_asignada_producto` (`id_producto`),
  KEY `FK_categoria_asignada_categoria` (`id_categoria`),
  CONSTRAINT `FK_categoria_asignada_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id_categoria`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `FK_categoria_asignada_producto` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla tp_pujol_db.categoria_asignada: ~0 rows (aproximadamente)
DELETE FROM `categoria_asignada`;
/*!40000 ALTER TABLE `categoria_asignada` DISABLE KEYS */;
INSERT INTO `categoria_asignada` (`id_categoria_asignada`, `id_producto`, `id_categoria`) VALUES
	(1, 1, 1),
	(2, 2, 1),
	(3, 3, 1),
	(4, 4, 1),
	(5, 8, 2),
	(6, 5, 2),
	(7, 6, 2),
	(8, 7, 2),
	(9, 10, 2);
/*!40000 ALTER TABLE `categoria_asignada` ENABLE KEYS */;

-- Volcando estructura para tabla tp_pujol_db.cliente
DROP TABLE IF EXISTS `cliente`;
CREATE TABLE IF NOT EXISTS `cliente` (
  `id_cliente` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `apellido` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `dni` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cuil_cuit` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_cond_iva` int DEFAULT NULL,
  `id_direccion` int DEFAULT NULL,
  `id_pais` int DEFAULT NULL,
  PRIMARY KEY (`id_cliente`),
  KEY `FK_cliente_condicion_iva` (`id_cond_iva`),
  KEY `FK_cliente_direccion` (`id_direccion`),
  KEY `FK_cliente_pais` (`id_pais`),
  CONSTRAINT `FK_cliente_condicion_iva` FOREIGN KEY (`id_cond_iva`) REFERENCES `condicion_iva` (`id_cond_iva`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `FK_cliente_direccion` FOREIGN KEY (`id_direccion`) REFERENCES `direccion` (`id_direccion`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `FK_cliente_pais` FOREIGN KEY (`id_pais`) REFERENCES `pais` (`id_pais`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla tp_pujol_db.cliente: ~0 rows (aproximadamente)
DELETE FROM `cliente`;
/*!40000 ALTER TABLE `cliente` DISABLE KEYS */;
/*!40000 ALTER TABLE `cliente` ENABLE KEYS */;

-- Volcando estructura para tabla tp_pujol_db.condicion_iva
DROP TABLE IF EXISTS `condicion_iva`;
CREATE TABLE IF NOT EXISTS `condicion_iva` (
  `id_cond_iva` int NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Descripcion de IVA',
  PRIMARY KEY (`id_cond_iva`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla tp_pujol_db.condicion_iva: ~0 rows (aproximadamente)
DELETE FROM `condicion_iva`;
/*!40000 ALTER TABLE `condicion_iva` DISABLE KEYS */;
INSERT INTO `condicion_iva` (`id_cond_iva`, `descripcion`) VALUES
	(1, 'Estandar');
/*!40000 ALTER TABLE `condicion_iva` ENABLE KEYS */;

-- Volcando estructura para tabla tp_pujol_db.detalle_venta
DROP TABLE IF EXISTS `detalle_venta`;
CREATE TABLE IF NOT EXISTS `detalle_venta` (
  `id_det_venta` int NOT NULL AUTO_INCREMENT,
  `cantidad` float NOT NULL,
  `id_histprecio` int NOT NULL,
  `id_promocion` int DEFAULT NULL,
  `id_factura_venta` int NOT NULL,
  `id_tipo_det_venta` int NOT NULL,
  PRIMARY KEY (`id_det_venta`),
  KEY `FK_detalle_venta_historial_precio` (`id_histprecio`),
  KEY `FK_detalle_venta_promocion` (`id_promocion`),
  KEY `FK_detalle_venta_factura_venta` (`id_factura_venta`),
  KEY `FK_detalle_venta_tipo_detalle_venta` (`id_tipo_det_venta`),
  CONSTRAINT `FK_detalle_venta_factura_venta` FOREIGN KEY (`id_factura_venta`) REFERENCES `factura_venta` (`id_factura_venta`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `FK_detalle_venta_historial_precio` FOREIGN KEY (`id_histprecio`) REFERENCES `historial_precio` (`id_histprecio`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `FK_detalle_venta_promocion` FOREIGN KEY (`id_promocion`) REFERENCES `promocion` (`id_promocion`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `FK_detalle_venta_tipo_detalle_venta` FOREIGN KEY (`id_tipo_det_venta`) REFERENCES `tipo_detalle_venta` (`id_tipo_det_venta`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla tp_pujol_db.detalle_venta: ~0 rows (aproximadamente)
DELETE FROM `detalle_venta`;
/*!40000 ALTER TABLE `detalle_venta` DISABLE KEYS */;
INSERT INTO `detalle_venta` (`id_det_venta`, `cantidad`, `id_histprecio`, `id_promocion`, `id_factura_venta`, `id_tipo_det_venta`) VALUES
	(1, 12, 1, NULL, 1, 1),
	(2, 4, 2, NULL, 1, 1),
	(3, 10, 3, NULL, 1, 1);
/*!40000 ALTER TABLE `detalle_venta` ENABLE KEYS */;

-- Volcando estructura para tabla tp_pujol_db.direccion
DROP TABLE IF EXISTS `direccion`;
CREATE TABLE IF NOT EXISTS `direccion` (
  `id_direccion` int NOT NULL AUTO_INCREMENT,
  `calle` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `altura` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `piso` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `departamento` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `id_localidad` int DEFAULT NULL,
  PRIMARY KEY (`id_direccion`),
  KEY `id_localidad` (`id_localidad`),
  CONSTRAINT `FK_direccion_localidad` FOREIGN KEY (`id_localidad`) REFERENCES `localidad` (`id_localidad`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla tp_pujol_db.direccion: ~0 rows (aproximadamente)
DELETE FROM `direccion`;
/*!40000 ALTER TABLE `direccion` DISABLE KEYS */;
/*!40000 ALTER TABLE `direccion` ENABLE KEYS */;

-- Volcando estructura para tabla tp_pujol_db.factura_venta
DROP TABLE IF EXISTS `factura_venta`;
CREATE TABLE IF NOT EXISTS `factura_venta` (
  `id_factura_venta` int NOT NULL AUTO_INCREMENT,
  `fecha` datetime DEFAULT CURRENT_TIMESTAMP,
  `total` float DEFAULT NULL,
  `id_metodo_pago` int DEFAULT NULL,
  `id_cond_iva` int NOT NULL,
  `id_cliente` int DEFAULT NULL,
  `id_usuario` int NOT NULL,
  `habilitado` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_factura_venta`),
  KEY `FK_factura_venta_metodo_pago` (`id_metodo_pago`),
  KEY `FK_factura_venta_condicion_iva` (`id_cond_iva`),
  KEY `FK_factura_venta_cliente` (`id_cliente`),
  KEY `FK_factura_venta_usuarios` (`id_usuario`),
  CONSTRAINT `FK_factura_venta_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `FK_factura_venta_condicion_iva` FOREIGN KEY (`id_cond_iva`) REFERENCES `condicion_iva` (`id_cond_iva`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `FK_factura_venta_metodo_pago` FOREIGN KEY (`id_metodo_pago`) REFERENCES `metodo_pago` (`id_metpago`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `FK_factura_venta_usuarios` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla tp_pujol_db.factura_venta: ~0 rows (aproximadamente)
DELETE FROM `factura_venta`;
/*!40000 ALTER TABLE `factura_venta` DISABLE KEYS */;
INSERT INTO `factura_venta` (`id_factura_venta`, `fecha`, `total`, `id_metodo_pago`, `id_cond_iva`, `id_cliente`, `id_usuario`, `habilitado`) VALUES
	(1, '2024-05-08 07:35:58', NULL, 1, 1, NULL, 1, 1);
/*!40000 ALTER TABLE `factura_venta` ENABLE KEYS */;

-- Volcando estructura para tabla tp_pujol_db.historial_precio
DROP TABLE IF EXISTS `historial_precio`;
CREATE TABLE IF NOT EXISTS `historial_precio` (
  `id_histprecio` int NOT NULL AUTO_INCREMENT,
  `precio` float NOT NULL,
  `fecha_vigencia` datetime DEFAULT CURRENT_TIMESTAMP,
  `id_stock` int NOT NULL,
  PRIMARY KEY (`id_histprecio`),
  KEY `FK_historial_precio_stock_lote` (`id_stock`),
  CONSTRAINT `FK_historial_precio_stock_lote` FOREIGN KEY (`id_stock`) REFERENCES `stock_lote` (`id_stock`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla tp_pujol_db.historial_precio: ~0 rows (aproximadamente)
DELETE FROM `historial_precio`;
/*!40000 ALTER TABLE `historial_precio` DISABLE KEYS */;
INSERT INTO `historial_precio` (`id_histprecio`, `precio`, `fecha_vigencia`, `id_stock`) VALUES
	(1, 2400, '2024-05-07 09:33:09', 1),
	(2, 2500, '2024-05-07 09:33:09', 2),
	(3, 2600, '2024-05-07 09:33:09', 3),
	(4, 2200, '2024-05-07 09:34:42', 4);
/*!40000 ALTER TABLE `historial_precio` ENABLE KEYS */;

-- Volcando estructura para tabla tp_pujol_db.localidad
DROP TABLE IF EXISTS `localidad`;
CREATE TABLE IF NOT EXISTS `localidad` (
  `id_localidad` int NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `codigo_postal` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id_provincia` int NOT NULL,
  PRIMARY KEY (`id_localidad`),
  KEY `id_provincia` (`id_provincia`),
  CONSTRAINT `FK_localidad_provincia` FOREIGN KEY (`id_provincia`) REFERENCES `provincia` (`id_provincia`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla tp_pujol_db.localidad: ~0 rows (aproximadamente)
DELETE FROM `localidad`;
/*!40000 ALTER TABLE `localidad` DISABLE KEYS */;
/*!40000 ALTER TABLE `localidad` ENABLE KEYS */;

-- Volcando estructura para tabla tp_pujol_db.marca
DROP TABLE IF EXISTS `marca`;
CREATE TABLE IF NOT EXISTS `marca` (
  `id_marca` int NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Nueva Marca',
  PRIMARY KEY (`id_marca`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla tp_pujol_db.marca: ~3 rows (aproximadamente)
DELETE FROM `marca`;
/*!40000 ALTER TABLE `marca` DISABLE KEYS */;
INSERT INTO `marca` (`id_marca`, `descripcion`) VALUES
	(1, 'Pepsi'),
	(2, 'Coca-Cola'),
	(3, 'Fanta'),
	(4, 'Cañuelas'),
	(5, 'Vicentin');
/*!40000 ALTER TABLE `marca` ENABLE KEYS */;

-- Volcando estructura para tabla tp_pujol_db.metodo_pago
DROP TABLE IF EXISTS `metodo_pago`;
CREATE TABLE IF NOT EXISTS `metodo_pago` (
  `id_metpago` int NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `habilitado` tinyint(1) NOT NULL,
  PRIMARY KEY (`id_metpago`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla tp_pujol_db.metodo_pago: ~0 rows (aproximadamente)
DELETE FROM `metodo_pago`;
/*!40000 ALTER TABLE `metodo_pago` DISABLE KEYS */;
INSERT INTO `metodo_pago` (`id_metpago`, `descripcion`, `habilitado`) VALUES
	(1, 'Efectivo', 1),
	(2, 'Tarjeta', 2);
/*!40000 ALTER TABLE `metodo_pago` ENABLE KEYS */;

-- Volcando estructura para tabla tp_pujol_db.pais
DROP TABLE IF EXISTS `pais`;
CREATE TABLE IF NOT EXISTS `pais` (
  `id_pais` int NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_pais`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla tp_pujol_db.pais: ~0 rows (aproximadamente)
DELETE FROM `pais`;
/*!40000 ALTER TABLE `pais` DISABLE KEYS */;
/*!40000 ALTER TABLE `pais` ENABLE KEYS */;

-- Volcando estructura para tabla tp_pujol_db.producto
DROP TABLE IF EXISTS `producto`;
CREATE TABLE IF NOT EXISTS `producto` (
  `id_producto` int NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Nuevo Producto',
  `cantidad_minima` float DEFAULT NULL,
  `habilitado` tinyint(1) NOT NULL DEFAULT '1',
  `id_marca` int DEFAULT NULL,
  PRIMARY KEY (`id_producto`),
  KEY `FK_producto_marca` (`id_marca`),
  CONSTRAINT `FK_producto_marca` FOREIGN KEY (`id_marca`) REFERENCES `marca` (`id_marca`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla tp_pujol_db.producto: ~0 rows (aproximadamente)
DELETE FROM `producto`;
/*!40000 ALTER TABLE `producto` DISABLE KEYS */;
INSERT INTO `producto` (`id_producto`, `descripcion`, `cantidad_minima`, `habilitado`, `id_marca`) VALUES
	(1, 'Gaseosa 3L Naranja', NULL, 1, 3),
	(2, 'Gaseosa 3L Manzana', NULL, 1, 3),
	(3, 'Gaseosa 3L Cola', NULL, 1, 2),
	(4, 'Gaseosa 3L Cola', NULL, 1, 1),
	(5, 'Aceite 900 ml', NULL, 1, 4),
	(6, 'Aceite 900 ml', NULL, 1, 5),
	(7, 'Aceite 1,5L', NULL, 1, 4),
	(8, 'Harina 000', NULL, 1, 4),
	(10, 'Aceite 1,5L', NULL, 0, 5);
/*!40000 ALTER TABLE `producto` ENABLE KEYS */;

-- Volcando estructura para tabla tp_pujol_db.promocion
DROP TABLE IF EXISTS `promocion`;
CREATE TABLE IF NOT EXISTS `promocion` (
  `id_promocion` int NOT NULL AUTO_INCREMENT,
  `precio_especial` float NOT NULL,
  `fecha_fin` datetime DEFAULT NULL,
  `fecha_inicio` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `stock_limite` float DEFAULT NULL,
  `stock_temp` float DEFAULT NULL,
  `id_producto` int NOT NULL,
  `habilitado` tinyint(1) NOT NULL DEFAULT '1',
  `id_tipoprom` int NOT NULL,
  PRIMARY KEY (`id_promocion`),
  KEY `FK_promocion_tipo_promocion` (`id_tipoprom`),
  KEY `FK_promocion_producto` (`id_producto`),
  CONSTRAINT `FK_promocion_producto` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `FK_promocion_tipo_promocion` FOREIGN KEY (`id_tipoprom`) REFERENCES `tipo_promocion` (`id_tipoprom`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla tp_pujol_db.promocion: ~0 rows (aproximadamente)
DELETE FROM `promocion`;
/*!40000 ALTER TABLE `promocion` DISABLE KEYS */;
/*!40000 ALTER TABLE `promocion` ENABLE KEYS */;

-- Volcando estructura para tabla tp_pujol_db.proveedor
DROP TABLE IF EXISTS `proveedor`;
CREATE TABLE IF NOT EXISTS `proveedor` (
  `id_proveedor` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `cuil_cuit` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `habilitado` tinyint(1) NOT NULL DEFAULT '1',
  `id_direccion` int DEFAULT NULL,
  PRIMARY KEY (`id_proveedor`),
  KEY `id_direccion` (`id_direccion`),
  CONSTRAINT `FK_proveedor_direccion` FOREIGN KEY (`id_direccion`) REFERENCES `direccion` (`id_direccion`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla tp_pujol_db.proveedor: ~0 rows (aproximadamente)
DELETE FROM `proveedor`;
/*!40000 ALTER TABLE `proveedor` DISABLE KEYS */;
/*!40000 ALTER TABLE `proveedor` ENABLE KEYS */;

-- Volcando estructura para tabla tp_pujol_db.provincia
DROP TABLE IF EXISTS `provincia`;
CREATE TABLE IF NOT EXISTS `provincia` (
  `id_provincia` int NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id_pais` int NOT NULL,
  PRIMARY KEY (`id_provincia`),
  KEY `id_pais` (`id_pais`),
  CONSTRAINT `FK_provincia_provincia` FOREIGN KEY (`id_pais`) REFERENCES `provincia` (`id_provincia`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla tp_pujol_db.provincia: ~0 rows (aproximadamente)
DELETE FROM `provincia`;
/*!40000 ALTER TABLE `provincia` DISABLE KEYS */;
/*!40000 ALTER TABLE `provincia` ENABLE KEYS */;

-- Volcando estructura para tabla tp_pujol_db.roles
DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id_rol` int NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(244) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `codigo` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`id_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla tp_pujol_db.roles: ~2 rows (aproximadamente)
DELETE FROM `roles`;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` (`id_rol`, `descripcion`, `codigo`) VALUES
	(1, 'ADMINISTRADOR DEL SISTEMA', 'ADMIN'),
	(2, 'ENCARGADO DE CAJA', 'CAJERO');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;

-- Volcando estructura para tabla tp_pujol_db.roles_asignados
DROP TABLE IF EXISTS `roles_asignados`;
CREATE TABLE IF NOT EXISTS `roles_asignados` (
  `id_rol_asignado` int NOT NULL AUTO_INCREMENT,
  `id_rol` int NOT NULL,
  `id_usuario` int NOT NULL,
  PRIMARY KEY (`id_rol_asignado`),
  KEY `id_rol` (`id_rol`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `roles_asignados_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `roles_asignados_ibfk_2` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla tp_pujol_db.roles_asignados: ~2 rows (aproximadamente)
DELETE FROM `roles_asignados`;
/*!40000 ALTER TABLE `roles_asignados` DISABLE KEYS */;
INSERT INTO `roles_asignados` (`id_rol_asignado`, `id_rol`, `id_usuario`) VALUES
	(1, 1, 1),
	(2, 2, 1),
	(3, 2, 2);
/*!40000 ALTER TABLE `roles_asignados` ENABLE KEYS */;

-- Volcando estructura para tabla tp_pujol_db.stock_lote
DROP TABLE IF EXISTS `stock_lote`;
CREATE TABLE IF NOT EXISTS `stock_lote` (
  `id_stock` int NOT NULL AUTO_INCREMENT,
  `id_producto` int NOT NULL,
  `cantidad` float NOT NULL,
  `coste` float NOT NULL,
  `fecha_vto` datetime DEFAULT NULL,
  PRIMARY KEY (`id_stock`),
  KEY `FK_stock_lote_producto` (`id_producto`),
  CONSTRAINT `FK_stock_lote_producto` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id_producto`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla tp_pujol_db.stock_lote: ~0 rows (aproximadamente)
DELETE FROM `stock_lote`;
/*!40000 ALTER TABLE `stock_lote` DISABLE KEYS */;
INSERT INTO `stock_lote` (`id_stock`, `id_producto`, `cantidad`, `coste`, `fecha_vto`) VALUES
	(1, 1, 36, 2000, NULL),
	(2, 2, 24, 2000, NULL),
	(3, 3, 24, 2000, NULL),
	(4, 4, 18, 1800, NULL);
/*!40000 ALTER TABLE `stock_lote` ENABLE KEYS */;

-- Volcando estructura para tabla tp_pujol_db.tipo_detalle_venta
DROP TABLE IF EXISTS `tipo_detalle_venta`;
CREATE TABLE IF NOT EXISTS `tipo_detalle_venta` (
  `id_tipo_det_venta` int NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Descripcion Tipo Detalle Venta',
  `codigo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Nuevo Codigo',
  PRIMARY KEY (`id_tipo_det_venta`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla tp_pujol_db.tipo_detalle_venta: ~0 rows (aproximadamente)
DELETE FROM `tipo_detalle_venta`;
/*!40000 ALTER TABLE `tipo_detalle_venta` DISABLE KEYS */;
INSERT INTO `tipo_detalle_venta` (`id_tipo_det_venta`, `descripcion`, `codigo`) VALUES
	(1, 'Venta', 'VENTA'),
	(2, 'Promocion', 'PROM');
/*!40000 ALTER TABLE `tipo_detalle_venta` ENABLE KEYS */;

-- Volcando estructura para tabla tp_pujol_db.tipo_promocion
DROP TABLE IF EXISTS `tipo_promocion`;
CREATE TABLE IF NOT EXISTS `tipo_promocion` (
  `id_tipoprom` int NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Nueva Descripcion',
  `codigo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Nuevo Codigo',
  PRIMARY KEY (`id_tipoprom`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla tp_pujol_db.tipo_promocion: ~0 rows (aproximadamente)
DELETE FROM `tipo_promocion`;
/*!40000 ALTER TABLE `tipo_promocion` DISABLE KEYS */;
INSERT INTO `tipo_promocion` (`id_tipoprom`, `descripcion`, `codigo`) VALUES
	(1, '2 x 1', '2X1');
/*!40000 ALTER TABLE `tipo_promocion` ENABLE KEYS */;

-- Volcando estructura para tabla tp_pujol_db.tokens_de_sesion
DROP TABLE IF EXISTS `tokens_de_sesion`;
CREATE TABLE IF NOT EXISTS `tokens_de_sesion` (
  `id_token` int NOT NULL AUTO_INCREMENT,
  `token` varchar(244) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `id_usuario` int NOT NULL,
  `habilitado` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_token`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `tokens_de_sesion_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla tp_pujol_db.tokens_de_sesion: ~69 rows (aproximadamente)
DELETE FROM `tokens_de_sesion`;
/*!40000 ALTER TABLE `tokens_de_sesion` DISABLE KEYS */;
INSERT INTO `tokens_de_sesion` (`id_token`, `token`, `id_usuario`, `habilitado`) VALUES
	(1, 'cec4704f0776b2a30efc57ddde6c408c', 1, 0),
	(2, 'e2e8852eec2757e7dc147dc7c578fbfe', 1, 0),
	(3, '44ba454353b51ee080205a927db1d417', 1, 0),
	(4, '67e81801cb114e59deefd667e7d252a8', 1, 0),
	(5, 'de293c5847ebece54dd68ee3650b3020', 1, 0),
	(6, '9478ea877acff6181a566d6c3aeb0b1c', 1, 0),
	(7, 'c20221dfcad293802a76e80fa056f318', 1, 0),
	(8, 'd8c58c2ddd49f377d41ee0cb92da77c2', 1, 0),
	(9, '5cacd4b149498809e17dd4ce1744c59b', 1, 0),
	(10, '46b4e3982b55975be4e390720e695603', 1, 0),
	(11, '89ebb84a0a228dcf278848bbc4dbb419', 1, 0),
	(12, '885a74a9752d4edd171c3153a5bcfb82', 1, 0),
	(13, 'f7f125cae7f19338bf2b12fa842ce0ea', 1, 0),
	(14, '20784af1ddb9e6e68f26eca978b52845', 1, 0),
	(15, '93d825938eb594d0632473a5a1c9c15c', 1, 0),
	(16, 'a9e39bd796426dba807e0f954c84b3ee', 1, 0),
	(17, '41814212c3c578b404f6f37d24b71c3c', 1, 0),
	(18, '279a8ad069483076fdb6a63c21a72131', 1, 0),
	(19, 'd5324e8a8b89b4717e9146aa49c6c967', 1, 0),
	(20, '235236aad2875143a7e6caa4bdb37969', 1, 0),
	(21, 'b35fce7d4712cef4aab47e25e4c1150d', 1, 0),
	(22, '5630aa4b3ff79ff1a239626b1a32431d', 1, 0),
	(23, 'bce984789340f713d911b387cb42ff48', 1, 0),
	(24, '7bfa2af75505dfc11c424bb2774da8db', 1, 0),
	(25, 'f8c917b5597a3da9166f11a574c003e8', 1, 0),
	(26, '1a3fb5440c1a6ba2b51c08a79324ae23', 1, 0),
	(27, 'c1b2f16f52eb03c67ff5301be5f11ba7', 1, 0),
	(28, 'bba3d27def220f275a75d577f8f19638', 1, 0),
	(29, 'd2923d83bb4c8bc0a5460b6f97913bb9', 1, 0),
	(30, '6fd751495ea895237951368df51e8c11', 1, 0),
	(31, 'e8d4d2a08ce509af2aad883800d9614a', 1, 0),
	(32, 'ebcbb3232286317b5496d30b3041f4fc', 1, 0),
	(33, 'f16ebee558b7c3fec77b532e34994a5e', 1, 0),
	(34, 'eeb0540f6cea9c435f8539148327ae50', 1, 0),
	(35, '10d1a6e68865918867d1c41123fee1bb', 1, 0),
	(36, '07c59403d06d0e645d522ac086d008bf', 1, 0),
	(37, 'a2c36fa15932b9213e2069f0bc189258', 1, 0),
	(38, '2d129e79b326d372905a897bdbbb43d2', 1, 0),
	(39, 'a1e006d30e1d85f22a64fa748ef41d99', 1, 0),
	(40, '50f3b39b5480184d2829c176477708c5', 1, 0),
	(41, '1d7fd7b92a01717bf29cdaea2d8b20a9', 1, 0),
	(42, '4e895aaf6e7b24ec4603b31f45ed8139', 1, 0),
	(43, 'c6ea5ed7abb791c8d1315b45535d3583', 1, 0),
	(44, '2051dca57577ffb621457f4985a04d68', 1, 0),
	(45, 'ed390adcdd4c27020af3adb086a713e3', 1, 0),
	(46, 'd722cf3793e9f9aa4f7f13d135b88493', 1, 0),
	(47, '8bec6d84e513262b400e4ad5858c5e34', 1, 0),
	(48, '1e1bf74714dc237e24401d05a1481f0f', 1, 0),
	(49, '398db73d99b02b86c49300d40aba4729', 1, 0),
	(50, '6833c56747b33197801d1faa82be8489', 1, 0),
	(51, '8832a09b60ae18b6887ab673f5df306a', 1, 0),
	(52, '08438cbbf491915bee87358c93fc786d', 1, 0),
	(53, '1618f66d8c2ba2131dd33c398856af91', 1, 0),
	(54, '537e0541006e32a3b6596cf8f16df80f', 1, 0),
	(55, 'e694942c459a2f3a841f8032d3d38c4d', 1, 0),
	(56, 'def8ff620d3700dd970c6c413c9c7eac', 1, 0),
	(57, 'c5652add1aa8f725e349cd6393f8fc7b', 1, 0),
	(58, 'fa353ef683fcd98170a9d4e64806a9fe', 1, 0),
	(59, '15e47cdb09a959f8c1ef7db71ae2a90f', 1, 0),
	(60, '57a22b3a6b74b496ad6f8910e8062e28', 1, 0),
	(61, '67c9f25ef51487051852743feeba2f1f', 1, 0),
	(62, '8ed8ba8bfd86524710cc88300eea7e09', 1, 0),
	(63, 'c82cdfec37f9e60f3a2fa7369ef30cb4', 1, 0),
	(64, 'be54646dfe1401113112e845049d950c', 1, 0),
	(65, 'bdb4eaf2b183550a0b8d6605d75af630', 1, 0),
	(66, '0c3b0a0e37d9011e357eadb431fb16bb', 1, 0),
	(67, 'acfc88e2a3c088f488646a41e335af00', 1, 0),
	(68, 'bf323e78e535494ecf2086b7eb74d127', 1, 0),
	(69, '27c74ab88e6d802d57d853ae80681ef2', 1, 0),
	(70, '669ae725e55dcab582bad1505268092f', 1, 0),
	(71, 'ca060c054fb2d5ef200221ad0b193114', 1, 0),
	(72, 'b3a58e9fe4c023d80a0c2219f073b2d0', 1, 0),
	(73, '327eabe2d409d93abb2ee642fbce45b3', 1, 0),
	(74, '73b6477a2020195f784ad98ff41a1462', 1, 0),
	(75, '781df4e059d6c1b5c9403e182d66de5d', 1, 0),
	(76, 'eaac879b65dab86134d7d09b60153fae', 1, 1);
/*!40000 ALTER TABLE `tokens_de_sesion` ENABLE KEYS */;

-- Volcando estructura para tabla tp_pujol_db.usuarios
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `password` varchar(244) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `nombre` varchar(244) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `apellido` varchar(244) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `palabra_secreta` varchar(244) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `habilitado` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcando datos para la tabla tp_pujol_db.usuarios: ~0 rows (aproximadamente)
DELETE FROM `usuarios`;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` (`id_usuario`, `username`, `password`, `email`, `nombre`, `apellido`, `palabra_secreta`, `habilitado`) VALUES
	(1, 'Administrador', 'admin', 'admin@admin.com.ar', 'Administrador', 'Administrador', 'Pure de papas', 1),
	(2, 'Eric123', 'Eric1234', 'eric@cajero.com.ar', 'Eric', 'Ferrer', 'Perfume Chino', 1);
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
