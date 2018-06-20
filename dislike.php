<?php

header ( "Content-type:text/html;charset=utf-8" );

$comment_id = $_POST['comment_id'];
$u_no = $_POST['u_no'];

$link = mysqli_connect(
    '140.127.218.154',
    'nukcomet2589',
    'commentnuk2589',
    'nukcomet'
);

mysqli_query($link,'set names utf8');
$sql_all_dislike_user ="SELECT *
FROM dislike
WHERE dislike.User_Id = $u_no AND dislike.Comment_Id = $comment_id";
$result_dislike_user = mysqli_query($link,$sql_all_dislike_user);
$all_dislike_user = mysqli_fetch_assoc($result_dislike_user);

if($u_no == $all_dislike_user[user_id] || $comment_id == $all_dislike_user[comment_id]){
    $sql = "DELETE FROM dislike
    WHERE dislike.User_Id = $u_no and dislike.Comment_Id = $comment_id";
}

else{
    $sql = "INSERT INTO dislike (user_id,comment_id) VALUES('$u_no','$comment_id')";
}

mysqli_query($link,$sql);

$sql_countdislike = "SELECT dislike.Comment_Id as c_id,count(dislike.User_Id) as num
FROM dislike
WHERE dislike.comment_id = $comment_id
GROUP BY dislike.Comment_Id";
$result_num = mysqli_query($link,$sql_countdislike);
$dislike_num = mysqli_fetch_assoc($result_num);

if($dislike_num == null){
    $dislike_num[num] = 0;
    $dislike_num[c_id] = $comment_id;
}

$json = array(
    'dislike_num' =>$dislike_num[num],
    'comment_id' =>$dislike_num[c_id]
);
echo json_encode($json);
?>
