m4_include(WAMroot/`mac.inc')

WAMdtd
WAMhead(`<meta HTTP-EQUIV="Refresh" CONTENT="30">')
WAMbody(BACKGROUND="WAMimg_path/bg_status.gif")

<TABLE BORDER="0" ALIGN="center" CELLSPACING="5">
  <TR><TD VALIGN="CENTER">
  
<?php
  include('WAMroot/wamlib.php');
  if ($ck_login=="") {
    echo('WAMimg_disconnected');
    echo('</TD><TD>');
    echo('WAMlink(/inet/inet.php?httpget_cmd=connect,connect)');
  }
  else {
    WAMprime();
    
    $rh = mysql_query("select unix_timestamp(now())-unix_timestamp(Start) as Duration ".
      "from tblConnect where PersonID=".$person["PersonID"]." and End is null");
    
    $connected = mysql_num_rows($rh);

    if ($connected) {
      $row = mysql_fetch_array($rh);
      $duration = $row["Duration"];
      
      echo 'WAMimg_connected';
      echo '</TD><TD>';
      echo 'WAMlink(/inet/inet.php?httpget_cmd=disconnect,disconnect) ';
      echo sprintf("%.1f min",$duration/60);
    }
    else {
      echo('WAMimg_disconnected');
      echo('</TD><TD>');
      echo('WAMlink(/inet/inet.php?httpget_cmd=connect,connect)');
    }
  }
?>
				
    </TD></TR>
  </TABLE>

WAMendbody
WAMtail
