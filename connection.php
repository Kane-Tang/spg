<?php
$conn = new mysqli('ksiresearchorg.ipagemysql.com', 'duncan', 'duncan', 'tdr_save');
if(mysqli_connect_error()){
    die('Could not connect: ' .  mysqli_connect_error());
}
?>