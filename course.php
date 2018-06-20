
<script src='https://code.jquery.com/jquery-1.12.4.js'></script>
<script src="js/lightbox.js" type="text/javascript"></script>
<link rel=stylesheet type="text/css" href="css/main.css">
<link rel=stylesheet type="text/css" href="css/lightbox.css">
 
<?php
session_start();
header ( "Content-type:text/html;charset=utf-8" );

$c_id = $_GET["course_id"];
$u_no = $_SESSION["uno"];
$_SESSION["cid"]=$c_id;
$order_stat = $_GET["order_stat"];
$back_num = $_GET["back_num"];      #上一頁系列:由於排序採取傳值且重新整理，所以每排一次序就會產生新網址，用來計算要排序幾次，要回去幾頁才能到search.php

if(!isset($order_stat)){
    $orderby ="ORDER BY comment.comment_AcademicYear DESC,comment.comment_semester DESC,comment.comment_id DESC";   #排序系列:尚未排序時，預設為DESC
    $order_stat = 'DESC';
    $back_num = -1;             #上一頁系列:尚未排序時，直接回到上一頁
}
else{
    $back_num = $back_num -1;   #上一頁系列:偵測到一次排序時，代表要回去的頁面+1
    if($order_stat == 'DESC'){      
        $orderby = "ORDER BY comment.comment_AcademicYear DESC,comment.comment_semester DESC,comment.comment_id DESC";
        $order_stat = 'DESC';
    }
    else if ($order_stat == 'ASC'){
        $orderby = "ORDER BY comment.comment_AcademicYear ASC,comment.comment_semester ASC,comment.comment_id DESC";
        $order_stat = 'ASC';
    }
    else if ($order_stat == 'nuk'){
        $orderby = "ORDER BY nuk_num DESC";
        $order_stat = 'nuk';
    }
    else if ($order_stat == 'nuk_ASC'){
        $orderby = "ORDER BY nuk_num ASC";
        $order_stat = 'nuk_ASC';
    }
}

echo "<a class='backtoup'  onclick=back(".$back_num.")><img src='image/回上一頁.png' width='260'></a><br>";     #回去上一頁的按鈕



$link = mysqli_connect(
    '140.127.218.154',
    'nukcomet2589',
    'commentnuk2589',
    'nukcomet'
);
mysqli_query($link,'set names utf8');    #讓資料庫可寫進中文


#排序系列:點選之後依照學年度排序


$sql_course_info = "SELECT course.Course_Name as c_name,teacher.Teacher_Name as t_name
FROM course,teacher
WHERE course.Teacher_Id = teacher.Teacher_Id AND course.Course_Id =$c_id";      #顯示課程與教授名稱的SQL
$result = mysqli_query($link,$sql_course_info);
$row = mysqli_fetch_assoc($result);


$sql_comment = "SELECT comment.user_id,comment.comment_id,comment.course_id,comment.comment_AcademicYear,comment.comment_semester,comment.rating_easy,comment.rating_highscored,comment.rating_enriched,comment.rating_all,comment.content,
count(list.dislike_uid) as dislike_num,count(list.nuk_uid) as nuk_num
FROM comment
LEFT JOIN(SELECT dislike.Comment_Id as dis_com_id,dislike.User_Id as dislike_uid,nuk.user_id as nuk_uid
        FROM dislike
        LEFT JOIN nuk
        ON nuk.user_id = dislike.User_Id and nuk.comment_id = dislike.Comment_Id
    UNION
    SELECT nuk.Comment_Id as nuk_com_id,dislike.User_Id as dislike_uid,nuk.user_id as nuk_uid
        FROM dislike
        RIGHT JOIN nuk
        ON nuk.user_id = dislike.User_Id and nuk.comment_id = dislike.Comment_Id) as list
ON comment.comment_id = list.dis_com_id
WHERE comment.course_id = $c_id
GROUP BY comment.comment_id ".$orderby; #排序SQL

$result_comment = mysqli_query($link,$sql_comment);
    while($comment = mysqli_fetch_assoc($result_comment)){
    
    echo "<div class ='comment'>";
    echo "<td><a>".$comment[comment_AcademicYear]."學年   第<td>";
    echo "<td>".$comment[comment_semester]."學期  </a><td>";
    echo "<td><a>涼: ".$comment[rating_easy]."   </a></td>";
    echo "<td><a>甜: ".$comment[rating_highscored]." </a></td>";
    echo "<td><a>實: ".$comment[rating_enriched]."   </a></td>";
    echo "<td><a>總分:    ".$comment[rating_all]."</a></td></tr>";  
        if ($comment[user_id]==$u_no) {
        $_SESSION['cmmntid']=$comment[comment_id];
        ${"txt".$_SESSION['cmmntid']} = $comment[content];
        ${"cacdy".$_SESSION['cmmntid']} = $comment[comment_AcademicYear];
        ${"csem".$_SESSION['cmmntid']} = $comment[comment_semester];
        echo "<a href='#edit'>編輯</a> | ";
        $_SESSION['delcid']=$comment[comment_id];
        echo "<a href='delcmmnt.php'>刪除</a><br/>";
    }else if($_SESSION["auth"]==1){
        $_SESSION['delcid']=$comment[comment_id];
        echo "<a href='delcmmnt.php'>刪除</a><br/>";
    }
    echo "<div class='comment_txt'><tr><td>".$comment[content]."</td></div>";
    echo "</div>";
    echo "<div class='like'>";
    echo "<input type='button' onclick=nuk(".$comment[comment_id].",'".$u_no."') value='nuk'> :";
    echo "<div class='nuk' id='nuk_num".$comment[comment_id]."'></div>";
    echo "<script>document.querySelector('#nuk_num".$comment[comment_id]."').textContent =".$comment[nuk_num]."</script><br>";
    echo "<input type='button' onclick=dislike(".$comment[comment_id].",'".$u_no."') value='dislike'> :";
    echo "<div class ='dislike' id='dislike_num".$comment[comment_id]."'></div>";
    echo "<script>document.querySelector('#dislike_num".$comment[comment_id]."').textContent = ".$comment[dislike_num]."</script><br>";

    echo "</div>";
    
};
echo "</form>";
echo "</div>";
echo "<div id='test'></div>"

?>

<script>
function back(order_times){     //點選上一頁按鈕時進入的function
    history.go(order_times);
}

function nuk(comment_id,u_no){
    $.ajax({
        url:'nuk.php',
        dataType:'json',
        data:'&comment_id='+comment_id+'&u_no='+u_no,
        type:'POST',
        error:function(){
            alert("需要登入才能使用!");
            },
        success:function(data){
            document.querySelector('#nuk_num'+data.comment_id).textContent = data.nuk_num;
        }
    });
    
};

function dislike(comment_id,u_no){
    $.ajax({
        url:'dislike.php',
        dataType:'json',
        data:'&comment_id='+comment_id+'&u_no='+u_no,
        type:'POST',
        error:function(){
            alert("需要登入才能使用!")
            }
        ,
        success:function(data){
            document.querySelector('#dislike_num'+data.comment_id).textContent = data.dislike_num;
        }
    });
};

function order_by_year(order_stat,back_num,u_no){
    if(order_stat !='DESC'){            //排序系列:當排序狀態不等於(依照學年)DESC時，依照學年且DESC排序
        document.location.href="http://localhost/nuk_comet/course.php?course_id="+<?php echo $c_id?>+"&u_no="+u_no+"&back_num="+back_num+"&order_stat=DESC";
        }
    else if(order_stat !='ASC'){        //排序系列:當排序狀態不等於(依照學年)ASC時，依照學年且DESC排序
        document.location.href="http://localhost/nuk_comet/course.php?course_id="+<?php echo $c_id?>+"&u_no="+u_no+"&back_num="+back_num+"&order_stat=ASC";
        }    
};

function order_by_nuk(order_stat,back_num,u_no){
    if(order_stat !='nuk')              //排序系列:當排序狀態不等於(依照NUK)DESC時，依照NUK且DESC排序
        document.location.href="http://localhost/nuk_comet/course.php?course_id="+<?php echo $c_id?>+"&u_no="+u_no+"&back_num="+back_num+"&order_stat=nuk";
    else if (order_stat == 'nuk')       //排序系列:當排序狀態不等於(依照NUK)ASC時，依照NUK且ASC排序
        document.location.href="http://localhost/nuk_comet/course.php?course_id="+<?php echo $c_id?>+"&u_no="+u_no+"&back_num="+back_num+"&order_stat=nuk_ASC";
}


</script>

<html>
<head>
<meta charset="UTF-8">
    <link rel=stylesheet type="text/css" href="css/cssReset.css">
    <link rel=stylesheet type="text/css" href="css/star_rating.css">
    <link rel=stylesheet type="text/css" href="css/main.css">
    <link rel=stylesheet type="text/css" href="css/top.css">
    <link rel=stylesheet type="text/css" href="css/lightbox.css">
    <link rel=stylesheet type="text/css" href="css/coursecss.css">
    
    <script src="js/lightbox.js" type="text/javascript"></script>
    <title>鈕課彗星 NUK COMET</title>
    <script>
    	
    </script>
</head>
<body>
    <div class="warp">   
    <?php echo "<p class='title classname'>".$row[c_name]."</p>";   
    echo "<p class='title teachername'>".$row[t_name]."</p>"; ?>   
    <img class="logo" src="image/slogo.png">
    <section>
      <?php 
if(isset($_SESSION["login"])){
  echo "<a href='logout.php' class='login'>登出</a>";
}else{
  echo "<a href='#notice' class='login'>登入</a>";
}
 ?>
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
        <?PHP    echo "<a class='x'  onclick=back(".$back_num.")><img src='image/x.png' width='60'></a><br>"; ?>
          
        </form><br/><br/>
        <div class="forgetpwd">
        <a href="resetpwd.php"; >忘記密碼？</a></div>
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
        <?PHP    echo "<a class='x'  onclick=back(".$back_num.")><img src='image/x.png' width='60'></a><br>"; ?>
        
      </div>
      
</div>

<div class="lightbox-target" id="notice2">
        <div class="content regisform">
        <img class="lgimg" src="image/重設密碼.png">
        <br/><br/>
        <pre class="text">
        <form action="resetpwd.php" method="post">
            學校信箱：<input type="email" class="input2" placeholder="a1000000@mail.nuk.edu.tw" name="email" required="required">  
  
            設定新密碼：<input type="password" class="input2" name="upwd" required="required">


            確認密碼：<input type="password" class="input2" name="upwd2" required="required">
        </pre><br/><br/><br/>
        <input type="submit" class="pink button2" value="確定"></form><br/><br/>
        <?PHP    echo "<a class='x'  onclick=back(".$back_num.")><img src='image/x.png' width='60'></a><br>"; ?>
        
      </div>
      
</div>

<div class="adiv">
<section><a class="hnone" href="#notice3">
<button class="button3">我要寫評論 ✎</button></a>
<div class='selectbut'>
    <? echo "<div class='aaaa' onclick=order_by_year('".$order_stat."',".$back_num.",".$u_no.")><span class='termsite' > 學年度&nbsp;</span>"; ?> 

    <? echo "<div class='bbbb' onclick=order_by_nuk('".$order_stat."',".$back_num.",".$u_no.")><span class='termsite' > &nbsp;鈕扣數多-少 </span></div>"; ?> 
    </section>
</div>

 <div class="lightbox-target" id="notice3">
        <div class="respon">
        <?php echo "<p class='sub'>".'我要評論'. ' - ' .$row[c_name]._.$row[t_name]."</p>"."<br/>"."<br/>"; ?>
        <form class="cometform" action="addcomment.php" method="POST">
        
        <h4>學年：<select class="year" name="acdy">
              <option value="NULL">請選擇</option>
              <option value="106">106學年度</option>
              <option value="105">105學年度</option>
              <option value="104">104學年度</option>
              <option value="103">103學年度</option>
              <option value="102">102學年度</option>
              <option value="101">101學年度</option>
              <option value="100">100學年度</option>
        </select></h4>
        <h4 class="semester_site">學期：<select class="year" name="sem">
              <option value="NULL">請選擇</option>
              <option value="1">上學期</option>
              <option value="2">下學期</option>
        </select></h4></br></br>
        <div class="star">   
        <h4 class="cold">涼：</div><div class="star"><fieldset class="rating1">
        <input type="radio" id="star5" name="rating1" value="1" /><label class = "full" for="star5" title="Awesome - 5 stars"></label>
        <!--<input type="radio" id="star4half" name="rating" value="4 and a half" /><label class="half" for="star4half" title="Pretty good - 4.5 stars"></label>-->
        <input type="radio" id="star4" name="rating1" value="2" /><label class = "full" for="star4" title="Pretty good - 4 stars"></label>
        <!--<input type="radio" id="star3half" name="rating" value="3 and a half" /><label class="half" for="star3half" title="Meh - 3.5 stars"></label>-->
        <input type="radio" id="star3" name="rating1" value="3" /><label class = "full" for="star3" title="Meh - 3 stars"></label>
        <!--<input type="radio" id="star2half" name="rating" value="2 and a half" /><label class="half" for="star2half" title="Kinda bad - 2.5 stars"></label>-->
        <input type="radio" id="star2" name="rating1" value="4" /><label class = "full" for="star2" title="Kinda bad - 2 stars"></label>
        <!--<input type="radio" id="star1half" name="rating" value="1 and a half" /><label class="half" for="star1half" title="Meh - 1.5 stars"></label>-->
        <input type="radio" id="star1" name="rating1" value="5" /><label class = "full" for="star1" title="Sucks big time - 1 star"></label>
        <!--<input type="radio" id="starhalf" name="rating" value="half" /><label class="half" for="starhalf" title="Sucks big time - 0.5 stars"></label>-->
        </fieldset></h4></div>

        <div class="star">   
        <h4 class="cold">甜：</div><div class="star"><fieldset class="rating2">
        <input type="radio" id="star5" name="rating2" value="1" /><label class = "full" for="star5" title="Awesome - 5 stars"></label>
        <!--<input type="radio" id="star4half" name="rating" value="4 and a half" /><label class="half" for="star4half" title="Pretty good - 4.5 stars"></label>-->
        <input type="radio" id="star4" name="rating2" value="2" /><label class = "full" for="star4" title="Pretty good - 4 stars"></label>
        <!--<input type="radio" id="star3half" name="rating" value="3 and a half" /><label class="half" for="star3half" title="Meh - 3.5 stars"></label>-->
        <input type="radio" id="star3" name="rating2" value="3" /><label class = "full" for="star3" title="Meh - 3 stars"></label>
        <!--<input type="radio" id="star2half" name="rating" value="2 and a half" /><label class="half" for="star2half" title="Kinda bad - 2.5 stars"></label>-->
        <input type="radio" id="star2" name="rating2" value="4" /><label class = "full" for="star2" title="Kinda bad - 2 stars"></label>
        <!--<input type="radio" id="star1half" name="rating" value="1 and a half" /><label class="half" for="star1half" title="Meh - 1.5 stars"></label>-->
        <input type="radio" id="star1" name="rating2" value="5" /><label class = "full" for="star1" title="Sucks big time - 1 star"></label>
        <!--<input type="radio" id="starhalf" name="rating" value="half" /><label class="half" for="starhalf" title="Sucks big time - 0.5 stars"></label>-->
        </fieldset></h4></div>

        <div class="star">   
        <h4 class="cold">實：</div><div class="star"><fieldset class="rating3">
        <input type="radio" id="star5" name="rating3" value="1" /><label class = "full" for="star5" title="Awesome - 5 stars"></label>
        <!--<input type="radio" id="star4half" name="rating" value="4 and a half" /><label class="half" for="star4half" title="Pretty good - 4.5 stars"></label>-->
        <input type="radio" id="star4" name="rating3" value="2" /><label class = "full" for="star4" title="Pretty good - 4 stars"></label>
        <!--<input type="radio" id="star3half" name="rating" value="3 and a half" /><label class="half" for="star3half" title="Meh - 3.5 stars"></label>-->
        <input type="radio" id="star3" name="rating3" value="3" /><label class = "full" for="star3" title="Meh - 3 stars"></label>
        <!--<input type="radio" id="star2half" name="rating" value="2 and a half" /><label class="half" for="star2half" title="Kinda bad - 2.5 stars"></label>-->
        <input type="radio" id="star2" name="rating3" value="4" /><label class = "full" for="star2" title="Kinda bad - 2 stars"></label>
        <!--<input type="radio" id="star1half" name="rating" value="1 and a half" /><label class="half" for="star1half" title="Meh - 1.5 stars"></label>-->
        <input type="radio" id="star1" name="rating3" value="5" /><label class = "full" for="star1" title="Sucks big time - 1 star"></label>
        <!--<input type="radio" id="starhalf" name="rating" value="half" /><label class="half" for="starhalf" title="Sucks big time - 0.5 stars"></label>-->
        </fieldset></h4></div>

        <div class="star">   
        <h4 class="cold">總：</div><div class="star"><fieldset class="rating4">
        <input type="radio" id="star5" name="rating4" value="1" /><label class = "full" for="star5" title="Awesome - 5 stars"></label>
        <!--<input type="radio" id="star4half" name="rating" value="4 and a half" /><label class="half" for="star4half" title="Pretty good - 4.5 stars"></label>-->
        <input type="radio" id="star4" name="rating4" value="2" /><label class = "full" for="star4" title="Pretty good - 4 stars"></label>
        <!--<input type="radio" id="star3half" name="rating" value="3 and a half" /><label class="half" for="star3half" title="Meh - 3.5 stars"></label>-->
        <input type="radio" id="star3" name="rating4" value="3" /><label class = "full" for="star3" title="Meh - 3 stars"></label>
        <!--<input type="radio" id="star2half" name="rating" value="2 and a half" /><label class="half" for="star2half" title="Kinda bad - 2.5 stars"></label>-->
        <input type="radio" id="star2" name="rating4" value="4" /><label class = "full" for="star2" title="Kinda bad - 2 stars"></label>
        <!--<input type="radio" id="star1half" name="rating" value="1 and a half" /><label class="half" for="star1half" title="Meh - 1.5 stars"></label>-->
        <input type="radio" id="star1" name="rating4" value="5" /><label class = "full" for="star1" title="Sucks big time - 1 star"></label>
        <!--<input type="radio" id="starhalf" name="rating" value="half" /><label class="half" for="starhalf" title="Sucks big time - 0.5 stars"></label>-->
        </fieldset></h4></div> <br/><br/><br/>
        <div class="text">
        <textarea class="textarea" cols="100" rows="10" name="content">輸入你想要評論的內容...</textarea></div>

        <input type="reset" class="pink button2 sbutton p" value="重設"><br/>
        <input type="submit" class="blue button2 sbutton b" value="提交"><br/>
        </form>
        <?PHP    echo "<a class='x'  onclick=back(".$back_num.")><img src='image/x.png' width='60'></a><br>"; ?></div>
</div>

<!-- 編輯評論 -->

<div class="lightbox-target" id="edit">
<div class="respon">
        <?php echo "<p class='sub'>".'我要評論'. ' - ' .$row[c_name]._.$row[t_name]."</p>"."<br/>"."<br/>"; ?>
        <form class="cometform" action="updatecmmnt.php" method="POST">
        
        <h4>學年：<select class="year" name="nacdy">
              <option value="NULL">請選擇</option>
              <option value="106" <?php if(${"cacdy".$_SESSION['cmmntid']}==106){ echo "selected='selected'";} ?>>
              106學年度</option>
              <option value="105" <?php if(${"cacdy".$_SESSION['cmmntid']}==105){ echo "selected='selected'";} ?>>105學年度</option>
              <option value="104" <?php if(${"cacdy".$_SESSION['cmmntid']}==104){ echo "selected='selected'";} ?>>104學年度</option>
              <option value="103" <?php if(${"cacdy".$_SESSION['cmmntid']}==103){ echo "selected='selected'";} ?>>103學年度</option>
              <option value="102" <?php if(${"cacdy".$_SESSION['cmmntid']}==102){ echo "selected='selected'";} ?>>102學年度</option>
              <option value="101" <?php if(${"cacdy".$_SESSION['cmmntid']}==101){ echo "selected='selected'";} ?>>101學年度</option>
              <option value="100" <?php if(${"cacdy".$_SESSION['cmmntid']}==100){ echo "selected='selected'";} ?>>100學年度</option>
        </select></h4>
        <h4 class="semester_site">學期：<select class="year" name="nsem">
              <option value="NULL">請選擇</option>
              <option value="1" <?php if(${"csem".$_SESSION['cmmntid']}==1){ echo "selected='selected'";} ?>>上學期</option>
              <option value="2" <?php if(${"csem".$_SESSION['cmmntid']}==2){ echo "selected='selected'";} ?>>下學期</option>
        </select></h4></br></br>
        <div class="star">   
        <h4 class="cold">涼：</div><div class="star"><fieldset class="rating">
        <input type="radio" id="star5" name="nrating" value="1" /><label class = "full" for="star5" title="Awesome - 5 stars"></label>
        <!--<input type="radio" id="star4half" name="rating" value="4 and a half" /><label class="half" for="star4half" title="Pretty good - 4.5 stars"></label>-->
        <input type="radio" id="star4" name="nrating" value="2" /><label class = "full" for="star4" title="Pretty good - 4 stars"></label>
        <!--<input type="radio" id="star3half" name="rating" value="3 and a half" /><label class="half" for="star3half" title="Meh - 3.5 stars"></label>-->
        <input type="radio" id="star3" name="nrating" value="3" /><label class = "full" for="star3" title="Meh - 3 stars"></label>
        <!--<input type="radio" id="star2half" name="rating" value="2 and a half" /><label class="half" for="star2half" title="Kinda bad - 2.5 stars"></label>-->
        <input type="radio" id="star2" name="nrating" value="4" /><label class = "full" for="star2" title="Kinda bad - 2 stars"></label>
        <!--<input type="radio" id="star1half" name="rating" value="1 and a half" /><label class="half" for="star1half" title="Meh - 1.5 stars"></label>-->
        <input type="radio" id="star1" name="nrating" value="5" /><label class = "full" for="star1" title="Sucks big time - 1 star"></label>
        <!--<input type="radio" id="starhalf" name="rating" value="half" /><label class="half" for="starhalf" title="Sucks big time - 0.5 stars"></label>-->
        </fieldset></h4></div>

        <div class="star">   
        <h4 class="cold">甜：</div><div class="star"><fieldset class="rating2">
        <input type="radio" id="star5" name="nrating2" value="1" /><label class = "full" for="star5" title="Awesome - 5 stars"></label>
        <!--<input type="radio" id="star4half" name="rating" value="4 and a half" /><label class="half" for="star4half" title="Pretty good - 4.5 stars"></label>-->
        <input type="radio" id="star4" name="nrating2" value="2" /><label class = "full" for="star4" title="Pretty good - 4 stars"></label>
        <!--<input type="radio" id="star3half" name="rating" value="3 and a half" /><label class="half" for="star3half" title="Meh - 3.5 stars"></label>-->
        <input type="radio" id="star3" name="nrating2" value="3" /><label class = "full" for="star3" title="Meh - 3 stars"></label>
        <!--<input type="radio" id="star2half" name="rating" value="2 and a half" /><label class="half" for="star2half" title="Kinda bad - 2.5 stars"></label>-->
        <input type="radio" id="star2" name="nrating2" value="4" /><label class = "full" for="star2" title="Kinda bad - 2 stars"></label>
        <!--<input type="radio" id="star1half" name="rating" value="1 and a half" /><label class="half" for="star1half" title="Meh - 1.5 stars"></label>-->
        <input type="radio" id="star1" name="nrating2" value="5" /><label class = "full" for="star1" title="Sucks big time - 1 star"></label>
        <!--<input type="radio" id="starhalf" name="rating" value="half" /><label class="half" for="starhalf" title="Sucks big time - 0.5 stars"></label>-->
        </fieldset></h4></div>

        <div class="star">   
        <h4 class="cold">實：</div><div class="star"><fieldset class="rating3">
        <input type="radio" id="star5" name="nrating3" value="1" /><label class = "full" for="star5" title="Awesome - 5 stars"></label>
        <!--<input type="radio" id="star4half" name="rating" value="4 and a half" /><label class="half" for="star4half" title="Pretty good - 4.5 stars"></label>-->
        <input type="radio" id="star4" name="nrating3" value="2" /><label class = "full" for="star4" title="Pretty good - 4 stars"></label>
        <!--<input type="radio" id="star3half" name="rating" value="3 and a half" /><label class="half" for="star3half" title="Meh - 3.5 stars"></label>-->
        <input type="radio" id="star3" name="nrating3" value="3" /><label class = "full" for="star3" title="Meh - 3 stars"></label>
        <!--<input type="radio" id="star2half" name="rating" value="2 and a half" /><label class="half" for="star2half" title="Kinda bad - 2.5 stars"></label>-->
        <input type="radio" id="star2" name="nrating3" value="4" /><label class = "full" for="star2" title="Kinda bad - 2 stars"></label>
        <!--<input type="radio" id="star1half" name="rating" value="1 and a half" /><label class="half" for="star1half" title="Meh - 1.5 stars"></label>-->
        <input type="radio" id="star1" name="nrating3" value="5" /><label class = "full" for="star1" title="Sucks big time - 1 star"></label>
        <!--<input type="radio" id="starhalf" name="rating" value="half" /><label class="half" for="starhalf" title="Sucks big time - 0.5 stars"></label>-->
        </fieldset></h4></div>

        <div class="star">   
        <h4 class="cold">總：</div><div class="star"><fieldset class="rating4">
        <input type="radio" id="star5" name="nrating4" value="1" /><label class = "full" for="star5" title="Awesome - 5 stars"></label>
        <!--<input type="radio" id="star4half" name="rating" value="4 and a half" /><label class="half" for="star4half" title="Pretty good - 4.5 stars"></label>-->
        <input type="radio" id="star4" name="nrating4" value="2" /><label class = "full" for="star4" title="Pretty good - 4 stars"></label>
        <!--<input type="radio" id="star3half" name="rating" value="3 and a half" /><label class="half" for="star3half" title="Meh - 3.5 stars"></label>-->
        <input type="radio" id="star3" name="nrating4" value="3" /><label class = "full" for="star3" title="Meh - 3 stars"></label>
        <!--<input type="radio" id="star2half" name="rating" value="2 and a half" /><label class="half" for="star2half" title="Kinda bad - 2.5 stars"></label>-->
        <input type="radio" id="star2" name="nrating4" value="4" /><label class = "full" for="star2" title="Kinda bad - 2 stars"></label>
        <!--<input type="radio" id="star1half" name="rating" value="1 and a half" /><label class="half" for="star1half" title="Meh - 1.5 stars"></label>-->
        <input type="radio" id="star1" name="nrating4" value="5" /><label class = "full" for="star1" title="Sucks big time - 1 star"></label>
        <!--<input type="radio" id="starhalf" name="rating" value="half" /><label class="half" for="starhalf" title="Sucks big time - 0.5 stars"></label>-->
        </fieldset></h4></div> <br/><br/><br/>
        <div class="text">
        <textarea class="textarea" cols="100" rows="10" name="newcomment"><?php echo ${"txt".$_SESSION['cmmntid']}; ?></textarea></div>

        <input type="reset" class="pink button2 sbutton p" value="重設"><br/>
        <input type="submit" class="blue button2 sbutton b" value="提交"><br/>
        </form>
        <?PHP  echo "<a class='x'  onclick=back(".$back_num.")><img src='image/x.png' width='60'></a><br>"; ?></div>
</div>

     <img class="bgd" src="image/pink.png">
</body>
</html>