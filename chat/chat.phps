m4_include(WAMroot/`mac.inc')

WAMdtd
WAMhead
<meta HTTP-EQUIV="Refresh" CONTENT="10">
WAMbody

WAMtitle(`WG Chat-Page!')

<?php

	include('WAMroot/wamlib.php');

	WAMprime();
	
	if ($frm_text != "") {
		mysql_query("insert into tblChat values (NULL,'$person[PersonID]','$frm_text')");
		}

	$count = mysql_fetch_array(mysql_query("select count(*) from tblChat"));
	while ($count[0] > 10) {
		$deleteMe = mysql_fetch_array(mysql_query("select * from tblChat order by MessageID limit 1"));
		mysql_query("delete from tblChat where MessageID='$deleteMe[MessageID]'");
		$count = mysql_fetch_array(mysql_query("select count(*) from tblChat"));
		}

	$messages = mysql_query("select * from tblChat order by MessageID");
	$persons = mysql_query("select PersonID,Name from tblPerson");

	mysql_rewind($messages);
        
	while ($message = mysql_fetch_array($messages)) {

		mysql_rewind($persons);
		while ($person = mysql_fetch_array($persons)) {
			$sender = (intval($message['Person']) == intval($person['PersonID'])) ? $person['Name'] : $sender;
			}
						
		echo "<B> ", $sender, ": </B> ";
		echo $message['Text'];
		echo "<BR><BR>";
		}
	?>
	
<FORM METHOD="POST" ACTION="chat.php" NAME="sendMessage">

	<INPUT TYPE="text" MAXLENGTH="150" SIZE="100" NAME="frm_text">
	<INPUT TYPE="button" VALUE="send message" onClick="submit();">

	</FORM>
    
WAMendbody
WAMtail
