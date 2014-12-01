<?php
	//, JSON_UNESCAPED_UNICODE可让json转为明码
	require './Slim/Slim.php';
	\Slim\Slim::registerAutoloader();
	define("pagesize", 15, true);
	define("saltkey", 'nigoule');
	define("url",'http://www.flappyant.com/book/API.php');
	$app = new \Slim\Slim();

	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: 'GET'");
	header("Access-Control-Allow-Methods: 'POST'");
	header("Access-Control-Max-Age: '60'");
	/**
	//----------通用类----------
	**/
	//test
	$app->get('/',function(){
		echo '<div style="margin-left:550px;margin-top:200px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp欢迎光临<br>=><a href="http://www.baidu.com">www.我爱作死作作死.com</a>=<</div>';
	});
	//test
	$app->post('/',function(){
		global $app;
		$req = $app->request(); 
		$postdata = file_get_contents("php://input");
		$request = json_decode($postdata);
		if(is_json($postdata)){
			echo '蛤蛤';
		}
		else{
			echo '习习';
			//var_dump($postdata);
		}
		
	});
	//（已验证）POST修改密码http://localhost/webservice/book/API.php/public/passChange   12108413/12108413/123/123
	$app->post('/public/passChange',function(){
		require 'conn.php';
		global $app;
		$req = $app->request();// var_dump($req);
		$postdata = file_get_contents("php://input");
		$request = json_decode($postdata);
		$IsJson="";
		is_json($postdata)?$IsJson=true:$IsJson=false;
		$xuserId = getPost($req,$request,$IsJson,'userId');
		$xoldPass = getPost($req,$request,$IsJson,'oldPass');
		$xnewPass = getPost($req,$request,$IsJson,'newPass');
		$xrenewPass = getPost($req,$request,$IsJson,'renewPass');
		//var_dump($xuserId.'<br/>'.$xoldPass.'<br/>'.$xnewPass.'<br/>'.$xrenewPass);
		if($xnewPass!=$xrenewPass) 
			error('password_error');//确定password是否等于repassword
		else{
			$xnewPass=setSecret($xnewPass);
			$xoldPass=setSecret($xoldPass);
			!identity('user','user_id',$xuserId,'user_password',$xoldPass)?error('verify_error'):passChange($xuserId,$xnewPass);
		}
		mysql_close($con);
	});
	//（已验证）GET点赞http://localhost/webservice/book/API.php/public/like/1/12108238/12108413
	$app->get('/public/like/:bookKind/:userId/:password',function($xbookKind,$xuserId,$xpassword){
		require 'conn.php';
		if(identity('booklike','user_id',$xuserId,'book_kind',$xbookKind)){
			error('like_error');
		}
		else{
			$xpassword=setSecret($xpassword);
			!identity('user','user_id',$xuserId,'user_password',$xpassword)?error('verify_error'):like($xbookKind,$xuserId);
		}
		mysql_close($con);
	});
	//（已验证）GET书本总数http://localhost/webservice/book/API.php/public/bookSum/0/1/12108413
	$app->get('/public/bookSum/:IsAdmin/:type/:userId',function($IsAdmin,$type,$xuserId){//flag决定身份，type决定搜索类型
		require 'conn.php';
		bookSum($IsAdmin,$type,$xuserId);
		mysql_close($con);
	});
	//（已验证）GET返回密码http://localhost/webservice/book/API.php/public/password/12108413
	$app->get('/public/password/:userId',function ($xuserId){
		require 'conn.php';
	    !identity('user','user_id',$xuserId,'','')?error('verify_error'):password($xuserId);
	    mysql_close($con);
	});
	//（已验证）GET显示图书详细(book_kind->booklist)http://localhost/webservice/book/API.php/public/detail/47
	$app->get('/public/detail/:bookKind',function($xbookKind){
		require 'conn.php';
		detail($xbookKind);
		mysql_close($con);
	});
	//（已验证）GET最近添加的图书http://localhost/webservice/book/API.php/public/recentAdd/12108413
	$app->get('/public/recentAdd/:userId',function($xuserId){
		require 'conn.php';
		!identity('user','user_id',$xuserId,'','')?error('verify_error'):recentAdd($xuserId);
		mysql_close($con);
	});
	/**
	//----------用户类----------
	**/
	//（已验证）POST登录http://localhost/webservice/book/API.php/normal/login   12108413/12108413
	$app->post('/normal/login', function () {
		require 'conn.php';
		global $app;
		$req = $app->request(); 
		$postdata = file_get_contents("php://input");
		$request = json_decode($postdata);
		$IsJson="";
		is_json($postdata)?$IsJson=true:$IsJson=false;
		$xuserId = getPost($req,$request,$IsJson,'userId');
		$xpassword = getPost($req,$request,$IsJson,'password');
		$xpassword = setSecret($xpassword);
		!identity('user','user_id',$xuserId,'user_password',$xpassword)?error('verify_error'):found();
		mysql_close($con);
	});
	//（已验证）POST注册http://localhost/webservice/book/API.php/normal/register   12108413/黄可庆/0/0
	$app->post('/normal/register', function () {
		require 'conn.php';
		global $app;//
		$req = $app->request(); 
		$postdata = file_get_contents("php://input");
		$request = json_decode($postdata);
		$IsJson="";
		is_json($postdata)?$IsJson=true:$IsJson=false;
		$xuserId = getPost($req,$request,$IsJson,'userId');
		$xuserName = getPost($req,$request,$IsJson,'userName');
		$xpassword = getPost($req,$request,$IsJson,'password');
		$xrepassword = getPost($req,$request,$IsJson,'rePassword');
		if($xpassword!=$xrepassword) 
			error('password_error');//确定password是否等于repassword
		else{
			$xpassword=setSecret($xpassword);
			identity('user','user_id',$xuserId,'','')?error('id_error'):register($xuserId,$xuserName,$xpassword);//确定user_id是否重复，重复返回0//未找到且注册成功返回1
		}
		mysql_close($con);
	});
	//（已验证）GET扫一扫借书http://localhost/webservice/book/API.php/normal/borrow/1/12108413/12108413
	$app->get('/normal/borrow/:bookId/:userId/:password', function ($xbookId,$xuserId,$xpassword) {
		require 'conn.php';
		if(identity('booklist','id',$xbookId,'book_status','已被借')){//先确认书的状态book_status 1为已被借
			error('bookverify_error');
		}
		else{//再验证扫描人密码成功则借取
			$xpassword=setSecret($xpassword);
			!identity('user','user_id',$xuserId,'user_password',$xpassword)?error('verify_error'):swap($xbookId,$xuserId);
		}
		mysql_close($con);
	});
	//（已验证）GET图书搜索http://localhost/webservice/book/API.php/normal/search/12108413/1/page=1/php
	//（已验证）GET获取图书列表http://localhost/webservice/book/API.php/normal/search/12108413/5/page=1/all
	$app->get('/normal/search/:userId/:type/page=:page/:keyword', function ($xuserId,$xtype,$xpage,$xkeyword) {
		if($xtype=='5')
			$xkeyword='';
		$page_size=pagesize;
		$offset=($xpage-1)*$page_size;
		require 'conn.php';
		switch ($xtype) {
			case '1'://书名
				$xtype='book_name';
				break;
			case '2'://book_id
				$xtype='id';
				break;
			case '3'://作者
				$xtype='book_author';
				break;
			case '4'://种类
				$xtype='book_type';
				break;
			case '5'://全部
				$xtype='';
				break;
			default:
				error('url_error');
				break;
		}
		search($xuserId,0,$xtype,$page_size,$offset,$xkeyword);
		mysql_close($con);
	});
	//（已验证）GET已借阅http://localhost/webservice/book/API.php/normal/showRe/12108413/12108413/page=1
	$app->get('/normal/showRe/:userId/:password/page=:page', function ($xuserId,$xpassword,$xpage) {
		require 'conn.php';
		//先验证再输出
		$page_size=pagesize;
		$offset=($xpage-1)*$page_size;
		$xpassword=setSecret($xpassword);
		!identity('user','user_id',$xuserId,'user_password',$xpassword)?error('verify_error'):usershow($xuserId,$page_size,$offset);
		mysql_close($con);
	});
	/**
	//----------管理类----------
	**/
	//（已验证）POST登录http://localhost/webservice/book/API.php/admin/login   12108238/12108238
	$app->post('/admin/login', function () {
		require 'conn.php';
		global $app;
		$req = $app->request(); 
		$postdata = file_get_contents("php://input");
		$request = json_decode($postdata);
		$IsJson="";
		is_json($postdata)?$IsJson=true:$IsJson=false;
		$xuserId = getPost($req,$request,$IsJson,'userId');
		$xpassword = getPost($req,$request,$IsJson,'password');
		if(adminverify($xuserId,$xpassword)){
			found();
		}
		mysql_close($con);
	});
	//（已验证）GET图书搜索http://localhost/webservice/book/API.php/admin/search/12108238/1/page=1/php
	//（已验证）GET获取图书列表http://localhost/webservice/book/API.php/admin/search/12108238/5/page=1/all
	//（已验证）GET图书搜索http://localhost/webservice/book/API.php/admin/search/12108238/1/page=0/php
	//（已验证）GET获取图书列表http://localhost/webservice/book/API.php/admin/search/12108238/5/page=0/all
	$app->get('/admin/search/:userId/:type/page=:page/:keyword', function ($xuserId,$xtype,$xpage,$xkeyword){
		require 'conn.php';
		$page_size=pagesize;
		$offset=($xpage-1)*$page_size;
		if($xtype=='5'){
			$xkeyword='';//必须要传$xkeyword不然无法访问，将input中空值自动变为all
		}
		if(!identity('user','user_rank','图书管理','user_id',$xuserId)){
			error('rankverify_error');
		}
		else{
				switch ($xtype) {
				case '1'://书名
					$xtype='book_name';
					break;
				case '2'://book_id
					$xtype='id';
					break;
				case '3'://作者
					$xtype='book_author';
					break;
				case '4'://种类
					$xtype='book_type';
					break;
				case '5'://全部
					$xtype='';
					break;
				default:
					error('url_error');
					break;
			}
			search($xuserId,1,$xtype,$page_size,$offset,$xkeyword);
		}
		
		mysql_close($con);
	});
	//（已验证）GET归还图书http://localhost/webservice/book/API.php/admin/confirm/47/12108238/12108238
	$app->get('/admin/confirm/:bookId/:userId/:password', function ($xbookId,$xuserId,$xpassword) {
		require 'conn.php';
		if(adminverify($xuserId,$xpassword)){
			confirm($xbookId);
		}
		mysql_close($con);
	});
	//（已验证）GET修改图书为已超期http://localhost/webservice/book/API.php/admin/alter/100/12108238/12108238
	$app->get('/admin/alter/:bookId/:userId/:password', function ($xbookId,$xuserId,$xpassword) {
		require 'conn.php';
		/*global $app;
		$req = $app->request(); 
		$postdata = file_get_contents("php://input");
		$request = json_decode($postdata);
		$IsJson="";
		is_json($postdata)?$IsJson=true:$IsJson=false;*/
		if(adminverify($xuserId,$xpassword)){
			alter($xbookId);
		}
		mysql_close($con);
	});
	//（已验证）GET更新图书资料http://localhost/webservice/book/API.php/admin/renew/47/9787111358732/12108238/12108238
	$app->get('/admin/renew/:bookId/:bookIsbn/:userId/:password', function ($xbookId,$xbookIsbn,$xuserId,$xpassword) {
		require 'conn.php';
		/*global $app;
		$req = $app->request(); 
		$postdata = file_get_contents("php://input");
		$request = json_decode($postdata);
		$IsJson="";
		is_json($postdata)?$IsJson=true:$IsJson=false;*/
		if(adminverify($xuserId,$xpassword)){
			renew($xbookId,$xbookIsbn);
		}
		mysql_close($con);
	});
	//（已验证）GET添加图书http://localhost/webservice/book/API.php/admin/add/9787111358732/移动端/12108238/12108238
	$app->get('/admin/add/:bookIsbn/:bookType/:userId/:password',function($xbookIsbn,$xbookType,$xuserId,$xpassword){
		require 'conn.php';
		if(adminverify($xuserId,$xpassword)){
			getIsbn('',$xbookIsbn,$xbookType,0);
		}
	});
	//（已验证）GET删除图书http://localhost/webservice/book/API.php/admin/delete/100/12108238/12108238
	$app->get('/admin/delete/:bookId/:userId/:password', function ($xbookId,$xuserId,$xpassword) {
		require 'conn.php';
		if(adminverify($xuserId,$xpassword)){
			del($xbookId);
		}
		mysql_close($con);
	});
	//（已验证）GET查看已经借出的图书http://localhost/webservice/book/API.php/admin/showRe/12108238/12108238/page=1
	$app->get('/admin/showRe/:userId/:password/page=:page', function ($xuserId,$xpassword,$xpage) {
		require 'conn.php';
		$page_size=pagesize;
		$offset=($xpage-1)*$page_size;
		if(adminverify($xuserId,$xpassword)){
			adminshow($page_size,$offset);
		}
		mysql_close($con);
	});
	//（已验证）GET查看已超期的图书http://localhost/webservice/book/API.php/admin/showOut/12108238/12108238
	$app->get('/admin/showOut/:userId/:password', function ($xuserId,$xpassword) {
		require 'conn.php';
		//先验证再输出
		if(adminverify($xuserId,$xpassword)){
			showOut();
		}
		mysql_close($con);
	});
	/**
	//----------程序入口----------
	**/
	$app->run();
	/**
	//----------通用类----------公共------------
	**/
	//用于更改密码
	function passChange($userId,$password){
		$sql="update user set user_password='$password' where user_id='$userId'";
		//echo $sql."<br/>";
		$query = mysql_query($sql);
		!$query?error('sql_error'):found();
	}
	//用于点赞功能
	function like($bookKind,$userId){
		$sql="update bookbasic set favour=favour+1 where id='$bookKind'";
		//echo $sql."<br/>";
		$query = mysql_query($sql);
		if(!$query) {
			error('sql_error');
		}
		else {//在booklike表中插入记录
			$sql="insert booklike (book_kind,user_id) values ('$bookKind','$userId')";
			$query = mysql_query($sql);
			//echo $sql."<br/>";
			!$query?error('sql_error'):found();
		}
	}
	//用于获取书本总数以分页
	function bookSum($flag,$type,$userId){
		$where="";
		$sql="select count(*) from booklist";
		if(!$flag){//用户
			if($type){//已被借(自己)
				$sql="select count(*) from bookcirculate where user_id='$userId'";
			}
			else{//总数
				$where=" where book_status in ('已被借','未被借') ";
			}
		}
		else{//管理员
			if($type){//已被借
				$where=" WHERE book_status = '已被借'";
			}
			else{//总数
				$where=" where book_status in ('已被借','未被借','已超期') ";
			}
		}
		$sql=$sql.$where;
		$query = mysql_query($sql);
		//echo $sql."<br/>";
		!$query?error('sql_error'):query2json($query,'sum','count(*)');
	}
	//根据user_id返回密码提供校验
	function password($userId){
		$sql="select user_password from user where user_id='$userId'";
		$query = mysql_query($sql);
		//echo $sql."<br/>";
		!$query?error('sql_error'):query2json($query,'password','user_password');
	}
	//根据bookKind返回书本详细
	/*{
    "book_detail": {
        "book_name": ,
        "book_author": ,
        "book_pub": ,
        "book_type": ,
        "book_edit": ,
        "book_price": ,
        "book_pic": ,
        "book_link": ,
        "book_info": ,
        "favour": 
    },
    "book_list": [
        {
            "book_id": ,
            "book_status": 
        },
        {
             ...
        },
        ...
    ]
	}*/
	function detail($bookKind){
		$sql="SELECT book_name,book_author,book_pub,book_type,book_edit,book_price,book_pic,book_link,book_info,favour from bookbasic where id='$bookKind'";
		//echo $sql."<br/>";
		$query = mysql_query($sql);
		if(!$query) {
			error('sql_error');
		}
		else {
			$res = mysql_fetch_array($query);
			foreach ($res as $key => $value) {
				$res[$key]=toString($res[$key]);
			}
			unset($value);
			$book_detail = array('book_name'=>$res['book_name'],
								'book_author'=>$res['book_author'],
								'book_pub'=>$res['book_pub'],
								'book_type'=>$res['book_type'],
								'book_edit'=>$res['book_edit'],
								'book_price'=>$res['book_price'],
								'book_pic'=>$res['book_pic'],
								'book_link'=>$res['book_link'],
								'book_info'=>$res['book_info'],
								'favour'=>$res['favour']);
			$sql="SELECT li.id book_id, book_status FROM booklist li WHERE book_kind = '$bookKind'";
			//echo $sql."<br/>";
			$query = mysql_query($sql);
			if(!$query) {
				error('sql_error');
			}
			else {
				$book_list = array();
				$i = 0;
				while($res = mysql_fetch_array($query)) {
					$book_list[$i] = array('book_id'=>toString($res['book_id']),
											'book_status'=>toString($res['book_status'])/*,
											'user_name'=>$res['user_name'],
											'created_at'=>$res['created_at']*/);			  
					$i++;
				}
				$array=array('book_detail'=>$book_detail,'book_list'=>$book_list);
				$response = json_encode($array);
				echo $response;
			}
		}
	}
	//返回最近添加的图书
	/*"book_kind":书本kind,
	"book_detail_url":书本详细url,
	"book_name":书本名称,
	"book_author":书本作者,
	"book_status":书本状态,
	"favour":点赞数,
	"book_pic":图书图片,
	"isLike":是否被赞*/
	function recentAdd($userId){
		$sql="SELECT book_kind, book_name,book_author, book_status, favour, book_pic, ( SELECT count(*) FROM booklist WHERE book_kind = ba.id ) AS sum, ( SELECT count(*) FROM booklist WHERE book_kind = ba.id AND book_status = '已被借' ) AS rent, CASE ba.id IN ( SELECT book_kind FROM booklike WHERE user_id = '$userId' ) WHEN FALSE THEN '0' ELSE '1' END AS isLike FROM booklist li JOIN bookbasic ba ON li.book_kind = ba.id WHERE book_time IN ( SELECT max(book_time) FROM booklist ) ORDER BY book_time";
		sql2response_book_outline($sql,false);
	}
	//用于搜索与获取图书列表(kind)
	/*"book_kind":书本kind,
	"book_detail_url":书本详细url,
	"book_name":书本名称,
	"book_author":书本作者,
	"book_status":书本状态,
	"favour":点赞数,
	"book_pic":图书图片,
	"isLike":是否被赞*/
	function search($userId,$flag,$type,$page_size,$offset,$keyword){
		$turn="";
		$where="";
		$sql="SELECT DISTINCT li.book_kind AS book_kind, book_name,book_author, ( SELECT count(*) FROM booklist WHERE book_kind = ba.id ) AS sum, ( SELECT count(*) FROM booklist WHERE book_kind = ba.id AND book_status = '已被借' ) AS rent, favour, book_pic, CASE ba.id IN ( SELECT book_kind FROM booklike WHERE user_id = '$userId' ) WHEN FALSE THEN '0' ELSE '1' END AS isLike FROM bookbasic ba JOIN booklist li ON ba.id = li.book_kind ";
		if($keyword==''){//获取列表
			if(!$flag){//用户
				$where=" where book_status in ('已被借','未被借') ";
			}
			else{//管理员
				$where=" where book_status not in ('未购买')";
			}
		}
		else{//搜索
			if(!$flag){//用户
				$type=='id'?
				$where=" where book_status in ('已被借','未被借') and li.id = '$keyword' ":
				$where=" where book_status in ('已被借','未被借') and $type like '%$keyword%' ";
			}
			else{//管理员
				$type=='id'?
				$where=" where book_status not in ('未购买') and li.id = '$keyword' ":
				$where=" where book_status not in ('未购买') and $type like '%$keyword%' ";
			}
			
		}
		$sql=$sql.$where." order by book_kind";
		if($offset>=0){
			$turn=" LIMIT $page_size OFFSET $offset ";
		}
		$sql=$sql.$turn;
		//echo $turn.'<br/>';
		sql2response_book_outline($sql,false);
	}
	/**
	//----------用户类----------
	**/
	//注册
	function register($userId,$userName,$password){
		$sql="insert into user (user_id,user_name,user_password) values ('$userId','$userName','$password')";
		//echo $sql."<br/>";
		$query = mysql_query($sql);
		$query?found():error('sql_error');
	}
	//完成扫一扫借书
	function swap($bookId,$userId){
		$updated_at = date('Y-m-d');//更新bookbasic表
		$sql="update booklist set book_status='已被借' where id='$bookId'";
		$query = mysql_query($sql);
		//echo $sql."<br/>";
		if(!$query) {
			error('sql_error');
		}
		else {//bookcirculate插入借书记录
			$sql="insert bookcirculate (book_id,user_id,created_at) values ('$bookId','$userId','$updated_at')";
			$query = mysql_query($sql);
			//echo $sql."<br/>";
			!$query?error('sql_error'):found();
		}
	}
	//查看曾借过的书
	/*"book_id":书本kind,
	"book_detail_url":书本详细url,
	"book_name":书本名称,
	"book_author":书本作者,
	"book_status":书本状态,
	"favour":点赞数,
	"book_pic":图书图片,
	"isLike":是否被赞
	"created_at":借阅时间,
	"return_at":剩余天数*/
	function usershow($userId,$page_size,$offset){//增加显示借阅时间、剩余时间
		$turn="";
		if($offset>=0){
			$turn=" LIMIT $page_size OFFSET $offset ";
		}
		$sql="SELECT ba.id AS book_kind, book_name,book_author, CASE updated_at WHEN '0000-00-00' THEN '未还' ELSE '已还' END AS book_status, favour, book_pic, CASE ba.id IN ( SELECT book_kind FROM booklike WHERE user_id = '$userId' ) WHEN FALSE THEN '0' ELSE '1' END AS isLike, created_at, datediff( date_add(created_at, INTERVAL 1 MONTH), now()) AS return_at FROM booklist li JOIN bookbasic ba ON li.book_kind = ba.id JOIN bookcirculate cir ON li.id = cir.book_id WHERE cir.user_id = '$userId' ORDER BY book_kind ";
		$sql=$sql.$turn;
		//echo $sql."<br/>";
		sql2response_book_outline($sql,true);
	}
	/**
	//----------管理类----------
	**/
	//完成书的return
	function confirm($bookId){
		if(!identity('booklist','id',$bookId,'book_status !','未被借')){
			error('bookverify_error');
		}
		else{
			$sql="update booklist set book_status='未被借' where id='$bookId'";
			$query = mysql_query($sql);
			//echo $sql."<br/>";
			if(!$query) {
				error('sql_error');
			}
			else {//更新bookcirculate
				$updated_at = date('Y-m-d');
				$sql="update bookcirculate set updated_at='$updated_at' where book_id='$bookId' and updated_at='0000-00-00'";
				$query = mysql_query($sql);
				//echo $sql."<br/>";
				!$query?error('sql_error'):found();
			}
		} 
	}
	//设置图书为已超期
	function alter($bookId){
		if(!identity('booklist','book_status','已被借','id',$bookId)){
			error('bookverify_error');
		}
		else{
			if(identity('bookcirculate','updated_at','0000-00-00','book_id',$bookId)){
				error('bookverify_error');
			}
			else{
				$sql="update booklist set book_status='已超期' where id=$bookId";
				$query = mysql_query($sql);//更新bookbasic
				//echo $sql."<br/>";
				!$query?error('sql_error'):found();
			}
		}
	}
	//更新图书数据准备
	function renew($bookId,$bookIsbn){
		$sql="select DISTINCT book_type from bookbasic ba,booklist li where ba.id=li.book_kind and li.id='$bookId'";//查出书在数据库中的类型填写getIsbn
		$query = mysql_query($sql);
		//echo $sql."<br/>";
		if(!$query){
			error('sql_error');
		}
		else{
			$res=mysql_fetch_array($query);
			$bookType=toString($res['book_type']);//要求表中bookId与填入的bookIsbn对应
			!identity('bookbasic','id',$bookId,'book_isbn',$bookIsbn)?error('isbn_error'):getIsbn($bookId,$bookIsbn,$bookType,1);
		}
	}
	//更新图书数据
	function update($bookId,$bookIsbn,$bookName,$bookAuthor,$bookType,$bookPic,$bookEdit,$bookPrice,$bookPub,$bookInfo,$bookLink){//要求表中bookName与填入的bookIsbn对应
		/*$sql="select count(*) from bookbasic ba join booklist li on li.book_kind=ba.id where book_name='$bookName' and book_isbn='$bookIsbn'";
		$query = mysql_query($sql);
		//echo $sql."<br/>";
		if(!$query){
			error('sql_error');
		}
		else{
			$res=mysql_fetch_array($query);
			if($res['count(*)']==0){
				error('bookverify_error');
			}
			else{*/
				$sql="UPDATE bookbasic set book_isbn='$bookIsbn',book_name='$bookName',book_author='$bookAuthor',book_type='$bookType',book_pic='$bookPic',book_edit='$bookEdit',book_price='$bookPrice',book_pub='$bookPub',book_info='$bookInfo',book_link='$bookLink' where id=(select book_kind from booklist where id='$bookId')";
				//$query = mysql_query($sql);
				echo $sql.'<br/>';
				!$query?error('sql_error'):found();
			/*}
		}*/
	}
	//关联isbn与id
    function getIsbn($bookId,$bookIsbn,$bookType,$IsRenew){//book_name book_author book_pub book_type book_edit book_price book_info
    	$curl = curl_init('https://api.douban.com/v2/book/isbn/:'.$bookIsbn); 
		curl_setopt($curl, CURLOPT_FAILONERROR, true); 
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); 
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); //
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //
		$result = curl_exec($curl); 
        curl_close($curl); 
  		$arr = (Array)json_decode($result,true); 
 		$bookName=$arr['title'];
 		//echo $book_name;
 		$bookAuthor="";
 		foreach ($arr['author'] as $key => $value) {
 			//var_dump($value);
 			$bookAuthor=$bookAuthor.$value;
 		}
 		//echo $bookAuthor;
 		$bookPic=$arr['image'];
 		//echo $bookPpic;
 		$bookEdit=$arr['publisher'];
 		//echo $bookEdit;
 		$bookPrice=$arr['price'];
 		//echo $bookPrice;
 		$bookPub=$arr['pubdate'];
 		//echo $bookPub;
 		$bookInfo=$arr['summary'];
 		//echo $bookInfo;
 		$bookLink=$arr['alt'];
 		//echo $bookInfo;
 		if($IsRenew){//更新
 			update($bookId,$bookIsbn,$bookName,$bookAuthor,$bookType,$bookPic,$bookEdit,$bookPrice,$bookPub,$bookInfo,$bookLink);
 		}
 		else{//添加
	 		if(!identity('bookbasic','book_name',$bookName,'','')){//basic中不存在两张表中add
	    		add($bookIsbn,$bookName,$bookAuthor,$bookType,$bookPic,$bookEdit,$bookPrice,$bookPub,$bookInfo,$bookLink);
	    	}
	    	else{//basic中存在只在一张表中add_insert
		 		add_insert_booklist($bookIsbn);
	    	}
 		}
    }
	//添加图书数据
	function add($bookIsbn,$bookName,$bookAuthor,$bookType,$bookPic,$bookEdit,$bookPrice,$bookPub,$bookInfo,$bookLink){
		$sql="insert bookbasic (book_isbn,book_name,book_author,book_type,book_edit,book_price,book_pub,book_info) values ('$bookIsbn','$bookName','$bookAuthor','$bookType','$bookEdit','$bookPrice','$bookPub','$bookInfo')";
		$query = mysql_query($sql);//bookbasic插入图书数据
		//echo $sql.'<br/>';
		!$query?error('sql_error'):add_insert_booklist($bookIsbn);
	}
	//向booklist中插入数据
	function add_insert_booklist($bookIsbn){
		$buyTime = date('Y-m-d');
		$sql="INSERT booklist (book_kind, book_time) VALUE (( SELECT id FROM bookbasic WHERE book_isbn = '$bookIsbn' ),'$buyTime')";
		$query = mysql_query($sql);
		//echo $sql.'<br/>';
		!$query?error('sql_error'):found();
	}

	//删除图书数据
	function del($bookId){//从bookbasic删除数据
		$sql="select book_kind from booklist where id='$bookId'";
		$query = mysql_query($sql);
		//echo $sql."<br/>";
		if(!$query){
			error('sql_error');
		}
		else{
			$res=mysql_fetch_array($query);
			$bookKind=$res['book_kind'];
			$sql="delete from booklist where id='$bookId'";
			$query = mysql_query($sql);
			//echo $sql."<br/>";
			if(!$query){
				error('sql_error');
			}else{
				$sql="delete from bookcirculate where book_id='$bookId'";
				$query = mysql_query($sql);
				//echo $sql."<br/>";
				if(!$query){
					error('sql_error');
				}else{
					if(identity('booklist','book_kind',$bookKind,'','')){//找到重复不删除bookbasic
						found();
					}
					else{
						$sql="delete from bookbasic where id='$bookKind'";
						$query = mysql_query($sql);
						//echo $sql."<br/>";
						!$query?error('sql_error'):found();
					}
				}
					
			}
		}

	}
	//查看已借出的书
	/*"book_kind":书本kind,
	"book_name":书本名称,
	"boou_author":作者,
	"book_status":书本状态,
	"user_name":借阅人,
	"favour":点赞数,
	"book_pic":图书图片,
	"created_at":借阅时间,
	"return_at":剩余天数*/
	function adminshow($page_size,$offset){//增加显示借阅人、借书时间、剩余天数（30天）
		$turn="";
		if($offset>=0){
			$turn=" LIMIT $page_size OFFSET $offset ";
		}
		$sql="SELECT li.id AS book_id, book_name,book_author, book_status, `user`.user_name, favour, book_pic,  created_at, datediff( date_add(created_at, INTERVAL 1 MONTH), now()) AS return_at FROM booklist li LEFT JOIN bookbasic ba ON li.book_kind = ba.id LEFT JOIN bookcirculate cir ON cir.book_id = li.id LEFT JOIN `user` ON `user`.user_id = cir.user_id WHERE book_status = '已被借' or '已超期' AND cir.updated_at = '0000-00-00' ORDER BY book_id";
		//echo $sql."<br/>";
		$sql=$sql.$turn;
		$query = mysql_query($sql);
		$response = array();
		if(!$query) {
			error('sql_error');
		}
		else {
			$i = 0;
			while($res = mysql_fetch_array($query)) {
				$response[$i] = array(  'book_id'=>toString($res['book_id']),
										'book_name'=>toString($res['book_name']),
										'book_status'=>toString($res['book_status']),
										'user_name'=>toString($res['user_name']),
										'favour'=>toString($res['favour']),
										'book_pic'=>toString($res['book_pic']),
										'created_at'=>toString($res['created_at']),
										'return_at'=>toString($res['return_at']));			  
				$i++;
			}
			$response = json_encode($response);
			echo $response;
		}
	}
	//查看已超期图书
	/*"book_id":书本id,
	"book_name":书本名称,
	"boou_author":作者,
	"book_status":书本状态,
	"user_name":借阅人,
	"favour":点赞数,
	"book_pic":图书图片,
	"created_at":借阅时间,
	"return_at":剩余天数*/
	function showOut(){
		$sql="SELECT li.id as book_id, book_name,book_author, book_status, `user`.user_name, favour, book_pic, created_at, datediff( date_add(created_at, INTERVAL 1 MONTH), now()) AS return_at FROM booklist li LEFT JOIN bookcirculate cir ON li.id = cir.book_id LEFT JOIN bookbasic ba ON ba.id = li.book_kind LEFT JOIN `user` ON `user`.id = cir.user_id HAVING return_at < 0";
		//echo $sql."<br/>";
		$query = mysql_query($sql);
		$response = array();
		if(!$query) {
			error('sql_error');
		}
		else {
			$i = 0;
			while($res = mysql_fetch_array($query)) {
				$response[$i] = array(  'book_id'=>toString($res['book_id']),
										'book_name'=>toString($res['book_name']),
										'book_status'=>toString($res['book_status']),
										'user_name'=>toString($res['user_name']),
										'favour'=>toString($res['favour']),
										'book_pic'=>toString($res['book_pic']),
										'created_at'=>toString($res['created_at']),
										'return_at'=>toString($res['return_at']));			  
				$i++;
			}
			$response = json_encode($response);
			echo $response;
		}
	}
	/**
	//----------工具函数----------
	**/
	//验证管理员身份
	function adminverify($xuserId,$xpassword){
		if(!identity('user','user_rank','图书管理','user_id',$xuserId)){
			error('rankverify_error');
		}
		else{
			$xpassword=setSecret($xpassword);
			if(!identity('user','user_id',$xuserId,'user_password',$xpassword)){
				error('verify_error');
			}
			else{
				return 1;
			}
		}
	}
	//将sql语句结果输出
	function sql2response_book_outline($sql,$isTime){
		//echo $sql."<br/>";
		$query = mysql_query($sql);
		if(!$query) {
			error('sql_error');
		}
		else {
			$i = 0;
			$response = array();
			while($res = mysql_fetch_array($query)) {
				!$isTime?
				$book_status="共有".toString($res['sum'])."本,已借出".toString($res['rent'])."本":
				$book_status=$res['book_status'];
				$book_detail_url=url."/public/detail/$res[book_kind]";
				if(!$isTime){
					$response[$i] = array(  'book_kind'=>toString($res['book_kind']),
											'book_detail_url'=>$book_detail_url,
											'book_name'=>toString($res['book_name']),
											'book_author'=>toString($res['book_author']),
											'book_status'=>$book_status,
											'favour'=>toString($res['favour']),
											'book_pic'=>toString($res['book_pic']),
											'isLike'=>toString($res['isLike']));		
				}
				else{
					$response[$i] = array(  'book_kind'=>toString($res['book_kind']),
											'book_detail_url'=>toString($book_detail_url),
											'book_name'=>toString($res['book_name']),
											'book_author'=>toString($res['book_author']),
											'book_status'=>toString($book_status),
											'favour'=>toString($res['favour']),
											'book_pic'=>toString($res['book_pic']),
											'isLike'=>toString($res['isLike']),
											'created_at'=>toString($res['created_at']),
											'return_at'=>toString($res['return_at']));
				}		  
				$i++;
			}
			$response = json_encode($response);
			echo $response;
		}
	}
	//验证唯一性
	function identity($table,$row1,$value1,$row2,$value2){
		$value2!=''&&$row2!=''?$sql="select count(*) from $table where $row1= '$value1' and $row2= '$value2'":$sql="select count(*) from $table where $row1= '$value1'";
		$query = mysql_query($sql);
		//echo $sql."<br/>";
		$response = array();
		if(!$query) {
			return 0;
		}
		else {
			$res = mysql_fetch_array($query);
			//echo $res['count(*)'];
			if($res['count(*)']!=0){//找到则为重复返回1		
				return 1;
			}
			else{
				return 0;
			}
		}
	}
	//获取post数据
	function getPost($req,$request,$IsJson,$name){
		//var_dump($IsJson);
		if($IsJson){//json
			return $request->$name;
		}
		else{//nojson
			return $req->params($name);
		}
	}
	//将查询结果(单个)json输出
	function query2json($query,$from,$to){
		$res = mysql_fetch_array($query);
		$response = array($from=>$res[$to]);
		$response = json_encode($response);
		echo $response;
	}
	//判断post数据是否为json
	function is_json($string) {
		json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE);
	}
	//null转为string
	function toString($string){
		if($string ==null)
			return "";
		else return $string;
	}
	//向外输出错误信息
	function error($type){
		switch ($type) {
			case 'rankverify_error':
				$info="当前用户没有足够权限";
				break;
			case 'bookverify_error':
				$info="书在数据库中状态出错";
				break;
			case 'url_error':
				$info="post中url错误";
				break;
			case 'verify_error':
				$info="提交的用户名/密码错误";
				break;
			case 'password_error':
				$info="两次输入的密码不一致";
				break;
			case 'id_error':
				$info="id重复";
				break;
			case 'sql_error':
				$info="sql语句编写出错";
				break;
			case 'like_error':
				$info="您已赞过此书";
				break;
			case 'id_error':
				$info="此id已存在于数据库中，如需替换请删除原有信息";
				break;
			case 'isbn_error':
				$info="输入的ISBN不匹配";
			default:
				$info="杂七杂八的错误";
				break;
		}
		$response = array('status'=>"0",'info'=>$info);
		$response = json_encode($response);
		echo $response;
	}
	//返回成功信息
	function found(){
		$response = array('status'=>"1",'info'=>"成功");
		$response = json_encode($response);
		echo $response;
		//echo '{"status":"1","info":"成功"}';
	}
	//密码处理
	//UPDATE user set user_password=MD5(CONCAT(user_id,'nigoule'))
	//define("saltkey",'',true);
	function setSecret($pass){
		return md5( $pass.saltkey,false );
	}
?>