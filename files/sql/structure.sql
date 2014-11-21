CREATE TABLE `stations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `host_ip` int(10) unsigned NOT NULL,
  `interface` varchar(10) NOT NULL,
  `mac` bigint(20) unsigned NOT NULL,
  `username` varchar(250) DEFAULT NULL,
  `rx_packets` bigint(20) unsigned NOT NULL DEFAULT '0',
  `rx_bytes` bigint(20) unsigned NOT NULL DEFAULT '0',
  `tx_packets` bigint(20) unsigned NOT NULL DEFAULT '0',
  `tx_bytes` bigint(20) unsigned NOT NULL DEFAULT '0',
  `signal` varchar(125) DEFAULT NULL,
  `avg_signal` varchar(125) DEFAULT NULL,
  `connected_time` int(10) unsigned NOT NULL DEFAULT '0',
  `flags` varchar(500) DEFAULT NULL,
  `mtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `host_ip` (`host_ip`,`interface`,`mac`)
) ENGINE=InnoDB;