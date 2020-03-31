<?php
  header('Content-Type: application/json');
  require_once("conn.php");
  $op = $_POST['op'];
  $query = "";
  $startdate = $_POST["start"];
  $enddate = $_POST["end"];
  $ret = array();
  if($op == "check"){
      $query = "SELECT type, COUNT(*), SUM(understand), SUM(clarity) From spg_tally WHERE timestamp BETWEEN '".$startdate."' AND '".$enddate."' GROUP BY type ORDER BY type";
  }
  else if($op == "clear"){
      $query = "DELETE FROM spg_tally WHERE timestamp BETWEEN '".$startdate."' AND '".$enddate."'";
  }
  else {
    $ret['err'] = 1;
    return;
  }
  $data = array();
  $modelName = $_POST['modelname'];
  if ($stmt = $conn->prepare($query)) {
      if(!$stmt->execute()) {
        $ret['err'] = 1;
        echo json_encode($ret);
        return;
      }
      if($op == "clear") {
        echo json_encode($ret);
        return;
      }
      $stmt->bind_result($data['type'], $data['count'], $data['sum1'], $data['sum2']);
      $typename = ['default', 'CS1530 Student', 'CS1631 Student', 'Graduate Student', 'Professional', 'Other'];
      $ret['Total'] = array();
      $ret['Total']['amount'] =  $ret['Total']['aver1'] =  $ret['Total']['aver2'] = 0;
      while($stmt->fetch()) {
        $t = $typename[$data['type']];
        $ret[$t] = array();
        $ret[$t]['aver1'] = $data['sum1'] / $data['count'];
        $ret[$t]['aver2'] = $data['sum2'] / $data['count'];
        $ret[$t]['amount'] = $data['count'];
        $ret['Total']['aver1'] += $data['sum1'];
        $ret['Total']['aver2'] += $data['sum2'];
        $ret['Total']['amount'] += $data['count'];
      }
      if($ret['Total']['amount'] > 0) { 
        $ret['Total']['aver1'] /=  $ret['Total']['amount'];
        $ret['Total']['aver2'] /= $ret['Total']['amount'];
      }
      echo json_encode($ret);
  } else {
    $conn->close();
    die("DB preparation failed!");
  }
?>