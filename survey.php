<?php
  require_once("conn.php");
  $type=(int)$_POST['usertype'];
  $understand=(int)$_POST['understand'];
  $clarity=(int)$_POST['clarity'];
  $comments=$_POST['comments'];
  $ret = 0;
  if ($stmtTally = $conn->prepare("INSERT INTO spg_tally (type, understand, clarity) VALUES (?, ?, ?)")) {
      $stmtTally->bind_param("iii", $type, $understand, $clarity);
      $stmtTally->execute(); 
      if($stmtTally->affected_rows == 0) {
        $ret = 1;
      }  } else {
    $conn->close();
    die("DB preparation failed!");
  }
  if(empty(trim($comments))) {
    echo $ret;
    return;
  }
  if ($stmtComm = $conn->prepare("INSERT INTO spg_comment (type, comment) VALUES (?, ?)")) {
      $stmtComm->bind_param("is", $type, $comments);
      $stmtComm->execute(); 
      if($stmtComm->affected_rows == 0) {
        $ret += 2;
      }
  } else {
    $conn->close();
    die("DB preparation failed!");
  }
  echo $ret;
?>