<?php
//if(isset($_POST['login'])){
include_once('conn_db.php');
$email = $_POST['email'];
$password = $_POST['password'];
// $starting_time = $_POST['starting'];
// $ending_time = $_POST['ending'];
// if($starting_time != null){
//     $_SESSION['starting'] = $starting_time;
// }
// if($ending_time != null){
//     $_SESSION['ending'] = $ending_time;
// }

//$query = " ";

if($email != null){
    $_SESSION['email'] = $email;
    //$_SESSION['query'] = $_SESSION['query'] . " and users.email = '$email'";
}

//select from users check username & password & set session[superuser]
$sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
$result = mysql_query($sql);
$row=mysql_fetch_assoc($result);
$count=mysql_num_rows($result);

if ($count == 1) {
    //$sql2 = " insert into Name(userName) values (".$email")  ";
    //$sql2 = " insert into msgTOsis(email,flag) values ('$email', '0')  ";
    //$rs = mysql_query($sql2);
    $_SESSION['isSuperUser'] = $row['isSuperUser'];
    $_SESSION['uid'] = $row['uid'];
    $_SESSION['loginuid'] = $row['uid'];
    $_SESSION['selectuser_uid'] = $row['uid'];
    
    if($_SESSION['isSuperUser'] == 's' || $_SESSION['isSuperUser'] == 'a'){
        //echo $_SESSION['isSuperUser'];
        //echo $_SESSION['query'];
        echo "<script>window.location = 'http://ksiresearchorg.ipage.com/spg20/sis/ipage.html';</script>";
        //echo "<script>window.location = 'select_option.php';</script>";
        $_SESSION['selectuser'] = $_SESSION['email'];
    }
    else{
        //echo "<script>window.location = 'dashboard.php';</script>";
        $_SESSION['selectuser'] = $_SESSION['email'];
        echo "<script>window.location = 'http://ksiresearchorg.ipage.com/spg20/sis/ipage.html';</script>";
    }
    
    
}
else {
    echo "Login Failed!!!";
}
//echo $_SESSION['query'];
//}
?>