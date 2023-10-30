-- MySQL dump 10.13  Distrib 8.1.0, for Linux (x86_64)
--
-- Host: localhost    Database: wanderlust
-- ------------------------------------------------------
-- Server version	8.1.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(225) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'ns@gmail.com','hfceflwefmew','2023-09-17 15:29:46'),(2,'hdcsde@sdcksd.com','jdcnkdjed','2023-09-17 15:35:05'),(3,'a@a.com','aaaaaaaa','2023-09-18 13:49:20'),(4,'a@a.com','aassaass','2023-09-18 13:54:19'),(5,'a@a.net','aaaaaaaa','2023-09-18 13:55:10'),(6,'abc@abc.abc','rjvMEs7N4gv3E5brnqA3vw==:b784cd7134e9e6452617960d1a4de2c8c825c1ade1ca5f5ef3b92828df1384cf','2023-09-18 14:19:12'),(7,'abc@a.a','nwoimcU/y9j9AvjIgD8Bcg==:4bc0d5bfe30e9c2cd2a00e3a2dfcc06f6ec71899e2213e0b5c14d0b17679d589','2023-09-18 14:23:12'),(8,'abc@a.a','+Scf94h59syLQI3zBw3hrw==:7e67bb58ec0141b39f8c37956546f3627152b713daff1ec854e9f497f8db22c3','2023-09-18 14:23:17'),(9,'x@x.com','Esg5mI0aDPErZNiKFkxHSw==:231a5e0a57ff615ba645f7b52472e14706d8759bbef6e1d79925110e51a8ee01','2023-09-18 15:38:56'),(10,'as@as.a','RT/FDuCiAL9J7T+dDI3Vrw==:65cff108543005b10eed34a075a788ea16b9a2e1b424b0070b72ac79d00ed973','2023-09-18 15:39:53'),(11,'q@q.com','gK4nP9w967w0iWvVtqls4Q==:d373216ba5ff4310b5f539ad9043e337b16ccf44342b7836c86dc952706a6339','2023-09-18 18:11:23'),(12,'q@q.org','Wqpnabl2cgf/ajjSlzxRNA==:58186097659142198467f257cf3e360bb6424abfbdabcbfd38c5d544558b50b0','2023-09-18 19:21:14'),(13,'dd@d.com','PQCG6JZKXmvJ4rQcSzu44A==:e6f498fc6bf9e22dbffcec1771e24ce51cce389fa99e8bd2d0f95a7010d3870c','2023-09-18 19:41:59');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-09-30 14:04:27
