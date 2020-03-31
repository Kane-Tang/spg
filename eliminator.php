<?php
include_once("conn_db.php");
include_once("menu.php");


/**** example 1 ****/
/*
 $records = fetch_data_from_table_events();
 // $records = [step_values(arr), brain_values(arr), image_values(arr), audio_values(arr)]
 $step_avg = round(average($records[0]), 3);
 $brain_avg = round(average($records[1]), 3);
 $image_avg = round(average($records[2]), 3);
 $audio_avg = round(average($records[3]), 3);
 
 insert_events_record($step_avg, $brain_avg, $image_avg, $audio_avg, 'algorithm1');
 */
/**** example 1 ****/
$threshold = $_POST["threshold"];
eliminate_with_threshold($threshold);

/* remove events record with average value better than threshold */
// 1 is the worst / 0 is the best
function eliminate_with_threshold($threshold=0) {
    if ($threshold < 0 || $threshold > 1) {
        print("Threshold should be in 0~1");
        return ;
    }
    
    $q = "DELETE FROM events WHERE strength >= " . $threshold;
    mysql_query($q);
}

?>
