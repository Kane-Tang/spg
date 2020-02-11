<?php
  require_once("conn.php");
  $query = "UPDATE spg_model SET func=?, adap=?, invo=?, docu=?, expe=?, tmode=?, sche=?, miti=?, psize=?, prot=?, pcross=? WHERE modelname=?";
  $ret = -1;
  $func=(int)$_POST['func'];
  $adap=(int)$_POST['adap'];
  $invo=(int)$_POST['invo'];
  $docu=(int)$_POST['docu'];
  $expe=(int)$_POST['expe'];
  $tmode=(int)$_POST['tmode'];
  $sche=(int)$_POST['sche'];
  $miti=(int)$_POST['miti'];
  $psize=(int)$_POST['psize'];
  $prot=(int)$_POST['prot'];
  $pcross=(int)$_POST['pcross'];
  $modelname=$_POST['modelname'];
  if ($stmt = $conn->prepare($query)) {
      $stmt->bind_param("iiiiiiiiiiis",$func, $adap, $invo, $docu, $expe, $tmode, $sche, $miti, $psize, $prot, $pcross, $modelname);
      $stmt->execute(); 
      if($stmt->affected_rows > 0) {
        $ret = 0;
      } 
      echo $ret;
  } else {
    $conn->close();
    die("DB preparation failed!");
  }
?>