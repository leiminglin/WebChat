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

while ( ($connect = socket_accept($soc)) == true ) {
	$receive = socket_read($connect, 10000);
	pln('recv='.$receive);
	if( substr($receive, 0, 4) == 'send' ) {
		$send_info = explode(SPLIT, $receive);
		$name = send_data_decode($send_info[1]);
		$send_message = send_data_decode($send_info[2]);
		$date = date("Y-m-d H:i:s");
		file_put_contents("chat_log.txt", '['.$date.'] name: '.$name.', message: '.$send_message.ENDLINE, FILE_APPEND);
		$message_array = array(
				"name"=>htmlspecialchars($name)
				,"message"=>htmlspecialchars($send_message)
				,'date'=>$date
				);
		$send_str = json_encode( $message_array );
		pln('['.date("Y-m-d H:i:s").'] send content is:'.$send_str);
		foreach ( $connect_list as $k=>$v ){
			socket_write($v, $send_str);
			unset($connect_list[$k]);
		}
		socket_close($connect);
	}else{
		$connect_list[] = $connect;
		pln( 'add connect to list ,length='.count($connect_list));
	}
}




