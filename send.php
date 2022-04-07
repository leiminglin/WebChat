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


if (!isset($_POST['name']) || !$_POST['name']) {
	return;
}
if (!isset($_POST['v']) || !$_POST['v']) {
	return;
}

file_put_contents($chat_file, '['.date("Y-m-d H:i:s").']' .SPLIT.send_preprocess($_POST['name']).SPLIT.send_preprocess($_POST['v']).SPLIT.ENDLINE, FILE_APPEND);

$soc = socket_create(AF_INET, SOCK_STREAM, getprotobyname("tcp"));
socket_connect($soc, '127.0.0.1', $port);
$buf = 'send'."\r\n";
socket_send($soc, $buf, strlen($buf), 0);
$recv = socket_read($soc, 100);
echo $recv;
echo '';
