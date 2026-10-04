<?php
session_start();
unset($_SESSION['LOGIN']);
unset($_SESSION['ID']);
header("location:index.php");
?>