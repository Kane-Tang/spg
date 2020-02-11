<?php
  session_start();
  require_once("conn.php");
  $name = $_POST["name"];
  $psw = $_POST["psw"];
  if ($stmt = $conn->prepare("SELECT * FROM spg_user WHERE username=? AND password=?")) {
      $stmt->bind_param("ss",$name,$psw);
      $stmt->execute();
      if($stmt->fetch()) {
        $_SESSION["admin"] = true;
        echo "<script>window.location.href='config.php';</script>";
      } else {
        $_SESSION["admin"] = false;
        echo "<script>alert('Wrong Username/Password!');history.back();</script>";
      }
  } else {
    $conn->close();
    die("DB preparation failed!");
  }
?>