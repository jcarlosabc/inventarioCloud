<?php 
session_start();
//$_SESSION=[];
session_destroy();
header("Location: https://sunny-part.000webhostapp.com/login.php");
?>