#!/usr/bin/php
<?php

$regex = "/([0-9a-fA-F][0-9a-fA-F]:){5}([0-9a-fA-F][0-9a-fA-F]).+?connected_time=[0-9]+/s";

$ifaces = $_SERVER['argv'];
array_shift($ifaces);

$data = array();

function processAssoc($if){
        $regex = "/(([0-9a-fA-F][0-9a-fA-F]:){5}([0-9a-fA-F][0-9a-fA-F])).+\)\n(.+)\n.*preamble/s";
        $matches = array();

        $output = array();
        exec('/sbin/iw dev ' . escapeshellcmd($if) . ' station dump', $output);
        $output = implode("\n", $output);

        preg_match_all( $regex, $output, $matches, PREG_SET_ORDER );

        $data = array();

        foreach($matches as $m){
                $values = array();

                foreach(explode("\n", $m[4]) as $line){
                        $tv = explode(":", $line);
                        $values[str_replace(' ', '-', trim($tv[0]))] = trim($tv[1]);
                }

                $data[$m[1]] = $values;
        }

        return $data;
}

foreach ($ifaces as $if){
        $matches = array();
        //preg_match_all( $regex, file_get_contents('status'), $matches, PREG_SET_ORDER);

        //print_r($matches);
        $assoc = processAssoc($if);

        $output = array();
        exec('/usr/sbin/hostapd_cli -i ' . escapeshellcmd($if) . ' all_sta', $output);
        $output = implode("\n", $output);

        preg_match_all( $regex, $output, $matches, PREG_SET_ORDER);

        foreach ($matches as $match){
                $nlist = explode("\n", $match[0]);

                $mac = array_shift($nlist);

                $data[$if][$mac] = array();

                foreach ($nlist as $d){
                        $d2 = explode("=", $d);
                        if (count($d2) == 2)
                                $data[$if][$mac][$d2[0]] = $d2[1];
                }

                if (is_array($assoc[$mac])) $data[$if][$mac] = array_merge($data[$if][$mac], $assoc[$mac]);
        }
}

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "http://10.148.0.254/wifi_update.php");
curl_setopt($ch, CURLOPT_POST, 1);
// in real life you should use something like:
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('json' => json_encode($data))));
// receive server response ...
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_exec($ch);

curl_close ($ch);

//print_r($_SERVER['argc']);
//print_r($_SERVER['argv']);
