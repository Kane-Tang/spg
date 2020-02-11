<?php
  session_start();
  if($_SESSION["admin"] != true) {
    $_SESSION["admin"] = false;
    echo "<script>alert('Please Sign in!'); window.location.href='admin.html';</script>";
  }
  require_once("conn.php");
  $len = 20;
  $pageIndex = (int)$_GET["page"];
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

  $result = $conn->query("SELECT * FROM spg_comment limit {$pos}, {$len}");
  $typename = ['default ', 'CS1530 Student', 'CS1631 Student', 'Graduate Student', 'Professional', 'Other'];
  while($row = $result->fetch_row()) {
    echo "[" . $row[3] . "].[" . $typename[$row[1]] ."]:&nbsp" . $row[2] . "<br>";
  }
  echo "<br><a href='comment.php?page={$prev}'>Previous</a>&nbsp&nbsp<a href='comment.php?page={$next}'>Next</a>";

?>