<?php
error_reporting(0);
$mysql = mysqli_connect("localhost","root","","silver_screen");

if(mysqli_connect_errno()){
    echo mysqli_connect_errno();
}