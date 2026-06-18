-- MySQL dump 10.13  Distrib 8.0.46, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: tallertec
-- ------------------------------------------------------
-- Server version	8.0.46

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clientes` (
  `id_cliente` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` text,
  `tipo` varchar(20) DEFAULT 'particular',
  PRIMARY KEY (`id_cliente`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT INTO `clientes` VALUES (1,'María González','maria@email.com','3112223344',NULL,'particular'),(2,'Empresa Tech SAS','info@techsas.com','6015556677',NULL,'empresa'),(3,'Luis Fernández','luis@email.com','3004445566',NULL,'particular'),(5,'jesusdd','dfdfd@gmail.com','3434434455','edd33344','particular');
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `equipos`
--

DROP TABLE IF EXISTS `equipos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `equipos` (
  `id_equipo` int NOT NULL AUTO_INCREMENT,
  `id_cliente` int NOT NULL,
  `marca` varchar(50) DEFAULT NULL,
  `modelo` varchar(50) DEFAULT NULL,
  `serial` varchar(50) DEFAULT NULL,
  `tipo` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id_equipo`),
  UNIQUE KEY `serial` (`serial`),
  KEY `id_cliente` (`id_cliente`),
  CONSTRAINT `equipos_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `equipos`
--

LOCK TABLES `equipos` WRITE;
/*!40000 ALTER TABLE `equipos` DISABLE KEYS */;
INSERT INTO `equipos` VALUES (1,1,'Apple','iPhone 13','SN-IP13-001','celular'),(2,1,'LG','Smart TV 55','SN-LG55-002','electrodomestico'),(3,2,'HP','Pavilion 14','SN-HP14-003','computador'),(4,2,'Xiaomi','Redmi Note 10','SN-XRN10-004','celular'),(5,3,'Samsung','Galaxy Tab S7','SN-SGT7-005','tablet'),(6,1,'infinix','nbmntr','fgfg--gyg','celular'),(7,5,'infinix','x3gt','33322','celular');
/*!40000 ALTER TABLE `equipos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orden_repuestos`
--

DROP TABLE IF EXISTS `orden_repuestos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orden_repuestos` (
  `id_orden` int NOT NULL,
  `id_repuesto` int NOT NULL,
  `cantidad` int DEFAULT '1',
  PRIMARY KEY (`id_orden`,`id_repuesto`),
  KEY `id_repuesto` (`id_repuesto`),
  CONSTRAINT `orden_repuestos_ibfk_1` FOREIGN KEY (`id_orden`) REFERENCES `ordenes_servicio` (`id_orden`) ON DELETE CASCADE,
  CONSTRAINT `orden_repuestos_ibfk_2` FOREIGN KEY (`id_repuesto`) REFERENCES `repuestos` (`id_repuesto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orden_repuestos`
--

LOCK TABLES `orden_repuestos` WRITE;
/*!40000 ALTER TABLE `orden_repuestos` DISABLE KEYS */;
INSERT INTO `orden_repuestos` VALUES (1,2,1),(4,1,1),(4,5,1),(7,1,1),(8,2,1),(9,4,1);
/*!40000 ALTER TABLE `orden_repuestos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ordenes_servicio`
--

DROP TABLE IF EXISTS `ordenes_servicio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ordenes_servicio` (
  `id_orden` int NOT NULL AUTO_INCREMENT,
  `id_equipo` int NOT NULL,
  `id_tecnico` int NOT NULL,
  `fecha_recepcion` datetime DEFAULT CURRENT_TIMESTAMP,
  `sintoma` text,
  `estado` enum('en_diagnostico','en_espera_repuestos','en_reparacion','pendiente','entregado') DEFAULT 'en_diagnostico',
  `mano_obra` decimal(10,2) DEFAULT '0.00',
  `total` decimal(10,2) DEFAULT '0.00',
  `fecha_entrega` datetime DEFAULT NULL,
  PRIMARY KEY (`id_orden`),
  KEY `id_equipo` (`id_equipo`),
  KEY `id_tecnico` (`id_tecnico`),
  CONSTRAINT `ordenes_servicio_ibfk_1` FOREIGN KEY (`id_equipo`) REFERENCES `equipos` (`id_equipo`),
  CONSTRAINT `ordenes_servicio_ibfk_2` FOREIGN KEY (`id_tecnico`) REFERENCES `tecnicos` (`id_tecnico`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ordenes_servicio`
--

LOCK TABLES `ordenes_servicio` WRITE;
/*!40000 ALTER TABLE `ordenes_servicio` DISABLE KEYS */;
INSERT INTO `ordenes_servicio` VALUES (1,1,1,'2026-06-17 15:48:37','Pantalla rota, no enciende','entregado',40000.00,125000.00,NULL),(2,2,3,'2026-06-17 15:48:37','No enciende, hace ruido','entregado',60000.00,60000.00,'2026-06-13 16:45:00'),(3,3,2,'2026-06-17 15:48:37','No carga, se apaga solo','pendiente',35000.00,35000.00,NULL),(4,4,1,'2026-06-17 15:48:37','No carga, no enciende','en_reparacion',65000.00,235000.00,NULL),(5,5,1,'2026-06-17 15:48:37','No enciende, no carga','entregado',40000.00,35000.00,'2026-06-14 10:00:00'),(6,1,4,'2026-06-10 09:00:00','Batería dañada, no retiene carga','entregado',35000.00,235000.00,'2026-06-15 14:30:00'),(7,1,4,'2026-06-10 09:00:00','Batería dañada, no retiene carga','entregado',35000.00,120000.00,'2026-06-16 11:00:00'),(8,4,1,'2026-06-12 15:00:00','Pantalla rota, no responde','entregado',40000.00,160000.00,'2026-06-17 09:30:00'),(9,3,2,'2026-06-11 11:30:00','Disco duro dañado, no arranca','entregado',50000.00,145000.00,'2026-06-15 17:00:00'),(15,7,1,'2026-06-18 09:29:39','bateria','en_diagnostico',20000.00,20000.00,NULL);
/*!40000 ALTER TABLE `ordenes_servicio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `repuestos`
--

DROP TABLE IF EXISTS `repuestos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `repuestos` (
  `id_repuesto` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `precio_unitario` decimal(10,2) DEFAULT '0.00',
  `stock` int DEFAULT '0',
  PRIMARY KEY (`id_repuesto`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `repuestos`
--

LOCK TABLES `repuestos` WRITE;
/*!40000 ALTER TABLE `repuestos` DISABLE KEYS */;
INSERT INTO `repuestos` VALUES (1,'Batería iPhone 13',85000.00,8),(2,'Pantalla iPhone 13',120000.00,5),(3,'Cargador LG TV',45000.00,10),(4,'Disco SSD 256GB',95000.00,6),(5,'Batería Xiaomi RN10',60000.00,4),(6,'Teclado HP Pavilion',35000.00,3),(7,'Cámara Samsung Tab S7',70000.00,2),(9,'pantalla infinix',60000.00,3),(13,'gggtt',30000.00,2);
/*!40000 ALTER TABLE `repuestos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tecnicos`
--

DROP TABLE IF EXISTS `tecnicos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tecnicos` (
  `id_tecnico` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `especialidad` varchar(100) DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `telefono` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id_tecnico`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tecnicos`
--

LOCK TABLES `tecnicos` WRITE;
/*!40000 ALTER TABLE `tecnicos` DISABLE KEYS */;
INSERT INTO `tecnicos` VALUES (1,'Carlos Pérez','Celulares/Tablets','activo',NULL),(2,'Ana Rodríguez','Computadores','activo',NULL),(3,'Juan Martínez','Electrodomésticos','activo',NULL),(4,'Laura Gómez','Celulares/Tablets','activo',NULL);
/*!40000 ALTER TABLE `tecnicos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `rol` varchar(20) DEFAULT 'admin',
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'admin','0192023a7bbd73250516f069df18b500','Administrador','admin');
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

-- Dump completed on 2026-06-18 10:02:49
