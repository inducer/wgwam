m4_include(WAMroot/`mac.inc')

WAMdtd
WAMhead
WAMbody

WAMtitle(`WAM online system information')

<?php
  include('WAMroot/wamlib.php');
  
  WAMprime();
  echo 'Logged in as ',$person['FirstName'],' ',$person['Name'],'<P>';
  
  phpinfo();
?>
WAMendbody
WAMtail
