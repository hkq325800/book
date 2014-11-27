<?php
	//, JSON_UNESCAPED_UNICODE可让json转为明码
	require './Slim/Slim.php';
	\Slim\Slim::registerAutoloader();
	define("pagesize", 15, true);
	define("saltkey", 'nigoule');
	$app = new \Slim\Slim();

	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: 'GET'");
	header("Access-Control-Allow-Methods: 'POST'");
	header("Access-Control-Max-Age: '60'");

	/*----------通用类----------*/

	//（已验证）POST修改密码http://localhost/webservice/book/API.php/public/passChange/12108238   6da788b5325d4e487f38930e2cd90c08/ca8e4dea8b6c7e5dffb81548989ea0b2/ca8e4dea8b6c7e5dffb81548989ea0b2
	$app->post('/public/passChange/:userId',function($xuserId){
		require 'conn.php';
		global $app;
		$req = $app->request(); 
		$postdata = file_get_contents("php://input");
		$request = json_decode($postdata);
		$xoldPass = $request->oldPass;
		$xnewPass = $request->newPass;
		$xrenewPass = $request->renewPass;
		if($xnewPass!=$xrenewPass) 
			error('password_error');//确定password是否等于repassword
		else{
			$xnewPass=setSecret($xnewPass);
			$xoldPass=setSecret($xoldPass);
			!identity('user','user_id',$xuserId,'user_password',$xoldPass)?error('verify_error'):passChange($xuserId,$xnewPass);
		}
		mysql_close($con);
	});
	//（已验证）GET点赞http://localhost/webservice/book/API.php/public/getLike/1/12108238/6da788b5325d4e487f38930e2cd90c08
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
	$app->get('/public/bookSum/:flag/:type/:userId',function($flag,$type,$xuserId){//flag决定身份，type决定搜索类型
		require 'conn.php';
		bookSum($flag,$type,$xuserId);
		mysql_close($con);
	});
	//（已验证）GET返回密码http://localhost/webservice/book/API.php/public/password/12108413
	$app->get('/public/password/:userId',function ($xuserId){
		require 'conn.php';
	    !identity('user','user_id',$xuserId,'','')?error('verify_error'):password($xuserId);
	    mysql_close($con);
	});
	//GET显示图书详细(book_kind->booklist)http://localhost/webservice/book/API.php/public/detail/47/12108413
	$app->get('/public/detail/:bookKind/:userId',function($xbookKind,$xuserId){
		require 'conn.php';
		!identity('user','user_id',$xuserId,'','')?error('verify_error'):detail($xbookKind,$xuserId);
		mysql_close($con);
	});
	//GET最近添加的图书http://localhost/webservice/book/API.php/public/recentAdd/12108413
	$app->get('/public/recentAdd/:userId',function($xuserId){
		require 'conn.php';
		/*$page_size=pagesize;
		$offset=($xpage-1)*$page_size;*/
		!identity('user','user_id',$xuserId,'','')?error('verify_error'):recentAdd($xuserId);
		mysql_close($con);
	});

	/*----------用户类----------*/

	//（已验证）POST登录http://localhost/webservice/book/API.php/normal/login   12108413/12345
	$app->post('/normal/login', function () {
		require 'conn.php';
		global $app;
		$req = $app->request(); 
		$postdata = file_get_contents("php://input");
		$request = json_decode($postdata);
		$xuserId = $request->userId;
		$xpassword = $request->password;
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
		$xuserId = $request->userId;
		$xuserName = $request->userName;
		$xpassword = $request->password;
		$xrepassword = $request->rePassword;
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
			$xkeyword='';//~~~~~必须要传$xkeyword不然无法访问，将input中空值自动变为all
		$page_size=pagesize;
		$offset=($xpage-1)*$page_size;
		if($xpage=='not'){//可以手动设置是否分页
			$page_size='';
			$offset='';
		}
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
	//（已验证）GET已借阅http://localhost/webservice/book/API.php/normal/showRe/12108413/12108413
	$app->get('/normal/showRe/:userId/:password/page=:page', function ($xuserId,$xpassword,$xpage) {
		require 'conn.php';
		//先验证再输出
		$page_size=pagesize;
		$offset=($xpage-1)*$page_size;
		$xpassword=setSecret($xpassword);
		!identity('user','user_id',$xuserId,'user_password',$xpassword)?error('verify_error'):usershow($xuserId,$page_size,$offset);
		mysql_close($con);
	});

	/*----------管理类----------*/

	//（已验证）POST登录http://localhost/webservice/book/API.php/admin/login   12108238/12108238
	$app->post('/admin/login', function () {
		require 'conn.php';
		global $app;
		$req = $app->request(); 
		$postdata = file_get_contents("php://input");
		$request = json_decode($postdata);
		$xuserId = $request->userId;
		$xpassword = $request->password;
		if(adminverify($xuserId,$xpassword)){
			found();
		}
		mysql_close($con);
	});
	//（已验证）GET图书搜索http://localhost/webservice/book/API.php/admin/search/12108238/1/page=1/php
	//（已验证）GET获取图书列表http://localhost/webservice/book/API.php/admin/search/12108238/5/page=1/all
	//（已验证）GET图书搜索http://localhost/webservice/book/API.php/admin/search/12108238/1/page=not/php
	//（已验证）GET获取图书列表http://localhost/webservice/book/API.php/admin/search/12108238/5/page=not/all
	$app->get('/admin/search/:userId/:type/page=:page/:keyword', function ($xuserId,$xtype,$xpage,$xkeyword){
		require 'conn.php';
		$page_size=pagesize;
		$offset=($xpage-1)*$page_size;
		if($xpage=='not'){//可以手动设置是否分页
			$page_size='';
			$offset='';
		}
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
	//（已验证）POST修改图书信息http://localhost/webservice/book/API.php/admin/update/12108238/100/12108238
	$app->post('/admin/update/:bookKin/:userIdd/:password', function ($xbookKind,$xuserId,$xpassword) {
		require 'conn.php';
		global $app;
		$req = $app->request(); 
		$postdata = file_get_contents("php://input");
		$request = json_decode($postdata);
		$book_name = $request->bookName;
		$book_author = $request->bookAuthor;
		$book_pub = $request->bookPub;
		$book_type = $request->bookType;
		$book_edit = $request->bookEdit;
		$book_price = $request->bookPrice;
		$book_status = $request->bookStatus;//
		$book_pic = $request->bookPic;
		$book_link = $request->bookLink;
		$book_info = $request->bookInfo;
		if(adminverify($xuserId,$xpassword)){
			update($xbookId,$book_name,$book_author,$book_pub,$book_type,$book_edit,$book_price,$book_status,$book_pic,$book_link,$book_info);
		}
		mysql_close($con);
	});
	//（已验证）GET添加图书http://localhost/webservice/book/API.php/admin/add/100/9787111358732/移动端/12108238/12108238
	$app->get('/admin/add/:bookId/:bookIsbn/:bookType/:userId/:userPassword',function($xbookId,$xbookIsbn,$xbookType,$xuserId,$xpassword){//是否需要确定图书唯一性？
		require 'conn.php';
		if(adminverify($xuserId,$xpassword)){
			identity('booklist','id',$xbookId,'','')?error('id_error'):getIsbn($xbookId,$xbookIsbn,$xbookType);
		}
	});
	/*//（已验证）GET删除图书http://localhost/webservice/book/API.php/admin/delete/100/12108238/12108238
	$app->get('/admin/delete/:bookId/:userId/:password', function ($xbookId,$xuserId,$xpassword) {
		require 'conn.php';
		if(adminverify($xuserId,$xpassword)){
			del($xbookId);
		}
		mysql_close($con);
	});*/
	//（已验证）GET查看已经借出的图书http://localhost/webservice/book/API.php/admin/showRe/12108238/12108238/page=1
	$app->get('/admin/showRe/:userId/:password/page=:page', function ($xuserId,$xpassword,$xpage) {
		require 'conn.php';
		$page_size=pagesize;
		$offset=($xpage-1)*$page_size;
		//先验证再输出
		if(adminverify($xuserId,$xpassword)){
			adminshow($page_size,$offset);
		}
		mysql_close($con);
	});
	//GET查看已超期的图书http://localhost/webservice/book/API.php/admin/showOut/12108238/12108238
	$app->get('/admin/showOut/:userId/:password', function ($xuserId,$xpassword) {
		require 'conn.php';
		//先验证再输出
		if(adminverify($xuserId,$xpassword)){
			showOut();
		}
		mysql_close($con);
	});

	/*----------程序入口----------*/

	$app->run();

	/*----------处理函数----------*/
	/*------------公共------------*/
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
		/*}*/
	}
	//根据user_id返回密码提供校验
	function password($userId){
		$sql="select user_password from user where user_id='$userId'";
		$query = mysql_query($sql);
		//echo $sql."<br/>";
		!$query?error('sql_error'):query2json($query,'password','user_password');
	}
	//根据bookKind返回书本详细
	/*{	"book_name":书本名称,
		"book_author":书本作者,
		"book_pub":书本版次,
		"book_type":书本类型,
		"book_edit":书本出版社,
		"book_price":书本价格,
		"book_pic":图书图片,
		"book_link":图书url,
		"book_info":图书简介
		"favour":点赞数},
	  { "id":书本id(booklist),
		"book_status":书本状态,
		"user_name":借阅人,
		"created_at":借阅日期}*/
	function detail($bookKind,$userId){
		$sql="SELECT book_name,book_author,book_pub,book_type,book_edit,book_price,book_pic,book_link,book_info,favour from bookbasic where id='$bookKind'";
		//echo $sql."<br/>";
		$query = mysql_query($sql);
		$response = array();
		if(!$query) {
			error('sql_error');
		}
		else {
			$i = 0;
			while($res = mysql_fetch_array($query)) {
				$response[$i] = array(  'book_name'=>$res['book_name'],
										'book_author'=>$res['book_author'],
										'book_pub'=>$res['book_pub'],
										'book_type'=>$res['book_type'],
										'book_edit'=>$res['book_edit'],
										'book_price'=>$res['book_price'],
										'book_pic'=>$res['book_pic'],
										'book_link'=>$res['book_link'],
										'book_info'=>$res['book_info'],
										'favour'=>$res['favour']);			  
				$i++;
			}
			$sql="SELECT li.id book_id, book_status FROM booklist li WHERE book_kind = '$bookKind'";
			//echo $sql."<br/>";
			$query = mysql_query($sql);
			if(!$query) {
				error('sql_error');
			}
			else {
				while($res = mysql_fetch_array($query)) {
					$response[$i] = array(  'book_id'=>$res['book_id'],
											'book_status'=>$res['book_status']/*,
											'user_name'=>$res['user_name'],
											'created_at'=>$res['created_at']*/);			  
					$i++;
				}
				$response = json_encode($response);
				echo $response;
			}
		}
	}
	//返回最近添加的图书
	/*"book_kind":书本kind,
	"book_name":书本名称,
	"book_status":书本状态,
	"favour":点赞数,
	"book_pic":图书图片,
	"isLike":是否被赞*/
	function recentAdd($userId){
		$sql="SELECT book_kind, book_name, book_status, favour, book_pic, ( SELECT count(*) FROM booklist WHERE book_kind = ba.id ) AS sum, ( SELECT count(*) FROM booklist WHERE book_kind = ba.id AND book_status = '已被借' ) AS rent, CASE ba.id IN ( SELECT book_kind FROM booklike WHERE user_id = '$userId' ) WHEN FALSE THEN '0' ELSE '1' END AS isLike FROM booklist li JOIN bookbasic ba ON li.book_kind = ba.id WHERE book_time IN ( SELECT max(book_time) FROM booklist ) ORDER BY book_time";
		//echo $sql."<br/>";
		$query = mysql_query($sql);
		$response = array();
		if(!$query) {
			error('sql_error');
		}
		else {
			$i = 0;
			while($res = mysql_fetch_array($query)) {
				$book_status="共有".$res['sum']."本,已借出".$res['rent']."本";
				$response[$i] = array(  'book_kind'=>$res['book_kind'],
										'book_name'=>$res['book_name'],
										'book_status'=>$book_status,
										'favour'=>$res['favour'],
										'book_pic'=>$res['book_pic'],
										'isLike'=>$res['isLike']);			  
				$i++;
			}
			$response = json_encode($response);
			echo $response;
		}
	}
	//用于搜索与获取图书列表(kind)
	/*"book_kind":书本kind,
	"book_name":书本名称,
	"book_status":书本状态,
	"favour":点赞数,
	"book_pic":图书图片,
	"isLike":是否被赞*/
	function search($userId,$flag,$type,$page_size,$offset,$keyword){
		$turn="";
		$where="";
		$sql="SELECT DISTINCT li.book_kind AS book_kind, book_name, ( SELECT count(*) FROM booklist WHERE book_kind = ba.id ) AS sum, ( SELECT count(*) FROM booklist WHERE book_kind = ba.id AND book_status = '已被借' ) AS rent, favour, book_pic, CASE ba.id IN ( SELECT book_kind FROM booklike WHERE user_id = '$userId' ) WHEN FALSE THEN '0' ELSE '1' END AS isLike FROM bookbasic ba JOIN booklist li ON ba.id = li.book_kind ";
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
		if(!$page_size==''){
			$turn=" LIMIT $page_size OFFSET $offset ";
		}
		$sql=$sql.$turn;
		//echo $sql."<br/>";
		$query = mysql_query($sql);
		$response = array();
		if(!$query) {
			error('sql_error');
		}
		else {
			$i = 0;
			while($res = mysql_fetch_array($query)) {
				$book_status="共有".$res['sum']."本,已借出".$res['rent']."本";
				$response[$i] = array(  'book_kind'=>$res['book_kind'],
										'book_name'=>$res['book_name'],
										'book_status'=>$book_status,
										'favour'=>$res['favour'],
										'book_pic'=>$res['book_pic'],
										'isLike'=>$res['isLike']);			  
				$i++;
			}
			$response = json_encode($response);
			echo $response;
		}
	}
	/*------------用户------------*/
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
	"book_name":书本名称,
	"book_status":书本状态,
	"favour":点赞数,
	"book_pic":图书图片,
	"isLike":是否被赞
	"created_at":借阅时间,
	"return_at":剩余天数*/
	function usershow($userId,$page_size,$offset){//增加显示借阅时间、剩余时间
		$sql="SELECT ba.id AS book_kind, book_name, CASE updated_at WHEN '0000-00-00' THEN '未还' ELSE '已还' END AS book_status, favour, book_pic, CASE ba.id IN ( SELECT book_kind FROM booklike WHERE user_id = '$userId' ) WHEN FALSE THEN '0' ELSE '1' END AS isLike, created_at, datediff( date_add(created_at, INTERVAL 1 MONTH), now()) AS return_at FROM booklist li JOIN bookbasic ba ON li.book_kind = ba.id JOIN bookcirculate cir ON li.id = cir.book_id WHERE cir.user_id = '$userId' ORDER BY book_kind LIMIT $page_size OFFSET $offset";
		//echo $sql."<br/>";
		$query = mysql_query($sql);
		$response = array();
		if(!$query) {
			error('sql_error');
		}
		else {
			$i = 0;
			while($res = mysql_fetch_array($query)) {
				$response[$i] = array(  'book_kind'=>$res['book_kind'],
										'book_name'=>$res['book_name'],
										'book_status'=>$res['book_status'],
										'favour'=>$res['favour'],
										'book_pic'=>$res['book_pic'],
										'isLike'=>$res['isLike'],
										'created_at'=>$res['created_at'],
										'return_at'=>$res['return_at']);		  
				$i++;
			}
			$response = json_encode($response);
			echo $response;
		}
	}
	/*------------管理------------*/
	//完成书的return
	function confirm($bookId){
		if(!identity('booklist','id',$bookId,'book_status','已被借')){
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
	//更新图书数据
	function update($bookKind,$bookName,$bookAuthor,$bookPub,$bookType,$bookEdit,$bookPrice,$bookStatus,$bookPic,$bookLink,$bookInfo){
		$sql="update bookbasic set book_name='$bookName',book_author='$bookAuthor',book_pub='$bookPub',book_type='$bookType',book_edit='$bookEdit',book_price='$bookPrice',book_pic='$bookPic',book_link='$bookLink',book_info='$bookInfo' where id=$bookKind";
		$query = mysql_query($sql);//更新bookbasic
		//echo $sql."<br/>";
		if(!$query){
			error('sql_error');
		}
		else{
			$sql="update booklist set book_status='$bookStatus' where book_kind='$bookKind'";
			$query = mysql_query($sql);//更新booklist
			//echo $sql."<br/>";
			!$query?error('sql_error'):found();
		}
	}
	//关联isbn与id
    function getIsbn($bookId,$bookIsbn,$bookType){//book_name book_author book_pub book_type book_edit book_price book_info
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
 		if(!identity('bookbasic','book_isbn',$bookIsbn,'','')){
    		add($bookId,$bookIsbn,$bookName,$bookAuthor,$bookType,$bookPic,$bookEdit,$bookPrice,$bookPub,$bookInfo,$bookLink);
    	}
    	else{
	 		add_insert_booklist($bookId,$bookIsbn);
    	}
    }
	//添加图书数据
	function add($bookId,$bookIsbn,$bookName,$bookAuthor,$bookType,$bookPic,$bookEdit,$bookPrice,$bookPub,$bookInfo,$bookLink){
		$sql="insert bookbasic ($bookId,book_isbn,book_name,book_author,book_type,book_edit,book_price,book_pub,book_info) values ('$bookId',$bookIsbn','$bookName','$bookAuthor','$bookType','$bookEdit','$bookPrice','$bookPub','$bookInfo')";
		$query = mysql_query($sql);//bookbasic插入图书数据
		//echo $sql.'<br/>';
		!$query?error('sql_error'):add_insert_booklist($bookIsbn);
	}
	//向booklist中插入数据
	function add_insert_booklist($bookId,$bookIsbn){
		$buyTime = date('Y-m-d');
		$sql="INSERT booklist (id,book_kind, book_time) VALUE ($bookId,( SELECT id FROM bookbasic WHERE book_isbn = '$bookIsbn' ),'$buyTime')";
		$query = mysql_query($sql);
		//echo $sql.'<br/>';
		!$query?error('sql_error'):found();
	}
	//删除图书数据
	function del($bookId){//从bookbasic删除数据
		identity('booklist','book_kind',$bookId);
		$sql="delete from bookbasic where id='$bookId'";
		$query = mysql_query($sql);
		//echo $sql."<br/>";
		if(!$query) {
			error('sql_error');
		}
		else {//从booklist删除数据
			$sql="delete from booklist where book_id='$bookId'";
			$query = mysql_query($sql);
			//echo $sql."<br/>";
			!$query?error('sql_error'):found();
		}
	}
	//查看已借出的书
	/*"book_kind":书本kind,
	"book_name":书本名称,
	"book_status":书本状态,
	"user_name":借阅人,
	"favour":点赞数,
	"book_pic":图书图片,
	"isLike":是否被赞,
	"created_at":借阅时间,
	"return_at":剩余天数*/
	function adminshow($page_size,$offset){//增加显示借阅人、借书时间、剩余天数（30天）
		$sql="SELECT li.id AS book_id, book_name, book_status, `user`.user_name, favour, book_pic, CASE ba.id IN ( SELECT book_kind FROM booklike WHERE user_id = '12108238' ) WHEN FALSE THEN '0' ELSE '1' END AS isLike, created_at, datediff( date_add(created_at, INTERVAL 1 MONTH), now()) AS return_at FROM booklist li LEFT JOIN bookbasic ba ON li.book_kind = ba.id LEFT JOIN bookcirculate cir ON cir.book_id = li.id LEFT JOIN `user` ON `user`.user_id = cir.user_id WHERE book_status = '已被借' AND cir.updated_at = '0000-00-00' ORDER BY book_id LIMIT $page_size OFFSET $offset";
		//echo $sql."<br/>";
		$query = mysql_query($sql);
		$response = array();
		if(!$query) {
			error('sql_error');
		}
		else {
			$i = 0;
			while($res = mysql_fetch_array($query)) {
				$response[$i] = array(  'book_id'=>$res['book_id'],
										'book_name'=>$res['book_name'],
										'book_status'=>$res['book_status'],
										'user_name'=>$res['user_name'],
										'favour'=>$res['favour'],
										'book_pic'=>$res['book_pic'],
										'isLike'=>$res['isLike'],
										'created_at'=>$res['created_at'],
										'return_at'=>$res['return_at']);			  
				$i++;
			}
			$response = json_encode($response);
			echo $response;
		}
	}
	//查看已超期图书
	/*"book_id":书本id,
	"book_name":书本名称,
	"book_status":书本状态,
	"user_name":借阅人,
	"favour":点赞数,
	"book_pic":图书图片,
	"isLike":是否被赞,
	"created_at":借阅时间,
	"return_at":剩余天数*/
	function showOut(){
		$sql="SELECT li.id as book_id, book_name, book_status, `user`.user_name, favour, book_pic, CASE ba.id IN ( SELECT book_kind FROM booklike WHERE user_id = '12108238' ) WHEN FALSE THEN '0' ELSE '1' END AS isLike, created_at, datediff( date_add(created_at, INTERVAL 1 MONTH), now()) AS return_at FROM booklist li LEFT JOIN bookcirculate cir ON li.id = cir.book_id LEFT JOIN bookbasic ba ON ba.id = li.book_kind LEFT JOIN `user` ON `user`.id = cir.user_id HAVING return_at < 0";
		//echo $sql."<br/>";
		$query = mysql_query($sql);
		$response = array();
		if(!$query) {
			error('sql_error');
		}
		else {
			$i = 0;
			while($res = mysql_fetch_array($query)) {
				$response[$i] = array(  'book_id'=>$res['book_id'],
										'book_name'=>$res['book_name'],
										'book_status'=>$res['book_status'],
										'user_name'=>$res['user_name'],
										'favour'=>$res['favour'],
										'book_pic'=>$res['book_pic'],
										'isLike'=>$res['isLike'],
										'created_at'=>$res['created_at'],
										'return_at'=>$res['return_at']);			  
				$i++;
			}
			$response = json_encode($response);
			echo $response;
		}
	}

	/*----------工具函数----------*/

	//验证唯一性
	function identity($table,$row1,$value1,$row2,$value2){
		$value2!=''&&$row2!=''?$sql="select count(*) from $table where $row1='$value1' and $row2='$value2'":$sql="select count(*) from $table where $row1='$value1'";
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
	//将查询结果(单个)json输出
	function query2json($query,$from,$to){
		$res = mysql_fetch_array($query);
		$response = array($from=>$res[$to]);
		$response = json_encode($response);
		echo $response;
	}
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