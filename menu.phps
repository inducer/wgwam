m4_include(WAMroot/`mac.inc')

WAMdtd
WAMhead
WAMbody

WAMtitle(`WAM online services main menu')

<?php
  include('WAMroot/wamlib.php');
  
  WAMprime();
  echo 'Logged in as ',$person['FirstName'],' ',$person['Name'],'<P>';
?>

WAMlink(WAMroot/system/changepwd.php,`Change password')<BR>
WAMlink(WAMroot/gimmicks/wam-up.php,`WAM up your pets')<BR>
WAMlink(WAMroot/chat/chatindex.php,`Chat')<BR>
WAMlink(WAMroot/shopping/shoppinglist.php,`Shopping list')
<HR>
WAMlink(WAMroot/services/rule-edit.php,`Edit service rules')<BR>
WAMlink(WAMroot/services/serviceplan.php,`Service plan')<BR>
<HR>
WAMlink(WAMroot/inet/usage.php,`Internet usage statistics')
<HR>
WAMlink(WAMroot/billing/iou-edit.php,`Billing editor')<BR>
WAMlink(WAMroot/billing/bill-survey.php,`Billing survey')<BR>
<HR>
WAMlink(WAMroot/system/info.php,`System information')<BR>
<HR>

<P ALIGN="right">WAMimg_sponsor</P>

WAMendbody
WAMtail
