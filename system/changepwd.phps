m4_include(WAMroot/`mac.inc')

WAMdtd
WAMhead
WAMbody

WAMtitle(`Change password')

<?php
  include('WAMroot/wamlib.php');
  
  WAMprime();
  if ($frm_filled == 'yes') {
    if ($frm_pwd != $frm_pwd2) {
      echo 'New passwords do not match. Please try again.';
      exit();
    }
  
    if (($person['Password'] == '') || (md5($frm_old) == $person['Password'])) {
      $frm_pwd = md5($frm_pwd);
      $login = $person['Login'];
      $res_id = mysql_query("update tblPerson set password='$frm_pwd' where login='$login'");
      if ($res_id) echo "Password change successful.";      
    }
    else {
      echo 'Old password was wrong. Please try again.';
      exit();
    }
  }
  else {
    echo 'Logged in as ',$person['FirstName'],' ',$person['Name'],'<P>';
  }
  
?>
<FORM METHOD="post">
  <INPUT TYPE="hidden" NAME="frm_filled" VALUE="yes">
  <TABLE BORDER="0">
    <TR><TD>Old password:</TD><TD><INPUT TYPE="password" NAME="frm_old"></TD></TR>
    <TR><TD>New Password:</TD><TD><INPUT TYPE="password" NAME="frm_pwd"></TD></TR>
    <TR><TD>Retype new Password:</TD><TD><INPUT TYPE="password" NAME="frm_pwd2"></TD></TR>
  </TABLE>
  <INPUT TYPE="submit" VALUE="change">
</FORM>

WAMendbody
WAMtail
