m4_include(WAMroot/`mac.inc')

WAMdtd
WAMhead
WAMbody

<CENTER>

<FORM METHOD="POST" ACTION="chatmessages.php" NAME="sendMessage" TARGET="chatmessages">

	<INPUT TYPE="text" MAXLENGTH="255" SIZE="75" NAME="frm_text">
	<INPUT TYPE="button" VALUE="send message" onClick="submit();sendMessage.frm_text.value='';">

	</FORM>
	
</CENTER>
    
WAMendbody
WAMtail
