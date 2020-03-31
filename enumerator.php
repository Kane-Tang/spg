<?php
include_once("conn_db.php");
include_once("menu.php");


/**** example 1 ****/
$w1 = $_POST["w1"];
$w2 = $_POST["w2"];
$w3 = $_POST["w3"];
 $records = fetch_data_from_table_events($w1, $w2, $w3);
 // $records = [step_values(arr), brain_values(arr), image_values(arr), audio_values(arr)]
 $step_avg = round(average($records[0]), 3);
 $brain_avg = round(average($records[1]), 3);
 $image_avg = round(average($records[2]), 3);
 $audio_avg = round(average($records[3]), 3);
 
 insert_events_record($step_avg, $brain_avg, $image_avg, $audio_avg, 'algorithm1');
 

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
function fetch_data_from_table_events($w1, $w2, $w3) {
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
