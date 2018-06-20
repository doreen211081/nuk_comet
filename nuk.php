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
$sql_all_nuk_user ="SELECT *
FROM nuk
WHERE nuk.user_id = $u_no AND nuk.comment_id = $comment_id";
$result_nuk_user = mysqli_query($link,$sql_all_nuk_user);
$all_nuk_user = mysqli_fetch_assoc($result_nuk_user);

if($u_no == $all_nuk_user[user_id] || $comment_id == $all_nuk_user[comment_id]){
    $sql = "DELETE FROM nuk
    WHERE nuk.user_id = $u_no AND nuk.comment_id = $comment_id";
}
else{
    $sql = "INSERT INTO nuk (user_id,comment_id) VALUES('$u_no','$comment_id')";
}
    mysqli_query($link,$sql);
    $sql_countnuk = "SELECT nuk.comment_id as c_id,COUNT(nuk.user_id) as num
    FROM nuk
    WHERE nuk.comment_id = $comment_id
    GROUP BY nuk.comment_id";

$result_num = mysqli_query($link,$sql_countnuk);
$nuk_num = mysqli_fetch_assoc($result_num);

if($nuk_num == null){
    $nuk_num[num] = 0;
    $nuk_num[c_id] = $comment_id;
}

$json = array(
    'nuk_num' =>$nuk_num[num],
    'comment_id' =>$nuk_num[c_id],
);
echo json_encode($json);
?>
