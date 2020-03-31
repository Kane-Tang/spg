<?php
//include_once("conn_db.php");
//include_once("menu.php");

$link = mysql_connect('ksiresearchorg.ipagemysql.com', 'duncan', 'duncan');
if (!$link) {
    die('Could not connect: ' . mysql_error());
}
mysql_select_db(chronobot);
session_start();
//$q = $_SESSION['query'];
//$result = mysql_query($q);
//echo $q;
//$link->close();

$servername = "ksiresearchorg.ipagemysql.com";
$username = "duncan";
$password = "duncan";
$dbname = "tdr_save";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("fail" . $conn->connect_error);
}

//$del_sql = "DELETE FROM chronobot.events";
//$conn->query($del_sql);k

$ins_sql = "INSERT INTO chronobot.events";
$ins_sql .= " SELECT * FROM tdr_save.";
$year = $_POST["year"];
$month = $_POST["month"];
$date = $_POST["date"];
$name = "table_";
$name .= $year;
$name .= "_";
$name .= $month;
$name .= "_";
$name .= $date;
$ins_sql .= $name;
if($conn->query($ins_sql) == TRUE){
    echo "restore successfully";
}else{
    echo "database name nonexistent";
}
$conn->close();

?>
