-- MariaDB dump 10.19  Distrib 10.4.24-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: tp_comanda
-- ------------------------------------------------------
-- Server version	10.4.24-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `encuestas`
--

DROP TABLE IF EXISTS `encuestas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `encuestas` (
  `idEncuesta` int(11) NOT NULL AUTO_INCREMENT,
  `idPedido` int(11) NOT NULL,
  `puntajeMesa` int(11) NOT NULL,
  `puntajeRestaurante` int(11) NOT NULL,
  `puntajeMozo` int(11) NOT NULL,
  `puntajeCocinero` int(11) NOT NULL,
  `promedio` int(11) NOT NULL,
  `comentarios` varchar(66) NOT NULL,
  PRIMARY KEY (`idEncuesta`),
  KEY `idPedido` (`idPedido`),
  CONSTRAINT `encuestas_ibfk_1` FOREIGN KEY (`idPedido`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `encuestas`
--

LOCK TABLES `encuestas` WRITE;
/*!40000 ALTER TABLE `encuestas` DISABLE KEYS */;
INSERT INTO `encuestas` VALUES (1,1,5,8,9,10,8,'Excelente atencion'),(2,2,7,7,7,7,7,'A la bebida le falto azucar'),(3,3,5,8,10,9,8,'La comida estaba muy rica');
/*!40000 ALTER TABLE `encuestas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estadosmesas`
--

DROP TABLE IF EXISTS `estadosmesas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `estadosmesas` (
  `idEstadoMesa` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(50) NOT NULL,
  PRIMARY KEY (`idEstadoMesa`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estadosmesas`
--

LOCK TABLES `estadosmesas` WRITE;
/*!40000 ALTER TABLE `estadosmesas` DISABLE KEYS */;
INSERT INTO `estadosmesas` VALUES (1,'Cliente esperando pedido'),(2,'Cliente comiendo'),(3,'Cliente pagando'),(4,'Cerrada');
/*!40000 ALTER TABLE `estadosmesas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estadospedidos`
--

DROP TABLE IF EXISTS `estadospedidos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `estadospedidos` (
  `idEstadoPedido` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(50) NOT NULL,
  PRIMARY KEY (`idEstadoPedido`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estadospedidos`
--

LOCK TABLES `estadospedidos` WRITE;
/*!40000 ALTER TABLE `estadospedidos` DISABLE KEYS */;
INSERT INTO `estadospedidos` VALUES (1,'Pendiente'),(2,'En preparacion'),(3,'Listo para servir'),(4,'Entregado'),(5,'Cancelado');
/*!40000 ALTER TABLE `estadospedidos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mesas`
--

DROP TABLE IF EXISTS `mesas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mesas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(5) NOT NULL,
  `idEstado` int(11) NOT NULL,
  `importeAcumulado` float DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idEstado` (`idEstado`),
  CONSTRAINT `mesas_ibfk_1` FOREIGN KEY (`idEstado`) REFERENCES `estadosmesas` (`idEstadoMesa`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mesas`
--

LOCK TABLES `mesas` WRITE;
/*!40000 ALTER TABLE `mesas` DISABLE KEYS */;
INSERT INTO `mesas` VALUES (1,'MESA1',1,9250),(2,'MESA2',4,12000),(3,'MESA3',4,11000),(5,'MESA4',4,NULL);
/*!40000 ALTER TABLE `mesas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `operaciones`
--

DROP TABLE IF EXISTS `operaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `operaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idUsuario` int(11) NOT NULL,
  `idSector` int(11) NOT NULL,
  `descripcion` varchar(100) NOT NULL,
  `fecha` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idUsuario` (`idUsuario`),
  KEY `idSector` (`idSector`),
  CONSTRAINT `operaciones_ibfk_1` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`idUsuario`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `operaciones_ibfk_2` FOREIGN KEY (`idSector`) REFERENCES `sectores` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `operaciones`
--

LOCK TABLES `operaciones` WRITE;
/*!40000 ALTER TABLE `operaciones` DISABLE KEYS */;
INSERT INTO `operaciones` VALUES (1,1,1,'Listar usuario','2022-07-11'),(2,1,1,'Se modifico usuario','2022-07-11'),(3,1,1,'Descargar usuarios en CSV','2022-07-11'),(4,1,1,'Cargar usuarios desde CSV','2022-07-11');
/*!40000 ALTER TABLE `operaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pedidos`
--

DROP TABLE IF EXISTS `pedidos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idUsuario` int(11) NOT NULL,
  `idMesa` int(11) NOT NULL,
  `idEstado` int(11) NOT NULL,
  `nombre_cliente` varchar(50) NOT NULL,
  `demoraEstimada` int(11) DEFAULT NULL,
  `demoraFinal` int(11) DEFAULT NULL,
  `ruta_imagen` varchar(150) DEFAULT NULL,
  `importe` float DEFAULT NULL,
  `fecha` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idUsuario` (`idUsuario`),
  KEY `idMesa` (`idMesa`),
  KEY `idEstado` (`idEstado`),
  CONSTRAINT `pedidos_ibfk_5` FOREIGN KEY (`idMesa`) REFERENCES `mesas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `pedidos_ibfk_7` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`idUsuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `pedidos_ibfk_8` FOREIGN KEY (`idEstado`) REFERENCES `estadospedidos` (`idEstadoPedido`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedidos`
--

LOCK TABLES `pedidos` WRITE;
/*!40000 ALTER TABLE `pedidos` DISABLE KEYS */;
INSERT INTO `pedidos` VALUES (1,3,1,4,'Juan',28,25,'Juan_1_2022-07-04.png',9250,'2022-07-04'),(2,11,2,4,'Maria',16,25,NULL,12000,'2022-07-04'),(3,11,3,4,'Pepe',22,20,NULL,11000,'2022-07-04'),(4,11,1,1,'Andrea',NULL,NULL,NULL,11000,'2022-07-04');
/*!40000 ALTER TABLE `pedidos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `perfiles`
--

DROP TABLE IF EXISTS `perfiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `perfiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(25) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `perfiles`
--

LOCK TABLES `perfiles` WRITE;
/*!40000 ALTER TABLE `perfiles` DISABLE KEYS */;
INSERT INTO `perfiles` VALUES (1,'Administrador'),(2,'Socio'),(3,'Mozo'),(4,'Cocinero'),(5,'Bartender'),(6,'Cervecero');
/*!40000 ALTER TABLE `perfiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `productos`
--

DROP TABLE IF EXISTS `productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `productos` (
  `idProducto` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) NOT NULL,
  `precio` float NOT NULL,
  `idSector` int(11) NOT NULL,
  PRIMARY KEY (`idProducto`),
  KEY `idSector` (`idSector`),
  CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`idSector`) REFERENCES `sectores` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos`
--

LOCK TABLES `productos` WRITE;
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
INSERT INTO `productos` VALUES (1,'tiramisu',2000,6),(3,'milanesa a caballo',2350,3),(4,'daikiri',1500,4),(6,'hamburguesa de garbanzo',1200,3),(7,'corona',700,5),(8,'empanada de queso',500,3);
/*!40000 ALTER TABLE `productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `productospedidos`
--

DROP TABLE IF EXISTS `productospedidos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `productospedidos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idPedido` int(11) NOT NULL,
  `idProducto` int(11) NOT NULL,
  `idUsuario` int(11) NOT NULL,
  `idEstadoPedido` int(11) NOT NULL DEFAULT 1,
  `cantidad` int(11) NOT NULL,
  `subtotal` float NOT NULL,
  `demora` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idPedido` (`idPedido`,`idProducto`,`idUsuario`),
  KEY `idEstadoPedido` (`idEstadoPedido`),
  KEY `idProducto` (`idProducto`),
  KEY `idUsuario` (`idUsuario`),
  CONSTRAINT `productospedidos_ibfk_1` FOREIGN KEY (`idEstadoPedido`) REFERENCES `estadospedidos` (`idEstadoPedido`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `productospedidos_ibfk_2` FOREIGN KEY (`idProducto`) REFERENCES `productos` (`idProducto`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `productospedidos_ibfk_4` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`idUsuario`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `productospedidos_ibfk_5` FOREIGN KEY (`idPedido`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productospedidos`
--

LOCK TABLES `productospedidos` WRITE;
/*!40000 ALTER TABLE `productospedidos` DISABLE KEYS */;
INSERT INTO `productospedidos` VALUES (1,1,3,2,4,1,2350,11),(2,1,7,7,4,3,2100,24),(3,1,6,8,4,4,4800,28),(4,2,8,2,4,12,6000,16),(5,2,1,8,4,3,6000,11),(6,3,8,2,4,10,5000,8),(7,3,1,8,4,3,6000,22),(8,4,8,2,1,10,5000,NULL),(9,4,1,8,1,3,6000,NULL);
/*!40000 ALTER TABLE `productospedidos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sectores`
--

DROP TABLE IF EXISTS `sectores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sectores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(25) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sectores`
--

LOCK TABLES `sectores` WRITE;
/*!40000 ALTER TABLE `sectores` DISABLE KEYS */;
INSERT INTO `sectores` VALUES (1,'Administracion'),(2,'Salon'),(3,'Cocina'),(4,'Barra de tragos'),(5,'Barra de choperas'),(6,'Candy bar');
/*!40000 ALTER TABLE `sectores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `idUsuario` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(100) NOT NULL,
  `clave` varchar(500) NOT NULL,
  `idPerfil` int(11) NOT NULL,
  `idSector` int(11) NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `fechaIngreso` timestamp NOT NULL DEFAULT current_timestamp(),
  `fechaBaja` date DEFAULT NULL,
  PRIMARY KEY (`idUsuario`),
  KEY `idPerfil` (`idPerfil`,`idSector`),
  KEY `idSector` (`idSector`),
  CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`idPerfil`) REFERENCES `perfiles` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `usuarios_ibfk_2` FOREIGN KEY (`idSector`) REFERENCES `sectores` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'jorge','$2y$10$vKzU17IcrT43lcyC/j1woePT/LnfZL8UJBYE.RXTT0r0AYGAMI3i6',1,1,1,'2022-06-11 08:25:16',NULL),(2,'cocinero1','$2y$10$WlvVhl80eXp6ZYUC40Gm1u1YKuoXQc98eXJr0c2Yk/mJn/7wW8oPW',4,3,1,'2022-06-11 18:09:25',NULL),(3,'mozo1','$2y$10$b.71IfHPZ1tr1iqzNAcLXuUc/6D.U1FkEHF.cuKt7biY7puQHChDe',3,2,1,'2022-06-11 17:34:09',NULL),(4,'bartender1','$2y$10$I4u.KdvTAns738IVzEUvXOLgNYfZCxMi2F4t6atth3KekT8sbJHtm',5,4,1,'2022-06-11 03:00:00',NULL),(6,'socio1','$2y$10$Q15VgpJ/qrwGNqrvznaQTedaJMRnDICUvQuyH..xwITrQFnYtrygi',2,1,1,'2022-06-12 02:31:48',NULL),(7,'cervecero1','$2y$10$SNJSv592MtX8eYuhH0bsIueC7w/QZVsU.dD0ZeoSLEcI3AzsO94MG',6,5,0,'2022-06-11 21:33:15','2022-07-03'),(8,'cocinero2','$2y$10$oH/tHp8EZAWQZ6a34NiZO.sa9/.v2tRuPuzRUDGsSO6fpPCFnOrCq',4,6,1,'2022-06-14 03:20:37',NULL),(9,'mozo2','$2y$10$lYBHaCXaXyNW6mf.rcUqLO9i/zRiLuEUMPrQk0wTFmz7tpIEVehLi',3,2,1,'2022-07-03 04:38:28',NULL),(11,'mozo3','$2y$10$nvBg3bDcavEcGj.rgdghH..fj53XmDYRiQ8Fjdj0Kc36GNHLTMh1y',3,2,1,'2022-07-03 04:43:02',NULL),(13,'pepe','$2y$10$L1I84NH4pyZaxIEO/7rNh.RAPTPQTcii0EwS/50GoLicji4sr61hi',6,5,1,'2022-07-03 05:04:49',NULL);
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-07-21 23:26:37
