<?php
header ( "Content-type:text/html;charset=utf-8" );
echo "<script src='https://code.jquery.com/jquery-1.12.4.js'></script>";

$c_id = $_GET["course_id"];
$u_no = $_GET["u_no"];
$link = mysqli_connect(
    '140.127.218.154',
    'nukcomet2589',
    'commentnuk2589',
    'nukcomet'
);

mysqli_query($link,'set names utf8');

echo "<input type='button' value='我要寫評論'><br>";
echo "<div onclick=order_by_year()><a>學年度</a></div>";
echo "<div  onclick=order_by_nuk()><a>鈕扣數多-少</a></div>";

$test = 'test123456';

$sql_course_info = "SELECT course.Course_Name as c_name,teacher.Teacher_Name as t_name
FROM course,teacher
WHERE course.Teacher_Id = teacher.Teacher_Id AND course.Course_Id =$c_id";
$result = mysqli_query($link,$sql_course_info);
$row = mysqli_fetch_assoc($result);
echo $row[c_name].$row[t_name];


$sql_comment = "SELECT comment.comment_id,comment.course_id,comment.comment_AcademicYear,comment.comment_semester,comment.rating_easy,comment.rating_highscored,comment.rating_enriched,comment.rating_all,comment.content,
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
GROUP BY comment.comment_id";
$result_comment = mysqli_query($link,$sql_comment);
    echo "<form metho='POST'>";
while($comment = mysqli_fetch_assoc($result_comment)){
    echo "<div class ='comment'>";
    echo "<td><a>".$comment[comment_AcademicYear]."學年第<td>";
    echo "<td>".$comment[comment_semester]."學期</a><td>";
    echo "<td><a>涼:".$comment[rating_easy]."</a></td>";
    echo "<td><a>甜:".$comment[rating_highscored]."</a></td>";
    echo "<td><a>實:".$comment[rating_enriched]."</a></td>";
    echo "<td><a>總分:".$comment[rating_all]."</a></td></tr>";  
    echo "<div>";
    echo "<input type='image' img src='image/nuk.png' onclick=nuk(".$comment[comment_id].",".$u_no.",".document.",".formname.",".submit().") value='nuk'>:";
    echo "<div class='nuk' id='nuk_num".$comment[comment_id]."'></div>";
    echo "<script>document.querySelector('#nuk_num".$comment[comment_id]."').textContent =".$comment[nuk_num]."</script><br>";
    echo "<input type='button' onclick=dislike(".$comment[comment_id].",'".$u_no."') value='dislike'>:";
    echo "<div class ='dislike' id='dislike_num".$comment[comment_id]."'></div>";
    echo "<script>document.querySelector('#dislike_num".$comment[comment_id]."').textContent = ".$comment[dislike_num]."</script><br>";
    echo "</div>";
    echo "<tr><td>".$comment[content]."</td></div>";
};
echo "</form>";

echo "<div id='test'></div>"

?>

<script>
function nuk(comment_id,u_no){
    $.ajax({
        url:'nuk.php',
        dataType:'json',
        data:'&comment_id='+comment_id+'&u_no='+u_no,
        type:'POST',
        error:function(msg){
            alert(u_no);
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
        error:function(msg){
            alert("fail")
            }
        ,
        success:function(data){
            document.querySelector('#dislike_num'+data.comment_id).textContent = data.dislike_num;
        }
    });
};

function order_by_year(){
     
    <?php 
    $sql_comment = $sql_comment.'
    order by comment.comment_AcademicYear ASC' 
    ?>;
    
    var $sql_comment = <?php echo $sql_comment; ?>;

document.querySelector('#test').textContent = $sql_comment;
};
</script>