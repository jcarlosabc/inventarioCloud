<?php 
session_start();
$link = isset($_SESSION['link']) ? $_SESSION['link'] : '';
$_SESSION = [];
session_destroy();
if (!empty($link)) {
    header("Location: https://sunny-part.000webhostapp.com/login.php?link=".$link);
} else {
    header("Location: https://sunny-part.000webhostapp.com/login.php");
}
?>
