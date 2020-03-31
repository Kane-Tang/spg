<?php
  session_start();
  if($_SESSION["admin"] != true) {
    $_SESSION["admin"] = false;
    echo "<script>alert('Please Sign in!'); window.location.href='admin.html';</script>";
  }
  require_once("conn.php");
  $len = 20;
  $pageIndex = (int)$_GET["page"];
  $year1 = $_GET["year1"];
  $month1 = $_GET["month1"];
  $date1 = $_GET["date1"];
  $year2 = $_GET["year2"];
  $month2 = $_GET["month2"];
  $date2 = $_GET["date2"];
  $startdate = $year1;
  $startdate .= "-";
  $startdate .= $month1;
  $startdate .= "-";
  $startdate .= $date1;
  $startdate = $_GET["start"];
 //echo $startdate;
  //$startdate .= " 00:00:00";
  //$startdate = "2018-01-01";
  $enddate = $year2;
  $enddate .= "-";
  $enddate .= $month2;
  $enddate .= "-";
  $enddate .= $date2;
  $enddate = $_GET["end"];
  //echo $enddate;
  //$enddate .= " 00:00:00";
  //$enddate = "2020-03-26";
  if($pageIndex < 1) $pageIndex = 1;
  $result = $conn->query("SELECT COUNT(*) FROM spg_comment");
  $row = $result->fetch_row();
  $pageAmount = ceil($row[0] / $len);
  if($pageIndex > $pageAmount) $pageIndex = $pageAmount; 
  $prev = $pageIndex - 1;
  $pos = $prev * $len;
  if($prev < 1) $prev = 1;
  $next = $pageIndex + 1;
  if($next > $pageAmount) $next = $pageAmount;
  $query = "SELECT * FROM spg_comment WHERE time BETWEEN '".$startdate."' AND '".$enddate."' limit {$pos}, {$len}";
  //echo $query;
  $result = $conn->query($query);
  $typename = ['default ', 'CS1530 Student', 'CS1631 Student', 'Graduate Student', 'Professional', 'Other'];
  while($row = $result->fetch_row()) {
    echo "[" . $row[3] . "].[" . $typename[$row[1]] ."]:&nbsp" . $row[2] . "<br>";
  }
  echo "<br><a href='comment.php?page={$prev}&start={$startdate}&end={$enddate}'>Previous</a>&nbsp&nbsp<a href='comment.php?page={$next}&start={$startdate}&end={$enddate}'>Next</a>";

?>