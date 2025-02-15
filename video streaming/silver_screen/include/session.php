<?php
error_reporting(0);
    session_start();
    
    if(!isset($_SESSION["username"])){
        session_destroy();
        header("Location: ./login.php");
        die();
    }
?>