<?php
ob_start();
set_time_limit(0);
require 'config.php';

$soc = socket_create(AF_INET, SOCK_STREAM, getprotobyname("tcp"));
socket_connect($soc, '127.0.0.1', $port);

$buf = 'conn\r\n';
socket_send($soc, $buf, strlen($buf), 0);

$recv = socket_read($soc, 10000);
echo $recv ;

socket_close($soc);

?>