<?php
    $host="localhost";
    $username="root";
    $password="";
    $database="sunrise_dbs";

    $con=mysqli_connect($host,$username,$password,$database);
    if(!$con){
        die("failed to connect");
    }
?>