<?php
include_once("conn_db.php");
include_once("menu.php");

$selectuser = $_SESSION['selectuser'];
$sql1 = " SELECT * FROM users WHERE users.email = '$selectuser' ";
$rrrr = mysql_query($sql1);
$rrrs = mysql_fetch_assoc($rrrr);
// foreach($rrrs as $value){
// echo $value;
// echo "-- --";}
$uid = $rrrs['uid'];

$from_time = $_SESSION['starting'];
$to_time = $_SESSION['ending'];

$brainwave_event_icon = mysql_fetch_assoc(mysql_query("SELECT * FROM `events` WHERE `source`='BrainWave' ORDER BY `timestamp` DESC LIMIT 1;"));
$audio_event_icon = mysql_fetch_assoc(mysql_query("SELECT * FROM `events` WHERE `source`='Audio' ORDER BY `timestamp` DESC LIMIT 1;"));
$image_event_icon = mysql_fetch_assoc(mysql_query("SELECT * FROM `events` WHERE `source`='Image' ORDER BY `timestamp` DESC LIMIT 1;"));
$steps_event_icon = mysql_fetch_assoc(mysql_query("SELECT * FROM `events` WHERE `source`='Steps' ORDER BY `timestamp` DESC LIMIT 1;"));

//foreach($brainwave_event_icon as $value){
//    echo $value;
//    echo "-- --";
//}

function event_icon_color_from_db($strength) {
    $origin_red_icon = decide_color_stats_from_user_feedback("Worst");
    $origin_yellow_icon = decide_color_stats_from_user_feedback("Worse");
    $origin_green_icon = decide_color_stats_from_user_feedback("Normal");
    $origin_blue_icon = decide_color_stats_from_user_feedback("Great");
    $event_icon_color_boundary_1 = 0.25;
    $event_icon_color_boundary_2 = 0.50;
    $event_icon_color_boundary_3 = 0.75;
    $event_icon_color_boundary_1 = decide_range_stats_from_user_feedback("NormalValue");
    $event_icon_color_boundary_2 = decide_range_stats_from_user_feedback("WorseValue");
    $event_icon_color_boundary_3 = decide_range_stats_from_user_feedback("WorstValue");
    if ($strength > $event_icon_color_boundary_3){
        return $origin_red_icon;
    } else{
        if($strength > $event_icon_color_boundary_2){
            return $origin_yellow_icon;
        }else{
            if($strength > $event_icon_color_boundary_1){
                return $origin_green_icon;
            }
        }
    }
    return $origin_blue_icon;
}


function decide_color_stats_from_user_feedback($origin_color_icon) {
    $check_questionar_sql = mysql_fetch_assoc(mysql_query("SELECT `stats` FROM `questionnaire` WHERE `basicInfo` = '$origin_color_icon' ORDER BY `timestamp` DESC LIMIT 1;"));
    if($check_questionar_sql["stats"] == "Red") {
        return 4;
    } else{
        if ($check_questionar_sql["stats"] == "Yellow") {
            return 3;
        } else{
            if ($check_questionar_sql["stats"] == "Green") {
                return 2;
            }else{
                return 1;
            }
        }
    }
    return 1;
}

function decide_range_stats_from_user_feedback($origin_range_icon){
    $origin_range_sql= mysql_fetch_assoc(mysql_query("SELECT `stats` FROM `questionnaire` WHERE `basicInfo` = '$origin_range_icon' ORDER BY `timestamp` DESC LIMIT 1;"));
    return $origin_range_sql["stats"];
}

function event_icon_change_timestamp($event_in_db, $strength_color_temp, $time_temp_from_user){
    $insert_events_db_node_ID=$event_in_db["node_ID"];
    $insert_events_db_node_value=$event_in_db["node_value"];
    $insert_events_db_previous_nodeID=$event_in_db["previous_nodeID"];
    $insert_events_db_pattern_ID=$event_in_db["pattern_ID"];
    $insert_events_db_strength=$strength_color_temp;
    $insert_events_db_timestamp=$time_temp_from_user;
    $insert_events_db_source=$event_in_db["source"];
    $insert_events_db_update_type="user";
    $sql_insert_to_events= "INSERT INTO `events` (`EventGraph_ID`, `node_ID`, `node_value`, `previous_nodeID`, `pattern_ID`, `strength`, `timestamp`, `source`, `update_type`) VALUES('0','$insert_events_db_node_ID','$insert_events_db_node_value','$insert_events_db_previous_nodeID','$insert_events_db_pattern_ID','$insert_events_db_strength','$insert_events_db_timestamp','$insert_events_db_source','$insert_events_db_update_type');";
//    echo "\n";
//    echo $sql_insert_to_events;
//    echo "\n";
    return $sql_insert_to_events;
}

//CHECKING SUBMIT BUTTON PRESS or NOT.
$test_user_select_color = $_POST['color_selection'];
if($test_user_select_color != null){
    $user_change_color = $_POST['feature_selection'];
    $user_select_color = $_POST['color_selection'];

    $strength_color = 0.0;
    if($user_select_color==1){
        $strength_color = 0.12;
    }
    if($user_select_color==2){
        $strength_color = 0.37;
    }
    if($user_select_color==3){
        $strength_color = 0.62;
    }
    if($user_select_color==4){
        $strength_color = 0.87;
    }

    $user_change_time = date("Y-m-d H:i:s");
//    echo $user_change_time;

    // 1 Brainwave
    if($user_change_color==1){
        $insert_to_events = mysql_query(event_icon_change_timestamp($brainwave_event_icon, $strength_color,$user_change_time));
        $insert_to_events = mysql_query(event_icon_change_timestamp($audio_event_icon, $audio_event_icon["strength"],$user_change_time));
        $insert_to_events = mysql_query(event_icon_change_timestamp($image_event_icon, $image_event_icon["strength"],$user_change_time));
        $insert_to_events = mysql_query(event_icon_change_timestamp($steps_event_icon, $steps_event_icon["strength"],$user_change_time));
    }
    // 2 Audio
    if($user_change_color==2){
        $insert_to_events = mysql_query(event_icon_change_timestamp($brainwave_event_icon, $brainwave_event_icon["strength"],$user_change_time));
        $insert_to_events = mysql_query(event_icon_change_timestamp($audio_event_icon,$strength_color,$user_change_time));
        $insert_to_events = mysql_query(event_icon_change_timestamp($image_event_icon, $image_event_icon["strength"],$user_change_time));
        $insert_to_events = mysql_query(event_icon_change_timestamp($steps_event_icon, $steps_event_icon["strength"],$user_change_time));
    }
    // 3 Image
    if($user_change_color==3){
        $insert_to_events = mysql_query(event_icon_change_timestamp($brainwave_event_icon, $brainwave_event_icon["strength"],$user_change_time));
        $insert_to_events = mysql_query(event_icon_change_timestamp($audio_event_icon, $audio_event_icon["strength"],$user_change_time));
        $insert_to_events = mysql_query(event_icon_change_timestamp($image_event_icon, $strength_color,$user_change_time));
        $insert_to_events = mysql_query(event_icon_change_timestamp($steps_event_icon, $steps_event_icon["strength"],$user_change_time));
    }
    // 4 Wellbeing
    if($user_change_color==4){
        $insert_to_events = mysql_query(event_icon_change_timestamp($brainwave_event_icon, $strength_color,$user_change_time));
        $insert_to_events = mysql_query(event_icon_change_timestamp($audio_event_icon, $strength_color,$user_change_time));
        $insert_to_events = mysql_query(event_icon_change_timestamp($image_event_icon, $strength_color,$user_change_time));
        $insert_to_events = mysql_query(event_icon_change_timestamp($steps_event_icon, $strength_color,$user_change_time));
    }

    // 5 Steps
    if($user_change_color==5){
        $insert_to_events = mysql_query(event_icon_change_timestamp($brainwave_event_icon, $brainwave_event_icon["strength"],$user_change_time));
        $insert_to_events = mysql_query(event_icon_change_timestamp($audio_event_icon, $audio_event_icon["strength"],$user_change_time));
        $insert_to_events = mysql_query(event_icon_change_timestamp($image_event_icon, $image_event_icon["strength"],$user_change_time));
        $insert_to_events = mysql_query(event_icon_change_timestamp($steps_event_icon, $strength_color,$user_change_time));
    }
}

$brainwave_event_icon_strength = 0;
$audio_event_icon_strength = 0;
$image_event_icon_strength = 0;
$steps_event_icon_strength = 0;
$wellbeing_event_icon_strength = 0;
$brainwave_event_icon = mysql_fetch_assoc(mysql_query("SELECT * FROM `events` WHERE `source`='BrainWave' ORDER BY `timestamp` DESC LIMIT 1;"));
$audio_event_icon = mysql_fetch_assoc(mysql_query("SELECT * FROM `events` WHERE `source`='Audio' ORDER BY `timestamp` DESC LIMIT 1;"));
$image_event_icon = mysql_fetch_assoc(mysql_query("SELECT * FROM `events` WHERE `source`='Image' ORDER BY `timestamp` DESC LIMIT 1;"));
$steps_event_icon = mysql_fetch_assoc(mysql_query("SELECT * FROM `events` WHERE `source`='Steps' ORDER BY `timestamp` DESC LIMIT 1;"));

$brainwave_event_icon_strength = $brainwave_event_icon["strength"];
$audio_event_icon_strength = $audio_event_icon["strength"];
$image_event_icon_strength = $image_event_icon["strength"];
$steps_event_icon_strength = $steps_event_icon["strength"];
$wellbeing_event_icon_strength = 0.25*($brainwave_event_icon_strength + $audio_event_icon_strength + $image_event_icon_strength + $steps_event_icon_strength);

//echo "testing icon color number";
//echo $brainwave_event_icon_strength;
//echo $audio_event_icon_strength;
//echo $image_event_icon_strength;
//echo $steps_event_icon_strength;

$_SESSION['user_select_color1-'. $uid] = event_icon_color_from_db($brainwave_event_icon_strength);
$_SESSION['user_select_color2-'. $uid] = event_icon_color_from_db($audio_event_icon_strength);
$_SESSION['user_select_color3-'. $uid] = event_icon_color_from_db($image_event_icon_strength);
$_SESSION['user_select_color4-'. $uid] = event_icon_color_from_db($steps_event_icon_strength);
$_SESSION['user_select_color5-'. $uid] = event_icon_color_from_db($wellbeing_event_icon_strength);


//if($fromtime != null){
//    echo $fromtime;
//    $sqltest = "SELECT * FROM `records` WHERE uid = '$uid' AND datetime >= '$fromtime'";
//    echo $sqltest;
//    $result_test = mysql_query($sqltest);
////$r_test_dt = mysql_fetch_assoc($result_test);
//// foreach($r_test_dt as $value){
//// echo $value;
//// echo "-- --";}
//    $dt_test_result = mysql_num_rows($result_test);
//    echo $dt_test_result;
//
//} else {
//    echo "testing";
//    $sqltest = "SELECT * FROM `records` WHERE uid = '$uid' AND datetime >= '2019-10-14T12:50:00.000' AND datetime <= '2019-10-14T13:00:00.000'";
//    $result_test = mysql_query($sqltest);
////$r_test_dt = mysql_fetch_assoc($result_test);
//// foreach($r_test_dt as $value){
//// echo $value;
//// echo "-- --";}
//    $dt_test_result = mysql_num_rows($result_test);
//    echo $dt_test_result;
//
//}

$hide = true;
$q = "select * from records, users where users.uid = records.uid AND (records.source = 'Analysis' OR records.source = 'EKG' OR records.source = 'SPO2' OR records.source = 'BloodPressure' OR records.source = 'temperature') AND users.email = '$selectuser'";
$result = mysql_query($q);
$num = mysql_num_rows($result);
if ($num > 0) {
    $hide = false;
}

$hide_air_temperature = true;
// source.php?type=ChiMonitor
$q = "select * from records, users where users.uid = records.uid AND records.source = 'SocialNetwork' AND records.type = 'chiTotal' AND users.email = '$selectuser'";
$result = mysql_query($q);
$num = mysql_num_rows($result);
if ($num > 0) {
    $hide_air_temperature = false;
}

$hide_fertilizer = true;
// source_notChi.php?type=Tian
$q = "select * from records, users where users.uid = records.uid AND records.source = 'Parrot' AND users.email = '$selectuser'";
$result = mysql_query($q);
$num = mysql_num_rows($result);
if ($num > 0) {
    $hide_fertilizer = false;
}

$hide_light = true;
// source_notChi.php?type=Di
$q = "select * from records, users where users.uid = records.uid AND records.source = 'Temperature' AND records.type = 'temp' AND users.email = '$selectuser'";
$result = mysql_query($q);
$num = mysql_num_rows($result);
if ($num > 0) {
    $hide_light = false;
}

$hide_moisture = true;
// source_notChi.php?type=Ren
$q = "select * from records, users where users.uid = records.uid AND (records.source = 'Analysis' OR records.source = 'EKG' OR records.source = 'SPO2' OR records.source = 'BloodPressure' OR records.source = 'temperature') AND users.email = '$selectuser'";
$result = mysql_query($q);
$num = mysql_num_rows($result);
if ($num > 0) {
    $hide_moisture = false;
}

$hide_air_temperature2 = true;
// source_notChi.php?type=temp
$q = "select * from records, users where users.uid = records.uid AND records.source = 'Temperature' AND records.type = 'temp' AND users.email = '$selectuser'";
$result = mysql_query($q);
$num = mysql_num_rows($result);
if ($num > 0) {
    $hide_air_temperature2 = false;
}

$hide_fertilizer2 = true;
// source_notChi.php?type=fertilizer
$q = "select * from records, users where users.uid = records.uid AND records.source = 'Parrot' AND records.type = 'fertilizer' AND users.email = '$selectuser'";
$result = mysql_query($q);
$num = mysql_num_rows($result);
if ($num > 0) {
    $hide_fertilizer2 = false;
}

$hide_light2 = true;
// source_notChi.php?type=light
$q = "select * from records, users where users.uid = records.uid AND records.source = 'Parrot' AND records.type = 'light' AND users.email = '$selectuser'";
$result = mysql_query($q);
$num = mysql_num_rows($result);
if ($num > 0) {
    $hide_light2 = false;
}

$hide_moisture2 = true;
// source_notChi.php?type=moisture
$q = "select * from records, users where users.uid = records.uid AND records.source = 'Parrot' AND records.type = 'moisture' AND users.email = '$selectuser'";
$result = mysql_query($q);
$num = mysql_num_rows($result);
if ($num > 0) {
    $hide_moisture2 = false;
}

$hide_sop2 = true;
// source_notChi.php?type=spo2
$q = "select * from records, users where users.uid = records.uid AND records.source = 'SPO2' AND users.email = '$selectuser'";
$result = mysql_query($q);
$num = mysql_num_rows($result);
if ($num > 0) {
    $hide_sop2 = false;
}

$hide_systolic = true;
// source_notChi.php?type=systolic
$q = "select * from records, users where users.uid = records.uid AND records.source = 'BloodPressure' AND records.type = 'systolic' AND users.email = '$selectuser'";
$result = mysql_query($q);
$num = mysql_num_rows($result);
if ($num > 0) {
    $hide_systolic = false;
}

$hide_diastolic = true;
// source_notChi.php?type=diastolic
$q = "select * from records, users where users.uid = records.uid AND records.source = 'BloodPressure' AND records.type = 'diastolic' AND users.email = '$selectuser'";
$result = mysql_query($q);
$num = mysql_num_rows($result);
if ($num > 0) {
    $hide_diastolic = false;
}

$hide_pulse = true;
// source_notChi.php?type=pulse
$q = "select * from records, users where users.uid = records.uid AND records.source = 'BloodPressure' AND records.type = 'pulse' AND users.email = '$selectuser'";
$result = mysql_query($q);
$num = mysql_num_rows($result);
if ($num > 0) {
    $hide_pulse = false;
}

$hide_ekg = true;
// source_notChi.php?type=EKG
$q = "select * from records, users where users.uid = records.uid AND records.source = 'EKG' AND users.email = '$selectuser'";
$result = mysql_query($q);
$num = mysql_num_rows($result);
if ($num > 0) {
    $hide_ekg = false;
}

$hide_bloodpressure = false;
// source_notChibrainwave.php?type=brainwave
$q1 = "select * from records, users where users.uid = records.uid AND records.source = 'ReadingBehavior' AND users.email = '$selectuser'";
$result = mysql_query($q);
$num = mysql_num_rows($result);
if ($num > 0) {
    $hide_bloodpressure = false;
}

$hide_gaze = true;
// source_notChigaze.php?type=ReadingBehavior
$q = "select * from GazeRelation where GazeRelation.UserID = '$uid'";
$result = mysql_query($q);
$num = mysql_num_rows($result);
if ($num > 0) {
    $hide_gaze = false;
}

$hide_temp = true;
// source_notChi.php?type=temp
$q = "select * from records, users where users.uid = records.uid AND records.source = 'Temperature' AND records.type = 'temp' AND users.email = '$selectuser'";
$result = mysql_query($q);
$num = mysql_num_rows($result);
if ($num > 0) {
    $hide_temp = false;
}

$hide_fatigue = true;
// source_notChi.php?type=fatigue
$q = "select * from records, users where users.uid = records.uid AND records.source = 'SocialNetwork' AND records.type = 'fatigue' AND users.email = '$selectuser'";
$result = mysql_query($q);
$num = mysql_num_rows($result);
if ($num > 0) {
    $hide_fatigue = false;
}

$hide_heartbeat = true;
// source_notChi.php?type=pulse
$q = "select * from records, users where users.uid = records.uid AND records.source = 'BloodPressure' AND records.type = 'pulse' AND users.email = '$selectuser'";
$result = mysql_query($q);
$num = mysql_num_rows($result);
if ($num > 0) {
    $hide_heartbeat = false;
}

$hide_weakbreadth = true;
// source_notChi.php?type=weakBreadth
$q = "select * from records, users where users.uid = records.uid AND records.source = 'SocialNetwork' AND records.type = 'weakBreadth' AND users.email = '$selectuser'";
$result = mysql_query($q);
$num = mysql_num_rows($result);
if ($num > 0) {
    $hide_weakbreadth = false;
}

$hide_sweaty = true;
// source_notChi.php?type=sweaty
$q = "select * from records, users where users.uid = records.uid AND records.source = 'SocialNetwork' AND records.type = 'sweaty' AND users.email = '$selectuser'";
$result = mysql_query($q);
$num = mysql_num_rows($result);
if ($num > 0) {
    $hide_sweaty = false;
}

$hide_chitotal = true;
// source_notChi.php?type=chiTotal
$q = "select * from records, users where users.uid = records.uid AND records.source = 'SocialNetwork' AND records.type = 'chiTotal' AND users.email = '$selectuser'";
$result = mysql_query($q);
$num = mysql_num_rows($result);
if ($num > 0) {
    $hide_chitotal = false;
}

$hide_tongue = true;
// source_notChi.php?type=tongue
$q = "select * from records, users where users.uid = records.uid AND records.source = 'SocialNetwork' AND records.type = 'tongue' AND users.email = '$selectuser'";
$result = mysql_query($q);
$num = mysql_num_rows($result);
if ($num > 0) {
    $hide_tongue = false;
}


?>

<!-- Custom CSS -->
<link href="css/half-slider.css" rel="stylesheet">

<!-- Page Heading -->
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Dashboard <small><?php
                if (($_SESSION['selectuser'])) {
                    echo "Showing Data For Email: ";
                    echo $_SESSION['selectuser'];
                } else {
                    echo "Showing Data";
                }

                $flag = 0;

                ?></small>
        </h1>

        <p>
            <button onclick="myFunction()">Hide Text</button>
        </p>

        <div id="myDIV">
            <ol class="breadcrumb">
                <li class="active">
                    <i class="fa fa-dashboard"></i>
                    Statistics Overview

                    <form method="POST" action="dashboard1.php">
                        <p> sql: <input type="text" name="sql"></p>

                        <input type="submit" value="submit" name="submit">
                    </form>

                    <i class="fa fa-dashboard"></i>
                    <form method="POST" action="dashboard2.php">
                        <p> time slice <input type="text" name="time"></p>

                        <input type="submit" value="submit" name="submit">
                    </form>
                    <i class="fa fa-dashboard"></i>
                    <form method="POST" action="dashboard3.php">
                        <p> extract patterns <select name="patterns">
                                <option value="">Select...</option>
                                <option value="2">Increasing</option>
                                <option value="1">Decreasing</option>
                            </select>
                        </p>

                        <input type="submit" value="submit" name="submit">
                    </form>
                    <i class="fa fa-dashboard"></i>

                    <!--color change choice-->
                    <form method="POST" action="dashboard.php">
                        <p> select feature
                            <select name="feature_selection">
                                <option value="">Select...</option>
                                <option value="1">Brainwave</option>
                                <option value="2">Audio</option>
                                <option value="3">Image</option>
                                <option value="4">Wellbeing</option>
                                <option value="5">Steps</option>
                            </select>
                        </p>
                        <p> select color
                            <select name="color_selection">
                                <option value="">Select...</option>
                                <option value="1">Blue</option>
                                <option value="2">Green</option>
                                <option value="3">Yellow</option>
                                <option value="4">Red</option>
                                <option value="">Cancel</option>
                            </select>
                        </p>
                        <i class="fa fa-dashboard"></i>

                        <input type="submit" value="submit" name="submitBtn" id="submitBtn">
                    </form>

                    <i class="fa fa-dashboard"></i>
                    <form method="post">
                        <input type="submit" name="help" id="help" value="help"/>
                    </form>

                    <i class="fa fa-dashboard"></i>
                    <form method="POST" action="questionnaire.php">
                        <input type = "submit" name = "questionnaire" id = "questionnaire" value = "take questionnaire"/>
                    </form>
        </div>
        <script>
            function myFunction() {
                var x = document.getElementById("myDIV");
                if (x.style.display === "none") {
                    x.style.display = "block";
                } else {
                    x.style.display = "none";
                }
            }
        </script>


        <?php

        function testfun()
        {

            echo "<script>alert('help menu \\n ________________________________________________________\\n To run a query on past data insert a standard SQL query\\n To run BrainWaveHeadset wear headset and click on “BrainWave icon” \\n To set time slice parameter enter a number in seconds \\n To run VoiceEmoDetect click on “VoiceEmoDetect icon” \\n To run ImageEmoDetect click on “imageEmoDetect icon”')</script>";
        }

        if (array_key_exists('help', $_POST)) {
            testfun();
        }

        ?>


        </li>
        </ol>
    </div>
</div>

<!-- /.row -->
<header id="myCarousel-lg" class="carousel slide hidden-xs">
    <!-- Wrapper for Slides -->
    <div class="carousel-inner">

        <div class="item active">
            <!--Bloodpressure-->
            <div id="show_bloodpressure" class="col-xs-3" <?php if ($hide_bloodpressure) {
                echo 'style="display:none;"';
            } ?>>
                <?php

                $q = $_SESSION['latest'];
                $result = mysql_query($q);
                $rows = array();
                //calculate the average value
                $total = 0;
                while ($row = mysql_fetch_assoc($result)) {
                    //get the latest time
                    if ($total == 0) {
                        $temstamp = $row['datetime'];
                    }
                    $total += $row['value'];
                }
                $average = $total / mysql_num_rows($result);

                //decide the color
                if ($average <= 25) {
                    $color = "blue";
                } else if ($average <= 50 && $average > 25) {
                    $color = "green";
                } else if ($average <= 75 && $average > 50) {
                    $color = "yellow";
                } else {
                    $color = "red";
                }

                //fetch the color pattern
                $patterns = "SELECT * FROM patterns WHERE patterns.pattern_ID BETWEEN 101 AND 104";
                $result = mysql_query($patterns);
                $rows = array();

                //get the pattern_ID
                while ($row = mysql_fetch_assoc($result)) {
                    if (strcmp($color, $row['description']) == 0) {
                        $ID = $row['pattern_ID'];
                        break;
                    }
                }
                $EventGraph_ID = $_SESSION['user'];
                //fetch the previous nodeID
                $previous = "SELECT * FROM events WHERE events.EventGraph_ID = '$EventGraph_ID' order by node_ID desc limit 1";
                $result = mysql_query($previous);
                $rows = array();
                $row = mysql_fetch_assoc($result);
                if (is_null($row)) {
                    $previous_ID = NULL;
                    $node_ID = 1;
                } else {
                    $previous_ID = $row['node_ID'];
                    $node_ID = $previous_ID + 1;
                }
                //ready to insert into the events graph
                $node_value = $average;
                $pattern_ID = $ID;
                $strength = 0;
                $wellBing = $average;
                $sql = "INSERT INTO events (EventGraph_ID, node_ID, node_value, previous_nodeID, pattern_ID, strength, temstamp) VALUES ('$EventGraph_ID', '$node_ID', '$node_value', '$previous_ID', '$pattern_ID', '$strength', '$temstamp') ";
                mysql_query($sql);
                ?>

                <?php if ($_SESSION['user_select_color1-'.$uid] == "1") : ?>
                <div class="panel panel-primary">
                    <?php elseif ($_SESSION['user_select_color1-'.$uid] == "2") : ?>
                    <div class="panel panel-green">
                        <?php elseif ($_SESSION['user_select_color1-'.$uid] == "3") : ?>
                        <div class="panel panel-yellow">
                            <?php elseif ($_SESSION['user_select_color1-'.$uid] == "4") : ?>
                            <div class="panel panel-red">
                                <?php elseif ($wellBing <= 25) : ?>
                                <div class="panel panel-primary">
                                    <?php elseif ($wellBing > 25 && $wellBing <= 50) : ?>
                                    <div class="panel panel-green">
                                        <?php elseif ($wellBing > 50 && $wellBing <= 75) : ?>
                                        <div class="panel panel-yellow">
                                            <?php elseif ($wellBing > 75) : ?>
                                            <div class="panel panel-red">
                                                <?php endif; ?>
                                                <div class="panel-heading">
                                                    <div class="row">
                                                        <div class="col-xs-3">
                                                            <i class="fa fa-stethoscope fa-5x"></i>
                                                        </div>
                                                        <div class="col-xs-9 text-right">
                                                            <div class="huge">Brainwave</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <a href="source_notChibrainwave.php?type=brainwave">
                                                    <div class="panel-footer">
                                                        <span class="pull-left">View Details</span>
                                                        <span class="pull-right"><i
                                                                    class="fa fa-arrow-circle-right"></i></span>
                                                        <div class="clearfix"></div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                        <!-- test_audio -->
                                        <div id="show_audio" class="col-xs-3">
                                            <?php if ($_SESSION['user_select_color2-'.$uid] == "1") : ?>
                                            <div class="panel panel-primary">
                                                <?php elseif ($_SESSION['user_select_color2-'.$uid] == "2") : ?>
                                                <div class="panel panel-green">
                                                    <?php elseif ($_SESSION['user_select_color2-'.$uid] == "3") : ?>
                                                    <div class="panel panel-yellow">
                                                        <?php elseif ($_SESSION['user_select_color2-'.$uid] == "4") : ?>
                                                        <div class="panel panel-red">
                                                            <?php else : ?>
                                                            <div class="panel panel-primary">
                                                                <?php endif; ?>
                                                                <div class="panel-heading">
                                                                    <div class="row">
                                                                        <div class="col-xs-3">
                                                                            <i class="fa fa-eyedropper fa-5x"></i>
                                                                        </div>
                                                                        <div class="col-xs-9 text-right">
                                                                            <div class="huge">Audio</div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <a href="source_notChi.php?type=audio">
                                                                    <div class="panel-footer">
                                                                        <span class="pull-left">View Details</span>
                                                                        <span class="pull-right"><i
                                                                                    class="fa fa-arrow-circle-right"></i></span>
                                                                        <div class="clearfix"></div>
                                                                    </div>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <!--test_image-->
                                                        <div id="show_image" class="col-xs-3">
                                                            <?php if ($_SESSION['user_select_color3-'.$uid] == "1") : ?>
                                                            <div class="panel panel-primary">
                                                                <?php elseif ($_SESSION['user_select_color3-'.$uid] == "2") : ?>
                                                                <div class="panel panel-green">
                                                                    <?php elseif ($_SESSION['user_select_color3-'.$uid] == "3") : ?>
                                                                    <div class="panel panel-yellow">
                                                                        <?php elseif ($_SESSION['user_select_color3-'.$uid] == "4") : ?>
                                                                        <div class="panel panel-red">
                                                                            <?php else : ?>
                                                                            <div class="panel panel-primary">
                                                                                <?php endif; ?>
                                                                                <div class="panel-heading">
                                                                                    <div class="row">
                                                                                        <div class="col-xs-3">
                                                                                            <i class="fa fa-heartbeat fa-5x"></i>
                                                                                        </div>
                                                                                        <div class="col-xs-9 text-right">
                                                                                            <div class="huge">
                                                                                                Image
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <a href="source_notChi.php?type=image">
                                                                                    <div class="panel-footer">
                                                                                        <span class="pull-left">View Details</span>
                                                                                        <span class="pull-right"><i
                                                                                                    class="fa fa-arrow-circle-right"></i></span>
                                                                                        <div class="clearfix"></div>
                                                                                    </div>
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                        <!--test_wellbeing-->
                                                                        <div id="show_wellbeing"
                                                                             class="col-xs-3">
                                                                            <?php if ($_SESSION['user_select_color4-'.$uid] == "1") : ?>
                                                                            <div class="panel panel-primary">
                                                                                <?php elseif ($_SESSION['user_select_color4-'.$uid] == "2") : ?>
                                                                                <div class="panel panel-green">
                                                                                    <?php elseif ($_SESSION['user_select_color4-'.$uid] == "3") : ?>
                                                                                    <div class="panel panel-yellow">
                                                                                        <?php elseif ($_SESSION['user_select_color4-'.$uid] == "4") : ?>
                                                                                        <div class="panel panel-red">
                                                                                            <?php else : ?>
                                                                                            <div class="panel panel-primary">
                                                                                                <?php endif; ?>
                                                                                                <div class="panel-heading">
                                                                                                    <div class="row">
                                                                                                        <div class="col-xs-3">
                                                                                                            <i class="fa fa-medkit fa-5x"></i>
                                                                                                        </div>
                                                                                                        <div class="col-xs-9 text-right">
                                                                                                            <div class="huge">
                                                                                                                Wellbeing
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <a href="dashboard.php">
                                                                                                    <div class="panel-footer">
                                                                                                        <span class="pull-left">          </span>
                                                                                                        <span class="pull-right"><i
                                                                                                                    class="fa fa-arrow-circle-right"></i></span>
                                                                                                        <div class="clearfix"></div>
                                                                                                    </div>
                                                                                                </a>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>

                                                                                    <!--  add steps into new item-->
                                                                                    <div class="item">
                                                                                        <!--test_steps-->
                                                                                        <div id="show_steps" class="col-xs-3">
                                                                                            <?php if ($_SESSION['user_select_color5-'.$uid] == "1") : ?>
                                                                                            <div class="panel panel-primary">
                                                                                                <?php elseif ($_SESSION['user_select_color5-'.$uid] == "2") : ?>
                                                                                                <div class="panel panel-green">
                                                                                                    <?php elseif ($_SESSION['user_select_color5-'.$uid] == "3") : ?>
                                                                                                    <div class="panel panel-yellow">
                                                                                                        <?php elseif ($_SESSION['user_select_color5-'.$uid] == "4") : ?>
                                                                                                        <div class="panel panel-red">
                                                                                                            <?php else : ?>
                                                                                                            <div class="panel panel-primary">
                                                                                                                <?php endif; ?>
                                                                                                                <div class="panel-heading">
                                                                                                                    <div class="row">
                                                                                                                        <div class="col-xs-3">
                                                                                                                            <i class="fa fa-medkit fa-5x"></i>
                                                                                                                        </div>
                                                                                                                        <div class="col-xs-9 text-right">
                                                                                                                            <div class="huge">
                                                                                                                                Steps
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                                <a href="source_notChi.php?type=steps">
                                                                                                                    <div class="panel-footer">
                                                                                                                        <span class="pull-left">View Details</span>
                                                                                                                        <span class="pull-right"><i
                                                                                                                                    class="fa fa-arrow-circle-right"></i></span>
                                                                                                                        <div class="clearfix"></div>
                                                                                                                    </div>
                                                                                                                </a>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>


                                                                                                    <!-- <div class="item active"> -->
                                                                                                    <div class="item">
                                                                                                        <!-- air temperature -->
                                                                                                        <div id="show_air_temperature"
                                                                                                             class="col-xs-3" <?php if ($hide_air_temperature) {
                                                                                                            echo 'style="display:none;"';
                                                                                                        } ?>>
                                                                                                            <?php if ($_SESSION['Chi_color'] == 'B') : ?>
                                                                                                            <div class="panel panel-primary">
                                                                                                                <?php elseif ($_SESSION['Chi_color'] == 'Y') : ?>
                                                                                                                <div class="panel panel-yellow">
                                                                                                                    <?php elseif ($_SESSION['Chi_color'] == 'R') : ?>
                                                                                                                    <div class="panel panel-red">
                                                                                                                        <?php else : ?>
                                                                                                                        <div class="panel panel-primary">
                                                                                                                            <?php endif; ?>
                                                                                                                            <div class="panel-heading">
                                                                                                                                <div class="row">
                                                                                                                                    <div class="col-xs-3">
                                                                                                                                        <i class="fa fa-eyedropper fa-5x"></i>
                                                                                                                                    </div>
                                                                                                                                    <div class="col-xs-9 text-right">
                                                                                                                                        <div class="huge">
                                                                                                                                            Chi
                                                                                                                                            氣
                                                                                                                                        </div>
                                                                                                                                    </div>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                            <a href="source.php?type=ChiMonitor">
                                                                                                                                <div class="panel-footer">
                                                                                                                                    <span class="pull-left">View Details</span>
                                                                                                                                    <span class="pull-right"><i
                                                                                                                                                class="fa fa-arrow-circle-right"></i></span>
                                                                                                                                    <div class="clearfix"></div>
                                                                                                                                </div>
                                                                                                                            </a>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                    <!-- fertilizer -->
                                                                                                                    <div id="show_fertilizer"
                                                                                                                         class="col-xs-3" <?php if ($hide_fertilizer) {
                                                                                                                        echo 'style="display:none;"';
                                                                                                                    } ?>>
                                                                                                                        <?php if ($_SESSION['Tian_color'] == 'B') : ?>
                                                                                                                        <div class="panel panel-primary">
                                                                                                                            <?php elseif ($_SESSION['Tian_color'] == 'Y') : ?>
                                                                                                                            <div class="panel panel-yellow">
                                                                                                                                <?php elseif ($_SESSION['Tian_color'] == 'R') : ?>
                                                                                                                                <div class="panel panel-red">
                                                                                                                                    <?php else : ?>
                                                                                                                                    <div class="panel panel-primary">
                                                                                                                                        <?php endif; ?>
                                                                                                                                        <div class="panel-heading">
                                                                                                                                            <div class="row">
                                                                                                                                                <div class="col-xs-3">
                                                                                                                                                    <i class="fa fa-eyedropper fa-5x"></i>
                                                                                                                                                </div>
                                                                                                                                                <div class="col-xs-9 text-right">
                                                                                                                                                    <div class="huge">
                                                                                                                                                        Tian
                                                                                                                                                        天
                                                                                                                                                    </div>
                                                                                                                                                </div>
                                                                                                                                            </div>
                                                                                                                                        </div>
                                                                                                                                        <a href="source_notChi.php?type=Tian">
                                                                                                                                            <div class="panel-footer">
                                                                                                                                                <span class="pull-left">View Details</span>
                                                                                                                                                <span class="pull-right"><i
                                                                                                                                                            class="fa fa-arrow-circle-right"></i></span>
                                                                                                                                                <div class="clearfix"></div>
                                                                                                                                            </div>
                                                                                                                                        </a>
                                                                                                                                    </div>
                                                                                                                                </div>
                                                                                                                                <!--light-->
                                                                                                                                <div id="show_light"
                                                                                                                                     class="col-xs-3" <?php if ($hide_light) {
                                                                                                                                    echo 'style="display:none;"';
                                                                                                                                } ?>>
                                                                                                                                    <?php if ($_SESSION['Di_color'] == 'B') : ?>
                                                                                                                                    <div class="panel panel-primary">
                                                                                                                                        <?php elseif ($_SESSION['Di_color'] == 'Y') : ?>
                                                                                                                                        <div class="panel panel-yellow">
                                                                                                                                            <?php elseif ($_SESSION['Di_color'] == 'R') : ?>
                                                                                                                                            <div class="panel panel-red">
                                                                                                                                                <?php else : ?>
                                                                                                                                                <div class="panel panel-primary">
                                                                                                                                                    <?php endif; ?>
                                                                                                                                                    <div class="panel-heading">
                                                                                                                                                        <div class="row">
                                                                                                                                                            <div class="col-xs-3">
                                                                                                                                                                <i class="fa fa-heartbeat fa-5x"></i>
                                                                                                                                                            </div>
                                                                                                                                                            <div class="col-xs-9 text-right">
                                                                                                                                                                <div class="huge">
                                                                                                                                                                    Di
                                                                                                                                                                    地
                                                                                                                                                                </div>
                                                                                                                                                            </div>
                                                                                                                                                        </div>
                                                                                                                                                    </div>
                                                                                                                                                    <a href="source_notChi.php?type=Di">
                                                                                                                                                        <div class="panel-footer">
                                                                                                                                                            <span class="pull-left">View Details</span>
                                                                                                                                                            <span class="pull-right"><i
                                                                                                                                                                        class="fa fa-arrow-circle-right"></i></span>
                                                                                                                                                            <div class="clearfix"></div>
                                                                                                                                                        </div>
                                                                                                                                                    </a>
                                                                                                                                                </div>
                                                                                                                                            </div>
                                                                                                                                            <!--moisture-->
                                                                                                                                            <div id="show_moisture"
                                                                                                                                                 class="col-xs-3" <?php if ($hide_moisture) {
                                                                                                                                                echo 'style="display:none;"';
                                                                                                                                            } ?>>
                                                                                                                                                <?php if ($_SESSION['Ren_color'] == 'B') : ?>
                                                                                                                                                <div class="panel panel-primary">
                                                                                                                                                    <?php elseif ($_SESSION['Ren_color'] == 'Y') : ?>
                                                                                                                                                    <div class="panel panel-yellow">
                                                                                                                                                        <?php elseif ($_SESSION['Ren_color'] == 'R') : ?>
                                                                                                                                                        <div class="panel panel-red">
                                                                                                                                                            <?php else : ?>
                                                                                                                                                            <div class="panel panel-primary">
                                                                                                                                                                <?php endif; ?>
                                                                                                                                                                <div class="panel-heading">
                                                                                                                                                                    <div class="row">
                                                                                                                                                                        <div class="col-xs-3">
                                                                                                                                                                            <i class="fa fa-medkit fa-5x"></i>
                                                                                                                                                                        </div>
                                                                                                                                                                        <div class="col-xs-9 text-right">
                                                                                                                                                                            <div class="huge">
                                                                                                                                                                                Ren
                                                                                                                                                                                人
                                                                                                                                                                            </div>
                                                                                                                                                                        </div>
                                                                                                                                                                    </div>
                                                                                                                                                                </div>
                                                                                                                                                                <a href="source_notChi.php?type=Ren">
                                                                                                                                                                    <div class="panel-footer">
                                                                                                                                                                        <span class="pull-left">View Details</span>
                                                                                                                                                                        <span class="pull-right"><i
                                                                                                                                                                                    class="fa fa-arrow-circle-right"></i></span>
                                                                                                                                                                        <div class="clearfix"></div>
                                                                                                                                                                    </div>
                                                                                                                                                                </a>
                                                                                                                                                            </div>
                                                                                                                                                        </div>
                                                                                                                                                    </div>


                                                                                                                                                    <div class="item">
                                                                                                                                                        <!-- air temperature -->
                                                                                                                                                        <div id="show_air_temperature2"
                                                                                                                                                             class="col-xs-3" <?php if ($hide_air_temperature2) {
                                                                                                                                                            echo 'style="display:none;"';
                                                                                                                                                        } ?>>
                                                                                                                                                            <div class="panel panel-primary">
                                                                                                                                                                <div class="panel-heading">
                                                                                                                                                                    <div class="row">
                                                                                                                                                                        <div class="col-xs-3">
                                                                                                                                                                            <i class="fa fa-eyedropper fa-5x"></i>
                                                                                                                                                                        </div>
                                                                                                                                                                        <div class="col-xs-9 text-right">
                                                                                                                                                                            <div class="huge">
                                                                                                                                                                                AIR
                                                                                                                                                                                TEMP
                                                                                                                                                                            </div>
                                                                                                                                                                        </div>
                                                                                                                                                                    </div>
                                                                                                                                                                </div>
                                                                                                                                                                <a href="source_notChi.php?type=temp">
                                                                                                                                                                    <div class="panel-footer">
                                                                                                                                                                        <span class="pull-left">View Details</span>
                                                                                                                                                                        <span class="pull-right"><i
                                                                                                                                                                                    class="fa fa-arrow-circle-right"></i></span>
                                                                                                                                                                        <div class="clearfix"></div>
                                                                                                                                                                    </div>
                                                                                                                                                                </a>
                                                                                                                                                            </div>
                                                                                                                                                        </div>
                                                                                                                                                        <!-- fertilizer -->
                                                                                                                                                        <div id="show_fertilizer2"
                                                                                                                                                             class="col-xs-3" <?php if ($hide_fertilizer2) {
                                                                                                                                                            echo 'style="display:none;"';
                                                                                                                                                        } ?>>
                                                                                                                                                            <div class="panel panel-primary">
                                                                                                                                                                <div class="panel-heading">
                                                                                                                                                                    <div class="row">
                                                                                                                                                                        <div class="col-xs-3">
                                                                                                                                                                            <i class="fa fa-eyedropper fa-5x"></i>
                                                                                                                                                                        </div>
                                                                                                                                                                        <div class="col-xs-9 text-right">
                                                                                                                                                                            <div class="huge">
                                                                                                                                                                                FERTILIZER
                                                                                                                                                                            </div>
                                                                                                                                                                        </div>
                                                                                                                                                                    </div>
                                                                                                                                                                </div>
                                                                                                                                                                <a href="source_notChi.php?type=fertilizer">
                                                                                                                                                                    <div class="panel-footer">
                                                                                                                                                                        <span class="pull-left">View Details</span>
                                                                                                                                                                        <span class="pull-right"><i
                                                                                                                                                                                    class="fa fa-arrow-circle-right"></i></span>
                                                                                                                                                                        <div class="clearfix"></div>
                                                                                                                                                                    </div>
                                                                                                                                                                </a>
                                                                                                                                                            </div>
                                                                                                                                                        </div>
                                                                                                                                                        <!--light-->
                                                                                                                                                        <div id="show_light2"
                                                                                                                                                             class="col-xs-3" <?php if ($hide_light2) {
                                                                                                                                                            echo 'style="display:none;"';
                                                                                                                                                        } ?>>
                                                                                                                                                            <div class="panel panel-green">
                                                                                                                                                                <div class="panel-heading">
                                                                                                                                                                    <div class="row">
                                                                                                                                                                        <div class="col-xs-3">
                                                                                                                                                                            <i class="fa fa-heartbeat fa-5x"></i>
                                                                                                                                                                        </div>
                                                                                                                                                                        <div class="col-xs-9 text-right">
                                                                                                                                                                            <div class="huge">
                                                                                                                                                                                LIGHT
                                                                                                                                                                            </div>
                                                                                                                                                                        </div>
                                                                                                                                                                    </div>
                                                                                                                                                                </div>
                                                                                                                                                                <a href="source_notChi.php?type=light">
                                                                                                                                                                    <div class="panel-footer">
                                                                                                                                                                        <span class="pull-left">View Details</span>
                                                                                                                                                                        <span class="pull-right"><i
                                                                                                                                                                                    class="fa fa-arrow-circle-right"></i></span>
                                                                                                                                                                        <div class="clearfix"></div>
                                                                                                                                                                    </div>
                                                                                                                                                                </a>
                                                                                                                                                            </div>
                                                                                                                                                        </div>
                                                                                                                                                        <!--moisture-->
                                                                                                                                                        <div id="show_moisture"
                                                                                                                                                             class="col-xs-3" <?php if ($hide_moisture) {
                                                                                                                                                            echo 'style="display:none;"';
                                                                                                                                                        } ?>>
                                                                                                                                                            <div class="panel panel-yellow">
                                                                                                                                                                <div class="panel-heading">
                                                                                                                                                                    <div class="row">
                                                                                                                                                                        <div class="col-xs-3">
                                                                                                                                                                            <i class="fa fa-medkit fa-5x"></i>
                                                                                                                                                                        </div>
                                                                                                                                                                        <div class="col-xs-9 text-right">
                                                                                                                                                                            <div class="huge">
                                                                                                                                                                                MOISTURE
                                                                                                                                                                            </div>
                                                                                                                                                                        </div>
                                                                                                                                                                    </div>
                                                                                                                                                                </div>
                                                                                                                                                                <a href="source_notChi.php?type=moisture">
                                                                                                                                                                    <div class="panel-footer">
                                                                                                                                                                        <span class="pull-left">View Details</span>
                                                                                                                                                                        <span class="pull-right"><i
                                                                                                                                                                                    class="fa fa-arrow-circle-right"></i></span>
                                                                                                                                                                        <div class="clearfix"></div>
                                                                                                                                                                    </div>
                                                                                                                                                                </a>
                                                                                                                                                            </div>
                                                                                                                                                        </div>
                                                                                                                                                    </div>

                                                                                                                                                    <div class="item">
                                                                                                                                                        <!--spo2-->
                                                                                                                                                        <div id="show_sop2"
                                                                                                                                                             class="col-xs-3" <?php if ($hide_sop2) {
                                                                                                                                                            echo 'style="display:none;"';
                                                                                                                                                        } ?>>
                                                                                                                                                            <div class="panel panel-primary">
                                                                                                                                                                <div class="panel-heading">
                                                                                                                                                                    <div class="row">
                                                                                                                                                                        <div class="col-xs-3">
                                                                                                                                                                            <i class="fa fa-eyedropper fa-5x"></i>
                                                                                                                                                                        </div>
                                                                                                                                                                        <div class="col-xs-9 text-right">
                                                                                                                                                                            <div class="huge">
                                                                                                                                                                                SPO2
                                                                                                                                                                            </div>
                                                                                                                                                                        </div>
                                                                                                                                                                    </div>
                                                                                                                                                                </div>
                                                                                                                                                                <a href="source_notChi.php?type=spo2">
                                                                                                                                                                    <div class="panel-footer">
                                                                                                                                                                        <span class="pull-left">View Details</span>
                                                                                                                                                                        <span class="pull-right"><i
                                                                                                                                                                                    class="fa fa-arrow-circle-right"></i></span>
                                                                                                                                                                        <div class="clearfix"></div>
                                                                                                                                                                    </div>
                                                                                                                                                                </a>
                                                                                                                                                            </div>
                                                                                                                                                        </div>
                                                                                                                                                        <!--systolic-->
                                                                                                                                                        <div id="show_systolic"
                                                                                                                                                             class="col-xs-3" <?php if ($hide_systolic) {
                                                                                                                                                            echo 'style="display:none;"';
                                                                                                                                                        } ?>>
                                                                                                                                                            <div class="panel panel-red">
                                                                                                                                                                <div class="panel-heading">
                                                                                                                                                                    <div class="row">
                                                                                                                                                                        <div class="col-xs-3">
                                                                                                                                                                            <i class="fa fa-stethoscope fa-5x"></i>
                                                                                                                                                                        </div>
                                                                                                                                                                        <div class="col-xs-9 text-right">
                                                                                                                                                                            <div class="huge">
                                                                                                                                                                                SYSTOLIC
                                                                                                                                                                            </div>
                                                                                                                                                                        </div>
                                                                                                                                                                    </div>
                                                                                                                                                                </div>
                                                                                                                                                                <a href="source_notChi.php?type=systolic">
                                                                                                                                                                    <div class="panel-footer">
                                                                                                                                                                        <span class="pull-left">View Details</span>
                                                                                                                                                                        <span class="pull-right"><i
                                                                                                                                                                                    class="fa fa-arrow-circle-right"></i></span>
                                                                                                                                                                        <div class="clearfix"></div>
                                                                                                                                                                    </div>
                                                                                                                                                                </a>
                                                                                                                                                            </div>
                                                                                                                                                        </div>
                                                                                                                                                        <!--diastolic-->
                                                                                                                                                        <div id="show_diastolic"
                                                                                                                                                             class="col-xs-3" <?php if ($hide_diastolic) {
                                                                                                                                                            echo 'style="display:none;"';
                                                                                                                                                        } ?>>
                                                                                                                                                            <div class="panel panel-red">
                                                                                                                                                                <div class="panel-heading">
                                                                                                                                                                    <div class="row">
                                                                                                                                                                        <div class="col-xs-3">
                                                                                                                                                                            <i class="fa fa-stethoscope fa-5x"></i>
                                                                                                                                                                        </div>
                                                                                                                                                                        <div class="col-xs-9 text-right">
                                                                                                                                                                            <div class="huge">
                                                                                                                                                                                DIASTOLIC
                                                                                                                                                                            </div>
                                                                                                                                                                        </div>
                                                                                                                                                                    </div>
                                                                                                                                                                </div>
                                                                                                                                                                <a href="source_notChi.php?type=diastolic">
                                                                                                                                                                    <div class="panel-footer">
                                                                                                                                                                        <span class="pull-left">View Details</span>
                                                                                                                                                                        <span class="pull-right"><i
                                                                                                                                                                                    class="fa fa-arrow-circle-right"></i></span>
                                                                                                                                                                        <div class="clearfix"></div>
                                                                                                                                                                    </div>
                                                                                                                                                                </a>
                                                                                                                                                            </div>
                                                                                                                                                        </div>
                                                                                                                                                        <!--pulse-->
                                                                                                                                                        <div id="show_pulse"
                                                                                                                                                             class="col-xs-3" <?php if ($hide_pulse) {
                                                                                                                                                            echo 'style="display:none;"';
                                                                                                                                                        } ?>>
                                                                                                                                                            <div class="panel panel-red">
                                                                                                                                                                <div class="panel-heading">
                                                                                                                                                                    <div class="row">
                                                                                                                                                                        <div class="col-xs-3">
                                                                                                                                                                            <i class="fa fa-stethoscope fa-5x"></i>
                                                                                                                                                                        </div>
                                                                                                                                                                        <div class="col-xs-9 text-right">
                                                                                                                                                                            <div class="huge">
                                                                                                                                                                                PULSE
                                                                                                                                                                            </div>
                                                                                                                                                                        </div>
                                                                                                                                                                    </div>
                                                                                                                                                                </div>
                                                                                                                                                                <a href="source_notChi.php?type=pulse">
                                                                                                                                                                    <div class="panel-footer">
                                                                                                                                                                        <span class="pull-left">View Details</span>
                                                                                                                                                                        <span class="pull-right"><i
                                                                                                                                                                                    class="fa fa-arrow-circle-right"></i></span>
                                                                                                                                                                        <div class="clearfix"></div>
                                                                                                                                                                    </div>
                                                                                                                                                                </a>
                                                                                                                                                            </div>
                                                                                                                                                        </div>
                                                                                                                                                    </div>

                                                                                                                                                    <div class="item">
                                                                                                                                                        <!--ekg-->
                                                                                                                                                        <div id="show_ekg"
                                                                                                                                                             class="col-xs-3" <?php if ($hide_ekg) {
                                                                                                                                                            echo 'style="display:none;"';
                                                                                                                                                        } ?>>
                                                                                                                                                            <div class="panel panel-red">
                                                                                                                                                                <div class="panel-heading">
                                                                                                                                                                    <div class="row">
                                                                                                                                                                        <div class="col-xs-3">
                                                                                                                                                                            <i class="fa fa-stethoscope fa-5x"></i>
                                                                                                                                                                        </div>
                                                                                                                                                                        <div class="col-xs-9 text-right">
                                                                                                                                                                            <div class="huge">
                                                                                                                                                                                EKG
                                                                                                                                                                            </div>
                                                                                                                                                                        </div>
                                                                                                                                                                    </div>
                                                                                                                                                                </div>
                                                                                                                                                                <a href="source_notChi.php?type=EKG">
                                                                                                                                                                    <div class="panel-footer">
                                                                                                                                                                        <span class="pull-left">View Details</span>
                                                                                                                                                                        <span class="pull-right"><i
                                                                                                                                                                                    class="fa fa-arrow-circle-right"></i></span>
                                                                                                                                                                        <div class="clearfix"></div>
                                                                                                                                                                    </div>
                                                                                                                                                                </a>
                                                                                                                                                            </div>
                                                                                                                                                        </div>

                                                                                                                                                        <!--Bloodpressure moved to brainwave-->

                                                                                                                                                        <!--gaze-->
                                                                                                                                                        <div id="show_gaze"
                                                                                                                                                             class="col-xs-3" <?php if ($hide_gaze) {
                                                                                                                                                            echo 'style="display:none;"';
                                                                                                                                                        } ?>>
                                                                                                                                                            <div class="panel panel-red">
                                                                                                                                                                <div class="panel-heading">
                                                                                                                                                                    <div class="row">
                                                                                                                                                                        <div class="col-xs-3">
                                                                                                                                                                            <i class="fa fa-stethoscope fa-5x"></i>
                                                                                                                                                                        </div>
                                                                                                                                                                        <div class="col-xs-9 text-right">
                                                                                                                                                                            <div class="huge">
                                                                                                                                                                                Gaze
                                                                                                                                                                            </div>
                                                                                                                                                                        </div>
                                                                                                                                                                    </div>
                                                                                                                                                                </div>
                                                                                                                                                                <a href="source_notChigaze.php?type=ReadingBehavior">
                                                                                                                                                                    <div class="panel-footer">
                                                                                                                                                                        <span class="pull-left">View Details</span>
                                                                                                                                                                        <span class="pull-right"><i
                                                                                                                                                                                    class="fa fa-arrow-circle-right"></i></span>
                                                                                                                                                                        <div class="clearfix"></div>
                                                                                                                                                                    </div>
                                                                                                                                                                </a>
                                                                                                                                                            </div>
                                                                                                                                                        </div>
                                                                                                                                                        <!--temp-->
                                                                                                                                                        <div id="show_temp"
                                                                                                                                                             class="col-xs-3" <?php if ($hide_temp) {
                                                                                                                                                            echo 'style="display:none;"';
                                                                                                                                                        } ?>>
                                                                                                                                                            <div class="panel panel-green">
                                                                                                                                                                <div class="panel-heading">
                                                                                                                                                                    <div class="row">
                                                                                                                                                                        <div class="col-xs-3">
                                                                                                                                                                            <i class="fa fa-heartbeat fa-5x"></i>
                                                                                                                                                                        </div>
                                                                                                                                                                        <div class="col-xs-9 text-right">
                                                                                                                                                                            <div class="huge">
                                                                                                                                                                                TEMP
                                                                                                                                                                            </div>
                                                                                                                                                                        </div>
                                                                                                                                                                    </div>
                                                                                                                                                                </div>
                                                                                                                                                                <a href="source_notChi.php?type=temp">
                                                                                                                                                                    <div class="panel-footer">
                                                                                                                                                                        <span class="pull-left">View Details</span>
                                                                                                                                                                        <span class="pull-right"><i
                                                                                                                                                                                    class="fa fa-arrow-circle-right"></i></span>
                                                                                                                                                                        <div class="clearfix"></div>
                                                                                                                                                                    </div>
                                                                                                                                                                </a>
                                                                                                                                                            </div>
                                                                                                                                                        </div>
                                                                                                                                                        <!--1-->
                                                                                                                                                        <!--1-->
                                                                                                                                                        <!--1-->
                                                                                                                                                        <!--1-->
                                                                                                                                                        <!--1-->
                                                                                                                                                        <!--1-->

                                                                                                                                                    </div>

                                                                                                                                                    <div class="item">
                                                                                                                                                        <div id="show_fatigue"
                                                                                                                                                             class="col-xs-3" <?php if ($hide_fatigue) {
                                                                                                                                                            echo 'style="display:none;"';
                                                                                                                                                        } ?>>
                                                                                                                                                            <div class="panel panel-green">
                                                                                                                                                                <div class="panel-heading fatigue"
                                                                                                                                                                     id="fatigue">
                                                                                                                                                                    <div class="row">
                                                                                                                                                                        <div class="col-xs-3">
                                                                                                                                                                            <i class="fa fa-heartbeat fa-5x"></i>
                                                                                                                                                                        </div>
                                                                                                                                                                        <div class="col-xs-9 text-right">
                                                                                                                                                                            <div class="huge">
                                                                                                                                                                                fatigue
                                                                                                                                                                            </div>
                                                                                                                                                                        </div>
                                                                                                                                                                    </div>
                                                                                                                                                                </div>
                                                                                                                                                                <a href="source_notChi.php?type=fatigue">
                                                                                                                                                                    <div class="panel-footer">
                                                                                                                                                                        <span class="pull-left">View Details</span>
                                                                                                                                                                        <span class="pull-right"><i
                                                                                                                                                                                    class="fa fa-arrow-circle-right"></i></span>
                                                                                                                                                                        <div class="clearfix"></div>
                                                                                                                                                                    </div>
                                                                                                                                                                </a>
                                                                                                                                                            </div>
                                                                                                                                                        </div>

                                                                                                                                                        <div id="show_heartbeat"
                                                                                                                                                             class="col-xs-3" <?php if ($hide_heartbeat) {
                                                                                                                                                            echo 'style="display:none;"';
                                                                                                                                                        } ?>>
                                                                                                                                                            <div class="panel panel-green">
                                                                                                                                                                <div class="panel-heading">
                                                                                                                                                                    <div class="row">
                                                                                                                                                                        <div class="col-xs-3">
                                                                                                                                                                            <i class="fa fa-heartbeat fa-5x"></i>
                                                                                                                                                                        </div>
                                                                                                                                                                        <div class="col-xs-9 text-right">
                                                                                                                                                                            <div class="huge">
                                                                                                                                                                                pulse
                                                                                                                                                                            </div>
                                                                                                                                                                        </div>
                                                                                                                                                                    </div>
                                                                                                                                                                </div>
                                                                                                                                                                <a href="source_notChi.php?type=pulse">
                                                                                                                                                                    <div class="panel-footer">
                                                                                                                                                                        <span class="pull-left">View Details</span>
                                                                                                                                                                        <span class="pull-right"><i
                                                                                                                                                                                    class="fa fa-arrow-circle-right"></i></span>
                                                                                                                                                                        <div class="clearfix"></div>
                                                                                                                                                                    </div>
                                                                                                                                                                </a>
                                                                                                                                                            </div>
                                                                                                                                                        </div>

                                                                                                                                                        <div id="show_weakbreadth"
                                                                                                                                                             class="col-xs-3" <?php if ($hide_weakbreadth) {
                                                                                                                                                            echo 'style="display:none;"';
                                                                                                                                                        } ?>>
                                                                                                                                                            <div class="panel panel-green">
                                                                                                                                                                <div class="panel-heading weakBreadth"
                                                                                                                                                                     id="weakbreadth">
                                                                                                                                                                    <div class="row">
                                                                                                                                                                        <div class="col-xs-3">
                                                                                                                                                                            <i class="fa fa-heartbeat fa-5x"></i>
                                                                                                                                                                        </div>
                                                                                                                                                                        <div class="col-xs-9 text-right">
                                                                                                                                                                            <div class="huge">
                                                                                                                                                                                weakBreadth
                                                                                                                                                                            </div>
                                                                                                                                                                        </div>
                                                                                                                                                                    </div>
                                                                                                                                                                </div>
                                                                                                                                                                <a href="source_notChi.php?type=weakBreadth">
                                                                                                                                                                    <div class="panel-footer">
                                                                                                                                                                        <span class="pull-left">View Details</span>
                                                                                                                                                                        <span class="pull-right"><i
                                                                                                                                                                                    class="fa fa-arrow-circle-right"></i></span>
                                                                                                                                                                        <div class="clearfix"></div>
                                                                                                                                                                    </div>
                                                                                                                                                                </a>
                                                                                                                                                            </div>
                                                                                                                                                        </div>

                                                                                                                                                        <div id="show_sweaty"
                                                                                                                                                             class="col-xs-3" <?php if ($hide_sweaty) {
                                                                                                                                                            echo 'style="display:none;"';
                                                                                                                                                        } ?>>
                                                                                                                                                            <div class="panel panel-green">
                                                                                                                                                                <div class="panel-heading sweaty"
                                                                                                                                                                     id="sweaty">
                                                                                                                                                                    <div class="row">
                                                                                                                                                                        <div class="col-xs-3">
                                                                                                                                                                            <i class="fa fa-heartbeat fa-5x"></i>
                                                                                                                                                                        </div>
                                                                                                                                                                        <div class="col-xs-9 text-right">
                                                                                                                                                                            <div class="huge">
                                                                                                                                                                                sweaty
                                                                                                                                                                            </div>
                                                                                                                                                                        </div>
                                                                                                                                                                    </div>
                                                                                                                                                                </div>
                                                                                                                                                                <a href="source_notChi.php?type=sweaty">
                                                                                                                                                                    <div class="panel-footer">
                                                                                                                                                                        <span class="pull-left">View Details</span>
                                                                                                                                                                        <span class="pull-right"><i
                                                                                                                                                                                    class="fa fa-arrow-circle-right"></i></span>
                                                                                                                                                                        <div class="clearfix"></div>
                                                                                                                                                                    </div>
                                                                                                                                                                </a>
                                                                                                                                                            </div>
                                                                                                                                                        </div>
                                                                                                                                                    </div>

                                                                                                                                                    <div class="item">
                                                                                                                                                        <div id="show_chitotal"
                                                                                                                                                             class="col-xs-3" <?php if ($hide_chitotal) {
                                                                                                                                                            echo 'style="display:none;"';
                                                                                                                                                        } ?>>
                                                                                                                                                            <div class="panel panel-green">
                                                                                                                                                                <div class="panel-heading chiTotal"
                                                                                                                                                                     id="chitotal">
                                                                                                                                                                    <div class="row">
                                                                                                                                                                        <div class="col-xs-3">
                                                                                                                                                                            <i class="fa fa-heartbeat fa-5x"></i>
                                                                                                                                                                        </div>
                                                                                                                                                                        <div class="col-xs-9 text-right">
                                                                                                                                                                            <div class="huge">
                                                                                                                                                                                chiTotal
                                                                                                                                                                            </div>
                                                                                                                                                                        </div>
                                                                                                                                                                    </div>
                                                                                                                                                                </div>
                                                                                                                                                                <a href="source_notChi.php?type=chiTotal">
                                                                                                                                                                    <div class="panel-footer">
                                                                                                                                                                        <span class="pull-left">View Details</span>
                                                                                                                                                                        <span class="pull-right"><i
                                                                                                                                                                                    class="fa fa-arrow-circle-right"></i></span>
                                                                                                                                                                        <div class="clearfix"></div>
                                                                                                                                                                    </div>
                                                                                                                                                                </a>
                                                                                                                                                            </div>
                                                                                                                                                        </div>

                                                                                                                                                        <div id="show_tongue"
                                                                                                                                                             class="col-xs-3" <?php if ($hide_tongue) {
                                                                                                                                                            echo 'style="display:none;"';
                                                                                                                                                        } ?>>
                                                                                                                                                            <div class="panel panel-green">
                                                                                                                                                                <div class="panel-heading tongue"
                                                                                                                                                                     id="tongue">
                                                                                                                                                                    <div class="row">
                                                                                                                                                                        <div class="col-xs-3">
                                                                                                                                                                            <i class="fa fa-heartbeat fa-5x"></i>
                                                                                                                                                                        </div>
                                                                                                                                                                        <div class="col-xs-9 text-right">
                                                                                                                                                                            <div class="huge">
                                                                                                                                                                                tongue
                                                                                                                                                                            </div>
                                                                                                                                                                        </div>
                                                                                                                                                                    </div>
                                                                                                                                                                </div>
                                                                                                                                                                <a href="source_notChi.php?type=tongue">
                                                                                                                                                                    <div class="panel-footer">
                                                                                                                                                                        <span class="pull-left">View Details</span>
                                                                                                                                                                        <span class="pull-right"><i
                                                                                                                                                                                    class="fa fa-arrow-circle-right"></i></span>
                                                                                                                                                                        <div class="clearfix"></div>
                                                                                                                                                                    </div>
                                                                                                                                                                </a>
                                                                                                                                                            </div>
                                                                                                                                                        </div>
                                                                                                                                                        <!--1-->
                                                                                                                                                        <!--1-->
                                                                                                                                                        <!--1-->
                                                                                                                                                        <!--1-->
                                                                                                                                                        <!--1-->
                                                                                                                                                        <!--1-->

                                                                                                                                                    </div>

                                                                                                                                                    <!-- Indicators -->
                                                                                                                                                    <ol class="carousel-indicators">
                                                                                                                                                        <li data-target="#myCarousel-lg"
                                                                                                                                                            data-slide-to="0"
                                                                                                                                                            class="active"></li>
                                                                                                                                                        <li data-target="#myCarousel-lg"
                                                                                                                                                            data-slide-to="1"></li>
                                                                                                                                                        <li data-target="#myCarousel-lg"
                                                                                                                                                            data-slide-to="2"></li>
                                                                                                                                                    </ol>
                                                                                                                                                    <!-- Controls -->
                                                                                                                                                    <a class="left carousel-control"
                                                                                                                                                       href="#myCarousel-lg"
                                                                                                                                                       data-slide="prev">
                                                                                                                                                        <span class="icon-prev"></span>
                                                                                                                                                    </a>
                                                                                                                                                    <a class="right carousel-control"
                                                                                                                                                       href="#myCarousel-lg"
                                                                                                                                                       data-slide="next">
                                                                                                                                                        <span class="icon-next"></span>
                                                                                                                                                    </a>


                                                                                                                                                </div>


</header>


<header id="myCarousel-sm" class="carousel slide visible-xs-block">
    <!-- Wrapper for Slides -->
    <div class="carousel-inner">
        <!-- test brainwave-->
        <div class="col-lg-12 item active" <?php if ($hide_bloodpressure) {
            echo 'style="display:none;"';
        } ?>>
            <?php if ($_SESSION['user_select_color1-'.$uid] == "1") : ?>
            <div class="panel panel-primary">
                <?php elseif ($_SESSION['user_select_color1-'.$uid] == "2") : ?>
                <div class="panel panel-green">
                    <?php elseif ($_SESSION['user_select_color1-'.$uid] == "3") : ?>
                    <div class="panel panel-yellow">
                        <?php elseif ($_SESSION['user_select_color1-'.$uid] == "4") : ?>
                        <div class="panel panel-red">
                            <?php elseif ($wellBing <= 25) : ?>
                            <div class="panel panel-primary">
                                <?php elseif ($wellBing > 25 && $wellBing <= 50) : ?>
                                <div class="panel panel-green">
                                    <?php elseif ($wellBing > 50 && $wellBing <= 75) : ?>
                                    <div class="panel panel-yellow">
                                        <?php elseif ($wellBing > 75) : ?>
                                        <div class="panel panel-red">
                                            <?php endif; ?>
                                            <div class="panel-heading">
                                                <div class="row">
                                                    <div class="col-xs-3">
                                                        <i class="fa fa-stethoscope fa-5x"></i>
                                                    </div>
                                                    <div class="col-xs-9 text-right">
                                                        <div class="huge">Brainwave</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <a href="source_notChibrainwave.php?type=brainwave">
                                                <div class="panel-footer">
                                                    <span class="pull-left">View Details</span>
                                                    <span class="pull-right"><i
                                                                class="fa fa-arrow-circle-right"></i></span>
                                                    <div class="clearfix"></div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                    <!-- test audio-->
                                    <div class="col-lg-12 item" <?php if ($hide_bloodpressure) {
                                        echo 'style="display:none;"';
                                    } ?>>
                                        <?php if ($_SESSION['user_select_color2-'.$uid] == "1") : ?>
                                        <div class="panel panel-primary">
                                            <?php elseif ($_SESSION['user_select_color2-'.$uid] == "2") : ?>
                                            <div class="panel panel-green">
                                                <?php elseif ($_SESSION['user_select_color2-'.$uid] == "3") : ?>
                                                <div class="panel panel-yellow">
                                                    <?php elseif ($_SESSION['user_select_color2-'.$uid] == "4") : ?>
                                                    <div class="panel panel-red">
                                                        <?php elseif ($wellBing <= 25) : ?>
                                                        <div class="panel panel-primary">
                                                            <?php elseif ($wellBing > 25 && $wellBing <= 50) : ?>
                                                            <div class="panel panel-green">
                                                                <?php elseif ($wellBing > 50 && $wellBing <= 75) : ?>
                                                                <div class="panel panel-yellow">
                                                                    <?php elseif ($wellBing > 75) : ?>
                                                                    <div class="panel panel-red">
                                                                        <?php endif; ?>
                                                                        <div class="panel-heading">
                                                                            <div class="row">
                                                                                <div class="col-xs-3">
                                                                                    <i class="fa fa-stethoscope fa-5x"></i>
                                                                                </div>
                                                                                <div class="col-xs-9 text-right">
                                                                                    <div class="huge">Audio</div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <a href="source_notChi.php?type=audio">
                                                                            <div class="panel-footer">
                                                                                <span class="pull-left">View Details</span>
                                                                                <span class="pull-right"><i
                                                                                            class="fa fa-arrow-circle-right"></i></span>
                                                                                <div class="clearfix"></div>
                                                                            </div>
                                                                        </a>
                                                                    </div>
                                                                </div>

                                                                <!-- test image-->
                                                                <div class="col-lg-12 item" <?php if ($hide_bloodpressure) {
                                                                    echo 'style="display:none;"';
                                                                } ?>>
                                                                    <?php if ($_SESSION['user_select_color3-'.$uid] == "1") : ?>
                                                                    <div class="panel panel-primary">
                                                                        <?php elseif ($_SESSION['user_select_color3-'.$uid] == "2") : ?>
                                                                        <div class="panel panel-green">
                                                                            <?php elseif ($_SESSION['user_select_color3-'.$uid] == "3") : ?>
                                                                            <div class="panel panel-yellow">
                                                                                <?php elseif ($_SESSION['user_select_color3-'.$uid] == "4") : ?>
                                                                                <div class="panel panel-red">
                                                                                    <?php elseif ($wellBing <= 25) : ?>
                                                                                    <div class="panel panel-primary">
                                                                                        <?php elseif ($wellBing > 25 && $wellBing <= 50) : ?>
                                                                                        <div class="panel panel-green">
                                                                                            <?php elseif ($wellBing > 50 && $wellBing <= 75) : ?>
                                                                                            <div class="panel panel-yellow">
                                                                                                <?php elseif ($wellBing > 75) : ?>
                                                                                                <div class="panel panel-red">
                                                                                                    <?php endif; ?>
                                                                                                    <div class="panel-heading">
                                                                                                        <div class="row">
                                                                                                            <div class="col-xs-3">
                                                                                                                <i class="fa fa-stethoscope fa-5x"></i>
                                                                                                            </div>
                                                                                                            <div class="col-xs-9 text-right">
                                                                                                                <div class="huge">Image</div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <a href="source_notChi.php?type=image">
                                                                                                        <div class="panel-footer">
                                                                                                            <span class="pull-left">View Details</span>
                                                                                                            <span class="pull-right"><i
                                                                                                                        class="fa fa-arrow-circle-right"></i></span>
                                                                                                            <div class="clearfix"></div>
                                                                                                        </div>
                                                                                                    </a>
                                                                                                </div>
                                                                                            </div>
                                                                                            <!-- test wellbeing-->
                                                                                            <div class="col-lg-12 item" <?php if ($hide_bloodpressure) {
                                                                                                echo 'style="display:none;"';
                                                                                            } ?>>
                                                                                                <?php if ($_SESSION['user_select_color4-'.$uid] == "1") : ?>
                                                                                                <div class="panel panel-primary">
                                                                                                    <?php elseif ($_SESSION['user_select_color4-'.$uid] == "2") : ?>
                                                                                                    <div class="panel panel-green">
                                                                                                        <?php elseif ($_SESSION['user_select_color4-'.$uid] == "3") : ?>
                                                                                                        <div class="panel panel-yellow">
                                                                                                            <?php elseif ($_SESSION['user_select_color4-'.$uid] == "4") : ?>
                                                                                                            <div class="panel panel-red">
                                                                                                                <?php elseif ($wellBing <= 25) : ?>
                                                                                                                <div class="panel panel-primary">
                                                                                                                    <?php elseif ($wellBing > 25 && $wellBing <= 50) : ?>
                                                                                                                    <div class="panel panel-green">
                                                                                                                        <?php elseif ($wellBing > 50 && $wellBing <= 75) : ?>
                                                                                                                        <div class="panel panel-yellow">
                                                                                                                            <?php elseif ($wellBing > 75) : ?>
                                                                                                                            <div class="panel panel-red">
                                                                                                                                <?php endif; ?>
                                                                                                                                <div class="panel-heading">
                                                                                                                                    <div class="row">
                                                                                                                                        <div class="col-xs-3">
                                                                                                                                            <i class="fa fa-stethoscope fa-5x"></i>
                                                                                                                                        </div>
                                                                                                                                        <div class="col-xs-9 text-right">
                                                                                                                                            <div class="huge">Wellbeing</div>
                                                                                                                                        </div>
                                                                                                                                    </div>
                                                                                                                                </div>
                                                                                                                                <a href="dashboard.php">
                                                                                                                                    <div class="panel-footer">
                                                                                                                                        <span class="pull-left">View Details</span>
                                                                                                                                        <span class="pull-right"><i
                                                                                                                                                    class="fa fa-arrow-circle-right"></i></span>
                                                                                                                                        <div class="clearfix"></div>
                                                                                                                                    </div>
                                                                                                                                </a>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                        <!-- test steps-->
                                                                                                                        <div class="col-lg-12 item" <?php if ($hide_bloodpressure) {
                                                                                                                            echo 'style="display:none;"';
                                                                                                                        } ?>>
                                                                                                                            <?php if ($_SESSION['user_select_color5-'.$uid] == "1") : ?>
                                                                                                                            <div class="panel panel-primary">
                                                                                                                                <?php elseif ($_SESSION['user_select_color5-'.$uid] == "2") : ?>
                                                                                                                                <div class="panel panel-green">
                                                                                                                                    <?php elseif ($_SESSION['user_select_color5-'.$uid] == "3") : ?>
                                                                                                                                    <div class="panel panel-yellow">
                                                                                                                                        <?php elseif ($_SESSION['user_select_color5-'.$uid] == "4") : ?>
                                                                                                                                        <div class="panel panel-red">
                                                                                                                                            <?php elseif ($wellBing <= 25) : ?>
                                                                                                                                            <div class="panel panel-primary">
                                                                                                                                                <?php elseif ($wellBing > 25 && $wellBing <= 50) : ?>
                                                                                                                                                <div class="panel panel-green">
                                                                                                                                                    <?php elseif ($wellBing > 50 && $wellBing <= 75) : ?>
                                                                                                                                                    <div class="panel panel-yellow">
                                                                                                                                                        <?php elseif ($wellBing > 75) : ?>
                                                                                                                                                        <div class="panel panel-red">
                                                                                                                                                            <?php endif; ?>
                                                                                                                                                            <div class="panel-heading">
                                                                                                                                                                <div class="row">
                                                                                                                                                                    <div class="col-xs-3">
                                                                                                                                                                        <i class="fa fa-stethoscope fa-5x"></i>
                                                                                                                                                                    </div>
                                                                                                                                                                    <div class="col-xs-9 text-right">
                                                                                                                                                                        <div class="huge">Steps</div>
                                                                                                                                                                    </div>
                                                                                                                                                                </div>
                                                                                                                                                            </div>
                                                                                                                                                            <a href="dashboard.php">
                                                                                                                                                                <div class="panel-footer">
                                                                                                                                                                    <span class="pull-left">View Details</span>
                                                                                                                                                                    <span class="pull-right"><i
                                                                                                                                                                                class="fa fa-arrow-circle-right"></i></span>
                                                                                                                                                                    <div class="clearfix"></div>
                                                                                                                                                                </div>
                                                                                                                                                            </a>
                                                                                                                                                        </div>
                                                                                                                                                    </div>
                                                                                                                                                    <!-- old components -->
                                                                                                                                                    <div class="col-lg-12 item" <?php if ($hide_air_temperature) {
                                                                                                                                                        echo 'style="display:none;"';
                                                                                                                                                    } ?>>
                                                                                                                                                        <div class="panel panel-primary">
                                                                                                                                                            <div class="panel-heading">
                                                                                                                                                                <div class="row">
                                                                                                                                                                    <div class="col-xs-3">
                                                                                                                                                                        <i class="fa fa-eyedropper fa-5x"></i>
                                                                                                                                                                    </div>
                                                                                                                                                                    <div class="col-xs-9 text-right">
                                                                                                                                                                        <div class="huge">Chi 氣</div>
                                                                                                                                                                    </div>
                                                                                                                                                                </div>
                                                                                                                                                            </div>
                                                                                                                                                            <a href="source.php?type=ChiMonitor">
                                                                                                                                                                <div class="panel-footer">
                                                                                                                                                                    <span class="pull-left">View Details</span>
                                                                                                                                                                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                                                                                                                                                    <div class="clearfix"></div>
                                                                                                                                                                </div>
                                                                                                                                                            </a>
                                                                                                                                                        </div>
                                                                                                                                                    </div>
                                                                                                                                                    <!-- fertilizer -->
                                                                                                                                                    <div class="col-lg-12 item"  <?php if ($hide_fertilizer) {
                                                                                                                                                        echo 'style="display:none;"';
                                                                                                                                                    } ?>>
                                                                                                                                                        <div class="panel panel-primary">
                                                                                                                                                            <div class="panel-heading">
                                                                                                                                                                <div class="row">
                                                                                                                                                                    <div class="col-xs-3">
                                                                                                                                                                        <i class="fa fa-eyedropper fa-5x"></i>
                                                                                                                                                                    </div>
                                                                                                                                                                    <div class="col-xs-9 text-right">
                                                                                                                                                                        <div class="huge">Tian 天</div>
                                                                                                                                                                    </div>
                                                                                                                                                                </div>
                                                                                                                                                            </div>
                                                                                                                                                            <a href="source_notChi.php?type=Tian">
                                                                                                                                                                <div class="panel-footer">
                                                                                                                                                                    <span class="pull-left">View Details</span>
                                                                                                                                                                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                                                                                                                                                    <div class="clearfix"></div>
                                                                                                                                                                </div>
                                                                                                                                                            </a>
                                                                                                                                                        </div>
                                                                                                                                                    </div>
                                                                                                                                                    <!--light-->
                                                                                                                                                    <div class="col-lg-12 item"  <?php if ($hide_light) {
                                                                                                                                                        echo 'style="display:none;"';
                                                                                                                                                    } ?>>
                                                                                                                                                        <div class="panel panel-green">
                                                                                                                                                            <div class="panel-heading">
                                                                                                                                                                <div class="row">
                                                                                                                                                                    <div class="col-xs-3">
                                                                                                                                                                        <i class="fa fa-heartbeat fa-5x"></i>
                                                                                                                                                                    </div>
                                                                                                                                                                    <div class="col-xs-9 text-right">
                                                                                                                                                                        <div class="huge">Di 地</div>
                                                                                                                                                                    </div>
                                                                                                                                                                </div>
                                                                                                                                                            </div>
                                                                                                                                                            <a href="source_notChi.php?type=Di">
                                                                                                                                                                <div class="panel-footer">
                                                                                                                                                                    <span class="pull-left">View Details</span>
                                                                                                                                                                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                                                                                                                                                    <div class="clearfix"></div>
                                                                                                                                                                </div>
                                                                                                                                                            </a>
                                                                                                                                                        </div>
                                                                                                                                                    </div>
                                                                                                                                                    <!--moisture-->
                                                                                                                                                    <div class="col-lg-12 item"  <?php if ($hide_moisture) {
                                                                                                                                                        echo 'style="display:none;"';
                                                                                                                                                    } ?>>
                                                                                                                                                        <div class="panel panel-primary">
                                                                                                                                                            <div class="panel-heading">
                                                                                                                                                                <div class="row">
                                                                                                                                                                    <div class="col-xs-3">
                                                                                                                                                                        <i class="fa fa-medkit fa-5x"></i>
                                                                                                                                                                    </div>
                                                                                                                                                                    <div class="col-xs-9 text-right">
                                                                                                                                                                        <div class="huge">Ren 人</div>
                                                                                                                                                                    </div>
                                                                                                                                                                </div>
                                                                                                                                                            </div>
                                                                                                                                                            <a href="source_notChi.php?type=Ren">
                                                                                                                                                                <div class="panel-footer">
                                                                                                                                                                    <span class="pull-left">View Details</span>
                                                                                                                                                                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                                                                                                                                                    <div class="clearfix"></div>
                                                                                                                                                                </div>
                                                                                                                                                            </a>
                                                                                                                                                        </div>
                                                                                                                                                    </div>

                                                                                                                                                    <!-- air temperature -->
                                                                                                                                                    <div class="col-lg-12 item"  <?php if ($hide_air_temperature2) {
                                                                                                                                                        echo 'style="display:none;"';
                                                                                                                                                    } ?>>
                                                                                                                                                        <div class="panel panel-primary">
                                                                                                                                                            <div class="panel-heading">
                                                                                                                                                                <div class="row">
                                                                                                                                                                    <div class="col-xs-3">
                                                                                                                                                                        <i class="fa fa-eyedropper fa-5x"></i>
                                                                                                                                                                    </div>
                                                                                                                                                                    <div class="col-xs-9 text-right">
                                                                                                                                                                        <div class="huge">AIR TEMP</div>
                                                                                                                                                                    </div>
                                                                                                                                                                </div>
                                                                                                                                                            </div>
                                                                                                                                                            <a href="source_notChi.php?type=temp">
                                                                                                                                                                <div class="panel-footer">
                                                                                                                                                                    <span class="pull-left">View Details</span>
                                                                                                                                                                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                                                                                                                                                    <div class="clearfix"></div>
                                                                                                                                                                </div>
                                                                                                                                                            </a>
                                                                                                                                                        </div>
                                                                                                                                                    </div>
                                                                                                                                                    <!-- fertilizer -->
                                                                                                                                                    <div class="col-lg-12 item" <?php if ($hide_fertilizer2) {
                                                                                                                                                        echo 'style="display:none;"';
                                                                                                                                                    } ?>>
                                                                                                                                                        <div class="panel panel-primary">
                                                                                                                                                            <div class="panel-heading">
                                                                                                                                                                <div class="row">
                                                                                                                                                                    <div class="col-xs-3">
                                                                                                                                                                        <i class="fa fa-eyedropper fa-5x"></i>
                                                                                                                                                                    </div>
                                                                                                                                                                    <div class="col-xs-9 text-right">
                                                                                                                                                                        <div class="huge">FERTILIZER</div>
                                                                                                                                                                    </div>
                                                                                                                                                                </div>
                                                                                                                                                            </div>
                                                                                                                                                            <a href="source_notChi.php?type=fertilizer">
                                                                                                                                                                <div class="panel-footer">
                                                                                                                                                                    <span class="pull-left">View Details</span>
                                                                                                                                                                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                                                                                                                                                    <div class="clearfix"></div>
                                                                                                                                                                </div>
                                                                                                                                                            </a>
                                                                                                                                                        </div>
                                                                                                                                                    </div>
                                                                                                                                                    <!--light-->
                                                                                                                                                    <div class="col-lg-12 item"  <?php if ($hide_light2) {
                                                                                                                                                        echo 'style="display:none;"';
                                                                                                                                                    } ?>>
                                                                                                                                                        <div class="panel panel-green">
                                                                                                                                                            <div class="panel-heading">
                                                                                                                                                                <div class="row">
                                                                                                                                                                    <div class="col-xs-3">
                                                                                                                                                                        <i class="fa fa-heartbeat fa-5x"></i>
                                                                                                                                                                    </div>
                                                                                                                                                                    <div class="col-xs-9 text-right">
                                                                                                                                                                        <div class="huge">LIGHT</div>
                                                                                                                                                                    </div>
                                                                                                                                                                </div>
                                                                                                                                                            </div>
                                                                                                                                                            <a href="source_notChi.php?type=light">
                                                                                                                                                                <div class="panel-footer">
                                                                                                                                                                    <span class="pull-left">View Details</span>
                                                                                                                                                                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                                                                                                                                                    <div class="clearfix"></div>
                                                                                                                                                                </div>
                                                                                                                                                            </a>
                                                                                                                                                        </div>
                                                                                                                                                    </div>
                                                                                                                                                    <!--moisture-->
                                                                                                                                                    <div class="col-lg-12 item" <?php if ($hide_moisture) {
                                                                                                                                                        echo 'style="display:none;"';
                                                                                                                                                    } ?>>
                                                                                                                                                        <div class="panel panel-yellow">
                                                                                                                                                            <div class="panel-heading">
                                                                                                                                                                <div class="row">
                                                                                                                                                                    <div class="col-xs-3">
                                                                                                                                                                        <i class="fa fa-medkit fa-5x"></i>
                                                                                                                                                                    </div>
                                                                                                                                                                    <div class="col-xs-9 text-right">
                                                                                                                                                                        <div class="huge">MOISTURE</div>
                                                                                                                                                                    </div>
                                                                                                                                                                </div>
                                                                                                                                                            </div>
                                                                                                                                                            <a href="source_notChi.php?type=moisture">
                                                                                                                                                                <div class="panel-footer">
                                                                                                                                                                    <span class="pull-left">View Details</span>
                                                                                                                                                                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                                                                                                                                                    <div class="clearfix"></div>
                                                                                                                                                                </div>
                                                                                                                                                            </a>
                                                                                                                                                        </div>
                                                                                                                                                    </div>

                                                                                                                                                    <!--spo2-->
                                                                                                                                                    <div class="col-lg-12 item" <?php if ($hide_sop2) {
                                                                                                                                                        echo 'style="display:none;"';
                                                                                                                                                    } ?>>
                                                                                                                                                        <div class="panel panel-primary">
                                                                                                                                                            <div class="panel-heading">
                                                                                                                                                                <div class="row">
                                                                                                                                                                    <div class="col-xs-3">
                                                                                                                                                                        <i class="fa fa-eyedropper fa-5x"></i>
                                                                                                                                                                    </div>
                                                                                                                                                                    <div class="col-xs-9 text-right">
                                                                                                                                                                        <div class="huge">SPO2</div>
                                                                                                                                                                    </div>
                                                                                                                                                                </div>
                                                                                                                                                            </div>
                                                                                                                                                            <a href="source_notChi.php?type=spo2">
                                                                                                                                                                <div class="panel-footer">
                                                                                                                                                                    <span class="pull-left">View Details</span>
                                                                                                                                                                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                                                                                                                                                    <div class="clearfix"></div>
                                                                                                                                                                </div>
                                                                                                                                                            </a>
                                                                                                                                                        </div>
                                                                                                                                                    </div>
                                                                                                                                                    <!--systolic-->
                                                                                                                                                    <div class="col-lg-12 item" <?php if ($hide_systolic) {
                                                                                                                                                        echo 'style="display:none;"';
                                                                                                                                                    } ?>>
                                                                                                                                                        <div class="panel panel-red">
                                                                                                                                                            <div class="panel-heading">
                                                                                                                                                                <div class="row">
                                                                                                                                                                    <div class="col-xs-3">
                                                                                                                                                                        <i class="fa fa-stethoscope fa-5x"></i>
                                                                                                                                                                    </div>
                                                                                                                                                                    <div class="col-xs-9 text-right">
                                                                                                                                                                        <div class="huge">SYSTOLIC</div>
                                                                                                                                                                    </div>
                                                                                                                                                                </div>
                                                                                                                                                            </div>
                                                                                                                                                            <a href="source_notChi.php?type=systolic">
                                                                                                                                                                <div class="panel-footer">
                                                                                                                                                                    <span class="pull-left">View Details</span>
                                                                                                                                                                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                                                                                                                                                    <div class="clearfix"></div>
                                                                                                                                                                </div>
                                                                                                                                                            </a>
                                                                                                                                                        </div>
                                                                                                                                                    </div>
                                                                                                                                                    <!--diastolic-->
                                                                                                                                                    <div class="col-lg-12 item" <?php if ($hide_diastolic) {
                                                                                                                                                        echo 'style="display:none;"';
                                                                                                                                                    } ?>>
                                                                                                                                                        <div class="panel panel-red">
                                                                                                                                                            <div class="panel-heading">
                                                                                                                                                                <div class="row">
                                                                                                                                                                    <div class="col-xs-3">
                                                                                                                                                                        <i class="fa fa-stethoscope fa-5x"></i>
                                                                                                                                                                    </div>
                                                                                                                                                                    <div class="col-xs-9 text-right">
                                                                                                                                                                        <div class="huge">DIASTOLIC</div>
                                                                                                                                                                    </div>
                                                                                                                                                                </div>
                                                                                                                                                            </div>
                                                                                                                                                            <a href="source_notChi.php?type=diastolic">
                                                                                                                                                                <div class="panel-footer">
                                                                                                                                                                    <span class="pull-left">View Details</span>
                                                                                                                                                                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                                                                                                                                                    <div class="clearfix"></div>
                                                                                                                                                                </div>
                                                                                                                                                            </a>
                                                                                                                                                        </div>
                                                                                                                                                    </div>
                                                                                                                                                    <!--pulse-->
                                                                                                                                                    <div class="col-lg-12 item" <?php if ($hide_pulse) {
                                                                                                                                                        echo 'style="display:none;"';
                                                                                                                                                    } ?>>
                                                                                                                                                        <div class="panel panel-red">
                                                                                                                                                            <div class="panel-heading">
                                                                                                                                                                <div class="row">
                                                                                                                                                                    <div class="col-xs-3">
                                                                                                                                                                        <i class="fa fa-stethoscope fa-5x"></i>
                                                                                                                                                                    </div>
                                                                                                                                                                    <div class="col-xs-9 text-right">
                                                                                                                                                                        <div class="huge">PULSE</div>
                                                                                                                                                                    </div>
                                                                                                                                                                </div>
                                                                                                                                                            </div>
                                                                                                                                                            <a href="source_notChi.php?type=pulse">
                                                                                                                                                                <div class="panel-footer">
                                                                                                                                                                    <span class="pull-left">View Details</span>
                                                                                                                                                                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                                                                                                                                                    <div class="clearfix"></div>
                                                                                                                                                                </div>
                                                                                                                                                            </a>
                                                                                                                                                        </div>
                                                                                                                                                    </div>

                                                                                                                                                    <!--ekg-->
                                                                                                                                                    <div class="col-lg-12 item" <?php if ($hide_ekg) {
                                                                                                                                                        echo 'style="display:none;"';
                                                                                                                                                    } ?>>
                                                                                                                                                        <div class="panel panel-red">
                                                                                                                                                            <div class="panel-heading">
                                                                                                                                                                <div class="row">
                                                                                                                                                                    <div class="col-xs-3">
                                                                                                                                                                        <i class="fa fa-stethoscope fa-5x"></i>
                                                                                                                                                                    </div>
                                                                                                                                                                    <div class="col-xs-9 text-right">
                                                                                                                                                                        <div class="huge">EKG</div>
                                                                                                                                                                    </div>
                                                                                                                                                                </div>
                                                                                                                                                            </div>
                                                                                                                                                            <a href="source_notChi.php?type=EKG">
                                                                                                                                                                <div class="panel-footer">
                                                                                                                                                                    <span class="pull-left">View Details</span>
                                                                                                                                                                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                                                                                                                                                    <div class="clearfix"></div>
                                                                                                                                                                </div>
                                                                                                                                                            </a>
                                                                                                                                                        </div>
                                                                                                                                                    </div>

                                                                                                                                                    <!-- bloodpressure (moved to brainwave) -->

                                                                                                                                                    <div class="col-lg-12 item" <?php if ($hide_gaze) {
                                                                                                                                                        echo 'style="display:none;"';
                                                                                                                                                    } ?>>
                                                                                                                                                        <div class="panel panel-red">
                                                                                                                                                            <div class="panel-heading">
                                                                                                                                                                <div class="row">
                                                                                                                                                                    <div class="col-xs-3">
                                                                                                                                                                        <i class="fa fa-stethoscope fa-5x"></i>
                                                                                                                                                                    </div>
                                                                                                                                                                    <div class="col-xs-9 text-right">
                                                                                                                                                                        <div class="huge">Gaze</div>
                                                                                                                                                                    </div>
                                                                                                                                                                </div>
                                                                                                                                                            </div>
                                                                                                                                                            <a href="source_notChi.php?type=ReadingBehavior">
                                                                                                                                                                <div class="panel-footer">
                                                                                                                                                                    <span class="pull-left">View Details</span>
                                                                                                                                                                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                                                                                                                                                    <div class="clearfix"></div>
                                                                                                                                                                </div>
                                                                                                                                                            </a>
                                                                                                                                                        </div>
                                                                                                                                                    </div>


                                                                                                                                                    <!--temp-->
                                                                                                                                                    <div class="col-lg-12 item" <?php if ($hide_temp) {
                                                                                                                                                        echo 'style="display:none;"';
                                                                                                                                                    } ?>>
                                                                                                                                                        <div class="panel panel-green">
                                                                                                                                                            <div class="panel-heading">
                                                                                                                                                                <div class="row">
                                                                                                                                                                    <div class="col-xs-3">
                                                                                                                                                                        <i class="fa fa-heartbeat fa-5x"></i>
                                                                                                                                                                    </div>
                                                                                                                                                                    <div class="col-xs-9 text-right">
                                                                                                                                                                        <div class="huge">TEMP</div>
                                                                                                                                                                    </div>
                                                                                                                                                                </div>
                                                                                                                                                            </div>
                                                                                                                                                            <a href="source_notChi.php?type=temp">
                                                                                                                                                                <div class="panel-footer">
                                                                                                                                                                    <span class="pull-left">View Details</span>
                                                                                                                                                                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                                                                                                                                                    <div class="clearfix"></div>
                                                                                                                                                                </div>
                                                                                                                                                            </a>
                                                                                                                                                        </div>
                                                                                                                                                    </div>
                                                                                                                                                    <!---------------------CHI---->

                                                                                                                                                    <div class="col-lg-12 item" <?php if ($hide_tongue) {
                                                                                                                                                        echo 'style="display:none;"';
                                                                                                                                                    } ?>>
                                                                                                                                                        <div class="panel panel-green">
                                                                                                                                                            <div class="panel-heading tongue" id="tongue">
                                                                                                                                                                <div class="row">
                                                                                                                                                                    <div class="col-xs-3">
                                                                                                                                                                        <i class="fa fa-heartbeat fa-5x"></i>
                                                                                                                                                                    </div>
                                                                                                                                                                    <div class="col-xs-9 text-right">
                                                                                                                                                                        <div class="huge">tongue</div>
                                                                                                                                                                    </div>
                                                                                                                                                                </div>
                                                                                                                                                            </div>
                                                                                                                                                            <a href="source_notChi.php?type=tongue">
                                                                                                                                                                <div class="panel-footer">
                                                                                                                                                                    <span class="pull-left">View Details</span>
                                                                                                                                                                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                                                                                                                                                    <div class="clearfix"></div>
                                                                                                                                                                </div>
                                                                                                                                                            </a>
                                                                                                                                                        </div>
                                                                                                                                                    </div>

                                                                                                                                                    <div class="col-lg-12 item" <?php if ($hide_fatigue) {
                                                                                                                                                        echo 'style="display:none;"';
                                                                                                                                                    } ?>>
                                                                                                                                                        <div class="panel panel-green">
                                                                                                                                                            <div class="panel-heading fatigue" id="fatigue">
                                                                                                                                                                <div class="row">
                                                                                                                                                                    <div class="col-xs-3">
                                                                                                                                                                        <i class="fa fa-heartbeat fa-5x"></i>
                                                                                                                                                                    </div>
                                                                                                                                                                    <div class="col-xs-9 text-right">
                                                                                                                                                                        <div class="huge">fatigue</div>
                                                                                                                                                                    </div>
                                                                                                                                                                </div>
                                                                                                                                                            </div>
                                                                                                                                                            <a href="source_notChi.php?type=fatigue">
                                                                                                                                                                <div class="panel-footer">
                                                                                                                                                                    <span class="pull-left">View Details</span>
                                                                                                                                                                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                                                                                                                                                    <div class="clearfix"></div>
                                                                                                                                                                </div>
                                                                                                                                                            </a>
                                                                                                                                                        </div>
                                                                                                                                                    </div>

                                                                                                                                                    <div class="col-lg-12 item" <?php if ($hide_pulse) {
                                                                                                                                                        echo 'style="display:none;"';
                                                                                                                                                    } ?>>
                                                                                                                                                        <div class="panel panel-green">
                                                                                                                                                            <div class="panel-heading">
                                                                                                                                                                <div class="row">
                                                                                                                                                                    <div class="col-xs-3">
                                                                                                                                                                        <i class="fa fa-heartbeat fa-5x"></i>
                                                                                                                                                                    </div>
                                                                                                                                                                    <div class="col-xs-9 text-right">
                                                                                                                                                                        <div class="huge">pulse</div>
                                                                                                                                                                    </div>
                                                                                                                                                                </div>
                                                                                                                                                            </div>
                                                                                                                                                            <a href="source_notChi.php?type=pulse">
                                                                                                                                                                <div class="panel-footer">
                                                                                                                                                                    <span class="pull-left">View Details</span>
                                                                                                                                                                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                                                                                                                                                    <div class="clearfix"></div>
                                                                                                                                                                </div>
                                                                                                                                                            </a>
                                                                                                                                                        </div>
                                                                                                                                                    </div>

                                                                                                                                                    <div class="col-lg-12 item" <?php if ($hide_weakbreadth) {
                                                                                                                                                        echo 'style="display:none;"';
                                                                                                                                                    } ?>>
                                                                                                                                                        <div class="panel panel-green">
                                                                                                                                                            <div class="panel-heading weakBreadth" id="weakbreadth">
                                                                                                                                                                <div class="row">
                                                                                                                                                                    <div class="col-xs-3">
                                                                                                                                                                        <i class="fa fa-heartbeat fa-5x"></i>
                                                                                                                                                                    </div>
                                                                                                                                                                    <div class="col-xs-9 text-right">
                                                                                                                                                                        <div class="huge">weakBreadth</div>
                                                                                                                                                                    </div>
                                                                                                                                                                </div>
                                                                                                                                                            </div>
                                                                                                                                                            <a href="source_notChi.php?type=weakBreadth">
                                                                                                                                                                <div class="panel-footer">
                                                                                                                                                                    <span class="pull-left">View Details</span>
                                                                                                                                                                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                                                                                                                                                    <div class="clearfix"></div>
                                                                                                                                                                </div>
                                                                                                                                                            </a>
                                                                                                                                                        </div>
                                                                                                                                                    </div>

                                                                                                                                                    <div class="col-lg-12 item" <?php if ($hide_sweaty) {
                                                                                                                                                        echo 'style="display:none;"';
                                                                                                                                                    } ?>>
                                                                                                                                                        <div class="panel panel-green">
                                                                                                                                                            <div class="panel-heading sweaty" id="sweaty">
                                                                                                                                                                <div class="row">
                                                                                                                                                                    <div class="col-xs-3">
                                                                                                                                                                        <i class="fa fa-heartbeat fa-5x"></i>
                                                                                                                                                                    </div>
                                                                                                                                                                    <div class="col-xs-9 text-right">
                                                                                                                                                                        <div class="huge">sweaty</div>
                                                                                                                                                                    </div>
                                                                                                                                                                </div>
                                                                                                                                                            </div>
                                                                                                                                                            <a href="source_notChi.php?type=sweaty">
                                                                                                                                                                <div class="panel-footer">
                                                                                                                                                                    <span class="pull-left">View Details</span>
                                                                                                                                                                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                                                                                                                                                    <div class="clearfix"></div>
                                                                                                                                                                </div>
                                                                                                                                                            </a>
                                                                                                                                                        </div>
                                                                                                                                                    </div>

                                                                                                                                                    <div class="col-lg-12 item" <?php if ($hide_chitotal) {
                                                                                                                                                        echo 'style="display:none;"';
                                                                                                                                                    } ?>>
                                                                                                                                                        <div class="panel panel-green">
                                                                                                                                                            <div class="panel-heading chiTotal" id="chitotal">
                                                                                                                                                                <div class="row">
                                                                                                                                                                    <div class="col-xs-3">
                                                                                                                                                                        <i class="fa fa-heartbeat fa-5x"></i>
                                                                                                                                                                    </div>
                                                                                                                                                                    <div class="col-xs-9 text-right">
                                                                                                                                                                        <div class="huge">chiTotal</div>
                                                                                                                                                                    </div>
                                                                                                                                                                </div>
                                                                                                                                                            </div>
                                                                                                                                                            <a href="source_notChi.php?type=chiTotal">
                                                                                                                                                                <div class="panel-footer">
                                                                                                                                                                    <span class="pull-left">View Details</span>
                                                                                                                                                                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                                                                                                                                                    <div class="clearfix"></div>
                                                                                                                                                                </div>
                                                                                                                                                            </a>
                                                                                                                                                        </div>
                                                                                                                                                    </div>


                                                                                                                                                    <!----------------------CHI---->>
                                                                                                                                                    <!-- Controls -->
                                                                                                                                                    <a class="left carousel-control" href="#myCarousel-sm" data-slide="prev">
                                                                                                                                                        <span class="icon-prev"></span>
                                                                                                                                                    </a>
                                                                                                                                                    <a class="right carousel-control" href="#myCarousel-sm" data-slide="next">
                                                                                                                                                        <span class="icon-next"></span>
                                                                                                                                                    </a>


                                                                                                                                                </div>
</header>


<script>
    $("#myCarousel-lg").carousel();
    $("#myCarousel-sm").carousel();
</script>

</div>
<!-- /.container-fluid -->

<!-- /.row -->
<table class="table table-bordered table-hover table-striped">
    <tr>
        <th>Rid</th>
        <th>Uid</th>
        <th>Date</th>
        <th>Source</th>
        <th>Type</th>
        <th>Reading</th>
        <th>Originator</th>
    </tr>
    <?php
    $q = $_SESSION['query'];
    //    echo $q;
    // $q = "SELECT * FROM `records` WHERE uid = '$uid' AND datetime >= '2019-10-14T12:50:00.000' AND datetime <= '2019-10-14T13:00:00.000'";
    $result = mysql_query($q);
    $rows = array();
    while ($row = mysql_fetch_assoc($result)) {
        echo "<tr><th>" . $row["rid"] . "</th><th>" . $row["uid"] . "</th><th>" . $row["datetime"] . "</th><th>" . $row["source"] . "</th><th>" . $row["type"] . "</th><th>" . $row["value"] . "</th><th>" . $row["originator"] . "</th></tr>";
    }
    ?>
</table>

</div>
<!-- /#page-wrapper -->

</div>
<!-- /#wrapper -->
<script>
    var ah = document.getElementById("ah");
    var xhp = new XMLHttpRequest();
    xhp.open("GET", "newchangbgcolor.php", true);
    xhp.send();
    xhp.onreadystatechange = function () {
        if (xhp.readyState == 4 && xhp.status == 200) {
            var str = xhp.responseText;
            var arr = str.split("|");
            console.log(arr[3]);
            document.getElementById("tongue").style.backgroundColor = arr[0];
            document.getElementById("fatigue").style.backgroundColor = arr[1];
            document.getElementById("sweaty").style.backgroundColor = arr[2];
            document.getElementById("weakbreadth").style.backgroundColor = arr[3];
            document.getElementById("chitotal").style.backgroundColor = arr[4];
        }
    }
</script>
<script>

</script>
</body>

</html>
