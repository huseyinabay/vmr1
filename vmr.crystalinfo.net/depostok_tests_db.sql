/*
SQLyog Ultimate - MySQL GUI v8.2 
MySQL - 5.6.30 : Database - DEPOSTOK
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`DEPOSTOK` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_turkish_ci */;

USE `DEPOSTOK`;

/*Table structure for table `category_details` */

DROP TABLE IF EXISTS `category_details`;

CREATE TABLE `category_details` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_name` varchar(120) COLLATE utf8_turkish_ci NOT NULL,
  `category_description` varchar(250) COLLATE utf8_turkish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

/*Data for the table `category_details` */

insert  into `category_details`(`id`,`category_name`,`category_description`) values (1,'Fiber Modem',''),(2,'Kablo','');

/*Table structure for table `customer_details` */

DROP TABLE IF EXISTS `customer_details`;

CREATE TABLE `customer_details` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(200) CHARACTER SET utf8 NOT NULL,
  `customer_cid` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  `customer_address` varchar(500) COLLATE utf8_turkish_ci NOT NULL,
  `customer_district` varchar(20) COLLATE utf8_turkish_ci DEFAULT NULL,
  `customer_province` varchar(20) COLLATE utf8_turkish_ci DEFAULT NULL,
  `customer_contact1` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  `customer_phone1` varchar(100) COLLATE utf8_turkish_ci DEFAULT NULL,
  `customer_type` varchar(20) COLLATE utf8_turkish_ci DEFAULT NULL,
  `balance` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

/*Data for the table `customer_details` */

insert  into `customer_details`(`id`,`customer_name`,`customer_cid`,`customer_address`,`customer_district`,`customer_province`,`customer_contact1`,`customer_phone1`,`customer_type`,`balance`) values (1,'Netser','','Koşuyolu İstanbul','','','Halil Mutlu','02165472200',NULL,448),(2,'MN İletişim A.Ş.','','Koşuyolu İstanbul','','','Aydın Bey','02165478899',NULL,749),(3,'Datagrup A.Ş.','','Koşuyolu İstanbul',NULL,NULL,'Ahmet','02165454432',NULL,299);

/*Table structure for table `personal_details` */

DROP TABLE IF EXISTS `personal_details`;

CREATE TABLE `personal_details` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `personal_name` varchar(200) CHARACTER SET utf8 NOT NULL,
  `personal_address` varchar(500) COLLATE utf8_turkish_ci NOT NULL,
  `personal_district` varchar(20) COLLATE utf8_turkish_ci DEFAULT NULL,
  `personal_province` varchar(20) COLLATE utf8_turkish_ci DEFAULT NULL,
  `personal_phone1` varchar(100) COLLATE utf8_turkish_ci DEFAULT NULL,
  `personal_tcid` varchar(11) COLLATE utf8_turkish_ci DEFAULT NULL,
  `balance` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

/*Data for the table `personal_details` */

insert  into `personal_details`(`id`,`personal_name`,`personal_address`,`personal_district`,`personal_province`,`personal_phone1`,`personal_tcid`,`balance`) values (2,'Halil Mutlu','Koşuyolu	1','Kadıköy','İstanbul','05333180900','55555555555',0),(3,'Halil Mutlu','Koşuyolu 1','Kadıköy','İstanbul','05333180900','12345678912',0),(4,'Ali Abay','sahil yolu','üsküdar','istanbul','05332215478','98765432198',0);

/*Table structure for table `stock_avail` */

DROP TABLE IF EXISTS `stock_avail`;

CREATE TABLE `stock_avail` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) COLLATE utf8_turkish_ci NOT NULL,
  `quantity` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

/*Data for the table `stock_avail` */

insert  into `stock_avail`(`id`,`name`,`quantity`) values (1,'Huawei Fiber Modem',136),(2,'Fiber Kablo',118),(3,'CAT6 kablo',152),(4,'RJ45 JAK',0),(5,'KONNEKTÖR',0),(6,'ONT',14);

/*Table structure for table `stock_details` */

DROP TABLE IF EXISTS `stock_details`;

CREATE TABLE `stock_details` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `stock_id` varchar(120) COLLATE utf8_turkish_ci NOT NULL,
  `stock_name` varchar(120) COLLATE utf8_turkish_ci NOT NULL,
  `brand` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  `model` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  `stock_quatity` int(11) NOT NULL,
  `supplier_id` varchar(250) COLLATE utf8_turkish_ci NOT NULL,
  `company_price` decimal(10,2) NOT NULL,
  `selling_price` decimal(10,2) NOT NULL,
  `category` varchar(120) COLLATE utf8_turkish_ci NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `expire_date` datetime DEFAULT NULL,
  `follow` varchar(50) COLLATE utf8_turkish_ci NOT NULL,
  `unit` varchar(20) COLLATE utf8_turkish_ci NOT NULL,
  `uom` varchar(120) COLLATE utf8_turkish_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

/*Data for the table `stock_details` */

insert  into `stock_details`(`id`,`stock_id`,`stock_name`,`brand`,`model`,`stock_quatity`,`supplier_id`,`company_price`,`selling_price`,`category`,`date`,`expire_date`,`follow`,`unit`,`uom`) values (1,'STK1','Huawei Fiber Modem','Huawei','',0,'SUPERONLINE','100.00','150.00','Fiber Modem','2016-05-06 12:05:11',NULL,'serial','adet',NULL),(2,'STK2','Fiber Kablo','SUPERONLINE','',0,'SUPERONLINE','10.00','15.00','Kablo','2016-05-06 12:11:47',NULL,'not','metre',NULL),(3,'STK3','CAT6 kablo','SUPERONLINE','',0,'SUPERONLINE','3.00','7.00','Kablo','2016-05-06 12:08:15',NULL,'not','metre',NULL),(4,'STK4','RJ45 JAK','SUPERONLINE','',0,'SUPERONLINE','1.00','3.00','JAK','2016-05-06 12:09:13',NULL,'not','adet',NULL),(5,'STK5','KONNEKTÖR','SUPERONLINE','',0,'SUPERONLINE','1.00','3.00','','2016-05-06 12:09:47',NULL,'not','adet',NULL),(6,'STK6','ONT','SUPERONLINE','ONT',0,'SUPERONLINE','50.00','75.00','','2016-05-10 14:55:13',NULL,'serial','adet',NULL);

/*Table structure for table `stock_entries` */

DROP TABLE IF EXISTS `stock_entries`;

CREATE TABLE `stock_entries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `stock_id` varchar(120) COLLATE utf8_turkish_ci NOT NULL,
  `stock_name` varchar(260) COLLATE utf8_turkish_ci NOT NULL,
  `stock_supplier_name` varchar(200) COLLATE utf8_turkish_ci DEFAULT NULL,
  `category` varchar(120) COLLATE utf8_turkish_ci NOT NULL DEFAULT 'NULL',
  `quantity` int(11) NOT NULL,
  `company_price` decimal(10,2) DEFAULT NULL,
  `selling_price` decimal(10,2) NOT NULL,
  `opening_stock` int(11) NOT NULL,
  `closing_stock` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `username` varchar(120) COLLATE utf8_turkish_ci NOT NULL,
  `type` varchar(50) COLLATE utf8_turkish_ci NOT NULL,
  `salesid` varchar(120) COLLATE utf8_turkish_ci NOT NULL DEFAULT 'DEPODA',
  `total` decimal(10,2) DEFAULT NULL,
  `payment` decimal(10,2) DEFAULT NULL,
  `balance` decimal(10,2) DEFAULT NULL,
  `mode` varchar(150) COLLATE utf8_turkish_ci DEFAULT NULL,
  `description` varchar(500) COLLATE utf8_turkish_ci DEFAULT NULL,
  `due` datetime DEFAULT NULL,
  `subtotal` int(11) DEFAULT NULL,
  `count1` int(11) NOT NULL,
  `unit` varchar(20) COLLATE utf8_turkish_ci DEFAULT NULL,
  `follow` varchar(11) COLLATE utf8_turkish_ci DEFAULT NULL,
  `billnumber` varchar(120) COLLATE utf8_turkish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

/*Data for the table `stock_entries` */

insert  into `stock_entries`(`id`,`stock_id`,`stock_name`,`stock_supplier_name`,`category`,`quantity`,`company_price`,`selling_price`,`opening_stock`,`closing_stock`,`date`,`username`,`type`,`salesid`,`total`,`payment`,`balance`,`mode`,`description`,`due`,`subtotal`,`count1`,`unit`,`follow`,`billnumber`) values (1,'AL3','Huawei Fiber Modem','SUPERONLINE','NULL',5,'100.00','150.00',0,5,'2016-05-13 00:00:00','halil.mutlu','entry','DEPODA','500.00','1.00','999.00','cash','','2016-05-13 00:00:00',1000,1,'adet','serial','ffdd445566'),(2,'AL3','Fiber Kablo','SUPERONLINE','NULL',50,'10.00','15.00',0,50,'2016-05-13 00:00:00','halil.mutlu','entry','DEPODA','500.00','1.00','999.00','cash','','2016-05-13 00:00:00',1000,2,'metre','not','ffdd445566'),(3,'SD3','Huawei Fiber Modem',NULL,'NULL',2,NULL,'150.00',5,3,'2016-05-13 00:00:00','halil.mutlu','sales','SD3','300.00',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'ffgrtr11'),(4,'AL6','Huawei Fiber Modem','SUPERONLINE','NULL',7,'100.00','150.00',3,10,'2016-05-14 00:00:00','halil.mutlu','entry','DEPODA','700.00','1.00','1839.00','cash','','2016-05-14 00:00:00',1840,1,'adet','serial','ffgg6677'),(5,'AL6','Fiber Kablo','SUPERONLINE','NULL',50,'10.00','15.00',50,100,'2016-05-14 00:00:00','halil.mutlu','entry','DEPODA','500.00','1.00','1839.00','cash','','2016-05-14 00:00:00',1840,2,'metre','not','ffgg6677'),(6,'AL6','ONT','SUPERONLINE','NULL',8,'50.00','75.00',0,8,'2016-05-14 00:00:00','halil.mutlu','entry','DEPODA','400.00','1.00','1839.00','cash','','2016-05-14 00:00:00',1840,3,'adet','serial','ffgg6677'),(7,'AL6','CAT6 kablo','SUPERONLINE','NULL',80,'3.00','7.00',0,80,'2016-05-14 00:00:00','halil.mutlu','entry','DEPODA','240.00','1.00','1839.00','cash','','2016-05-14 00:00:00',1840,4,'metre','not','ffgg6677'),(8,'SD8','Huawei Fiber Modem',NULL,'NULL',3,NULL,'150.00',10,7,'2016-05-14 00:00:00','halil.mutlu','sales','SD8','450.00',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'ffrrtt55'),(9,'SD8','Fiber Kablo',NULL,'NULL',20,NULL,'15.00',100,80,'2016-05-14 00:00:00','halil.mutlu','sales','SD8','300.00',NULL,NULL,NULL,NULL,NULL,NULL,2,NULL,NULL,'ffrrtt55'),(10,'AL12','Huawei Fiber Modem','SUPERONLINE','NULL',10,'100.00','150.00',7,17,'2016-05-17 00:00:00','halil.mutlu','entry','DEPODA','1000.00','1.00','2039.00','cash','','2016-05-17 00:00:00',2040,1,'adet','serial','ffdd33'),(11,'AL12','Fiber Kablo','SUPERONLINE','NULL',50,'10.00','15.00',80,130,'2016-05-17 00:00:00','halil.mutlu','entry','DEPODA','500.00','1.00','2039.00','cash','','2016-05-17 00:00:00',2040,2,'metre','not','ffdd33'),(12,'AL12','ONT','SUPERONLINE','NULL',6,'50.00','75.00',8,14,'2016-05-17 00:00:00','halil.mutlu','entry','DEPODA','300.00','1.00','2039.00','cash','','2016-05-17 00:00:00',2040,3,'adet','serial','ffdd33'),(13,'AL12','CAT6 kablo','SUPERONLINE','NULL',80,'3.00','7.00',80,160,'2016-05-17 00:00:00','halil.mutlu','entry','DEPODA','240.00','1.00','2039.00','cash','','2016-05-17 00:00:00',2040,4,'metre','not','ffdd33'),(14,'SD14','Huawei Fiber Modem',NULL,'NULL',2,NULL,'150.00',17,15,'2016-05-17 00:00:00','halil.mutlu','sales','SD14','300.00',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'fdfd3332'),(15,'SD15','Huawei Fiber Modem',NULL,'NULL',1,NULL,'150.00',15,14,'2016-05-17 00:00:00','halil.mutlu','sales','SD15','150.00',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'fff44'),(16,'AL18','Huawei Fiber Modem','SUPERONLINE','NULL',5,'100.00','150.00',64,69,'2016-05-17 00:00:00','halil.mutlu','entry','DEPODA','500.00','0.00','0.00','cash','','2016-05-17 00:00:00',500,1,'adet','serial','ee4455'),(17,'AL18','Huawei Fiber Modem','SUPERONLINE','NULL',5,'100.00','150.00',64,69,'2016-05-17 00:00:00','halil.mutlu','entry','DEPODA','500.00','0.00','0.00','cash','','2016-05-17 00:00:00',500,1,'adet','serial','ee4455'),(18,'AL18','Huawei Fiber Modem','SUPERONLINE','NULL',5,'100.00','150.00',64,69,'2016-05-17 00:00:00','halil.mutlu','entry','DEPODA','500.00','0.00','0.00','cash','','2016-05-17 00:00:00',500,1,'adet','serial','ee4455'),(19,'AL18','Huawei Fiber Modem','SUPERONLINE','NULL',5,'100.00','150.00',64,69,'2016-05-17 00:00:00','halil.mutlu','entry','DEPODA','500.00','0.00','0.00','cash','','2016-05-17 00:00:00',500,1,'adet','serial','ee4455'),(20,'AL18','Huawei Fiber Modem','SUPERONLINE','NULL',5,'100.00','150.00',64,69,'2016-05-17 00:00:00','halil.mutlu','entry','DEPODA','500.00','0.00','0.00','cash','','2016-05-17 00:00:00',500,1,'adet','serial','ee4455'),(21,'AL18','Huawei Fiber Modem','SUPERONLINE','NULL',5,'100.00','150.00',64,69,'2016-05-17 00:00:00','halil.mutlu','entry','DEPODA','500.00','0.00','0.00','cash','','2016-05-17 00:00:00',500,1,'adet','serial','ee4455'),(22,'AL18','Huawei Fiber Modem','SUPERONLINE','NULL',5,'100.00','150.00',64,69,'2016-05-17 00:00:00','halil.mutlu','entry','DEPODA','500.00','0.00','0.00','cash','','2016-05-17 00:00:00',500,1,'adet','serial','ee4455'),(23,'AL18','Huawei Fiber Modem','SUPERONLINE','NULL',5,'100.00','150.00',64,69,'2016-05-17 00:00:00','halil.mutlu','entry','DEPODA','500.00','0.00','0.00','cash','','2016-05-17 00:00:00',500,1,'adet','serial','ee4455'),(24,'AL18','Huawei Fiber Modem','SUPERONLINE','NULL',5,'100.00','150.00',64,69,'2016-05-17 00:00:00','halil.mutlu','entry','DEPODA','500.00','0.00','0.00','cash','','2016-05-17 00:00:00',500,1,'adet','serial','ee4455'),(25,'AL18','Huawei Fiber Modem','SUPERONLINE','NULL',5,'100.00','150.00',64,69,'2016-05-17 00:00:00','halil.mutlu','entry','DEPODA','500.00','0.00','0.00','cash','','2016-05-17 00:00:00',500,1,'adet','serial','ee4455'),(26,'AL18','Huawei Fiber Modem','SUPERONLINE','NULL',5,'100.00','150.00',64,69,'2016-05-17 00:00:00','halil.mutlu','entry','DEPODA','500.00','0.00','0.00','cash','','2016-05-17 00:00:00',500,1,'adet','serial','ee4455'),(27,'AL18','Huawei Fiber Modem','SUPERONLINE','NULL',5,'100.00','150.00',64,69,'2016-05-17 00:00:00','halil.mutlu','entry','DEPODA','500.00','0.00','0.00','cash','','2016-05-17 00:00:00',500,1,'adet','serial','ee4455'),(28,'AL18','Huawei Fiber Modem','SUPERONLINE','NULL',5,'100.00','150.00',64,69,'2016-05-17 00:00:00','halil.mutlu','entry','DEPODA','500.00','0.00','0.00','cash','','2016-05-17 00:00:00',500,1,'adet','serial','ee4455'),(29,'AL18','Huawei Fiber Modem','SUPERONLINE','NULL',5,'100.00','150.00',64,69,'2016-05-17 00:00:00','halil.mutlu','entry','DEPODA','500.00','0.00','0.00','cash','','2016-05-17 00:00:00',500,1,'adet','serial','ee4455'),(30,'AL18','Huawei Fiber Modem','SUPERONLINE','NULL',5,'100.00','150.00',64,69,'2016-05-17 00:00:00','halil.mutlu','entry','DEPODA','500.00','0.00','0.00','cash','','2016-05-17 00:00:00',500,1,'adet','serial','ee4455'),(31,'AL18','Huawei Fiber Modem','SUPERONLINE','NULL',5,'100.00','150.00',64,69,'2016-05-17 00:00:00','halil.mutlu','entry','DEPODA','500.00','0.00','0.00','cash','','2016-05-17 00:00:00',500,1,'adet','serial','ee4455'),(32,'AL18','Huawei Fiber Modem','SUPERONLINE','NULL',5,'100.00','150.00',64,69,'2016-05-17 00:00:00','halil.mutlu','entry','DEPODA','500.00','0.00','0.00','cash','','2016-05-17 00:00:00',500,1,'adet','serial','ee4455'),(33,'AL18','Huawei Fiber Modem','SUPERONLINE','NULL',5,'100.00','150.00',64,69,'2016-05-17 00:00:00','halil.mutlu','entry','DEPODA','500.00','0.00','0.00','cash','','2016-05-17 00:00:00',500,1,'adet','serial','ee4455'),(34,'AL18','Huawei Fiber Modem','SUPERONLINE','NULL',5,'100.00','150.00',64,69,'2016-05-17 00:00:00','halil.mutlu','entry','DEPODA','500.00','0.00','0.00','cash','','2016-05-17 00:00:00',500,1,'adet','serial','ee4455'),(35,'AL37','Huawei Fiber Modem','SUPERONLINE','NULL',5,'100.00','150.00',107,112,'2016-05-17 00:00:00','halil.mutlu','entry','DEPODA','500.00','1.00','499.00','cash','','2016-05-17 00:00:00',500,1,'adet','serial','44rrr'),(36,'AL38','Huawei Fiber Modem','SUPERONLINE','NULL',4,'100.00','150.00',118,122,'2016-05-17 00:00:00','halil.mutlu','entry','DEPODA','400.00','40.00','360.00','cash','','2016-05-17 00:00:00',400,1,'adet','serial','tt667'),(37,'AL39','Huawei Fiber Modem','SUPERONLINE','NULL',4,'100.00','150.00',122,126,'2016-05-17 00:00:00','halil.mutlu','entry','DEPODA','400.00','20.00','380.00','cash','','2016-05-17 00:00:00',400,1,'adet','serial','4455p'),(38,'AL40','Huawei Fiber Modem','SUPERONLINE','NULL',4,'100.00','150.00',128,132,'2016-05-17 00:00:00','halil.mutlu','entry','DEPODA','400.00','1.00','0.00','cash','','2016-05-17 00:00:00',401,1,'adet','serial','gg33'),(39,'AL41','Huawei Fiber Modem','SUPERONLINE','NULL',4,'100.00','150.00',132,136,'2016-05-25 00:00:00','halil.mutlu','entry','DEPODA','400.00','1.00','400.00','cash','','2016-05-25 00:00:00',901,1,'adet','serial','ddfdf545454'),(40,'AL41','Fiber Kablo','SUPERONLINE','NULL',50,'10.00','15.00',130,180,'2016-05-25 00:00:00','halil.mutlu','entry','DEPODA','500.00','1.00','400.00','cash','','2016-05-25 00:00:00',901,2,'metre','not','ddfdf545454'),(41,'SD41','Fiber Kablo',NULL,'NULL',12,NULL,'0.00',168,156,'2016-06-14 00:00:00','halil.mutlu','sales','SD41',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,''),(42,'SD42','Fiber Kablo',NULL,'NULL',4,NULL,'1.00',144,140,'2016-06-14 00:00:00','halil.mutlu','debit','SD42',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'debitSD42'),(43,'SD43','CAT6 kablo',NULL,'NULL',4,NULL,'1.00',160,156,'2016-06-14 00:00:00','halil.mutlu','debit','SD43',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'debitSD43'),(44,'SD44','Fiber Kablo',NULL,'NULL',7,NULL,'1.00',140,133,'2016-06-14 00:00:00','halil.mutlu','debit','SD44',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'debitSD44'),(45,'SD45','Fiber Kablo',NULL,'NULL',5,NULL,'1.00',133,128,'2016-06-14 00:00:00','halil.mutlu','debit','SD45',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'debitSD45'),(46,'SD46','Fiber Kablo',NULL,'NULL',7,NULL,'1.00',128,121,'2016-06-14 00:00:00','halil.mutlu','debit','SD46',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'debitSD46'),(47,'SD46','CAT6 kablo',NULL,'NULL',4,NULL,'1.00',156,152,'2016-06-14 00:00:00','halil.mutlu','debit','SD46',NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,NULL,NULL,'debitSD46'),(48,'SD48','Fiber Kablo',NULL,'NULL',3,NULL,'1.00',121,118,'2016-06-14 00:00:00','halil.mutlu','debit','SD48',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'debitSD48');

/*Table structure for table `stock_sales` */

DROP TABLE IF EXISTS `stock_sales`;

CREATE TABLE `stock_sales` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `transactionid` varchar(250) COLLATE utf8_turkish_ci NOT NULL,
  `stock_name` varchar(200) COLLATE utf8_turkish_ci NOT NULL,
  `category` varchar(120) COLLATE utf8_turkish_ci DEFAULT NULL,
  `supplier_name` varchar(200) COLLATE utf8_turkish_ci DEFAULT NULL,
  `selling_price` decimal(10,2) DEFAULT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `date` date NOT NULL,
  `username` varchar(120) COLLATE utf8_turkish_ci NOT NULL,
  `customer_id` varchar(120) COLLATE utf8_turkish_ci NOT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL,
  `payment` decimal(10,2) DEFAULT NULL,
  `balance` decimal(10,2) DEFAULT NULL,
  `discount` decimal(10,0) DEFAULT NULL,
  `tax` decimal(10,0) DEFAULT NULL,
  `tax_dis` varchar(100) COLLATE utf8_turkish_ci DEFAULT NULL,
  `dis_amount` decimal(10,0) DEFAULT NULL,
  `grand_total` decimal(10,0) DEFAULT NULL,
  `due` date DEFAULT NULL,
  `mode` varchar(250) COLLATE utf8_turkish_ci NOT NULL,
  `description` varchar(500) COLLATE utf8_turkish_ci NOT NULL,
  `count1` int(11) NOT NULL,
  `billnumber` varchar(120) COLLATE utf8_turkish_ci DEFAULT NULL,
  `contact1` varchar(30) COLLATE utf8_turkish_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

/*Data for the table `stock_sales` */

insert  into `stock_sales`(`id`,`transactionid`,`stock_name`,`category`,`supplier_name`,`selling_price`,`quantity`,`amount`,`date`,`username`,`customer_id`,`subtotal`,`payment`,`balance`,`discount`,`tax`,`tax_dis`,`dis_amount`,`grand_total`,`due`,`mode`,`description`,`count1`,`billnumber`,`contact1`) values (1,'SD3','Huawei Fiber Modem',NULL,NULL,'150.00','2.00','300.00','2016-05-13','halil.mutlu','Netser','300.00','1.00','299.00','0','0','','0','300','1970-01-01','cash','',1,'ffgrtr11',NULL),(2,'SD8','Huawei Fiber Modem',NULL,NULL,'150.00','3.00','450.00','2016-05-14','halil.mutlu','MN İletişim A.Ş.','750.00','1.00','749.00','0','0','','0','750','1970-01-01','cash','',1,'ffrrtt55',NULL),(3,'SD8','Fiber Kablo',NULL,NULL,'15.00','20.00','300.00','2016-05-14','halil.mutlu','MN İletişim A.Ş.','750.00','1.00','749.00','0','0','','0','750','1970-01-01','cash','',2,'ffrrtt55',NULL),(4,'SD14','Huawei Fiber Modem',NULL,NULL,'150.00','2.00','300.00','2016-05-17','halil.mutlu','Datagrup A.Ş.','300.00','1.00','299.00','0','0','','0','300','1970-01-01','cash','',1,'fdfd3332',NULL),(5,'SD15','Huawei Fiber Modem',NULL,NULL,'150.00','1.00','150.00','2016-05-17','halil.mutlu','Netser','150.00','1.00','149.00','0','0','','0','150','1970-01-01','cash','',1,'fff44',NULL),(6,'SD41','Fiber Kablo',NULL,NULL,NULL,'12.00',NULL,'2016-06-14','halil.mutlu','Netser',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1970-01-01','','',1,NULL,NULL),(7,'SD41','Fiber Kablo',NULL,NULL,NULL,'12.00',NULL,'2016-06-14','halil.mutlu','Netser',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1970-01-01','','',1,NULL,NULL),(8,'SD41','Fiber Kablo',NULL,NULL,NULL,'12.00',NULL,'2016-06-14','halil.mutlu','Netser',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1970-01-01','','',1,NULL,NULL),(9,'SD42','Fiber Kablo',NULL,NULL,NULL,'4.00',NULL,'2016-06-14','halil.mutlu','Netser',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1970-01-01','','',1,NULL,NULL),(10,'SD42','Fiber Kablo',NULL,NULL,NULL,'3.00',NULL,'2016-06-14','halil.mutlu','Netser',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1970-01-01','','',1,NULL,NULL),(11,'SD42','Fiber Kablo',NULL,NULL,NULL,'5.00',NULL,'2016-06-14','halil.mutlu','Netser',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1970-01-01','','',1,NULL,NULL),(12,'SD42','Fiber Kablo',NULL,NULL,NULL,'4.00',NULL,'2016-06-14','halil.mutlu','Netser',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1970-01-01','','',1,NULL,NULL),(13,'SD43','CAT6 kablo',NULL,NULL,NULL,'4.00',NULL,'2016-06-14','halil.mutlu','Netser',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1970-01-01','','',1,NULL,NULL),(14,'SD44','Fiber Kablo',NULL,NULL,NULL,'7.00',NULL,'2016-06-14','halil.mutlu','Netser',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1970-01-01','','',1,NULL,NULL),(15,'SD45','Fiber Kablo',NULL,NULL,NULL,'5.00',NULL,'2016-06-14','halil.mutlu','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1970-01-01','','',1,NULL,NULL),(16,'SD46','Fiber Kablo',NULL,NULL,NULL,'7.00',NULL,'2016-06-14','halil.mutlu','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1970-01-01','','',1,NULL,'Halil Mutlu'),(17,'SD46','CAT6 kablo',NULL,NULL,NULL,'4.00',NULL,'2016-06-14','halil.mutlu','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1970-01-01','','',2,NULL,'Halil Mutlu'),(18,'SD48','Fiber Kablo',NULL,NULL,NULL,'3.00',NULL,'2016-06-14','halil.mutlu','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1970-01-01','','',1,NULL,'Halil Mutlu');

/*Table structure for table `stock_serial` */

DROP TABLE IF EXISTS `stock_serial`;

CREATE TABLE `stock_serial` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `stock_id` varchar(120) COLLATE utf8_turkish_ci NOT NULL,
  `stock_name` varchar(260) COLLATE utf8_turkish_ci NOT NULL,
  `serial_number` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '1',
  `date` datetime NOT NULL,
  `transactionid` varchar(10) COLLATE utf8_turkish_ci DEFAULT NULL,
  `customer_id` varchar(120) COLLATE utf8_turkish_ci DEFAULT NULL,
  `s_date` datetime DEFAULT NULL,
  `contact1` varchar(100) COLLATE utf8_turkish_ci DEFAULT NULL,
  `username` varchar(120) COLLATE utf8_turkish_ci NOT NULL,
  `z_date` datetime DEFAULT NULL,
  `z_username` varchar(120) COLLATE utf8_turkish_ci DEFAULT NULL,
  `type` varchar(50) COLLATE utf8_turkish_ci NOT NULL,
  `mode` varchar(150) COLLATE utf8_turkish_ci NOT NULL,
  `description` varchar(500) COLLATE utf8_turkish_ci NOT NULL,
  `count1` int(11) NOT NULL,
  `supplier` varchar(100) COLLATE utf8_turkish_ci DEFAULT NULL,
  `billnumber` varchar(120) COLLATE utf8_turkish_ci NOT NULL,
  `follow` varchar(10) COLLATE utf8_turkish_ci DEFAULT NULL,
  `status` varchar(40) COLLATE utf8_turkish_ci DEFAULT NULL,
  `status_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`,`serial_number`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

/*Data for the table `stock_serial` */

insert  into `stock_serial`(`id`,`stock_id`,`stock_name`,`serial_number`,`quantity`,`date`,`transactionid`,`customer_id`,`s_date`,`contact1`,`username`,`z_date`,`z_username`,`type`,`mode`,`description`,`count1`,`supplier`,`billnumber`,`follow`,`status`,`status_date`) values (1,'AL3','Huawei Fiber Modem','1121212',5,'2016-05-13 16:26:39','SD8','MN İletişim A.Ş.',NULL,'Aydın Bey','halil.mutlu','2016-05-14 18:13:12','halil.mutlu','zimmet','cash','',1,NULL,'ffdd445566','serial',NULL,NULL),(2,'AL3','Huawei Fiber Modem','22232321',5,'2016-05-13 16:26:39','SD3','Netser',NULL,'Halil Mutlu','halil.mutlu','2016-05-13 16:48:50','halil.mutlu','zimmet','cash','',2,NULL,'ffdd445566','serial',NULL,NULL),(3,'AL3','Huawei Fiber Modem','44433232',5,'2016-05-13 16:26:39','SD8','MN İletişim A.Ş.',NULL,'Aydın Bey','halil.mutlu','2016-05-14 18:13:12','halil.mutlu','zimmet','cash','',3,NULL,'ffdd445566','serial',NULL,NULL),(4,'AL3','Huawei Fiber Modem','2125666',5,'2016-05-13 16:26:39','SD15','',NULL,'Ali Abay','halil.mutlu','2016-05-30 13:42:35','halil.mutlu','zimmet','cash','',4,NULL,'ffdd445566','serial','Personelde','2016-05-18 20:32:29'),(5,'AL3','Huawei Fiber Modem','5656565',5,'2016-05-13 16:26:39','SD15','','2016-05-24 10:24:00','Ali Abay','halil.mutlu','2016-05-30 13:42:35','halil.mutlu','zimmet','cash','',5,NULL,'ffdd445566','serial','Personelde','2016-05-24 10:54:45'),(6,'AL6','Huawei Fiber Modem','h1111222',7,'2016-05-14 18:00:02',NULL,'',NULL,'Ali Abay','halil.mutlu','2016-05-30 13:42:35','halil.mutlu','zimmet','cash','',1,NULL,'ffgg6677','serial','Personelde',NULL),(7,'AL6','Huawei Fiber Modem','h22223333',7,'2016-05-14 18:00:02','SD14','',NULL,'yigit','halil.mutlu','2016-05-30 12:40:21','halil.mutlu','zimmet','cash','',2,NULL,'ffgg6677','serial','Personelde',NULL),(8,'AL6','Huawei Fiber Modem','h33334444',7,'2016-05-14 18:00:02','','',NULL,'yigit','halil.mutlu','2016-05-30 12:40:21','halil.mutlu','zimmet','cash','',3,NULL,'ffgg6677','serial','Personelde',NULL),(9,'AL6','Huawei Fiber Modem','h44445555',7,'2016-05-14 18:00:02',NULL,'',NULL,'Ali Abay','halil.mutlu','2016-05-28 11:36:09','halil.mutlu','zimmet','cash','',4,NULL,'ffgg6677','serial','Personelde',NULL),(10,'AL6','Huawei Fiber Modem','h555556666',7,'2016-05-14 18:00:02',NULL,'',NULL,'Ali Abay','halil.mutlu','2016-05-28 11:36:09','halil.mutlu','zimmet','cash','',5,NULL,'ffgg6677','serial','Personelde',NULL),(11,'AL6','Huawei Fiber Modem','h66667777',7,'2016-05-14 18:00:02',NULL,'',NULL,'ömer','halil.mutlu','2016-05-30 13:43:03','halil.mutlu','zimmet','cash','',6,NULL,'ffgg6677','serial','Personelde',NULL),(12,'AL6','Huawei Fiber Modem','h777778888',7,'2016-05-14 18:00:02',NULL,NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'barcode','cash','',7,NULL,'ffgg6677','serial',NULL,NULL),(13,'AL6','ONT','on1122',8,'2016-05-14 18:00:53',NULL,NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'barcode','cash','',1,NULL,'ffgg6677','serial',NULL,NULL),(14,'AL6','ONT','on2233',8,'2016-05-14 18:00:53',NULL,NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'barcode','cash','',2,NULL,'ffgg6677','serial',NULL,NULL),(15,'AL6','ONT','on3344',8,'2016-05-14 18:00:53',NULL,NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'barcode','cash','',3,NULL,'ffgg6677','serial',NULL,NULL),(16,'AL6','ONT','on4455',8,'2016-05-14 18:00:53',NULL,NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'barcode','cash','',4,NULL,'ffgg6677','serial',NULL,NULL),(17,'AL6','ONT','on5566',8,'2016-05-14 18:00:53',NULL,NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'barcode','cash','',5,NULL,'ffgg6677','serial',NULL,NULL),(18,'AL6','ONT','on6677',8,'2016-05-14 18:00:53',NULL,NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'barcode','cash','',6,NULL,'ffgg6677','serial',NULL,NULL),(19,'AL6','ONT','on7788',8,'2016-05-14 18:00:53',NULL,NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'barcode','cash','',7,NULL,'ffgg6677','serial',NULL,NULL),(20,'AL6','ONT','on8877',8,'2016-05-14 18:00:53',NULL,NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'barcode','cash','',8,NULL,'ffgg6677','serial',NULL,NULL),(21,'AL12','Huawei Fiber Modem','',10,'2016-05-17 00:00:00',NULL,NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'entry','cash','',1,NULL,'ffdd33','serial',NULL,NULL),(22,'AL12','Huawei Fiber Modem','',10,'2016-05-17 00:00:00',NULL,NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'entry','cash','',2,NULL,'ffdd33','serial',NULL,NULL),(23,'AL12','Huawei Fiber Modem','',10,'2016-05-17 00:00:00',NULL,NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'entry','cash','',3,NULL,'ffdd33','serial',NULL,NULL),(24,'AL12','Huawei Fiber Modem','',10,'2016-05-17 00:00:00',NULL,NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'entry','cash','',4,NULL,'ffdd33','serial',NULL,NULL),(25,'AL12','Huawei Fiber Modem','',10,'2016-05-17 00:00:00',NULL,NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'entry','cash','',5,NULL,'ffdd33','serial',NULL,NULL),(26,'AL12','Huawei Fiber Modem','',10,'2016-05-17 00:00:00',NULL,NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'entry','cash','',6,NULL,'ffdd33','serial',NULL,NULL),(27,'AL12','Huawei Fiber Modem','',10,'2016-05-17 00:00:00',NULL,NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'entry','cash','',7,NULL,'ffdd33','serial',NULL,NULL),(28,'AL12','Huawei Fiber Modem','',10,'2016-05-17 00:00:00',NULL,NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'entry','cash','',8,NULL,'ffdd33','serial',NULL,NULL),(29,'AL12','Huawei Fiber Modem','',10,'2016-05-17 00:00:00',NULL,NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'entry','cash','',9,NULL,'ffdd33','serial',NULL,NULL),(30,'AL12','Huawei Fiber Modem','',10,'2016-05-17 00:00:00',NULL,NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'entry','cash','',10,NULL,'ffdd33','serial',NULL,NULL),(31,'AL12','ONT','',6,'2016-05-17 00:00:00',NULL,NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'entry','cash','',1,NULL,'ffdd33','serial',NULL,NULL),(32,'AL12','ONT','',6,'2016-05-17 00:00:00',NULL,NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'entry','cash','',2,NULL,'ffdd33','serial',NULL,NULL),(33,'AL12','ONT','',6,'2016-05-17 00:00:00',NULL,NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'entry','cash','',3,NULL,'ffdd33','serial',NULL,NULL),(34,'AL12','ONT','',6,'2016-05-17 00:00:00',NULL,NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'entry','cash','',4,NULL,'ffdd33','serial',NULL,NULL),(35,'AL12','ONT','',6,'2016-05-17 00:00:00',NULL,NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'entry','cash','',5,NULL,'ffdd33','serial',NULL,NULL),(36,'AL12','ONT','',6,'2016-05-17 00:00:00',NULL,NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'entry','cash','',6,NULL,'ffdd33','serial',NULL,NULL),(37,'AL37','Huawei Fiber Modem','',5,'2016-05-17 00:00:00',NULL,NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'entry','cash','',1,NULL,'44rrr','serial',NULL,NULL),(38,'AL37','Huawei Fiber Modem','',5,'2016-05-17 00:00:00',NULL,NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'entry','cash','',2,NULL,'44rrr','serial',NULL,NULL),(39,'AL37','Huawei Fiber Modem','',5,'2016-05-17 00:00:00',NULL,NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'entry','cash','',3,NULL,'44rrr','serial',NULL,NULL),(40,'AL37','Huawei Fiber Modem','',5,'2016-05-17 00:00:00',NULL,NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'entry','cash','',4,NULL,'44rrr','serial',NULL,NULL),(41,'AL37','Huawei Fiber Modem','',5,'2016-05-17 00:00:00',NULL,NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'entry','cash','',5,NULL,'44rrr','serial',NULL,NULL),(42,'AL38','Huawei Fiber Modem','',4,'2016-05-17 00:00:00',NULL,NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'entry','cash','',1,NULL,'tt667','serial',NULL,NULL),(43,'AL38','Huawei Fiber Modem','',4,'2016-05-17 00:00:00',NULL,NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'entry','cash','',2,NULL,'tt667','serial',NULL,NULL),(44,'AL38','Huawei Fiber Modem','',4,'2016-05-17 00:00:00',NULL,NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'entry','cash','',3,NULL,'tt667','serial',NULL,NULL),(45,'AL38','Huawei Fiber Modem','',4,'2016-05-17 00:00:00',NULL,NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'entry','cash','',4,NULL,'tt667','serial',NULL,NULL),(46,'AL39','Huawei Fiber Modem','',4,'2016-05-17 00:00:00',NULL,NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'entry','cash','',1,NULL,'4455p','serial',NULL,NULL),(47,'AL39','Huawei Fiber Modem','',4,'2016-05-17 00:00:00',NULL,NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'entry','cash','',2,NULL,'4455p','serial',NULL,NULL),(48,'AL39','Huawei Fiber Modem','',4,'2016-05-17 00:00:00',NULL,NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'entry','cash','',3,NULL,'4455p','serial',NULL,NULL),(49,'AL39','Huawei Fiber Modem','',4,'2016-05-17 00:00:00',NULL,NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'entry','cash','',4,NULL,'4455p','serial',NULL,NULL),(50,'AL40','Huawei Fiber Modem','',4,'2016-05-17 00:00:00',NULL,NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'entry','cash','',1,NULL,'gg33','serial',NULL,NULL),(51,'AL40','Huawei Fiber Modem','',4,'2016-05-17 00:00:00',NULL,NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'entry','cash','',2,NULL,'gg33','serial',NULL,NULL),(52,'AL40','Huawei Fiber Modem','',4,'2016-05-17 00:00:00',NULL,NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'entry','cash','',3,NULL,'gg33','serial',NULL,NULL),(53,'AL40','Huawei Fiber Modem','',4,'2016-05-17 00:00:00',NULL,NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'entry','cash','',4,NULL,'gg33','serial',NULL,NULL),(54,'AL41','Huawei Fiber Modem','34546',4,'2016-05-25 10:18:59',NULL,NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'barcode','cash','',1,'SUPERONLINE','ddfdf545454','serial',NULL,NULL),(55,'AL41','Huawei Fiber Modem','223344',4,'2016-05-25 10:18:59',NULL,NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'barcode','cash','',2,'SUPERONLINE','ddfdf545454','serial',NULL,NULL),(56,'AL41','Huawei Fiber Modem','334455',4,'2016-05-25 10:18:59',NULL,NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'barcode','cash','',3,'SUPERONLINE','ddfdf545454','serial',NULL,NULL),(57,'AL41','Huawei Fiber Modem','445566',4,'2016-05-25 10:18:59',NULL,NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'barcode','cash','',4,'SUPERONLINE','ddfdf545454','serial','Tamirde','2016-05-25 10:20:11');

/*Table structure for table `stock_serial_log` */

DROP TABLE IF EXISTS `stock_serial_log`;

CREATE TABLE `stock_serial_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `stock_id` varchar(120) COLLATE utf8_turkish_ci NOT NULL,
  `stock_name` varchar(260) COLLATE utf8_turkish_ci NOT NULL,
  `serial_number` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '1',
  `date` datetime NOT NULL,
  `transactionid` varchar(10) COLLATE utf8_turkish_ci DEFAULT NULL,
  `customer_id` varchar(120) COLLATE utf8_turkish_ci DEFAULT NULL,
  `s_date` datetime DEFAULT NULL,
  `contact1` varchar(100) COLLATE utf8_turkish_ci DEFAULT NULL,
  `username` varchar(120) COLLATE utf8_turkish_ci NOT NULL,
  `z_date` datetime DEFAULT NULL,
  `z_username` varchar(120) COLLATE utf8_turkish_ci DEFAULT NULL,
  `type` varchar(50) COLLATE utf8_turkish_ci DEFAULT NULL,
  `mode` varchar(150) COLLATE utf8_turkish_ci DEFAULT NULL,
  `description` varchar(500) COLLATE utf8_turkish_ci DEFAULT NULL,
  `count1` int(11) DEFAULT NULL,
  `supplier` varchar(100) COLLATE utf8_turkish_ci DEFAULT NULL,
  `billnumber` varchar(120) COLLATE utf8_turkish_ci NOT NULL,
  `follow` varchar(10) COLLATE utf8_turkish_ci DEFAULT NULL,
  `status` varchar(40) COLLATE utf8_turkish_ci DEFAULT NULL,
  `status_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`,`serial_number`)
) ENGINE=InnoDB AUTO_INCREMENT=129 DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

/*Data for the table `stock_serial_log` */

insert  into `stock_serial_log`(`id`,`stock_id`,`stock_name`,`serial_number`,`quantity`,`date`,`transactionid`,`customer_id`,`s_date`,`contact1`,`username`,`z_date`,`z_username`,`type`,`mode`,`description`,`count1`,`supplier`,`billnumber`,`follow`,`status`,`status_date`) values (1,'AL3','Huawei Fiber Modem','5656565',1,'2016-05-13 16:26:39',NULL,'MN İletişim A.Ş.','2016-05-24 10:14:00','Halil Mutlu','halil.mutlu','2016-05-23 17:43:25',NULL,NULL,NULL,NULL,NULL,NULL,'ffdd445566',NULL,'Müşteride','2016-05-23 17:43:25'),(2,'AL3','Huawei Fiber Modem','5656565',1,'2016-05-13 16:26:39',NULL,'MN İletişim A.Ş.','2016-05-24 10:14:00','Halil Mutlu','halil.mutlu','2016-05-23 17:43:25',NULL,NULL,NULL,NULL,NULL,NULL,'ffdd445566',NULL,'Tamirde','2016-05-23 17:43:25'),(3,'AL3','Huawei Fiber Modem','5656565',1,'2016-05-13 16:26:39',NULL,'MN İletişim A.Ş.','2016-05-24 10:14:00','Halil Mutlu','halil.mutlu','2016-05-23 17:43:25',NULL,NULL,NULL,NULL,NULL,NULL,'ffdd445566',NULL,'Tamirde','2016-05-24 10:23:06'),(4,'AL3','Huawei Fiber Modem','5656565',1,'2016-05-13 16:26:39',NULL,'MN İletişim A.Ş.','2016-05-24 10:14:00','Halil Mutlu','halil.mutlu','2016-05-24 10:14:00',NULL,NULL,NULL,NULL,NULL,NULL,'ffdd445566',NULL,'NDepoda','2016-05-24 10:23:13'),(5,'AL3','Huawei Fiber Modem','5656565',1,'2016-05-13 16:26:39',NULL,'MN İletişim A.Ş.','2016-05-24 10:14:00','Halil Mutlu','halil.mutlu','2016-05-24 10:23:00',NULL,NULL,NULL,NULL,NULL,NULL,'ffdd445566',NULL,'Personelde','2016-05-24 10:24:09'),(6,'AL3','Huawei Fiber Modem','5656565',1,'2016-05-13 16:26:39',NULL,'MN İletişim A.Ş.','2016-05-24 10:24:00','Halil Mutlu','halil.mutlu','2016-05-24 10:14:00',NULL,NULL,NULL,NULL,NULL,NULL,'ffdd445566',NULL,'Müşteride','2016-05-24 10:24:39'),(7,'AL3','Huawei Fiber Modem','5656565',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','','halil.mutlu','0000-00-00 00:00:00',NULL,NULL,NULL,NULL,NULL,NULL,'ffdd445566',NULL,'Tamirde','2016-05-24 10:54:45'),(8,'AL41','Huawei Fiber Modem','445566',1,'2016-05-25 10:18:59',NULL,'','0000-00-00 00:00:00','','halil.mutlu','0000-00-00 00:00:00',NULL,NULL,NULL,NULL,NULL,'Netser A.Ş.','ddfdf545454',NULL,'Tamirde','2016-05-25 10:20:11'),(9,'AL6','Huawei Fiber Modem','h1111222',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','mustafa','halil.mutlu','2016-05-27 16:29:03',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-27 16:29:03'),(10,'AL6','Huawei Fiber Modem','h1111222',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','ömer','halil.mutlu','2016-05-27 16:30:04',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-27 16:30:04'),(11,'AL6','Huawei Fiber Modem','h22223333',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','ömer','halil.mutlu','2016-05-27 16:30:04',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-27 16:30:04'),(12,'AL6','Huawei Fiber Modem','h1111222',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','ahmet','halil.mutlu','2016-05-27 16:38:09',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-27 16:38:09'),(13,'AL6','Huawei Fiber Modem','h22223333',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','ahmet','halil.mutlu','2016-05-27 16:38:09',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-27 16:38:09'),(14,'AL6','Huawei Fiber Modem','h33334444',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','Ali Abay','halil.mutlu','2016-05-28 11:36:09',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-28 11:36:09'),(15,'AL6','Huawei Fiber Modem','h44445555',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','Ali Abay','halil.mutlu','2016-05-28 11:36:09',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-28 11:36:09'),(16,'AL6','Huawei Fiber Modem','h555556666',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','Ali Abay','halil.mutlu','2016-05-28 11:36:09',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-28 11:36:09'),(17,'AL3','Huawei Fiber Modem','5656565',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','Ali Abay','halil.mutlu','2016-05-28 11:48:00',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-28 11:48:00'),(18,'AL6','Huawei Fiber Modem','h1111222',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','Ali Abay','halil.mutlu','2016-05-28 11:48:00',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-28 11:48:00'),(19,'AL6','Huawei Fiber Modem','h22223333',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','Ali Abay','halil.mutlu','2016-05-28 11:48:00',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-28 11:48:00'),(20,'AL3','Huawei Fiber Modem','2125666',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','ismail beyhan','halil.mutlu','2016-05-28 11:54:10',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-28 11:54:10'),(21,'AL3','Huawei Fiber Modem','5656565',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','ismail beyhan','halil.mutlu','2016-05-28 11:54:10',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-28 11:54:10'),(22,'AL6','Huawei Fiber Modem','h1111222',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','ismail beyhan','halil.mutlu','2016-05-28 11:54:10',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-28 11:54:10'),(23,'AL3','Huawei Fiber Modem','2125666',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','Ali Abay','halil.mutlu','2016-05-28 11:54:58',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-28 11:54:58'),(24,'AL3','Huawei Fiber Modem','5656565',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','Ali Abay','halil.mutlu','2016-05-28 11:54:58',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-28 11:54:58'),(25,'AL6','Huawei Fiber Modem','h1111222',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','Ali Abay','halil.mutlu','2016-05-28 11:54:58',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-28 11:54:58'),(26,'AL3','Huawei Fiber Modem','2125666',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','ismail','halil.mutlu','2016-05-28 11:59:27',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-28 11:59:27'),(27,'AL3','Huawei Fiber Modem','5656565',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','ismail','halil.mutlu','2016-05-28 11:59:27',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-28 11:59:27'),(28,'AL6','Huawei Fiber Modem','h1111222',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','ismail','halil.mutlu','2016-05-28 11:59:27',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-28 11:59:27'),(29,'AL3','Huawei Fiber Modem','2125666',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','Ali Abay','halil.mutlu','2016-05-28 12:00:13',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-28 12:00:13'),(30,'AL3','Huawei Fiber Modem','5656565',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','Ali Abay','halil.mutlu','2016-05-28 12:00:13',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-28 12:00:13'),(31,'AL3','Huawei Fiber Modem','2125666',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','ömer','halil.mutlu','2016-05-30 10:44:03',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 10:44:03'),(32,'AL3','Huawei Fiber Modem','5656565',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','ömer','halil.mutlu','2016-05-30 10:44:03',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 10:44:03'),(33,'AL6','Huawei Fiber Modem','h1111222',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','ömer','halil.mutlu','2016-05-30 10:44:03',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-30 10:44:03'),(34,'AL3','Huawei Fiber Modem','2125666',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','murat','halil.mutlu','2016-05-30 11:06:08',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 11:06:08'),(35,'AL3','Huawei Fiber Modem','5656565',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','murat','halil.mutlu','2016-05-30 11:06:08',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 11:06:08'),(36,'AL6','Huawei Fiber Modem','h1111222',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','murat','halil.mutlu','2016-05-30 11:06:08',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-30 11:06:08'),(37,'AL3','Huawei Fiber Modem','2125666',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','murat','halil.mutlu','2016-05-30 11:12:25',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 11:12:25'),(38,'AL3','Huawei Fiber Modem','5656565',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','murat','halil.mutlu','2016-05-30 11:12:25',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 11:12:25'),(39,'AL6','Huawei Fiber Modem','h1111222',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','murat','halil.mutlu','2016-05-30 11:12:25',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-30 11:12:25'),(40,'AL3','Huawei Fiber Modem','2125666',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','murat','halil.mutlu','2016-05-30 11:12:28',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 11:12:28'),(41,'AL3','Huawei Fiber Modem','5656565',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','murat','halil.mutlu','2016-05-30 11:12:28',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 11:12:28'),(42,'AL6','Huawei Fiber Modem','h1111222',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','murat','halil.mutlu','2016-05-30 11:12:28',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-30 11:12:28'),(43,'AL3','Huawei Fiber Modem','2125666',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','murat','halil.mutlu','2016-05-30 11:12:29',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 11:12:29'),(44,'AL3','Huawei Fiber Modem','5656565',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','murat','halil.mutlu','2016-05-30 11:12:29',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 11:12:29'),(45,'AL6','Huawei Fiber Modem','h1111222',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','murat','halil.mutlu','2016-05-30 11:12:29',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-30 11:12:29'),(46,'AL3','Huawei Fiber Modem','2125666',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','murat','halil.mutlu','2016-05-30 11:12:30',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 11:12:30'),(47,'AL3','Huawei Fiber Modem','5656565',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','murat','halil.mutlu','2016-05-30 11:12:30',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 11:12:30'),(48,'AL6','Huawei Fiber Modem','h1111222',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','murat','halil.mutlu','2016-05-30 11:12:30',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-30 11:12:30'),(49,'AL3','Huawei Fiber Modem','2125666',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','murat','halil.mutlu','2016-05-30 11:12:59',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 11:12:59'),(50,'AL3','Huawei Fiber Modem','5656565',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','murat','halil.mutlu','2016-05-30 11:12:59',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 11:12:59'),(51,'AL6','Huawei Fiber Modem','h1111222',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','murat','halil.mutlu','2016-05-30 11:12:59',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-30 11:12:59'),(52,'AL3','Huawei Fiber Modem','2125666',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','ömer','halil.mutlu','2016-05-30 11:13:08',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 11:13:08'),(53,'AL3','Huawei Fiber Modem','5656565',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','ömer','halil.mutlu','2016-05-30 11:13:08',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 11:13:08'),(54,'AL6','Huawei Fiber Modem','h1111222',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','ömer','halil.mutlu','2016-05-30 11:13:08',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-30 11:13:08'),(55,'AL3','Huawei Fiber Modem','2125666',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','ömer','halil.mutlu','2016-05-30 11:47:55',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 11:47:55'),(56,'AL3','Huawei Fiber Modem','5656565',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','ömer','halil.mutlu','2016-05-30 11:47:55',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 11:47:55'),(57,'AL6','Huawei Fiber Modem','h1111222',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','ömer','halil.mutlu','2016-05-30 11:47:55',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-30 11:47:55'),(58,'AL3','Huawei Fiber Modem','2125666',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','ömer','halil.mutlu','2016-05-30 11:48:00',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 11:48:00'),(59,'AL3','Huawei Fiber Modem','5656565',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','ömer','halil.mutlu','2016-05-30 11:48:00',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 11:48:00'),(60,'AL6','Huawei Fiber Modem','h1111222',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','ömer','halil.mutlu','2016-05-30 11:48:00',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-30 11:48:00'),(61,'AL3','Huawei Fiber Modem','2125666',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','ömer','halil.mutlu','2016-05-30 11:48:02',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 11:48:02'),(62,'AL3','Huawei Fiber Modem','5656565',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','ömer','halil.mutlu','2016-05-30 11:48:02',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 11:48:02'),(63,'AL6','Huawei Fiber Modem','h1111222',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','ömer','halil.mutlu','2016-05-30 11:48:02',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-30 11:48:02'),(64,'AL3','Huawei Fiber Modem','2125666',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','emin','halil.mutlu','2016-05-30 11:50:46',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 11:50:46'),(65,'AL3','Huawei Fiber Modem','5656565',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','emin','halil.mutlu','2016-05-30 11:50:46',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 11:50:46'),(66,'AL6','Huawei Fiber Modem','h1111222',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','emin','halil.mutlu','2016-05-30 11:50:46',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-30 11:50:46'),(67,'AL3','Huawei Fiber Modem','5656565',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','osman','halil.mutlu','2016-05-30 11:58:18',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 11:58:18'),(68,'AL6','Huawei Fiber Modem','h1111222',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','osman','halil.mutlu','2016-05-30 11:58:18',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-30 11:58:18'),(69,'AL3','Huawei Fiber Modem','5656565',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','osman','halil.mutlu','2016-05-30 12:03:00',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 12:03:00'),(70,'AL6','Huawei Fiber Modem','h1111222',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','osman','halil.mutlu','2016-05-30 12:03:00',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-30 12:03:00'),(71,'AL3','Huawei Fiber Modem','5656565',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','osman','halil.mutlu','2016-05-30 12:03:03',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 12:03:03'),(72,'AL6','Huawei Fiber Modem','h1111222',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','osman','halil.mutlu','2016-05-30 12:03:03',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-30 12:03:03'),(73,'AL3','Huawei Fiber Modem','2125666',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','adem','halil.mutlu','2016-05-30 12:07:37',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 12:07:37'),(74,'AL3','Huawei Fiber Modem','5656565',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','adem','halil.mutlu','2016-05-30 12:07:37',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 12:07:37'),(75,'AL6','Huawei Fiber Modem','h1111222',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','adem','halil.mutlu','2016-05-30 12:07:37',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-30 12:07:37'),(76,'AL3','Huawei Fiber Modem','2125666',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','ömer','halil.mutlu','2016-05-30 12:08:23',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 12:08:23'),(77,'AL3','Huawei Fiber Modem','5656565',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','ömer','halil.mutlu','2016-05-30 12:08:23',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 12:08:23'),(78,'AL6','Huawei Fiber Modem','h1111222',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','ömer','halil.mutlu','2016-05-30 12:08:23',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-30 12:08:23'),(79,'AL3','Huawei Fiber Modem','2125666',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','yigit','halil.mutlu','2016-05-30 12:09:41',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 12:09:41'),(80,'AL3','Huawei Fiber Modem','5656565',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','yigit','halil.mutlu','2016-05-30 12:09:41',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 12:09:41'),(81,'AL3','Huawei Fiber Modem','2125666',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','ömer','halil.mutlu','2016-05-30 12:14:46',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 12:14:46'),(82,'AL3','Huawei Fiber Modem','5656565',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','ömer','halil.mutlu','2016-05-30 12:14:46',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 12:14:46'),(83,'AL6','Huawei Fiber Modem','h1111222',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','ömer','halil.mutlu','2016-05-30 12:14:46',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-30 12:14:46'),(84,'AL3','Huawei Fiber Modem','2125666',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','ahmet','halil.mutlu','2016-05-30 12:16:26',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 12:16:26'),(85,'AL3','Huawei Fiber Modem','5656565',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','ahmet','halil.mutlu','2016-05-30 12:16:26',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 12:16:26'),(86,'AL6','Huawei Fiber Modem','h1111222',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','ahmet','halil.mutlu','2016-05-30 12:16:26',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-30 12:16:26'),(87,'AL3','Huawei Fiber Modem','2125666',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','ömer','halil.mutlu','2016-05-30 12:39:27',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 12:39:27'),(88,'AL3','Huawei Fiber Modem','5656565',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','ömer','halil.mutlu','2016-05-30 12:39:27',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 12:39:27'),(89,'AL6','Huawei Fiber Modem','h1111222',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','yigit','halil.mutlu','2016-05-30 12:40:21',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-30 12:40:21'),(90,'AL6','Huawei Fiber Modem','h22223333',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','yigit','halil.mutlu','2016-05-30 12:40:21',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-30 12:40:21'),(91,'AL6','Huawei Fiber Modem','h33334444',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','yigit','halil.mutlu','2016-05-30 12:40:21',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-30 12:40:21'),(92,'AL3','Huawei Fiber Modem','2125666',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','ahmet','halil.mutlu','2016-05-30 12:48:21',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 12:48:21'),(93,'AL3','Huawei Fiber Modem','5656565',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','ahmet','halil.mutlu','2016-05-30 12:48:21',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 12:48:21'),(94,'AL6','Huawei Fiber Modem','h1111222',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','ahmet','halil.mutlu','2016-05-30 12:48:21',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-30 12:48:21'),(95,'AL3','Huawei Fiber Modem','2125666',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','ahmet','halil.mutlu','2016-05-30 12:50:29',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 12:50:29'),(96,'AL3','Huawei Fiber Modem','5656565',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','ahmet','halil.mutlu','2016-05-30 12:50:29',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 12:50:29'),(97,'AL6','Huawei Fiber Modem','h1111222',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','ahmet','halil.mutlu','2016-05-30 12:50:29',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-30 12:50:29'),(98,'AL3','Huawei Fiber Modem','2125666',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','osman','halil.mutlu','2016-05-30 12:50:44',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 12:50:44'),(99,'AL3','Huawei Fiber Modem','5656565',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','osman','halil.mutlu','2016-05-30 12:50:44',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 12:50:44'),(100,'AL6','Huawei Fiber Modem','h1111222',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','osman','halil.mutlu','2016-05-30 12:50:44',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-30 12:50:44'),(101,'AL3','Huawei Fiber Modem','2125666',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','ali Abay','halil.mutlu','2016-05-30 12:52:34',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 12:52:34'),(102,'AL3','Huawei Fiber Modem','5656565',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','ali Abay','halil.mutlu','2016-05-30 12:52:34',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 12:52:34'),(103,'AL6','Huawei Fiber Modem','h1111222',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','ali Abay','halil.mutlu','2016-05-30 12:52:34',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-30 12:52:34'),(104,'AL3','Huawei Fiber Modem','2125666',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','ömer','halil.mutlu','2016-05-30 12:57:19',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 12:57:19'),(105,'AL3','Huawei Fiber Modem','5656565',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','ömer','halil.mutlu','2016-05-30 12:57:19',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 12:57:19'),(106,'AL6','Huawei Fiber Modem','h1111222',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','ömer','halil.mutlu','2016-05-30 12:57:19',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-30 12:57:19'),(107,'AL3','Huawei Fiber Modem','2125666',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','Halil Mutlu','halil.mutlu','2016-05-30 12:58:00',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 12:58:00'),(108,'AL3','Huawei Fiber Modem','5656565',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','Halil Mutlu','halil.mutlu','2016-05-30 12:58:00',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 12:58:00'),(109,'AL6','Huawei Fiber Modem','h1111222',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','Halil Mutlu','halil.mutlu','2016-05-30 12:58:00',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-30 12:58:00'),(110,'AL3','Huawei Fiber Modem','2125666',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','ömer','halil.mutlu','2016-05-30 12:58:38',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 12:58:38'),(111,'AL3','Huawei Fiber Modem','5656565',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','ömer','halil.mutlu','2016-05-30 12:58:38',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 12:58:38'),(112,'AL6','Huawei Fiber Modem','h1111222',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','ömer','halil.mutlu','2016-05-30 12:58:38',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-30 12:58:38'),(113,'AL3','Huawei Fiber Modem','2125666',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','ahmet','halil.mutlu','2016-05-30 12:59:40',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 12:59:40'),(114,'AL3','Huawei Fiber Modem','5656565',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','ahmet','halil.mutlu','2016-05-30 12:59:40',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 12:59:40'),(115,'AL6','Huawei Fiber Modem','h1111222',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','ahmet','halil.mutlu','2016-05-30 12:59:40',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-30 12:59:40'),(116,'AL3','Huawei Fiber Modem','2125666',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','ömer','halil.mutlu','2016-05-30 13:01:23',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 13:01:23'),(117,'AL3','Huawei Fiber Modem','5656565',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','ömer','halil.mutlu','2016-05-30 13:01:23',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 13:01:23'),(118,'AL6','Huawei Fiber Modem','h1111222',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','ömer','halil.mutlu','2016-05-30 13:01:23',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-30 13:01:23'),(119,'AL3','Huawei Fiber Modem','2125666',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','ahmet','halil.mutlu','2016-05-30 13:39:56',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 13:39:56'),(120,'AL3','Huawei Fiber Modem','5656565',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','ahmet','halil.mutlu','2016-05-30 13:39:57',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 13:39:57'),(121,'AL6','Huawei Fiber Modem','h1111222',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','ahmet','halil.mutlu','2016-05-30 13:39:57',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-30 13:39:57'),(122,'AL3','Huawei Fiber Modem','2125666',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','ömer','halil.mutlu','2016-05-30 13:40:44',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 13:40:44'),(123,'AL3','Huawei Fiber Modem','5656565',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','ömer','halil.mutlu','2016-05-30 13:40:44',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 13:40:44'),(124,'AL6','Huawei Fiber Modem','h1111222',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','ömer','halil.mutlu','2016-05-30 13:40:44',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-30 13:40:44'),(125,'AL3','Huawei Fiber Modem','2125666',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','Ali Abay','halil.mutlu','2016-05-30 13:42:35',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 13:42:35'),(126,'AL3','Huawei Fiber Modem','5656565',1,'2016-05-13 16:26:39',NULL,'','0000-00-00 00:00:00','Ali Abay','halil.mutlu','2016-05-30 13:42:35',NULL,NULL,NULL,NULL,NULL,'','ffdd445566',NULL,'Personelde','2016-05-30 13:42:35'),(127,'AL6','Huawei Fiber Modem','h1111222',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','Ali Abay','halil.mutlu','2016-05-30 13:42:35',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-30 13:42:35'),(128,'AL6','Huawei Fiber Modem','h66667777',1,'2016-05-14 18:00:02',NULL,'','0000-00-00 00:00:00','ömer','halil.mutlu','2016-05-30 13:43:03',NULL,NULL,NULL,NULL,NULL,'','ffgg6677',NULL,'Personelde','2016-05-30 13:43:03');

/*Table structure for table `stock_user` */

DROP TABLE IF EXISTS `stock_user`;

CREATE TABLE `stock_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(120) COLLATE utf8_turkish_ci NOT NULL,
  `password` varchar(120) COLLATE utf8_turkish_ci NOT NULL,
  `user_type` varchar(20) COLLATE utf8_turkish_ci NOT NULL,
  `answer` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

/*Data for the table `stock_user` */

insert  into `stock_user`(`id`,`username`,`password`,`user_type`,`answer`) values (1,'halil.mutlu','Yigidim2006','admin','indiana'),(2,'ali.abay','Netser2016','admin','turbo'),(3,'beyza.kaya','Datagrup2016','admin','x'),(4,'salim.genc','Netser2016','admin','y');

/*Table structure for table `store_details` */

DROP TABLE IF EXISTS `store_details`;

CREATE TABLE `store_details` (
  `name` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  `log` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  `type` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  `address` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  `place` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  `city` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  `phone` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  `web` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  `pin` varchar(100) COLLATE utf8_turkish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

/*Data for the table `store_details` */

insert  into `store_details`(`name`,`log`,`type`,`address`,`place`,`city`,`phone`,`email`,`web`,`pin`) values ('SUPERONLINE','depostok.png','png','KOŞUYOLU','KADIKÖY','İSTANBUL','02165472200','destek@datagrup.com.tr','www.datagrup.com.tr','123456');

/*Table structure for table `supplier_details` */

DROP TABLE IF EXISTS `supplier_details`;

CREATE TABLE `supplier_details` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_supid` varchar(50) COLLATE utf8_turkish_ci NOT NULL,
  `supplier_name` varchar(200) COLLATE utf8_turkish_ci NOT NULL,
  `supplier_address` varchar(200) COLLATE utf8_turkish_ci NOT NULL,
  `supplier_district` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  `supplier_province` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  `supplier_contact1` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  `supplier_phone1` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  `balance` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

/*Data for the table `supplier_details` */

insert  into `supplier_details`(`id`,`supplier_supid`,`supplier_name`,`supplier_address`,`supplier_district`,`supplier_province`,`supplier_contact1`,`supplier_phone1`,`balance`) values (1,'TD1','SUPERONLINE','Yeni mahalle no:5','Gebze','KOCAELİ','Ozan Bey','05333180900',0),(2,'TD2','Netwell A.Ş.','Cenapa Şehabettin Sok No:27 Koşuyolu','Kadıköy','İstanbul','Aydın Sevin','02165472270',0),(3,'TD3','Netser A.Ş.','Koşuyolu','Kadıköy','İstanbul','Halil Mutlu','02165472238',0),(4,'TD6','Arena Bilgisayar','Kemerburgaz','Eyüp','İstanbul','Mehmet','02122553366',0);

/*Table structure for table `transactions` */

DROP TABLE IF EXISTS `transactions`;

CREATE TABLE `transactions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(50) COLLATE utf8_turkish_ci NOT NULL,
  `customer` varchar(250) COLLATE utf8_turkish_ci NOT NULL,
  `supplier` varchar(250) COLLATE utf8_turkish_ci NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `payment` decimal(10,2) NOT NULL,
  `balance` decimal(10,2) NOT NULL,
  `due` datetime NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `rid` varchar(120) COLLATE utf8_turkish_ci NOT NULL,
  `receiptid` varchar(200) COLLATE utf8_turkish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

/*Data for the table `transactions` */

/*Table structure for table `uom_details` */

DROP TABLE IF EXISTS `uom_details`;

CREATE TABLE `uom_details` (
  `id` int(10) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `name` varchar(120) COLLATE utf8_turkish_ci NOT NULL,
  `spec` varchar(120) COLLATE utf8_turkish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

/*Data for the table `uom_details` */

insert  into `uom_details`(`id`,`name`,`spec`) values (0000000006,'UOM1','UOM1 Specification'),(0000000007,'UOM2','UOM2 Specification'),(0000000008,'UOM3','UOM3 Specification'),(0000000009,'UOM4','UOM4 Specification');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
