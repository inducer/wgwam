m4_include(WAMroot/`mac.inc')

WAMdtd
WAMhead(`<META HTTP-EQUIV="Refresh" CONTENT="5">')
WAMbody

WAMimg_olumchat<BR><BR>

<?php

	// $sc = popen("/opt/samba/bin/smbclient -M nemesis > /tmp/out","w");
	// $sc = popen("echo -M nemesis > /tmp/out","w");
	// fputs($sc,"Chat with me!");
	// pclose($sc);

	include('WAMroot/wamlib.php');

	WAMprime();
	
	$myID = $person[PersonID];
	
	if ($frm_text == "clear" ) {
		$count = mysql_fetch_array(mysql_query("select count(*) from tblChat"));
		while ($count[0] > 0) {
			$deleteMe = mysql_fetch_array(mysql_query("select * from tblChat order by MessageID limit 1"));
			mysql_query("delete from tblChat where MessageID='$deleteMe[MessageID]'");
			$count = mysql_fetch_array(mysql_query("select count(*) from tblChat"));
			}
		mysql_query("insert into tblChat values (NULL,'$person[PersonID]','cleared the display...')");
		$frm_text = "";
		}

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
	$persons = mysql_query("select PersonID,Name,FirstName,Handle from tblPerson");

	mysql_rewind($messages);
        
	while ($message = mysql_fetch_array($messages)) {

		mysql_rewind($persons);
		$sender = '';
		while ($person = mysql_fetch_array($persons)) {
			if (intval($message['Person']) == intval($person['PersonID'])) {
				$sender = $person['Handle'];
				}
			}
						
		if (intval($message['Person']) == $myID) {
			echo "<FONT COLOR=#58627E>";
			echo "<B> [", $sender, "] </B>";
			echo $message['Text'];
			echo "</FONT>";
			echo "<BR><BR>";
			}
		else {
			echo "<FONT COLOR=#8494bd>";
			echo "<B> [", $sender, "] </B>";
			echo $message['Text'];
			echo "</FONT>";
			echo "<BR><BR>";
			}

		}

	?>
	
WAMendbody
WAMtail
