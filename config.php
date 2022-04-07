<?php

define('IS_CLI',PHP_SAPI=='cli'? 1 : 0);

function isLogin() {
        $passwd = 'cx'.date("Ymd");
        $time = time();
        $expire_time = $time+86400*7;
        $domain = '';
        $salt = 'lmlphp_881cefe8991daf2a10c3c67ae42d31145e77b265';
        $token_name = 'lmlphp_token';
        $input_name = 'lmlphp_passwd';
        if (isset($_POST[$input_name]) && $_POST[$input_name] == $passwd) {
                setcookie($token_name, md5($passwd.$salt.$time).'_'.$time, $expire_time, '/', $domain);
                return true;
        }
        if (!isset($_COOKIE[$token_name])) {
                echo '<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"/><form method="post" action="">'.
                '<input placeholder="passwd" type="password" name="'.$input_name.'"/>'.
                '</form>';
                return false;
        } else {
                $arr = explode('_', $_COOKIE[$token_name]);
                if(count($arr) != 2 || $_COOKIE[$token_name] != md5($passwd.$salt.$arr[1]).'_'.$arr[1]) {
                        return false;
                }
        }
        return true;
}

if (!IS_CLI && !isLogin()) {
	return;
}

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

$chat_file = '29b176dc22c922c7e9ba8650155bef0d50af8fe0_chat_log.txt';

$start_time = time();

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
define('ENDLINE_ENTITY', "&endline;"); 

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
