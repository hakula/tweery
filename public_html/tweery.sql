# ************************************************************
# Sequel Pro SQL dump
# Version 4096
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table tweery
# ------------------------------------------------------------

CREATE TABLE `tweery` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `term` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `created` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `term` (`term`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table tweet
# ------------------------------------------------------------

CREATE TABLE `tweet` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tweery_id` int(11) NOT NULL,
  `twitter_id` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `text` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `created` int(11) DEFAULT NULL,
  `lang` char(3) CHARACTER SET utf8 NOT NULL DEFAULT 'en',
  `name` varchar(255) CHARACTER SET utf8 DEFAULT '',
  `username` varchar(255) CHARACTER SET utf8 DEFAULT '',
  `profile_image_url` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `retweets` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
