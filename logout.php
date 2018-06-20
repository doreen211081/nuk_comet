<?php 
session_start();
unset($_SESSION["uno"]);
session_destroy();

header("Location:index.php");
 ?>