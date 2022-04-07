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

ob_start();
set_time_limit(0);
require 'config.php';

pln('port='. $port);
$soc = socket_create(AF_INET, SOCK_STREAM, getprotobyname("tcp")) 
	or die("socket_create() error:" . socket_strerror(socket_last_error()) . "\n"); ;

socket_bind($soc, '127.0.0.1', $port);

$connect_list = array();

if( socket_listen($soc) ) {
	pln( 'server run. port='.$port);
}

$get_chat = function($stime) use ($chat_file) {
	$h = fopen($chat_file, 'r');
	$ret = [];
	while (!feof($h)) {
		$line = fgets($h, 10000);
		$date = substr($line, 1, 19);
		$t = strtotime($date);
		if ($t > $stime) {
			$t_arr = explode(SPLIT, substr($line,20));
			$name = send_data_decode($t_arr[1]);
			$send_message = send_data_decode($t_arr[2]);
			$ret[] = array(
				"name"=>htmlspecialchars($name)
				,"message"=>htmlspecialchars($send_message)
				,'date'=>$date
			);
		}
	}
	return $ret;
};

$last_sendtime = time();

while (true) {
	$connect = socket_accept($soc);
	$receive = socket_read($connect, 10000);
	pln('recv='.$receive);
	if (substr($receive, 0, 4) == 'send') {
		socket_close($connect);
		$send_str = json_encode($get_chat($last_sendtime));
		pln('['.date("Y-m-d H:i:s").'] send content is:'.$send_str);
		foreach ( $connect_list as $k=>$v ){
			socket_write($v, $send_str);
			unset($connect_list[$k]);
		}
		$last_sendtime = time();
	} elseif (substr($receive, 0, 4) == 'conn') {
		$connect_list[] = $connect;
		pln('add connect to list, length='.count($connect_list));
	}
	usleep(10);
}
