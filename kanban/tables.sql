CREATE TABLE `kaban_board` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task` varchar(65) DEFAULT NULL,
  `type` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
