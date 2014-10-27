<?php
/**
 * WebChat
 * Copyright (c) 2014 http://lmlphp.com All rights reserved.
 * Licensed ( http://mit-license.org/ )
 * Author: leiminglin <leiminglin@126.com>
 *
 * A web chat app 
 *
 */

require 'config.php';
$soc = socket_create(AF_INET, SOCK_STREAM, getprotobyname("tcp"));
socket_connect($soc, '127.0.0.1', $port);

$buf = 'send'.SPLIT.send_preprocess($_POST['name']).SPLIT.send_preprocess($_POST['v']).SPLIT."\r\n";
socket_send($soc, $buf, strlen($buf), 0);
$recv = socket_read($soc, 100000);
echo $recv;
echo '';
