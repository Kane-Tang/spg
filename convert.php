<?php
  include_once("conn_db.php");
  include_once("menu.php");


  /* keeps events having only certain amount of record */
  // if $amount >= actual_record_size : no record needs to be deleted
  // else : (actual_record_size - $amount) need to be deleted from events
  // worse node_value (close to 1) is first deleted
  function concentrate_by_amount($amount=-1) {
    if ($amount < 0) {
      echo "amount should be a positive number\n";
      return ;
    }

    $record_size = 0;
    $q = "SELECT count(*) FROM events";
    $result = mysql_query($q);
    while($row = mysql_fetch_row($result)) {
      $record_size = $row[0];
    }

    if ($amount >= $record_size) {
      // no need to concentrate
      return ;
    }

    $need_to_delete = $record_size - $amount;
    $delete_list = array();
    $q = "SELECT EventGraph_ID FROM events ORDER BY node_value DESC LIMIT " . $need_to_delete;
    $result = mysql_query($q);
    while($row = mysql_fetch_row($result)) {
      $delete_list[] = $row[0];
    }

    foreach($delete_list as $id) {
      $q = "DELETE FROM events WHERE EventGraph_ID = " . $id;
      mysql_query($q);
    }

  }

  /* remove events record with average value better than threshold */
  // 1 is the worst / 0 is the best
  function eliminate_with_threshold($threshold=0) {
    if ($threshold < 0 || $threshold > 1) {
      print("Threshold should be in 0~1");
      return ;
    }

    $q = "DELETE FROM events WHERE node_value < " . $threshold;
    mysql_query($q);
  }


  /* insert a new record into table: events
  // $step: inserted step value
  // $brain: inserted brain wave value
  // $image: inserted image value
  // audio: inserted audio value
  // algo_type: used algorithm type for calculation */
  function insert_events_record($step, $brain, $image, $audio, $algo_type) {
    // time format: YYYY-MM-DD 23:49:32
    $time = date("Y-m-d H:i:s");
    // insert calculated record
    $q = "INSERT INTO `events` (`EventGraph_ID`, `node_ID`, `node_value`, `previous_nodeID`, `pattern_ID`, `strength`, `timestamp`, `source`, `update_type`) VALUES ";
    $q = $q . "('0', '0', '" . $step  . "', '0', '0', '1', '" . $time . "', 'Steps', '" . $algo_type . "'), ";
    $q = $q . "('0', '0', '" . $brain . "', '0', '0', '1', '" . $time . "', 'BrainWave', '" . $algo_type . "'), ";
    $q = $q . "('0', '0', '" . $image . "', '0', '0', '1', '" . $time . "', 'Image', '" . $algo_type . "'), ";
    $q = $q . "('0', '0', '" . $audio . "', '0', '0', '1', '" . $time . "', 'Audio', '" . $algo_type . "')";
    mysql_query($q);
  }

  /* fetch records from table: events
  // $w1 == 1: fetched records included those with column update_type = 'date'
  // $w2 == 1: fetched records included those with column update_type = 'user'
  // $w3 == 1: fetched records included those with column update_type = 'algorithm1' */
  function fetch_data_from_table_events($w1=1, $w2=1, $w3=0) {
    $update_type_filter = '';
    if ($w1 == 1) {
      // if fetch records with update_type: data
      $update_type_filter .= "(update_type = 'data'";
    }

    if ($w2 == 1) {
      // if fetch records with update_type: user
      if ($update_type_filter != '') {
        $update_type_filter .= " OR update_type = 'user'";
      } else {
        $update_type_filter .= "(update_type = 'user'";
      }
    }

    if ($w3 == 1) {
      // if fetch records with update_type: algo
      if ($update_type_filter != '') {
        $update_type_filter .= " OR update_type = 'algorithm1'";
      } else {
        $update_type_filter .= "(update_type = 'algorithm1'";
      }
    }

    if ($update_type_filter != '') {
      $update_type_filter .= ")";
    } else {
      return array(NULL, NULL, NULL, NULL);
    }

    $q = "SELECT `node_value`, `source`, `timestamp` FROM `events` WHERE " . $update_type_filter . " AND (source = 'BrainWave' OR source = 'Steps' OR source = 'Image' OR source = 'Audio') ORDER BY timestamp";
    $result = mysql_query($q);

    $records = array();
    $step = array();
    $brain = array();
    $image = array();
    $audio = array();

    while ($row = mysql_fetch_row($result)) {
      switch($row[1]) {
        case 'Steps':
          $step[] = $row[0];
          break;
        case 'BrainWave':
          $brain[] = $row[0];
          break;
        case 'Image':
          $image[] = $row[0];
          break;
        case 'Audio':
          $audio[] = $row[0];
          break;
        default:
          echo 'wrong source';
          break;
      }
    }

    return array($step, $brain, $image, $audio);
  }

  // average distance
  // default algorithm 1
  function average($nums) {
    # edge case
    if ($nums == NULL || count($nums) == 0) {
      return 0.0;
    } else {
      $sum = 0.0;
      for ($i = 0; $i < count($nums); $i++) {
        $sum += $nums[$i];
      }
      return $sum / count($nums);
    }
  }

?>
