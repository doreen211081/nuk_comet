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

$newPwd=$_SESSION["upwd"];
$uid=$_SESSION["uid"];

if ($_SESSION['$mailadd']==$_POST['vcode']) {
	$link = @mysqli_connect(
			"140.127.218.154:3306",
			'nukcomet2589',
			'commentnuk2589',
			'nukcomet');

		if(!$link){
			echo "Failed to connect to database!";
		}

		else{

			mysqli_select_db($link, 'nukcomet');

			if (!mysqli_select_db($link, 'nukcomet')){
				echo "Unable to open database 'nukcomet'...<br/>";
			}else{

				$sql_update = " UPDATE user SET user_pwd='$newPwd' WHERE user_id='$uid' ";
				mysqli_query($link, $sql_update) or die ("密碼更改失敗".mysql_error());

				mysqli_close($link);
				session_destroy();
				
				echo "密碼已重設，請回首頁重新登入！";
				header("Refresh:2; url='index.php'");
			}
		}
}else{
	echo "驗證碼有誤！即將回上一頁";
	$url = $_SERVER['HTTP_REFERER'];  #前一頁網址
	header("Refresh:1;url='$url'");
}

 ?>

   	</body>
</html>