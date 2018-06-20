<html>
  <head>
    <meta charset="UTF-8">
    <link rel=stylesheet type="text/css" href="css/main.css">
    <link rel=stylesheet type="text/css" href="css/lightbox.css">
    <script src="js/lightbox.js" type="text/javascript"></script>
    <title>鈕課彗星 NUK COMET</title>
    <script>
    	
    </script>
  </head>
  <body>
    <div class="warp">
      <div class="top">
      <div class="logo"><img src="image/logo.png" width="350"></div>
      </div>
  	</div>

<?php 
session_start();
$uid=$_POST['uid'];
$pwd=$_POST['upwd'];
$serverName = "140.127.218.154:3306";

$link = mysqli_connect(
	$serverName,
	'nukcomet2589',
	'commentnuk2589',
	'nukcomet');

if(!$link){
	echo "Failed to connect to database!";
}

else{
		
	$sql_trans_id_no = "SELECT user.user_no as u_no 
	FROM user
	WHERE user.user_id = '$uid'";
	$result_no = mysqli_query($link,$sql_trans_id_no);
	$u_no =  mysqli_fetch_assoc($result_no);
	#以上將u_id(信箱)轉成u_no(編號)
	
	// echo gettype($u_no[u_no]);
	$_SESSION["uno"]=$u_no[u_no];

	mysqli_select_db($link, 'nukcomet');

	if (!mysqli_select_db($link, 'nukcomet')){
		echo "Unable to open database nukcomet...<br/>";
	}

	$sql = "SELECT user_id, user_pwd, authority FROM user";
	$result = mysqli_query($link, $sql);

	while($row = mysqli_fetch_assoc($result)){
		if($row['user_id']==$uid){
			if($row['user_pwd']==$pwd){
				//登入成功之後
				echo "<h2>登入成功！</h2>";
				$_SESSION["login"]="yes";
				$_SESSION["auth"]=$row['authority'];
				$url =  $_SERVER['HTTP_REFERER'];  #進入到LOGIN.php的前一頁網址
				header("Refresh:1;url='$url?u_no=$u_no[u_no]'"); #等待兩秒後帶著$UID(使用者ID)回到上一頁
				break;
			}else{
				echo "<h2>密碼有誤，請<a href='index.html#notice'>重新登入</a></h2>";
				break;
			}
		}
	}

	if(!isset($row['user_id'])){
		echo "<h2>帳號有誤或尚未註冊，請<a href='index.php#notice'>重新登入</a></h2>";
	}

	mysqli_close($link);
}

?>

</body>
</html>