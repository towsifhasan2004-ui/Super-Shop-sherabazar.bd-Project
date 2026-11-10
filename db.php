<?php
$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "supershop_db"
);

if(!$conn){
    die("Connection Failed");
}
?>