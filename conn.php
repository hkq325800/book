<?php
	$con = mysql_connect("localhost", "root", "");// mysql_connect("localhost", "mengma", "mengma");
		if(!$con) {
			die("连接数据库出错");
			database_error();
		}
		
		mysql_query('SET NAMES utf8');
		mysql_select_db("book", $con);
		//返回json0
	function database_error(){
		$response[0]= array('status'=>"0",'info'=>"数据库连接失败");
		$response = json_encode($response);
		echo $response;
	}
?>