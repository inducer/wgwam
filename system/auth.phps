m4_include(WAMroot/`mac.inc')m4_dnl
<?php
  include('WAMroot/wamlib.php');
  
  WAMprime(0);

  $res_id = mysql_query("select * from tblPerson where login='$frm_user';");
  if (mysql_num_rows($res_id)) { 
    $result = mysql_fetch_array($res_id);
    $login_ok = (md5($frm_pwd) == $result['Password']) || ($result['Password']=='');

    if ($login_ok) setcookie("ck_login",md5($result['Login']),0,"/");
  }
  else { $login_ok = false; }
  
  if ($login_ok) { 
    header('Location: WAMroot/menu.php');
  }
  else {
    echo 'WAMdtd WAMhead WAMbody';
    echo 'Login failure for <B>',$frm_user,'</B>. Either the password you typed was wrong,',
      'or the specified user does not exist.<P>WAMlink(WAMroot/system/login.php,Back to login)';
    echo "WAMendbody WAMtail"; 
  }  
  
  WAMdisconnect();
?>m4_dnl
