DROP TABLE IF EXISTS `example`;

CREATE TABLE `example` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;


LOCK TABLES `example` WRITE;
insert  into `example`(`id`,`title`) values (1,'Hello World!'),(2,'Hello World!!');

UNLOCK TABLES;