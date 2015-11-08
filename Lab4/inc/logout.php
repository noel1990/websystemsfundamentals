<?php
/*----------------------------
logout.php
Handles logging out
------------------------------*/

session_start();

$_SESSION = array();

session_unset();
session_destroy();


$redirect = isset($_GET['previous'])?$_GET['previous']:'';

header("Location: {$redirect}.php");

?>