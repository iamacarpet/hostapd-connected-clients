<?php

$data = @json_decode($_POST['json'], true);
$host_ip = $_SERVER['REMOTE_ADDR'];

$db = new MySQLi('127.0.0.1', 'root', '1234', 'wifi_data');

foreach ($data as $iface => $ifdata){
        /*foreach ($ifdata as $mac => $mdata){
                //echo $iface . "|" . $host_ip . "|" . $mac . "\n";
        }*/

        $db->query("DELETE FROM stations WHERE host_ip = INET_ATON('" . $db->escape_string($host_ip) . "') AND interface = '" . $db->escape_string($iface) . "' AND mac NOT IN ( 0x" . str_replace(":", "", implode(", 0x", array_keys($ifdata))) . " );");

        foreach ($ifdata as $mac => $mdata){
                //echo $iface . "|" . $host_ip . "|" . $mac . "\n";
                $db->query("REPLACE INTO stations (`host_ip`, `interface`, `mac`, `username`, `rx_packets`, `rx_bytes`, `tx_packets`, `tx_bytes`, `signal`, `avg_signal`, `connected_time`, `flags`)
                                        VALUES ( INET_ATON('" . $db->escape_string($host_ip) . "'), '" . $db->escape_string($iface) . "', 0x" . $db->escape_string(str_replace(":", "", $mac)) . ", '" . $db->escape_string($mdata['dot1xAuthSessionUserName']) . "', '" . $db->escape_string($mdata['rx_packets']) . "', '" . $db->escape_string($mdata['rx_bytes']) . "', '" . $db->escape_string($mdata['tx_packets']) . "', '" . $db->escape_string($mdata['tx_bytes']) . "', '" . $db->escape_string($mdata['signal']) . "', '" . $db->escape_string($mdata['signal-avg']) . "', '" . $db->escape_string($mdata['connected_time']) . "', '" . $db->escape_string($mdata['flags']) . "');");
        }
}

echo "OK";
