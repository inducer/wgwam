m4_include(WAMroot/`mac.inc')

WAMdtd
WAMhead
WAMbody

WAMtitle(`Edit service rules')

<FORM METHOD="POST">
  <TABLE CELLPADDING="0" CELLSPACING="0">
    <TR>
      <TD></TD>
      <TD>Type of service</TD>
      <TD>Person</TD>
      <TD>Rule</TD>
      <TD>Comment</TD>
    </TR>
  
    <?php
      include('WAMroot/wamlib.php');
      WAMprime();
      
      $services = mysql_query("select * from tblService");
      $persons = mysql_query("select PersonID,Name,FirstName from tblPerson");

      function out_servicerule($id,$rule)
      {
        global $services,$persons;
	
        echo '<TR><TD>';

	if ($id != 'new') echo '<INPUT TYPE="checkbox" NAME="frm_',$id,'_check">';
	echo '</TD>';
	
	echo '<TD><SELECT NAME="frm_',$id,'_service">';
	mysql_rewind($services);
	while ($service = mysql_fetch_array($services)) {
          $selected = 
            ($id != 'new') && ($rule['ServiceID'] == $service['ServiceID']) 
            ? ' SELECTED' : '';
	  echo '<OPTION',$selected,' VALUE="',$service['ServiceID'],'">',$service['ServiceName'],'</OPTION>';
        }
	echo '</SELECT></TD>';
        	
	echo '<TD><SELECT NAME="frm_',$id,'_person">';
	mysql_rewind($persons);
        
	while ($person = mysql_fetch_array($persons)) {
          $selected = 
            ($id != 'new') && (intval($rule['PersonID']) == intval($person['PersonID'])) 
            ? ' SELECTED' : '';
	  echo '<OPTION',$selected,' VALUE="',$person['PersonID'],'">',
            $person['FirstName'],' ',$person['Name'],'</OPTION>';
        }
	echo '</SELECT></TD>';
        
        echo '<TD><INPUT TYPE="text" SIZE="30" NAME="frm_',$id,'_rule"';
        if ($id != 'new') echo ' VALUE="',$rule['Rule'],'"';
        echo '></TD>';
        
        echo '<TD><INPUT TYPE="text" NAME="frm_',$id,'_comment"';
        if ($id != 'new') echo ' VALUE="',$rule['Comment'],'"';
        echo '></TD>';

	echo '</TR>';
      }

      if (html_getcommand() == "insert") {
        mysql_query("insert into tblServiceRules values (NULL,".
          "$frm_new_person,'$frm_new_service','$frm_new_rule','$frm_new_comment'".
        ")");
      }
      
      if (html_getcommand() == "modify") {
        do {
          $formvar = key($HTTP_POST_VARS);
          if (ereg("frm_([0-9]*)_check",$formvar,$regs)) {
            $mod_id = $regs[1];
            mysql_query("update tblServiceRules set ".
              "PersonID=".$HTTP_POST_VARS['frm_'.$mod_id.'_person'].",".
              "ServiceID='".$HTTP_POST_VARS['frm_'.$mod_id.'_service']."',".
              "Rule='".$HTTP_POST_VARS['frm_'.$mod_id.'_rule']."',".
              "Comment='".$HTTP_POST_VARS['frm_'.$mod_id.'_comment']."' ".
              "where RuleID=$mod_id");
          }          
        } while (each($HTTP_POST_VARS));
      }
      
      if (html_getcommand() == "delete") {
        do {
          $formvar = key($HTTP_POST_VARS);
          if (ereg("frm_([0-9]*)_check",$formvar,$regs)) {
            $del_id = $regs[1];
            mysql_query("delete from tblServiceRules where RuleID=$del_id");
          }          
        } while (each($HTTP_POST_VARS));
      }

      if (html_getcommand() == "duplicate") {
        do {
          $formvar = key($HTTP_POST_VARS);
          if (ereg("frm_([0-9]*)_check",$formvar,$regs)) {
            $dup_id = $regs[1];
            $dupe_result = mysql_query("select * from tblServiceRules where RuleID=$dup_id");
            $dupe = mysql_fetch_array($dupe_result);
            mysql_query("insert into tblServiceRules values (NULL,".
              $dupe["PersonID"].",".
              "'".$dupe["ServiceID"]."',".
              "'".$dupe["Rule"]."',".
              "'".$dupe["Comment"]."')"
              );
            echo mysql_error();
          }          
        } while (each($HTTP_POST_VARS));
      }

      $res_id = mysql_query("select * from tblServiceRules order by PersonID,ServiceID");
      while ($rule = mysql_fetch_array($res_id)) {
        out_servicerule($rule['RuleID'],$rule);
      }
      
      out_servicerule('new',1);
			
			echo "<TR><TD COLSPAN=\"5\" ALIGN=\"center\">",
			  html_getbutton("insert"),
			  html_getbutton("modify"),
			  html_getbutton("delete"),
			  html_getbutton("duplicate"),"</TD></TR>";

    ?>
    
  </TABLE>
</FORM>

The following special variables may be used in service rules:<P>

<TABLE>
  m4_define(WAMexplain,`<TR><TD><B><I>$1</I></B></TD><TD>$2</TD></TR>')
  
  WAMexplain(`$weekno',`weeks passed since one point in the past')
  WAMexplain(`$dayno',`days passed since 0.0.0')
  WAMexplain(`$wday',`weekday (0=sunday..6=saturday)')
</TABLE>

WAMendbody
WAMtail
