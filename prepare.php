<?php
  $cycle = (int)$_GET["cycle"];
  switch($cycle) {
    case 1:
        if (!copy("spiral/spage1A.html", "spiral/spage1.html") || !copy("spiral/spage2A.html", "spiral/spage2.html"))
          echo "<script>alert('Prepare Model failed!');history.back();</script>";
        break;
    case 2:
        if(!copy("spiral/spage1B.html", "spiral/spage1.html") || !copy("spiral/spage2B.html", "spiral/spage2.html") || !copy("spiral/spage3B.html", "spiral/spage3.html"))
          echo "<script>alert('Prepare Model failed!');history.back();</script>";
        break;
    case 3:
        if(!copy("spiral/spage1C.html", "spiral/spage1.html") || !copy("spiral/spage2C.html", "spiral/spage2.html") || !copy("spiral/spage3C.html", "spiral/spage3.html") || !copy("spiral/spage4C.html", "spiral/spage4.html"))
          echo "<script>alert('Prepare Model failed!');history.back();</script>";
        break;
    default:
        echo "<script>alert('Prepare Model failed!');history.back();</script>";
  }
  echo "<script> window.location.assign('spiral/spage1.html?' + (new Date()).valueOf()); </script>"; 
?>