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
header ( "Content-type:text/html;charset=utf-8" );
session_start();

	$cmmntid=$_SESSION['cmmntid'];
	$acdy=$_POST['nacdy'];
	$sem=$_POST['nsem'];
	$easy=$_POST['nrating'];
	$highscored=$_POST['nrating2'];
	$enriched=$_POST['nrating3'];
	$r_all=$_POST['nrating4'];
	$newCont=nl2br($_POST['newcomment']);

$link = @mysqli_connect(
		"140.127.218.154:3306",
		'nukcomet2589',
		'commentnuk2589',
		'nukcomet');
mysqli_query($link,'set names utf8');

if(!$link){
	echo "Failed to connect to database!";
}
else{
	mysqli_select_db($link, 'nukcomet');
	if (!mysqli_select_db($link, 'nukcomet')){
		echo "Unable to open database 'nukcomet'...<br/>";
	}else{
		$sql_update = " UPDATE comment SET content='$newCont', rating_all='$r_all', rating_easy='$easy', rating_enriched='$enriched', rating_highscored='$highscored', comment_AcademicYear='$acdy', comment_semester='$sem' WHERE comment_id='$cmmntid' ";
		mysqli_query($link, $sql_update) or die ("評論更改失敗".mysql_error());
		mysqli_close($link);
		
				
		echo "編輯成功！即將回到上一頁";
		$url = $_SERVER['HTTP_REFERER'];  #前一頁網址
		header("Refresh:1;url='$url'");
}
}

 ?>

   	</body>
</html>