CREATE TABLE IF NOT EXISTS `uc_object` (
  `uid` mediumint(8) NOT NULL COMMENT '用户ID',
  `username` char(15) NOT NULL COMMENT '用户名',
  `credit` int(11) NOT NULL default '0' COMMENT '积分',
  `addr` varchar(255) NOT NULL default '' COMMENT '地址',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `username` (`username`)
) TYPE=MyISAM;