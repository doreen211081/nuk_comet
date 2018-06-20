<?php
  session_start();
  $u_no = $_GET['u_no'];
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
?>
<script>
  function back(order_times){     //點選上一頁按鈕時進入的function
    history.go(order_times);
}
</script>
<html>
  <head>
    <meta charset="UTF-8">
    <link rel=stylesheet type="text/css" href="css/cssReset.css">
    <link rel=stylesheet type="text/css" href="css/main.css">
    <link rel=stylesheet type="text/css" href="css/lightbox.css">
    <script src="js/lightbox.js" type="text/javascript"></script>
    <title>鈕課彗星 NUK COMET</title>
    <script>
    	
    </script>
  </head>
  <body>
  <div class="warp">     
    <img class="title" src="image/課程查詢.png">
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
        <a href="#notice2"; >忘記密碼？</a></div>
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

    <div class="top">   

  
    <div class="logo">
    <img src="image/logo.png" width="350">
   
    </div>    
    </div> 
   
  </div>

  <script>
      department=new Array();
      department[0]=["請選擇"];	// 請選擇
      department[1]=["請選擇","共同必修系列1年級(A10606)","共同必修系列2年級(A10506)","共同必修系列4年級(A10306)"];    //共同必修系列
      department[2]=["請選擇","自然科學類-科學素養", "自然科學類-倫理素養", "人文科學類-思維方法", "人文科學類-美學素養", "社會科學類-公民素養","社會科學類-文化素養"];	// 核心通識
      department[3]=["請選擇","通識人文科學類1年級(A10603)"];			// 通識人文科學類
      department[4]=["請選擇","通識自然科學類1年級(A10604)"];			// 通識自然科學類
      department[5]=["請選擇","通識社會科學類1年級(A10601)"];      // 通識社會科學類
      department[6]=["請選擇","全民國防教育類1年級(A10608)"];      // 全民國防教育類
      department[7]=["請選擇","興趣選修1年級(A10609)"];            // 興趣選修
      department[8]=["請選擇","西洋語文學系1年級(A10611)","西洋語文學系2年級(A10511)","西洋語文學系3年級(A10411)","西洋語文學系4年級(A10311)","西洋語文學系5年級(A10211)","西洋語文學系6年級(A10111)",];      // 西洋語文學系
      department[9]=["請選擇","資訊管理學系1年級(A10633)","資訊管理學系2年級(A10533)","資訊管理學系3年級(A10433)","資訊管理學系4年級(A10333)","資訊管理學系5年級(A10233)","資訊管理學系6年級(A10133)",];      // 資訊管理學系
      
      function renew(index){
        for(var i=0;i<department[index].length;i++)
          document.myForm.member.options[i]=new Option(department[index][i], department[index][i]);	// 設定新選項
        document.myForm.member.length=department[index].length;	// 刪除多餘的選項
      }

      
      </script>
  
  <form action="search.php?u_no=<?php echo $u_no; ?>" method="POST" name="myForm">
    <div class="menu">
        <h2>開課系所</h2><br/>
        <select name="開課系所" onChange="renew(this.selectedIndex);">
              <option value="NULL">請選擇</option>
              <option value="共同必修系列">共同必修系列</option>
            　<option value="核心通識">核心通識</option>
            　<option value="通識人文科學類">通識人文科學類</option>
            　<option value="通識自然科學類">通識自然科學類</option>
            　<option value="通識社會科學類">通識社會科學類</option>
              <option value="全民國防教育類">全民國防教育類</option>
              <option value="興趣選修">興趣選修</option>
              <option value="西洋語文學系">西洋語文學系</option>
              <option value="資訊管理學系">資訊管理學系</option>
            </select>
    </div>
    <div class="space menu">
        <h2>課程分類/開課班級</h2><br/>
        <select name="member">
            <option value="">請先由左方選取開課系所
            </select>
    </div>
    <div class="space2 menu">
      <h2>授課教師</h2><h4>(請輸入教師姓名關鍵字)</h4><br/>
      <input type="text" class="input" name="t_keyword">
    </div>
    <div class="space3 menu">
        <h2>課程名稱關鍵字</h2><br/><br/>
        <input type="text" class="input" name="keyworcd">
      </div>
    <div>
    <input type="submit" class="button" value="搜尋">
    <input type="reset" class="gray button " value="重設">
  </div>
  </form>
  <img class="bgd" src="image/pink.png">

  <body>
</html>


