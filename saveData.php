<?php
// include_once("conn_db.php");
// include_once("menu.php");
$link = mysql_connect('ksiresearchorg.ipagemysql.com', 'duncan', 'duncan');
if (! $link) {
    die('Could not connect: ' . mysql_error());
}
mysql_select_db(chronobot);
session_start();
// $q = $_SESSION['query'];
// $result = mysql_query($q);
// echo $q;
// $link->close();

$servername = "ksiresearchorg.ipagemysql.com";
$username = "duncan";
$password = "duncan";
$dbname = "tdr_save";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("fail" . $conn->connect_error);
}

$rows = mysql_result(mysql_query('SELECT COUNT(*) FROM chronobot.events'), 0);
if (! $rows) {
    echo "already saved in the database 'tdr_save'";
} else {
    $q = "DROP TABLE IF EXISTS tdr_save.";
    $tablename = "table_";
    $tablename .= date("Y_m_d");
    $q .= $tablename;
    $conn->query($q);
    // session_start();
    $sql = "CREATE TABLE ";
    $name = "table_";
    $name .= date("Y_m_d");
    $sql .= $name;
    $sql .= " (
EventGraph_ID INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
node_ID INT(11) NOT NULL,
node_value VARCHAR(30) NOT NULL,
previous_nodeID INT(11),
pattern_ID INT(11) NOT NULL,
strength FLOAT NOT NULL,
timestamp DATETIME NOT NULL,
source VARCHAR(30),
update_type VARCHAR(30)
)";

    if ($conn->query($sql) === TRUE) {
        echo "create and";
    } else {
        echo "fail: " . $conn->error;
    }

    $xyear = $_POST["xyear"];
    $xmonth = $_POST["xmonth"];
    $xdate = $_POST["xdate"];
    $xno = $_POST["xno"];
    
    $usr_sql = "UPDATE chronobot.users SET xyear = ";
    $usr_sql .= $xyear;
    $usr_sql .= ", xmonth = ";
    $usr_sql .= $xmonth;
    $usr_sql .= ", xday = ";
    $usr_sql .= $xdate;
    $usr_sql .= ", xno = ";
    $usr_sql .= $xno;
    $uid = $_SESSION['uid'];
    $usr_sql .= " WHERE uid = ";
    $usr_sql .= $uid;
    if($conn->query($usr_sql) == TRUE){
        //echo "success\n";
    }else{
        echo "error:" . $conn->error;
    }
    
    $ins_sql = "INSERT INTO tdr_save.";
    $ins_sql .= $name;
    $ins_sql .= " SELECT * FROM chronobot.events";
    if ($conn->query($ins_sql) == TRUE) {
        echo "insert successfully into the database 'tdr_save': ";
        echo $name;
        $del_sql = "DELETE FROM chronobot.events";
        $conn->query($del_sql);
    } else {
        echo "fail: " . $conn->error;
    }
}

$conn->close();
exec('java -jar event_detection.jar', $output);
?>
