<?php

$db = new MySQLi('127.0.0.1', 'root', '1234', 'wifi_data');

$map = array('wlan0' => 'WiFI 1', 'wlan1' => 'WiFi 2');

function reload(){
		 header("Location: ./");
}

function getStations(){
    global $db;

    $data = array();

    $result = $db->query("SELECT HEX(mac) as mac, interface, INET_NTOA(host_ip) as host_ip, username, rx_bytes, tx_bytes, `signal`, connected_time FROM stations WHERE mtime > DATE_SUB(NOW(),INTERVAL 5 MINUTE)");

    while ($row = $result->fetch_assoc()){
        $data[$row['mac']] = $row;
    }

    return $data;
}

function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1000));
    $pow = min($pow, count($units) - 1);

    // Uncomment one of the following alternatives
    $bytes /= pow(1000, $pow);
    // $bytes /= (1 << (10 * $pow));

    return round($bytes, $precision) . ' ' . $units[$pow];
}

function formatmac($mac){
    return preg_replace('/([0-9A-F]{2})([0-9A-F]{2})([0-9A-F]{2})([0-9A-F]{2})([0-9A-F]{2})([0-9A-F]{2})/', '${1}:${2}:${3}:${4}:${5}:${6}', $mac);
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Active VPN WiFi Users</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/m-styles.min.css" rel="stylesheet">
    <link href="css/m-forms.min.css" rel="stylesheet">
    <link href="css/m-buttons.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">

    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300' rel='stylesheet' type='text/css'>

    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <script type="text/javascript">
	<!--
	    function toggle_visibility(id) {
	       var e = document.getElementById(id);
	       if(e.style.display == '')
	          e.style.display = 'none';
	       else
	          e.style.display = '';
	    }
	//-->
	</script>

  </head>

  <body>
	<div align="center">
    	<h1>Active VPN WiFi Users</h1>
    	<br />

    	<div style="width: 95%"><table class="table table-bordered">
        	<thead>
            	<tr>
            		<th>MAC</th>
                	<th>NETWORK</th>
                    <th>HOST</th>
                    <th>USERNAME</th>
                    <th>SIGNAL</th>
                    <th>RX</th>
                    <th>TX</th>
                    <th>CONNECTED</th>
            	</tr>
        	</thead>
            <tbody>
            	<?php
				foreach( getStations() as $mac => $sta ){
				?>
            	<tr>
                	<td style="vertical-align:middle"><?= formatmac($mac) ?></td>
                    <td style="vertical-align:middle"><?= $map[$sta['interface']] ?></td>
                    <td style="vertical-align:middle"><?= $sta['host_ip'] ?></td>
                    <td style="vertical-align:middle"><?= $sta['username'] ?></td>
                    <td style="vertical-align:middle"><?= $sta['signal'] ?></td>
                    <td style="vertical-align:middle"><?= formatBytes($sta['rx_bytes']) ?></td>
                    <td style="vertical-align:middle"><?= formatBytes($sta['tx_bytes']) ?></td>
                    <td style="vertical-align:middle"><?= gmdate("H:i:s", (int)$sta['connected_time']) ?></td>
           		</tr>
                <?php
				}
				?>
           	</tbody>
        </table></div>
    </div>
  </body>

  <script src="js/jquery.js"></script>
  <script src="js/jquery.form.js"></script>
  <script src="js/bootstrap.js"></script>

</html>
