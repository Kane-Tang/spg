<?php
include_once("conn_db.php");

$sql = "SELECT * FROM events";
$res = mysql_query($sql);
$rows=mysql_affected_rows($conn);
$colums=mysql_num_fields($res);

echo "<table><tr>";
for($i=0; $i < $colums; $i++){
    $field_name=mysql_field_name($res,$i);
    echo "<th>$field_name</th>";
}
echo "</tr>";
while($row=mysql_fetch_row($res)){
    echo "<tr>";
    for($i=0; $i<$colums; $i++){
        echo "<td>$row[$i]</td>";
    }
    echo "</tr>";
}
echo "</table>";

echo "
<script src='http://ksiresearchorg.ipage.com/spg20/js/jquery.js'></script>
<script>
function save(){
    var xyear = $('#xyear').val();
    var xmonth = $('#xmonth').val();
    var xdate = $('#xdate').val();
    var xno = $('#xno').val();
    $.ajax({
        type: 'POST',
		url: 'http://ksiresearchorg.ipage.com/chronobot/saveData.php',
		datatype: 'json',
		data: {xyear:xyear, xmonth:xmonth, xdate:xdate, xno:xno},
		success : function(res){
			alert(res);
		}
    });
}
</script>
<br><br>
<input type='submit' value='Save current events and Analyze records from following date' alt='submit1'
				onclick='save()' />
<select name = '' id = 'xyear'>
<option value = '2020'>2020</option>
<option value = '2019'>2019</option>
<option value = '2018'>2018</option>
</select>
<select name = '' id = 'xmonth'>
<option value = '01'>1</option>
<option value = '02'>2</option>
<option value = '03'>3</option>
<option value = '04'>4</option>
<option value = '05'>5</option>
<option value = '06'>6</option>
<option value = '07'>7</option>
<option value = '08'>8</option>
<option value = '09'>9</option>
<option value = '10'>10</option>
<option value = '11'>11</option>
<option value = '12'>12</option>
</select>
<select name = '' id = 'xdate'>
<option value = '01'>1</option>
<option value = '02'>2</option>
<option value = '03'>3</option>
<option value = '04'>4</option>
<option value = '05'>5</option>
<option value = '06'>6</option>
<option value = '07'>7</option>
<option value = '08'>8</option>
<option value = '09'>9</option>
<option value = '10'>10</option>
<option value = '11'>11</option>
<option value = '12'>12</option>
<option value = '13'>13</option>
<option value = '14'>14</option>
<option value = '15'>15</option>
<option value = '16'>16</option>
<option value = '17'>17</option>
<option value = '18'>18</option>
<option value = '19'>19</option>
<option value = '20'>20</option>
<option value = '21'>21</option>
<option value = '22'>22</option>
<option value = '23'>23</option>
<option value = '24'>24</option>
<option value = '25'>25</option>
<option value = '26'>26</option>
<option value = '27'>27</option>
<option value = '28'>28</option>
<option value = '29'>29</option>
<option value = '30'>30</option>
<option value = '31'>31</option>
</select>
no of records<input type='text' id='xno' name='paras' size='3' maxlength='400'
				alt='comment1' /><br>
";
echo "
<script src='http://ksiresearchorg.ipage.com/spg20/js/jquery.js'></script>
<script>
function restore(){
    var year = $('#year').val();
    var month = $('#month').val();
    var date = $('#date').val();
    $.ajax({
        type: 'POST',
		url: 'http://ksiresearchorg.ipage.com/chronobot/restoreData.php',
		datatype: 'json',
		data: {year:year, month:month, date:date},
		success : function(res){
			alert(res);
		}
    });
}
</script>
<br><br>
<input type='submit' value='Restore events from following date' alt='submit1'
				onclick='restore()' />
<select name = '' id = 'year'>
<option value = '2020'>2020</option>
<option value = '2019'>2019</option>
<option value = '2018'>2018</option>
</select>
<select name = '' id = 'month'>
<option value = '01'>1</option>
<option value = '02'>2</option>
<option value = '03'>3</option>
<option value = '04'>4</option>
<option value = '05'>5</option>
<option value = '06'>6</option>
<option value = '07'>7</option>
<option value = '08'>8</option>
<option value = '09'>9</option>
<option value = '10'>10</option>
<option value = '11'>11</option>
<option value = '12'>12</option>
</select>
<select name = '' id = 'date'>
<option value = '01'>1</option>
<option value = '02'>2</option>
<option value = '03'>3</option>
<option value = '04'>4</option>
<option value = '05'>5</option>
<option value = '06'>6</option>
<option value = '07'>7</option>
<option value = '08'>8</option>
<option value = '09'>9</option>
<option value = '10'>10</option>
<option value = '11'>11</option>
<option value = '12'>12</option>
<option value = '13'>13</option>
<option value = '14'>14</option>
<option value = '15'>15</option>
<option value = '16'>16</option>
<option value = '17'>17</option>
<option value = '18'>18</option>
<option value = '19'>19</option>
<option value = '20'>20</option>
<option value = '21'>21</option>
<option value = '22'>22</option>
<option value = '23'>23</option>
<option value = '24'>24</option>
<option value = '25'>25</option>
<option value = '26'>26</option>
<option value = '27'>27</option>
<option value = '28'>28</option>
<option value = '29'>29</option>
<option value = '30'>30</option>
<option value = '31'>31</option>
</select>
";
?>