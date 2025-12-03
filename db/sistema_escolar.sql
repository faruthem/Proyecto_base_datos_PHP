-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: sistema_escolar
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

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
-- Table structure for table `Cursos`
--

DROP TABLE IF EXISTS `Cursos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Cursos` (
  `ID_curso` int(11) NOT NULL AUTO_INCREMENT,
  `ID_UDA` int(11) NOT NULL,
  `ID_profesor` int(11) NOT NULL,
  `Aula` varchar(10) NOT NULL,
  `Horario` varchar(100) DEFAULT NULL,
  `Cupo` int(11) DEFAULT 30,
  PRIMARY KEY (`ID_curso`),
  KEY `ID_UDA` (`ID_UDA`),
  KEY `ID_profesor` (`ID_profesor`),
  CONSTRAINT `Cursos_ibfk_1` FOREIGN KEY (`ID_UDA`) REFERENCES `UDAS` (`ID_UDAS`),
  CONSTRAINT `Cursos_ibfk_2` FOREIGN KEY (`ID_profesor`) REFERENCES `Profesores` (`ID_profesores`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Cursos`
--

LOCK TABLES `Cursos` WRITE;
/*!40000 ALTER TABLE `Cursos` DISABLE KEYS */;
INSERT INTO `Cursos` VALUES (1,3,3,'Lab-Elec','Martes y Jueves 08:00-10:00',30),(2,8,6,'Lab-Vision','Lunes y Miércoles 08:00-10:00',30),(3,2,2,'A-201','Lunes y Miércoles 10:00-12:00',30),(4,9,7,'C-Centro','Martes y Jueves 10:00-12:00',30),(5,10,8,'Auditorio','Viernes 12:00-14:00',30),(6,5,5,'Idiomas-1','Lunes y Miércoles 14:00-16:00',30),(7,6,5,'Idiomas-1','Lunes y Miércoles 16:00-18:00',30),(8,7,5,'Idiomas-2','Martes y Jueves 16:00-18:00',30),(9,4,4,'A-305','Martes y Jueves 18:00-20:00',30);
/*!40000 ALTER TABLE `Cursos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Estudiante`
--

DROP TABLE IF EXISTS `Estudiante`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Estudiante` (
  `ID_estudiante` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(100) NOT NULL,
  `A_Paterno` varchar(100) NOT NULL,
  `A_Materno` varchar(100) DEFAULT NULL,
  `Carrera` varchar(100) NOT NULL,
  `promedio` float DEFAULT 0,
  `NUA` int(6) unsigned zerofill NOT NULL COMMENT 'NUA de 6 dígitos',
  PRIMARY KEY (`ID_estudiante`),
  UNIQUE KEY `NUA` (`NUA`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Estudiante`
--

LOCK TABLES `Estudiante` WRITE;
/*!40000 ALTER TABLE `Estudiante` DISABLE KEYS */;
INSERT INTO `Estudiante` VALUES (1,'Juan','Perez','Lopez','Sistemas',0,755579),(2,'Juanito','Porter','Botello','Sistemas Computacionales',0,887766),(3,'Kevin','López','Guzmán','Sistemas Computacionales',0,755578);
/*!40000 ALTER TABLE `Estudiante` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Inscripciones`
--

DROP TABLE IF EXISTS `Inscripciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Inscripciones` (
  `ID_inscripciones` int(11) NOT NULL AUTO_INCREMENT,
  `ID_estudiante` int(11) NOT NULL,
  `ID_curso` int(11) NOT NULL,
  PRIMARY KEY (`ID_inscripciones`),
  KEY `ID_estudiante` (`ID_estudiante`),
  KEY `fk_inscripciones_curso` (`ID_curso`),
  CONSTRAINT `Inscripciones_ibfk_1` FOREIGN KEY (`ID_estudiante`) REFERENCES `Estudiante` (`ID_estudiante`),
  CONSTRAINT `fk_inscripciones_curso` FOREIGN KEY (`ID_curso`) REFERENCES `Cursos` (`ID_curso`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Inscripciones`
--

LOCK TABLES `Inscripciones` WRITE;
/*!40000 ALTER TABLE `Inscripciones` DISABLE KEYS */;
INSERT INTO `Inscripciones` VALUES (8,3,2),(17,3,3),(18,3,4),(19,3,5),(20,3,6);
/*!40000 ALTER TABLE `Inscripciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Profesores`
--

DROP TABLE IF EXISTS `Profesores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Profesores` (
  `ID_profesores` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(100) NOT NULL,
  `A_paterno` varchar(100) NOT NULL,
  `A_materno` varchar(100) DEFAULT NULL,
  `NUE` int(6) unsigned zerofill NOT NULL COMMENT 'NUE de 6 dígitos',
  `Puesto` varchar(100) DEFAULT NULL,
  `cubiculo` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`ID_profesores`),
  UNIQUE KEY `NUE` (`NUE`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Profesores`
--

LOCK TABLES `Profesores` WRITE;
/*!40000 ALTER TABLE `Profesores` DISABLE KEYS */;
INSERT INTO `Profesores` VALUES (1,'Luis David','López','Bedolla',900001,'Docente','A-101'),(2,'Carlos','Rodríguez','Doñate',100001,'Director','1700'),(3,'Adán','Flores','Balderas',100002,'Coord. Ing. Com. y Elec.','1702'),(4,'Ernesto Isaac','Tlapanco','Ríos',100003,'Coord. Gestión Empresarial','1748'),(5,'Marcelina','Pantoja','Flores',100004,'Coord. Enseñanza Inglés','1749'),(6,'Luis Manuel','Ledesma','Carrillo',100005,'Coord. Posgrado','1756'),(7,'Juan Manuel','López','Hernández',100006,'Coord. Sist. Comp.','1746'),(8,'Everardo','Vargas','Rodríguez',100007,'Coord. Automatización','1716');
/*!40000 ALTER TABLE `Profesores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `UDAS`
--

DROP TABLE IF EXISTS `UDAS`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `UDAS` (
  `ID_UDAS` int(11) NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(200) NOT NULL,
  `Horario` varchar(100) DEFAULT NULL COMMENT 'Ej: Lunes 10-12, Martes 14-16',
  `Carrera` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`ID_UDAS`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `UDAS`
--

LOCK TABLES `UDAS` WRITE;
/*!40000 ALTER TABLE `UDAS` DISABLE KEYS */;
INSERT INTO `UDAS` VALUES (1,'Introducción a la vida universitaria','Jueves 09:00-11:00','Prueba'),(2,'Control',NULL,'Sistemas Computacionales'),(3,'Electrónica Aplicada',NULL,'Sistemas Computacionales'),(4,'Proyectos Administrativos',NULL,'Sistemas Computacionales'),(5,'Inglés I',NULL,'Sistemas Computacionales'),(6,'Inglés II',NULL,'Sistemas Computacionales'),(7,'Inglés III',NULL,'Sistemas Computacionales'),(8,'Tópicos de Visión Artificial',NULL,'Sistemas Computacionales'),(9,'Sistemas de Información',NULL,'Sistemas Computacionales'),(10,'Introducción a la vida en Yuriria',NULL,'Sistemas Computacionales');
/*!40000 ALTER TABLE `UDAS` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Usuarios`
--

DROP TABLE IF EXISTS `Usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Usuarios` (
  `ID_user` int(11) NOT NULL AUTO_INCREMENT,
  `User` varchar(50) NOT NULL COMMENT 'Nombre de usuario para login',
  `password` varchar(255) NOT NULL COMMENT 'JAMÁS guardar texto plano. Solo HASH.',
  `ID_Estudiante` int(11) NOT NULL COMMENT 'Un usuario por estudiante',
  PRIMARY KEY (`ID_user`),
  UNIQUE KEY `User` (`User`),
  UNIQUE KEY `ID_Estudiante` (`ID_Estudiante`),
  CONSTRAINT `Usuarios_ibfk_1` FOREIGN KEY (`ID_Estudiante`) REFERENCES `Estudiante` (`ID_estudiante`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Usuarios`
--

LOCK TABLES `Usuarios` WRITE;
/*!40000 ALTER TABLE `Usuarios` DISABLE KEYS */;
INSERT INTO `Usuarios` VALUES (1,'jperez','$2y$10$8CD2ZtPkLbJJPYetDoimEe4rrbdzO0XeO2KHLc/zWVM43JL.ECEly',1),(2,'JPorterB','$2y$10$H6P2g0A7f3wU6tZ3A2AUmuuPhfBI0wWYOk4zXklqjQW5Z9F2KVluK',2),(3,'Farithem','$2y$10$iqeqM6Ktfmp8up9AbRQKtuHLq8hXjHJr3tgYRx4oiwsZ5vi213hHK',3);
/*!40000 ALTER TABLE `Usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-02 18:06:04
