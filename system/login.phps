m4_include(WAMroot/`mac.inc')

WAMdtd
WAMhead
WAMbody

<TABLE ALIGN="center" BORDER="0"><TR ALIGN="center">
  <TD ALIGN="center">WAMimg_biglogo</TD></TR><TR>
  <TD VALIGN="center" ALIGN="center">
    <FORM METHOD="post" ACTION="WAMroot/system/auth.php">
      <TABLE BORDER="0" CELLSPACING="10">
        <TR><TD>User:</TD><TD><INPUT TYPE="text" NAME="frm_user"></TD>
        <TD>Password:</TD><TD><INPUT TYPE="password" NAME="frm_pwd"></TD>
	<TD><INPUT TYPE="submit" VALUE="login"></TD></TR>
      </TABLE>
      <?php
	if ($ck_login != '') {
	  echo 'You seem to be logged in already. Click WAMlink(WAMroot/menu.php,`here') ',
	   'to go to the main menu.';
	}
      ?>
    </FORM>
  </TD>  
</TR></TABLE>    

WAMendbody
WAMtail
