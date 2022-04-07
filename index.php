<?php

require 'config.php';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>WebChat</title>
<script type="text/javascript" src="https://lib.sinaapp.com/js/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript">
!window.jQuery && document.write('<script src=https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js><\/script>');
</script>
</head>
<style>
body{
	font-size:12px;
	font-family:"微软雅黑";
}
#content{
	border:1px solid #ccc;
	height:250px;
	overflow:auto;
	margin-bottom:20px;
	padding:0 5px;
}
#sendtext{
	width:100%;
	padding:2px 5px;
	box-sizing:border-box;
}
.wrap { 
	table-layout:fixed; 
	word-break: break-all; 
	overflow:hidden; 
	margin:5px 0px;
}
.middle{
	line-height:40px;
	font-size:12px;
}
</style>
<body>
<!-- 
/**
 * WebChat
 * Copyright (c) 2014 http://lmlphp.com All rights reserved.
 * Licensed ( http://mit-license.org/ )
 * Author: leiminglin <leiminglin@126.com>
 *
 * A web chat app 
 *
 */
-->
<h1>Chat</h1>
<div id="content">

</div>
<div class="middle">
Nickname：<input type="text" value="小明" id="nickname" />&nbsp;Use Ctrl+Enter to send message
</div>
<textarea rows="4" cols="50" id="sendtext" ></textarea>
<input type="button" value="send" id="send"/>
<script type="text/javascript">
(function(){

	var color = ['red', 'blue', 'orange', 'green', 'pink', '#000'];
	var nick_name = ['小花','小狗','小马','小猫','小鱼','小刘','小鸡','小鸭','小猪','小兔','小朵','小明'];
	$("#nickname").val(nick_name[ Math.floor(Math.random()*(nick_name.length) ) ]);
	var mycolor = color[ Math.floor(Math.random()*(color.length)) ];
	function send_request(){
		$.ajax({
			url:'realtime.php',
			type:'post',
			data:'',
			dataType:'json',
			success:function(rs){
				if (rs==null||rs.status!=undefined) {
					$('h1').css({"color":"gray"});
					setTimeout(function(){
						send_request();
					},1e4);
				}else if(typeof rs=='object'){
					$('h1').css({"color":"green"});
					for (i in rs) {
						var re = rs[i];
						var str = '<span>['+re.date+']&nbsp;'+re.name+":</span>&nbsp;"+re.message;
						$("#content").append( function(){
								var chat = $("<div/>").addClass("wrap").append(str);
								if( re.name == $("#nickname").val() ){
									$('span', chat).css({"color":mycolor});
								}
								return chat;
							}
						).scrollTop($("#content")[0].scrollHeight);
					}
					send_request();
				}
			},
			error:function(rs){
				if (rs.status==504) {
					$('h1').css({"color":"green"});
				}
				setTimeout(function(){
					send_request();
				},1e4);
			},
		});
	}

	setTimeout(function(){
		send_request();
		$("#sendtext").focus();
	}, 1000);
	
	var send_timer;
	var lazy_time = 200;
	$( document ).keydown(function(event){
		var kc = event.keyCode;
		if( kc == 13 || kc == 17 ){
			if(kc == 17){
				send_timer = setTimeout(function(){
					clearTimeout(send_timer);
					send_timer = undefined;
				}, lazy_time);
			}
			if( event.keyCode == 13 && send_timer){
				$("#send").trigger("click");
				return false;
			}
		}
	});

	$("#send").click(function(){
		var self = this;
		if( !self.flag ){
			self.flag = $("<span/>").insertAfter($(self));
		}
		if( $("#sendtext").val() == ''){
			return;
		}
		var sendval = $("#sendtext").val();
		$("#sendtext").val('');
		self.disabled = true;
		$.ajax({
			url:'send.php',
			type:'post',
			data:{"v":sendval, "name":$("#nickname").val()},
			dataType:'html',
			success:function(re){
				self.flag.html("send success!").css({"display":"block"});
				timer = setTimeout(function(){
					self.flag.css({"display":"none"});
					self.disabled = false;
					$("#sendtext").focus();
				}, 400);
			},
			error:function(){
				
			},
		});
	});
})();
</script>
</body>
</html>
