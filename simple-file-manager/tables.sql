CREATE TABLE `file_manager` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `file_name` varchar(125) DEFAULT NULL,
 `real_name` varchar(125) DEFAULT NULL,
 `file_size` varchar(45) DEFAULT NULL,
 `file_type` varchar(45) DEFAULT NULL,
 `folder` int(11) DEFAULT NULL,
 `createdOn` timestamp NULL DEFAULT current_timestamp(),
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4

CREATE TABLE `file_manager_folder` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `parent` int(11) DEFAULT NULL,
 `name` varchar(125) DEFAULT NULL,
 `createdOn` timestamp NOT NULL DEFAULT current_timestamp(),
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4
