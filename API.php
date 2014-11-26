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
		echo 'hh';
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
	$app->get('/public/getLike/:bookKind/:userId/:password',function($xbookKind,$xuserId,$xpassword){
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
	//（已验证）GET书本总数http://localhost/webservice/book/API.php/public/bookSum/0/1
	$app->get('/public/bookSum/:flag/:type',function($flag,$type){//flag决定身份，type决定搜索类型
		require 'conn.php';
		sum($flag,$type);
		mysql_close($con);
	});
	//（已验证）GET返回密码http://localhost/webservice/book/API.php/public/password/12108413
	$app->get('/public/password/:userId',function ($xuserId){
		require 'conn.php';
	    !identity('user','user_id',$xuserId,'','')?error('verify_error'):password($xuserId);
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
			identity('user','user_id',$xuserId,'','')?error('userid_error'):register($xuserId,$xuserName,$xpassword);//确定user_id是否重复，重复返回0//未找到且注册成功返回1
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
			case '2'://出版社
				$xtype='book_edit';
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
	$app->get('/normal/showRe/:userId/:password', function ($xuserId,$xpassword) {
		require 'conn.php';
		//先验证再输出
		$xpassword=setSecret($xpassword);
		!identity('user','user_id',$xuserId,'user_password',$xpassword)?error('verify_error'):usershow($xuserId);
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
				case '2'://出版社
					$xtype='book_edit';
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
	//（已验证）GET归还图书http://localhost/webservice/book/API.php/admin/return/47/12108238/12108238
	$app->get('/admin/return/:bookId/:userId/:password', function ($xbookId,$xuserId,$xpassword) {
		require 'conn.php';
		if(adminverify($xuserId,$xpassword)){
			confirm($xbookId);
		}
		mysql_close($con);
	});
	//（已验证）POST修改图书信息http://localhost/webservice/book/API.php/admin/update/12108238/100/12108238
	$app->post('/admin/update/:userId/:bookId/:password', function ($xuserId,$xbookId,$xpassword) {
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
		$book_pic = $request->bookPic;
		$book_link = $request->bookLink;
		$book_info = $request->bookInfo;
		if(adminverify($xuserId,$xpassword)){
			update($xbookId,$book_name,$book_author,$book_pub,$book_type,$book_edit,$book_price,$book_pic,$book_link,$book_info);
		}
		mysql_close($con);
	});
	//（已验证）GET添加图书http://localhost/webservice/book/API.php/admin/add/100/9787111358732/移动端/12108238/12108238
	$app->get('/admin/add/:bookId/:bookIsbn/:bookType/:userId/:userPassword',function($xbookId,$xbookIsbn,$xbookType,$xuserId,$xpassword){//是否需要确定图书唯一性？
		require 'conn.php';
		if(adminverify($xuserId,$xpassword)){
			getIsbn($xbookId,$xbookIsbn,$xbookType);
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
		//先验证再输出
		if(adminverify($xuserId,$xpassword)){
			adminshow($xuserId,$page_size,$offset);
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
		//echo $sql;
		$query = mysql_query($sql);
		!$query?error('sql_error'):found();
	}
	//用于点赞功能
	function like($bookKind,$userId){
		$sql="update bookbasic set favour=favour+1 where id='$bookKind'";
		//echo $sql;
		$query = mysql_query($sql);
		if(!$query) {
			error('sql_error');
		}
		else {//在booklike表中插入记录
			$sql="insert booklike (book_kind,user_id) values ('$bookKind','$userId')";
			$query = mysql_query($sql);
			//echo $sql;
			!$query?error('sql_error'):found();
		}
	}
	//用于获取书本总数以分页
	function sum($flag,$type){
		if(!$flag&&$type){//已被借(自己)
				error('');
		}
		else{
			$where="";
			$sql="select count(*) from booklist";
			if(!$flag){//用户
				if($type){//已被借(自己)
					//error('');
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
			//echo $sql;
			!$query?error('sql_error'):query2json($query,'sum','count(*)');
		}
	}
	//根据user_id返回密码提供校验
	function password($userId){
		$sql="select user_password from user where user_id='$userId'";
		$query = mysql_query($sql);
		//echo $sql;
		!$query?error('sql_error'):query2json($query,'password','user_password');
	}
	//用于搜索与获取图书列表(kind)
	function search($userId,$flag,$xtype,$page_size,$offset,$xkeyword){
		$turn="";
		$where="";
		$sql="SELECT DISTINCT ba.id as xid, book_name, book_author, book_type, book_edit, book_price, ( SELECT count(*) FROM booklist WHERE book_kind = ba.id ) AS sum, ( SELECT count(*) FROM booklist WHERE book_kind = ba.id AND book_status = '已被借' ) AS rent, favour, book_pic, CASE ba.id IN ( SELECT book_kind FROM booklike WHERE user_id = '$userId' ) WHEN FALSE THEN '0' ELSE '1' END AS isLike FROM bookbasic ba JOIN booklist li ON ba.id = li.book_kind ";
		if($xkeyword==''){//获取列表
			if(!$flag){//用户
				$where=" where book_status in ('已被借','未被借') ";
			}
			else{//管理员
				$where=" where book_status not in ('未购买')";
			}
		}
		else{//搜索
			!$flag?
			$where=" where book_status in ('已被借','未被借') and $xtype like '%$xkeyword%' ":
			$where=" where book_status not in ('未购买') and $xtype like '%$xkeyword%' ";
		}
		$sql=$sql.$where." order by xid";
		if(!$page_size==''){
			$turn=" LIMIT $page_size OFFSET $offset ";
		}
		$sql=$sql.$turn;
		//echo $sql;
		$query = mysql_query($sql);
		$response = array();
		if(!$query) {
			error('sql_error');
		}
		else {
			$i = 0;

			while($res = mysql_fetch_array($query)) {
				$book_status="共有".$res['sum']."本,已借出".$res['rent']."本";
				$response[$i] = array(  'book_id'=>$res['xid'],
										'book_name'=>$res['book_name'],
										'book_author'=>$res['book_author'],
										'book_type'=>$res['book_type'],
										'book_info'=>$res['book_edit'],
										'book_price'=>$res['book_price'],
										'book_status'=>$book_status,
										'favour'=>$res['favour'],
										'book_pic'=>$res['book_pic'],
										'isLike'=>$res['isLike']/*,
										'count'=>$res['count']*/);			  
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
		//echo $sql;
		$query = mysql_query($sql);
		$query?found():error('sql_error');
	}
	//完成扫一扫借书
	function swap($bookId,$userId){
		$updated_at = date('Y-m-d');//更新bookbasic表
		$sql="update booklist set book_status='已被借' where id='$bookId'";
		$query = mysql_query($sql);
		//echo $sql;
		if(!$query) {
			error('sql_error');
		}
		else {//bookcirculate插入借书记录
			$sql="insert bookcirculate (book_id,user_id,created_at) values ('$bookId','$userId','$updated_at')";
			$query = mysql_query($sql);
			//echo $sql;
			!$query?error('sql_error'):found();
		}
	}
	//查看曾借过的书
	function usershow($userId){//增加显示借阅时间、剩余时间
		$sql="SELECT li.id AS id, book_name, book_author, book_type, book_edit, book_price, created_at, datediff( date_add(created_at, INTERVAL 1 MONTH), now() ) AS return_at, CASE updated_at WHEN '0000-00-00' THEN '未还' ELSE '已还' END AS book_status, favour, book_pic, CASE li.id IN ( SELECT book_kind FROM booklike WHERE user_id = '$userId' ) WHEN FALSE THEN '0' ELSE '1' END AS isLike FROM booklist li JOIN bookbasic ba ON li.book_kind = ba.id JOIN bookcirculate cir ON li.id = cir.book_id WHERE cir.user_id = '$userId' ORDER BY id";
		//echo $sql;
		$query = mysql_query($sql);
		$response = array();
		if(!$query) {
			error('sql_error');
		}
		else {
			$i = 0;
			while($res = mysql_fetch_array($query)) {
				$response[$i] = array(  'book_id'=>$res['id'],
										'book_name'=>$res['book_name'],
										'book_author'=>$res['book_author'],
										'book_type'=>$res['book_type'],
										'book_info'=>$res['book_edit'],
										'book_price'=>$res['book_price'],
										'book_status'=>$res['book_status'],
										'created_at'=>$res['created_at'],
										'return_at'=>$res['return_at'],
										'favour'=>$res['favour'],
										'book_pic'=>$res['book_pic'],
										'isLike'=>$res['isLike']);		  
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
			//echo $sql;
			if(!$query) {
				error('sql_error');
			}
			else {//更新bookcirculate
				$updated_at = date('Y-m-d');
				$sql="update bookcirculate set updated_at='$updated_at' where book_id='$bookId' and updated_at='0000-00-00'";
				$query = mysql_query($sql);
				//echo $sql;
				!$query?error('sql_error'):found();
			}
		} 
	}
	//更新图书数据
	function update($bookId,$bookName,$bookAuthor,$bookPub,$bookType,$bookEdit,$bookPrice,$bookPic,$bookLink,$bookInfo){
		$sql="update bookbasic set book_name='$bookName',book_author='$bookAuthor',book_pub='$bookPub',book_type='$bookType',book_edit='$bookEdit',book_price='$bookPrice',book_pic='$bookPic',book_link='$bookLink',book_info='$bookInfo' where id=$bookId";
		$query = mysql_query($sql);//更新bookbasic
		//echo $sql;
		!$query?error('sql_error'):found();
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
    		add($bookIsbn,$bookName,$bookAuthor,$bookType,$bookPic,$bookEdit,$bookPrice,$bookPub,$bookInfo,$bookLink);
    	}
    	else{
	 		add_insert_booklist($bookIsbn);
    	}
    }
	//添加图书数据
	function add($bookIsbn,$bookName,$bookAuthor,$bookType,$bookPic,$bookEdit,$bookPrice,$bookPub,$bookInfo,$bookLink){
		//先确定书的信息是否已存在
		$sql="insert bookbasic (book_isbn,book_name,book_author,book_type,book_edit,book_price,book_pub,book_info) values ('$bookIsbn','$bookName','$bookAuthor','$bookType','$bookEdit','$bookPrice','$bookPub','$bookInfo')";
		$query = mysql_query($sql);//bookbasic插入图书数据
		//echo $sql;
		!$query?error('sql_error'):add_insert_booklist($bookIsbn);
	}
	//向booklist中插入数据
	function add_insert_booklist($bookIsbn){
		$buyTime = date('Y-m-d');
		$sql="INSERT booklist (book_kind, book_time) VALUE (( SELECT id FROM bookbasic WHERE book_isbn = '$bookIsbn' ),'$buyTime')";
		$query = mysql_query($sql);
		//echo $sql;
		!$query?error('sql_error'):found();
	}
	//删除图书数据
	function del($bookId){//从bookbasic删除数据
		identity('booklist','book_kind',$bookId);
		$sql="delete from bookbasic where id='$bookId'";
		$query = mysql_query($sql);
		//echo $sql;
		if(!$query) {
			error('sql_error');
		}
		else {//从bookdetail删除数据
			$sql="delete from bookdetail where book_id='$bookId'";
			$query = mysql_query($sql);
			//echo $sql;
			!$query?error('sql_error'):found();
		}
	}
	//查看已借出的书
	function adminshow($userId,$page_size,$offset){//增加显示借阅人、借书时间、剩余天数（30天）
		$sql="SELECT ba.id AS id, book_name, book_author, book_type, book_edit, book_price, book_status, `user`.user_name, created_at, datediff( date_add(created_at, INTERVAL 1 MONTH), now()) AS return_at, favour, book_pic, CASE ba.id IN ( SELECT book_id FROM booklike WHERE user_id = '12108238' ) WHEN FALSE THEN '0' ELSE '1' END AS isLike FROM bookbasic ba, booklist li, bookcirculate cir, `user` WHERE ba.id = li.book_kind AND book_status = '已被借' AND cir.book_id = ba.id AND `user`.user_id = cir.user_id LIMIT $page_size OFFSET $offset";
		//echo $sql;
		$query = mysql_query($sql);
		$response = array();
		if(!$query) {
			error('sql_error');
		}
		else {
			$i = 0;
			while($res = mysql_fetch_array($query)) {
				$response[$i] = array(  'book_id'=>$res['id'],
										'book_name'=>$res['book_name'],
										'book_author'=>$res['book_author'],
										'book_type'=>$res['book_type'],
										'book_info'=>$res['book_edit'],
										'book_price'=>$res['book_price'],
										'book_status'=>$res['book_status'],
										'user_name'=>$res['user_name'],
										'created_at'=>$res['created_at'],
										'return_at'=>$res['return_at'],
										'favour'=>$res['favour'],
										'book_pic'=>$res['book_pic'],
										'isLike'=>$res['isLike']);			  
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
		echo $sql;
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
			case 'userid_error':
				$info="用户id重复";
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