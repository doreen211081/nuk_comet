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
if(isset($_SESSION["login"])){
	
	$uno=$_SESSION["uno"];
	$cid=$_SESSION["cid"];
	$acdy=$_POST['acdy'];
	$sem=$_POST['sem'];
	$easy=$_POST['rating1'];
	$highscored=$_POST['rating2'];
	$enriched=$_POST['rating3'];
	$r_all=$_POST['rating4'];
	$cmmnt=nl2br($_POST['content']);
	// echo $uno.$cid.$acdy.$sem.$easy.$highscored.$enriched.$r_all.$cmmnt;

	$serverName = "140.127.218.154:3306";

	$link = mysqli_connect(
		$serverName,
		'nukcomet2589',
		'commentnuk2589',
		'nukcomet');

	mysqli_query($link,'set names utf8');

	if(!$link){
		echo "Failed to connect to database!";
	}

	else{

		mysqli_select_db($link, 'nukcomet');

		$sql_insert = "INSERT INTO comment (user_id, course_id, rating_all, rating_easy, rating_enriched, rating_highscored, comment_AcademicYear, comment_semester, content) VALUES('$uno', '$cid', '$r_all', '$easy', '$enriched', '$highscored', '$acdy', '$sem', '$cmmnt')";
		mysqli_query($link, $sql_insert) or die ("評論寫入失敗".mysql_error());
		mysqli_close($link);

		echo "<h2>評論成功，即將回到上一頁</h2>";
		$url = $_SERVER['HTTP_REFERER'];
		// $url = substr_replace($_SERVER['HTTP_REFERER'],'',-7);
		header("Refresh:2;url='$url'");
	}
}else{
	echo "評論請先登入";
	$url = $_SERVER['HTTP_REFERER'];  #前一頁網址
	header("Refresh:2;url='$url#notice'");
}


 ?>

   	</body>
</html>