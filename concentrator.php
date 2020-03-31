<?php
include_once("conn_db.php");
include_once("menu.php");

$amount = $_POST["amount"];
concentrate_by_amount($amount);


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
    $q = "SELECT EventGraph_ID FROM events ORDER BY strength DESC LIMIT " . $need_to_delete;
    $result = mysql_query($q);
    while($row = mysql_fetch_row($result)) {
        $delete_list[] = $row[0];
    }
    
    foreach($delete_list as $id) {
        $q = "DELETE FROM events WHERE EventGraph_ID = " . $id;
        mysql_query($q);
    }
    
}


?>
