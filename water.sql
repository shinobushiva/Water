-- Create syntax for TABLE 'info'
CREATE TABLE `info` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `time` int(11) DEFAULT NULL,
  `locate` varchar(64) DEFAULT NULL,
  `flg` int(1) DEFAULT NULL,
  `comment` text,
  `address` text,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'logs'
CREATE TABLE `logs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `post_text` text,
  `post_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'news'
CREATE TABLE `news` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `comment` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'rousui'
CREATE TABLE `rousui` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `time` int(20) DEFAULT NULL,
  `locate` varchar(64) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `flg` int(1) DEFAULT NULL,
  `comment` text,
  `address` text,
  `status` int(1) DEFAULT '0',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `info` ADD INDEX idx_time (`time`);
ALTER TABLE `rousui` ADD INDEX idx_time (`time`);
