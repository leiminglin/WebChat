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

if(version_compare(PHP_VERSION,'5.4.0','<')) {
	ini_set('magic_quotes_runtime',0);
	define('MAGIC_QUOTES_GPC',get_magic_quotes_gpc()?True:False);
}else{
	define('MAGIC_QUOTES_GPC',false);
}

if( MAGIC_QUOTES_GPC ){
	function deep_stripslashes($v) {
		if( is_array($v) ){
			foreach ( $v as $a=>$b ){
				$v[$a] = deep_stripslashes($b);
			}
		} else {
			return stripslashes($v);
		}
		return $v;
	}
	$_POST = deep_stripslashes($_POST);
	$_GET = deep_stripslashes($_GET);
	$_COOKIE = deep_stripslashes($_COOKIE);
}

$address = "127.0.0.1";

$port = 10004;

// 分隔符
define('SPLIT', ":");

// 换行符
define('ENDLINE', "\r\n");

// 实体标识，用于自定义协议发送前数据处理
define('ENTITIES_IDENTIFIER', "&"); // 实体标识符

// 实体标识符实体内容，用于自定义协议发送前内容处理
define('ENTITIES_IDENTIFIER_ENTITY', "&amp;");

// 分割符实体内容，用于自定义协议发送内容处理
define('SPLIT_ENTITY', "&split;"); 

// 换行符实体内容， 用于自定义协议发送内容处理
define('ENDLINE_ENTITY', "&endline"); 

date_default_timezone_set('PRC');

function pln($a){
	echo iconv('utf-8', 'gb2312', $a),"\n";
	ob_flush();
	flush();
}

function send_preprocess($v){
	// 首先将内容中的实体标识符进行转义，将其转义成实体标识符实体内容
	$v = str_replace(ENTITIES_IDENTIFIER, ENTITIES_IDENTIFIER_ENTITY, $v);
	// 再将内容中的分隔符和换行符进行转义，将其转义成对应得实体内容
	return str_replace(array(SPLIT, ENDLINE), array( SPLIT_ENTITY, ENDLINE_ENTITY ), $v);
}

function send_data_decode($v) {
	$v = str_replace( array( SPLIT_ENTITY, ENDLINE_ENTITY), array(SPLIT, ENDLINE), $v);
	$v = str_replace( ENTITIES_IDENTIFIER_ENTITY, ENTITIES_IDENTIFIER, $v);
	return $v;
}
