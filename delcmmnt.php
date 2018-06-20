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
$delcid=$_SESSION['delcid'];
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
		echo "Unable to open database nukcomet...<br/>";
	}else{

		//刪除資料
		$sql_del = "DELETE FROM comment WHERE comment_id='$delcid'";
		$del=mysqli_query($link, $sql_del);
		echo "評論已刪除";
		mysqli_close($link);

		$url = $_SERVER['HTTP_REFERER'];  #前一頁網址
		header("Refresh:1;url='$url'");
	}
}

 ?>

   	</body>
</html>