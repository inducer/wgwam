m4_include(WAMroot/`mac.inc')

WAMdtd
WAMhead
WAMbody

<?php
  include('WAMroot/wamlib.php');

  function randomint($max = 100) { 
    static $startseed = 0; 
    if (!$startseed) {
      $startseed = (double)microtime()*getrandmax(); 
      srand($startseed);
    } 
    return (rand()%$max); 
  } 

  
  WAMprime();
  
  $salt = randomint(50000);
  $code = md5($salt.$person["Password"]);
  $login = $person["Login"];
  
  switch ($httpget_cmd) {
    case "connect":
       echo "Executing connect script:<PRE><P>";
       passthru("/bin/local/connect $login $code $salt");
       echo "</PRE><P>";
       break;
    case "disconnect":
       echo "Executing disconnect script:<PRE><P>";
       passthru("/bin/local/disconnect $login $code $salt");
       echo "</PRE><P>";
       break;
    default:
      echo "This is an internal script. Do not execute manually.";
  }
?>

WAMendbody
WAMtail
