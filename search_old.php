<?php


header ( "Content-type:text/html;charset=utf-8" );

$u_no = $_GET['u_no'];

$dept = $_POST["開課系所"];
$class = $_POST["member"];
$class = preg_replace('/[^\d]/','',$class); #這個變數只取數字
$class_num = substr($class,0,1);        #拿第一個數字(年級)放到$CLASS_NUM，CLASS_NUM為開課班級數字
$back_num = $_GET["back_num"];      #上一頁系列:由於排序採取傳值且重新整理，所以每排一次序就會產生新網址，用來計算要排序幾次，要回去幾頁才能到search.php

$dept_order = $_GET['dept_order'];      #排序系列:點下任何一個排序之後，因為網址傳變數需要用GET來接，但前一頁表單傳來的變數要用POST接，因此在16行判斷如果有dept_order傳回(有人按下排序)則把此變數指定到原本的變數(17行)
$class_order = $_GET['class_order'];
$order_stat = $_GET['order_stat'];      #排序狀態，下方(25行)詳細說明

if (isset($dept_order)){                #排序系列:偵測到使用者點選排序後(dept_order有回傳值)的動作
        $dept = $dept_order;
}

if (isset($class_order)){
        $class_num = $class_order;
}

if(!isset($order_stat)){                #排序系列($orderby是附加在查詢課程資料的SQL尾端的SQL語法，$order_stat則是用來判斷現在是什麼排序)
        $orderby ="ORDER BY C_ALL DESC";        #默認排序狀態：尚未排序時，預設為依照C_ALL(總分)DESC排序
        $order_stat = 'C_ALL_DESC';             #排序狀態為C_ALL_DESC
        $back_num = -1;             #上一頁系列:尚未排序時，直接回到上一頁
    }
else{
        $back_num = $back_num -1;               #上一頁系列:偵測到一次排序時，代表要回去的頁面+1
        if($order_stat == 'com_num_DESC'){      #底部的JS FUNCTION會回傳排序狀態，在此判斷回傳的排序狀態是什麼
                $orderby ="ORDER BY C_NUM DESC,C_ALL DESC";  #當排序狀態為com_num_DESC(依照評論數高到低)時，優先排序為評論數高，評論數相同時，依照總分排序
        }
        else if($order_stat =='com_num_ASC'){
                $orderby ="ORDER BY C_NUM ASC,C_ALL ASC";
        }
        else if($order_stat == 'easy_DESC'){
                $orderby ="ORDER BY C_EASY DESC,C_ALL DESC";
        }
        else if($order_stat == 'easy_ASC'){
                $orderby ="ORDER BY C_EASY ASC,C_ALL ASC";
        }
        else if($order_stat == 'highscored_DESC'){
                $orderby ="ORDER BY C_HIGHSCORED DESC,C_ALL DESC";
        }
        else if($order_stat == 'highscored_ASC'){
                $orderby ="ORDER BY C_HIGHSCORED ASC,C_ALL ASC";
        }
        else if($order_stat == 'enriched_DESC'){
                $orderby ="ORDER BY C_ENRICHED DESC,C_ALL DESC";
        }
        else if($order_stat == 'enriched_ASC'){
                $orderby ="ORDER BY C_ENRICHED ASC,C_ALL ASC";
        }
        else if($order_stat == 'all_DESC'){
                $orderby ="ORDER BY C_ALL DESC,C_NUM DESC";     #當總分相同時，依照評論數高排序
        }
        else if($order_stat == 'all_ASC'){
                $orderby ="ORDER BY C_ALL ASC,C_NUM ASC";
        }
}

echo "<a class='backtoup' onclick=back(".$back_num.")><img src='image/回上一頁.png' width='260'></a><br>";               #回到上一頁的按鈕

$link = mysqli_connect(
    '140.127.218.154',
    'nukcomet2589',
    'commentnuk2589',
    'nukcomet'
);

$sql = "SELECT course.Course_Id as c_id,course.class as c_cla,course.Course_Name as c_name,teacher.Teacher_Name as t_name,COUNT(comment.content) as c_num,AVG(comment.rating_easy) as c_easy,AVG(comment.rating_highscored) as c_highscored,AVG(comment.rating_enriched) as c_enriched,AVG(comment.rating_all) as c_all 
FROM course 
JOIN teacher ON  teacher.Teacher_Id = course.Teacher_Id 
JOIN department ON department.Dept_Id = course.Dept_Id 
LEFT JOIN comment 
ON comment.course_id = course.Course_Id 
WHERE department.Dept_Name = '$dept' AND course.Class = $class_num 
GROUP BY course.Course_Id ".$orderby;   #排序SQL

mysqli_query($link,'set names utf8');
$result = mysqli_query($link,$sql);

echo "<table border=1>
<tr>
        <th Style='vertical-align:middle;'>課程</th>
        <th Style='vertical-align:middle;'>教授</th>
        <th Style='vertical-align:middle;'><a class='filter' onclick=order_by_com_num('".$order_stat."',".$class_num.",'".$dept."',".$back_num.",".$u_no.")>評論數</a></th>
        <th Style='vertical-align:middle;'><a class='filter' onclick=order_by_easy('".$order_stat."',".$class_num.",'".$dept."',".$back_num.",".$u_no.")>涼</a></th>        
        <th Style='vertical-align:middle;'><a class='filter' onclick=order_by_highscored('".$order_stat."',".$class_num.",'".$dept."',".$back_num.",".$u_no.")>甜</a></th>
        <th Style='vertical-align:middle;'><a class='filter' onclick=order_by_enriched('".$order_stat."',".$class_num.",'".$dept."',".$back_num.",".$u_no.")>實</a></th>
        <th Style='vertical-align:middle;'><a class='filter' onclick=order_by_all('".$order_stat."',".$class_num.",'".$dept."',".$back_num.",".$u_no.")>總評分</a></th>
        ";
while($row = mysqli_fetch_assoc($result)){
        $c_id = $row[c_id];
        echo "<tr><td Style='vertical-align:middle;'><a class='classbg' href ='course.php?course_id=".$c_id."&u_no=".$u_no." '>".$row[c_name]."</a></td>";
        echo "<td Style='vertical-align:middle;'>".$row[t_name]."</td>";
        echo "<td Style='vertical-align:middle;'>".$row[c_num]."</td>";
        echo "<td Style='vertical-align:middle;'>".round($row[c_easy],2)."</td>";
        echo "<td Style='vertical-align:middle;'>".round($row[c_highscored],2)."</td>";
        echo "<td Style='vertical-align:middle;'>".round($row[c_enriched],2)."</td>"  ;
        echo "<td Style='vertical-align:middle;'>".round($row[c_all],2)."</td></tr>";    
};

?>

<script>
function back(order_times){     //點選上一頁按鈕時進入的function
    history.go(order_times);
}

function order_by_com_num(order_stat,class_num,dept,back_num,u_no){
        if(order_stat != 'com_num_DESC')
                document.location.href="http://localhost/nuk_comet/search.php?u_no="+u_no+"&class_order="+class_num+"&dept_order="+dept+"&back_num="+back_num+"&order_stat=com_num_DESC";
        else if(order_stat != 'com_num_ASC')
                document.location.href="http://localhost/nuk_comet/search.php?u_no="+u_no+"&class_order="+class_num+"&dept_order="+dept+"&back_num="+back_num+"&order_stat=com_num_ASC";
}

function order_by_easy(order_stat,class_num,dept,back_num,u_no){
        if(order_stat != 'easy_DESC')
                document.location.href="http://localhost/nuk_comet/search.php?u_no="+u_no+"&class_order="+class_num+"&dept_order="+dept+"&back_num="+back_num+"&order_stat=easy_DESC";
        else if(order_stat != 'easy_ASC')
                document.location.href="http://localhost/nuk_comet/search.php?u_no="+u_no+"&class_order="+class_num+"&dept_order="+dept+"&back_num="+back_num+"&order_stat=easy_ASC";
}

function order_by_highscored(order_stat,class_num,dept,back_num,u_no){
        if(order_stat != 'highscored_DESC')
                document.location.href="http://localhost/nuk_comet/search.php?u_no="+u_no+"&class_order="+class_num+"&dept_order="+dept+"&back_num="+back_num+"&order_stat=highscored_DESC";
        else if (order_stat !='highscored_ASC')
                document.location.href="http://localhost/nuk_comet/search.php?u_no="+u_no+"&class_order="+class_num+"&dept_order="+dept+"&back_num="+back_num+"&order_stat=highscored_ASC";
}

function order_by_enriched(order_stat,class_num,dept,back_num,u_no){
        if(order_stat !='enriched_DESC')
                document.location.href="http://localhost/nuk_comet/search.php?u_no="+u_no+"&class_order="+class_num+"&dept_order="+dept+"&back_num="+back_num+"&order_stat=enriched_DESC";
        else if(order_stat !='enriched_ASC')
                document.location.href="http://localhost/nuk_comet/search.php?u_no="+u_no+"&class_order="+class_num+"&dept_order="+dept+"&back_num="+back_num+"&order_stat=enriched_ASC";
}

function order_by_all(order_stat,class_num,dept,back_num,u_no){
        if(order_stat !='all_DESC')
                document.location.href="http://localhost/nuk_comet/search.php?u_no="+u_no+"&class_order="+class_num+"&dept_order="+dept+"&back_num="+back_num+"&order_stat=all_DESC";
        else if(order_stat !='all_ASC')
                document.location.href="http://localhost/nuk_comet/search.php?u_no="+u_no+"&class_order="+class_num+"&dept_order="+dept+"&back_num="+back_num+"&order_stat=all_ASC";
}

</script>

<html>
<head>
<meta charset="UTF-8">
    <link rel=stylesheet type="text/css" href="css/cssReset.css">
    <link rel=stylesheet type="text/css" href="css/main.css">
    <link rel=stylesheet type="text/css" href="css/top.css">
    <link rel=stylesheet type="text/css" href="css/table.css">
    <link rel=stylesheet type="text/css" href="css/lightbox.css">
    <script src="js/lightbox.js" type="text/javascript"></script>
    <title>鈕課彗星 NUK COMET</title>
    <script>
    	
    </script>
</head>
<body>
    <div class="warp">     
    <img class="title" src="image/課程資料.png">
    <img class="logo" src="image/slogo.png">
    <section>
      <a href="#notice" class="login">登入</a>
    </section>
    <div class="lightbox-target" id="notice">
      <div class="content loginform">
        <img class="lgimg" src="image/登入.png">
        <form name="loginform" action="login.php" method="post">
          <h3>評論請先登入</h3><br/><br/>
          <pre class="text">
          學校信箱：<input type="email" class="input2" name="uid";>


          您的密碼：<input type="password" class="input2" name="upwd";>

          <label>
                                        <input type="checkbox"> 記住我</label>
          </pre><br/>
            <input type="submit" class="blue button2 " value="登入"><br/>
            <section><a class="hnone" href="#notice1">    
            <input type="button" class="pink button2 " value="註冊"></a></section>
            
        </form><br/><br/>
        <div class="forgetpwd">
        <a href="#"; >忘記密碼？</a></div>
      </div>
      <a class="lightbox-close" href="#"></a>
    </div>

    <div class="lightbox-target" id="notice1">
        <div class="content regisform">
        <img class="lgimg" src="image/註冊.png">
        <h3>僅限國立高雄大學在校生</h3><br/><br/>
        <pre class="text">
        <form action="verify.php" method="post">
            學校信箱：<input type="email" class="input2" placeholder="a1000000@mail.nuk.edu.tw" name="email" required="required">  
  
            您的密碼：<input type="password" class="input2" name="upwd" required="required">


            確認密碼：<input type="password" class="input2" name="upwd2" required="required">
        </pre><br/><br/><br/>
        <input type="submit" class="pink button2" value="註冊"></form><br/><br/>
        <div class="forgetpwd">
        <a href="#"; >重發認證信</a></div>
        <a class="site lightbox-close" href="#"></a>
        
      </div>
      
</div>


     <img class="bgd" src="image/pink.png">
</body>
</html>