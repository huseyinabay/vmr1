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
  `customer_address` varchar(500) COLLATE utf8_turkish_ci NOT NULL,
  `customer_district` varchar(20) COLLATE utf8_turkish_ci DEFAULT NULL,
  `customer_province` varchar(20) COLLATE utf8_turkish_ci DEFAULT NULL,
  `customer_contact1` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  `customer_phone1` varchar(100) COLLATE utf8_turkish_ci DEFAULT NULL,
  `balance` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

/*Data for the table `customer_details` */

insert  into `customer_details`(`id`,`customer_name`,`customer_address`,`customer_district`,`customer_province`,`customer_contact1`,`customer_phone1`,`balance`) values (1,'Netser','Koşuyolu İstanbul','','','Halil Mutlu','02165472200',0),(2,'MN İletişim A.Ş.','Koşuyolu İstanbul','','','Aydın Bey','02165478899',0),(3,'Datagrup A.Ş.','Koşuyolu İstanbul',NULL,NULL,'Ahmet','02165454432',0);

/*Table structure for table `stock_avail` */

DROP TABLE IF EXISTS `stock_avail`;

CREATE TABLE `stock_avail` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) COLLATE utf8_turkish_ci NOT NULL,
  `quantity` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

/*Data for the table `stock_avail` */

insert  into `stock_avail`(`id`,`name`,`quantity`) values (1,'Huawei Fiber Modem',15),(2,'Fiber Kablo',50),(3,'CAT6 kablo',0),(4,'RJ45 JAK',20),(5,'KONNEKTÖR',0);

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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

/*Data for the table `stock_details` */

insert  into `stock_details`(`id`,`stock_id`,`stock_name`,`brand`,`model`,`stock_quatity`,`supplier_id`,`company_price`,`selling_price`,`category`,`date`,`expire_date`,`follow`,`unit`,`uom`) values (1,'STK1','Huawei Fiber Modem','Huawei','',0,'SUPERONLINE','100.00','150.00','Fiber Modem','2016-05-06 12:05:11',NULL,'serial','adet',NULL),(2,'STK2','Fiber Kablo','SUPERONLINE','',0,'SUPERONLINE','10.00','15.00','Kablo','2016-05-06 12:11:47',NULL,'not','metre',NULL),(3,'STK3','CAT6 kablo','SUPERONLINE','',0,'SUPERONLINE','3.00','7.00','Kablo','2016-05-06 12:08:15',NULL,'not','metre',NULL),(4,'STK4','RJ45 JAK','SUPERONLINE','',0,'SUPERONLINE','1.00','3.00','JAK','2016-05-06 12:09:13',NULL,'not','adet',NULL),(5,'STK5','KONNEKTÖR','SUPERONLINE','',0,'SUPERONLINE','1.00','3.00','','2016-05-06 12:09:47',NULL,'not','adet',NULL);

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
  `total` decimal(10,2) NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

/*Data for the table `stock_entries` */

insert  into `stock_entries`(`id`,`stock_id`,`stock_name`,`stock_supplier_name`,`category`,`quantity`,`company_price`,`selling_price`,`opening_stock`,`closing_stock`,`date`,`username`,`type`,`salesid`,`total`,`payment`,`balance`,`mode`,`description`,`due`,`subtotal`,`count1`,`unit`,`follow`,`billnumber`) values (1,'AL3','Huawei Fiber Modem','SUPERONLINE','NULL',10,'100.00','150.00',0,10,'2016-05-06 00:00:00','halil.mutlu','entry','DEPODA','1000.00','1520.00','0.00','cash','','2016-05-06 00:00:00',1520,1,'Array','not','ffs3344'),(2,'AL3','Fiber Kablo','SUPERONLINE','NULL',50,'10.00','15.00',0,50,'2016-05-06 00:00:00','halil.mutlu','entry','DEPODA','500.00','1520.00','0.00','cash','','2016-05-06 00:00:00',1520,2,'Array','not','ffs3344'),(3,'AL3','RJ45 JAK','SUPERONLINE','NULL',20,'1.00','3.00',0,20,'2016-05-06 00:00:00','halil.mutlu','entry','DEPODA','20.00','1520.00','0.00','cash','','2016-05-06 00:00:00',1520,3,'Array','not','ffs3344'),(4,'AL6','Huawei Fiber Modem','SUPERONLINE','NULL',5,'100.00','150.00',10,15,'2016-05-06 00:00:00','halil.mutlu','entry','DEPODA','500.00','1.00','499.00','cash','','2016-05-06 00:00:00',500,1,'Array','serial','ff55443');

/*Table structure for table `stock_sales` */

DROP TABLE IF EXISTS `stock_sales`;

CREATE TABLE `stock_sales` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `transactionid` varchar(250) COLLATE utf8_turkish_ci NOT NULL,
  `stock_name` varchar(200) COLLATE utf8_turkish_ci NOT NULL,
  `category` varchar(120) COLLATE utf8_turkish_ci DEFAULT NULL,
  `supplier_name` varchar(200) COLLATE utf8_turkish_ci DEFAULT NULL,
  `selling_price` decimal(10,2) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `date` date NOT NULL,
  `username` varchar(120) COLLATE utf8_turkish_ci NOT NULL,
  `customer_id` varchar(120) COLLATE utf8_turkish_ci NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `payment` decimal(10,2) NOT NULL,
  `balance` decimal(10,2) NOT NULL,
  `discount` decimal(10,0) NOT NULL,
  `tax` decimal(10,0) NOT NULL,
  `tax_dis` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  `dis_amount` decimal(10,0) NOT NULL,
  `grand_total` decimal(10,0) NOT NULL,
  `due` date NOT NULL,
  `mode` varchar(250) COLLATE utf8_turkish_ci NOT NULL,
  `description` varchar(500) COLLATE utf8_turkish_ci NOT NULL,
  `count1` int(11) NOT NULL,
  `billnumber` varchar(120) COLLATE utf8_turkish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

/*Data for the table `stock_sales` */

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
  `contact1` varchar(100) COLLATE utf8_turkish_ci DEFAULT NULL,
  `username` varchar(120) COLLATE utf8_turkish_ci NOT NULL,
  `z_date` datetime DEFAULT NULL,
  `z_username` varchar(120) COLLATE utf8_turkish_ci DEFAULT NULL,
  `type` varchar(50) COLLATE utf8_turkish_ci NOT NULL,
  `mode` varchar(150) COLLATE utf8_turkish_ci NOT NULL,
  `description` varchar(500) COLLATE utf8_turkish_ci NOT NULL,
  `count1` int(11) NOT NULL,
  `closing_stock` int(11) DEFAULT NULL,
  `billnumber` varchar(120) COLLATE utf8_turkish_ci NOT NULL,
  PRIMARY KEY (`id`,`serial_number`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

/*Data for the table `stock_serial` */

insert  into `stock_serial`(`id`,`stock_id`,`stock_name`,`serial_number`,`quantity`,`date`,`transactionid`,`customer_id`,`contact1`,`username`,`z_date`,`z_username`,`type`,`mode`,`description`,`count1`,`closing_stock`,`billnumber`) values (1,'AL6','huawei Fiber Modem','',5,'2016-05-06 00:00:00',NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'entry','cash','',1,NULL,'ff55443'),(2,'AL6','huawei Fiber Modem','',5,'2016-05-06 00:00:00',NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'entry','cash','',2,NULL,'ff55443'),(3,'AL6','huawei Fiber Modem','',5,'2016-05-06 00:00:00',NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'entry','cash','',3,NULL,'ff55443'),(4,'AL6','huawei Fiber Modem','',5,'2016-05-06 00:00:00',NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'entry','cash','',4,NULL,'ff55443'),(5,'AL6','huawei Fiber Modem','',5,'2016-05-06 00:00:00',NULL,NULL,NULL,'halil.mutlu',NULL,NULL,'entry','cash','',5,NULL,'ff55443');

/*Table structure for table `stock_user` */

DROP TABLE IF EXISTS `stock_user`;

CREATE TABLE `stock_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(120) COLLATE utf8_turkish_ci NOT NULL,
  `password` varchar(120) COLLATE utf8_turkish_ci NOT NULL,
  `user_type` varchar(20) COLLATE utf8_turkish_ci NOT NULL,
  `answer` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

/*Data for the table `stock_user` */

insert  into `stock_user`(`id`,`username`,`password`,`user_type`,`answer`) values (1,'halil.mutlu','Yigidim2006','admin','indiana'),(2,'ali.abay','Netser2016','admin','turbo'),(3,'beyza.kaya','Datagrup2016','admin','x');

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
