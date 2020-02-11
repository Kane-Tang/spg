<?php
  header('Content-Type: application/json');
  require_once("conn.php");
  $query = "SELECT func, adap, invo, docu, expe, tmode, sche, miti, psize, prot, pcross FROM spg_model WHERE modelname=?";
  $ret = array();
  $modelName = $_POST['modelname'];
  if ($stmt = $conn->prepare($query)) {
      $stmt->bind_param("s",$modelName);
      $stmt->execute(); 
      $stmt->bind_result($ret['func'], $ret['adap'], $ret['invo'], 
                              $ret['docu'], $ret['expe'], $ret['tmode'], 
                              $ret['sche'], $ret['miti'], $ret['psize'], 
                              $ret['prot'], $ret['pcross']);
      if(!$stmt->fetch()) {
        $ret['err'] = 1;
      }
      echo json_encode($ret);
  } else {
    $conn->close();
    die("DB preparation failed!");
  }

?>