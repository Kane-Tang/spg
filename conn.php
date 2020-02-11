<?php
  
  $conn = new mysqli('ksiresearchorg.ipagemysql.com', 'spgconn', 'spgconn', 'spg');
  if(mysqli_connect_error()){
    die('Could not connect: ' .  mysqli_connect_error());
  }
  /*
  $sql = "insert into spg_model(modelname)
        values('sis')";
  
  if ($conn->query($sql) === true) {
      echo "Table guests insert Successfully";
  } else {
      echo "Create Error: " . $sql . "<br>" . $conn->error;
  }
  /*
  $link = mysql_connect('ksiresearchorg.ipagemysql.com', 'spgconn', 'spgconn'); 
  if (!$link) { 
    die('Could not connect: ' . mysql_error()); 
  } 
  echo 'Connected successfully'; 
  mysql_select_db(spg);
  */
?>
