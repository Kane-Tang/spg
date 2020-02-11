<?php
  header('Content-Type: application/json');
  require_once("conn.php");
  $query = "SELECT modelname, func, adap, invo, docu, expe, tmode, sche, miti, psize, prot, pcross FROM spg_model";
  $data = array();
  $ret = array();
  $para = array($_POST['func'],$_POST['adap'],$_POST['invo'],$_POST['docu'],$_POST['expe'],$_POST['tmode'],$_POST['sche'],$_POST['miti'],$_POST['psize'],$_POST['prot'],$_POST['pcross']);
  $weight = array(1.0,1.0,1.0,1.0,1.0,1.0,1.0,1.0,1.0,1.0,1.0);
  if ($stmt = $conn->prepare($query)) {
      $stmt->execute(); 
      $stmt->bind_result($data[0], $data[1], $data[2], $data[3], 
                              $data[4], $data[5], $data[6], 
                              $data[7], $data[8], $data[9], 
                              $data[10], $data[11]); 
    $opt = 12.0;
     while($stmt->fetch()) {
        $ret[$data[0]] = 0.0;
        for($i=1; $i < count($data); $i++) { 
          if(!ctype_digit($para[$i - 1])) continue;
          $ret[$data[0]] += $weight[$i - 1] * abs($data[$i] - (int)$para[$i - 1]);
        }
        if($opt > $ret[$data[0]]) {
          $opt = $ret[$data[0]];
          $ret['opt'] = $data[0];
        }
      }
      echo json_encode($ret);
  } else {
    $conn->close();
    die("DB preparation failed!");
  }
?>