CREATE DATABASE  IF NOT EXISTS `blogg` /*!40100 DEFAULT CHARACTER SET utf8 */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `blogg`;
-- MySQL dump 10.13  Distrib 5.7.29, for Linux (x86_64)
--
-- Host: portainer.proxmox    Database: blogg
-- ------------------------------------------------------
-- Server version	8.0.19

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `comment_love`
--

DROP TABLE IF EXISTS `comment_love`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comment_love` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` char(13) NOT NULL,
  `post_id` char(26) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `fk_comment_love_1` (`user_id`),
  KEY `fk_comment_love_2` (`post_id`),
  CONSTRAINT `fk_comment_love_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_comment_love_2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comment_love`
--

LOCK TABLES `comment_love` WRITE;
/*!40000 ALTER TABLE `comment_love` DISABLE KEYS */;
INSERT INTO `comment_love` VALUES (29,'5e848b1075373','5e85ec28ddb065e85ec28ddb0a');
/*!40000 ALTER TABLE `comment_love` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_post` char(26) NOT NULL,
  `user_id` char(13) NOT NULL,
  `body` text NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `fk_post_comment_1` (`id_post`),
  CONSTRAINT `fk_post_comment_1` FOREIGN KEY (`id_post`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posts` (
  `id` char(26) NOT NULL,
  `user_id` char(13) NOT NULL,
  `title` varchar(255) NOT NULL,
  `image_upload` varchar(255) DEFAULT NULL,
  `image_url` text,
  `header` text,
  `text` longtext NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `fk_posts_1` (`user_id`),
  CONSTRAINT `fk_posts_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` VALUES ('5e8377b1e07da5e8377b1e07e0','5e7b5e361a800','Redis','','https://upload.wikimedia.org/wikipedia/en/6/6b/Redis_Logo.svg','Redis from Wikipedia','Redis (/ˈrɛdɪs/; Remote Dictionary Server) is an in-memory data structure project implementing a distributed, in-memory key-value database with optional durability. Redis supports different kinds of abstract data structures, such as strings, lists, maps, sets, sorted sets, HyperLogLogs, bitmaps, streams, and spatial indexes. The project is mainly developed by Salvatore Sanfilippo and as of 2019, is sponsored by Redis Labs. It is open-source software released under a BSD 3-clause license.','2020-03-31 20:02:41'),('5e8378f858a015e8378f858a09','5e7b5e361a800','MySQL','','https://upload.wikimedia.org/wikipedia/en/6/62/MySQL.svg','Mysql from Wikipedia','MySQL (/ˌmaɪˌɛsˌkjuːˈɛl/ &#34;My S-Q-L&#34;) is an open-source relational database management system (RDBMS). Its name is a combination of &#34;My&#34;, the name of co-founder Michael Widenius&#39;s daughter, and &#34;SQL&#34;, the abbreviation for Structured Query Language.\r\n\r\nMySQL is free and open-source software under the terms of the GNU General Public License, and is also available under a variety of proprietary licenses. MySQL was owned and sponsored by the Swedish company MySQL AB, which was bought by Sun Microsystems (now Oracle Corporation). In 2010, when Oracle acquired Sun, Widenius forked the open-source MySQL project to create MariaDB.\r\n\r\nMySQL is a component of the LAMP web application software stack (and others), which is an acronym for Linux, Apache, MySQL, Perl/PHP/Python. MySQL is used by many database-driven web applications, including Drupal, Joomla, phpBB, and WordPress. MySQL is also used by many popular websites, including Facebook, Flickr, MediaWiki, Twitter, and YouTube.','2020-03-31 20:08:08'),('5e83ad8d0a7175e83ad8d0a719','5e7b5e361a800','NoSQL / MangoDB','','https://upload.wikimedia.org/wikipedia/en/4/45/MongoDB-Logo.svg','NoSQL / MangoDB from Wikipedia','MongoDB is a cross-platform document-oriented database program. Classified as a NoSQL database program, MongoDB uses JSON-like documents with schema. MongoDB is developed by MongoDB Inc. and licensed under the Server Side Public License (SSPL).&lt;p&gt;&lt;br&gt;&lt;p&gt;A NoSQL (originally referring to &quot;non SQL&quot; or &quot;non relational&quot;) database provides a mechanism for storage and retrieval of data that is modeled in means other than the tabular relations used in relational databases. Such databases have existed since the late 1960s, but the name &quot;NoSQL&quot; was only coined in the early 21st century, triggered by the needs of Web 2.0 companies. NoSQL databases are increasingly used in big data and real-time web applications. NoSQL systems are also sometimes called &quot;Not only SQL&quot; to emphasize that they may support SQL-like query languages, or sit alongside SQL databases in polyglot persistent architectures.&lt;/p&gt;&lt;p&gt;&lt;span style=&quot;font-size: 1rem;&quot;&gt;Motivations for this approach include: simplicity of design, simpler &quot;horizontal&quot; scaling to clusters of machines (which is a problem for relational databases), finer control over availability and limiting the object-relational impedance mismatch. The data structures used by NoSQL databases (e.g. key-value, wide column, graph, or document) are different from those used by default in relational databases, making some operations faster in NoSQL. The particular suitability of a given NoSQL database depends on the problem it must solve. Sometimes the data structures used by NoSQL databases are also viewed as &quot;more flexible&quot; than relational database tables.&lt;/span&gt;&lt;/p&gt;&lt;p&gt;Many NoSQL stores compromise consistency (in the sense of the CAP theorem) in favor of availability, partition tolerance, and speed. Barriers to the greater adoption of NoSQL stores include the use of low-level query languages (instead of SQL, for instance the lack of ability to perform ad-hoc joins across tables), lack of standardized interfaces, and huge previous investments in existing relational databases. Most NoSQL stores lack true ACID transactions, although a few databases have made them central to their designs.&lt;/p&gt;&lt;p&gt;Instead, most NoSQL databases offer a concept of &quot;eventual consistency&quot; in which database changes are propagated to all nodes &quot;eventually&quot; (typically within milliseconds) so queries for data might not return updated data immediately or might result in reading data that is not accurate, a problem known as stale reads. Additionally, some NoSQL systems may exhibit lost writes and other forms of data loss. Some NoSQL systems provide concepts such as write-ahead logging to avoid data loss. For distributed transaction processing across multiple databases, data consistency is an even bigger challenge that is difficult for both NoSQL and relational databases. Relational databases &quot;do not allow referential integrity constraints to span databases&quot;. Few systems maintain both ACID transactions and X/Open XA standards for distributed transaction processing. Interactive relational databases share conformational relay analysis techniques as a common feature. Limitations within the interface environment are overcome using semantic virtualization protocols, such that NoSQL services are accessible to most operating systems.&lt;/p&gt;&lt;/p&gt;','2020-03-31 23:52:29'),('5e848c94e60105e848c94e6012','5e848b1075373','Apache CouchDB','','https://upload.wikimedia.org/wikipedia/commons/f/f8/CouchDB.svg','Apache CouchDB from Wikipedia','Apache CouchDB is an open-source document-oriented NoSQL database, implemented in Erlang.&lt;p&gt;&lt;span style=&quot;font-size: 1rem;&quot;&gt;CouchDB uses multiple formats and protocols to store, transfer, and process its data, it uses JSON to store data, JavaScript as its query language using MapReduce, and HTTP for an API.&lt;/span&gt;&lt;p&gt;&lt;p&gt;&lt;p&gt;CouchDB was first released in 2005 and later became an Apache Software Foundation project in 2008.&lt;/p&gt;&lt;p&gt;&lt;span style=&quot;font-size: 1rem;&quot;&gt;Unlike a relational database, a CouchDB database does not store data and relationships in tables. Instead, each database is a collection of independent documents. Each document maintains its own data and self-contained schema. An application may access multiple databases, such as one stored on a user&#039;s mobile phone and another on a server. Document metadata contains revision information, making it possible to merge any differences that may have occurred while the databases were disconnected.&lt;/span&gt;&lt;/p&gt;&lt;p&gt;CouchDB implements a form of multiversion concurrency control (MVCC) so it does not lock the database file during writes. Conflicts are left to the application to resolve. Resolving a conflict generally involves first merging data into one of the documents, then deleting the stale one.&lt;br&gt;&lt;/p&gt;&lt;p&gt;Other features include document-level ACID semantics with eventual consistency, (incremental) MapReduce, and (incremental) replication. One of CouchDB&#039;s distinguishing features is multi-master replication, which allows it to scale across machines to build high-performance systems. A built-in Web application called Fauxton (formerly Futon) helps with administration.&lt;br&gt;&lt;/p&gt;&lt;/p&gt;&lt;/p&gt;&lt;/p&gt;','2020-04-01 15:44:04'),('5e848eaf8cc685e848eaf8cc6b','5e848b1075373','PostgreSQL','','https://upload.wikimedia.org/wikipedia/commons/2/29/Postgresql_elephant.svg','PostgreSQL from Wikipedia','PostgreSQL (/ˈpoʊstɡrɛs ˌkjuː ˈɛl/), also known as Postgres, is a free and open-source relational database management system (RDBMS) emphasizing extensibility and SQL compliance. It was originally named POSTGRES, referring to its origins as a successor to the Ingres database developed at the University of California, Berkeley. In 1996, the project was renamed to PostgreSQL to reflect its support for SQL. After a review in 2007, the development team decided to keep the name PostgreSQL.&lt;p&gt;PostgreSQL features transactions with Atomicity, Consistency, Isolation, Durability (ACID) properties, automatically updatable views, materialized views, triggers, foreign keys, and stored procedures. It is designed to handle a range of workloads, from single machines to data warehouses or Web services with many concurrent users. It is the default database for macOS Server, and is also available for Linux, FreeBSD, OpenBSD, and Windows.&lt;br&gt;&lt;/p&gt;','2020-04-01 15:53:03'),('5e85ec28ddb065e85ec28ddb0a','5e848b1075373','Amazon RDS','','https://upload.wikimedia.org/wikipedia/commons/9/93/Amazon_Web_Services_Logo.svg','Amazon RDS from Wikipedia','Amazon Relational Database Service (or Amazon RDS) is a distributed relational database service by Amazon Web Services (AWS). It is a web service running &quot;in the cloud&quot; designed to simplify the setup, operation, and scaling of a relational database for use in applications. Administration processes like patching the database software, backing up databases and enabling point-in-time recovery are managed automatically. Scaling storage and compute resources can be performed by a single API call as AWS does not offer an ssh connection to RDS instances.','2020-04-02 16:44:08');
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` char(13) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  UNIQUE KEY `email_UNIQUE` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES ('5e7b5e361a800','gary','gary@gmail.com','$2y$10$BJRMUe5/K7XPdsRCq10oBu2ZRbuXdE0kgOJUTRPO5DL/HdJFrjYy2'),('5e848b1075373','tim','tim@gmail.com','$2y$10$BInu1X/c3nsebh1DmGMfoOCZWXtAPSq.dv4eivuVX2/pvmNZ91cVy');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_avatar`
--

DROP TABLE IF EXISTS `users_avatar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_avatar` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` char(14) NOT NULL,
  `avatar_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `fk_users_avatar_1` (`user_id`),
  CONSTRAINT `fk_users_avatar_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_avatar`
--

LOCK TABLES `users_avatar` WRITE;
/*!40000 ALTER TABLE `users_avatar` DISABLE KEYS */;
INSERT INTO `users_avatar` VALUES (4,'5e7b5e361a800','2020.04.11.22.02.45-debian.png'),(6,'5e848b1075373','2020.04.01.15.37.36-git.png');
/*!40000 ALTER TABLE `users_avatar` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_ext`
--

DROP TABLE IF EXISTS `users_ext`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_ext` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` char(13) NOT NULL,
  `about_me` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  UNIQUE KEY `user_id_UNIQUE` (`user_id`),
  CONSTRAINT `fk_user_ext_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_ext`
--

LOCK TABLES `users_ext` WRITE;
/*!40000 ALTER TABLE `users_ext` DISABLE KEYS */;
INSERT INTO `users_ext` VALUES (11,'5e7b5e361a800','Be Right Back...');
/*!40000 ALTER TABLE `users_ext` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-04-12 12:46:52
