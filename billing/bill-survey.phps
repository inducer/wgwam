m4_include(WAMroot/`mac.inc')

WAMdtd
WAMhead
WAMbody

WAMtitle(`WAM billing survey')

<FORM METHOD="POST">

<?php
  include("WAMroot/wamlib.php");
  
  // Initialization -----------------------------------------------
  WAMprime();

  $persons = mysql_query("select PersonID,concat(FirstName,' ',Name) as CName from tblPerson order by Name");
  
  if (isset($frm_person)) $person_id = intval($frm_person);
  else $person_id = intval($person["PersonID"]);

  echo "<TABLE><TR><TD>Display for</TD><TD>",
    html_getselect("frm_person",$persons,$person_id),
    "</TD><TD>",html_getbutton("redisplay"),
    "</TD></TR></TABLE><P>";

  echo "<A HREF=\"download-qif.php?person_id=$person_id\">Download QIF File (complete)</A><P>\n";

  // Marking ------------------------------------------------------
  
  if (ereg("mark as paid \(([0-9]*)\)",html_getcommand(),$regs)) {
    $pay_debtor = intval($regs[1]);
    if ($person_id == $person["PersonID"]) WAMmark_paid($person_id,$pay_debtor);
    else echo "<B> SORRY!</B> You may not mark anyone else's assets as paid. :-( <P>";
  }
  
  // Debts --------------------------------------------------------
  echo "<B> Survey of debts: </B><HR>";
  
  $rh = mysql_query(
    "select tblBillingEntry.RecipientID,concat(FirstName,' ',Name) as Recipient,Due,Store,Text,tblBillingIOU.Amount as Amount,Paid ".
    "from tblBillingIOU,tblBillingEntry,tblPerson,tblStore ".
    "where tblBillingIOU.BillingEntryID=tblBillingEntry.BillingEntryID and ".
      "tblBillingEntry.RecipientID=tblPerson.PersonID and ".
      "tblBillingEntry.StoreID=tblStore.StoreID and ".
      "tblBillingIOU.DebtorID=$person_id and Done=0 and tblBillingIOU.Amount<>0 ".
    "order by Name,Due,Text");
  echo mysql_error();
  
  echo "<TABLE><TR><TD>Recipient</TD><TD>Due</TD><TD>Store</TD>",
    "<TD>Text</TD><TD>Amount</TD><TD>Paid</TD></TR>",
    "<TR><TD COLSPAN=\"6\"><HR></TD></TR>";

  while ($row = mysql_fetch_array($rh)) {
    $bgc = $person_id == $row["RecipientID"] ? "WAMcol_bglighter" : "WAMcol_bg";
    echo "<TR>",
      "<TD BGCOLOR=\"$bgc\">",$row["Recipient"],"</TD>",
      "<TD>",ger_format_datestring($row["Due"]),"</TD>",
      "<TD>",$row["Store"],"</TD>",
      "<TD>",$row["Text"],"</TD>",
      "<TD ALIGN=\"right\">",format_num($row["Amount"]),"</TD>",
      "<TD>",$row["Paid"]!=0 ? "X" : "-","</TD>",
      "</TR>";
  }
  echo "</TABLE><P>";
  
  $rh = mysql_query(
    "select RecipientID,concat(FirstName,' ',Name) as Recipient,sum(tblBillingIOU.Amount) as Amount ".
    "from tblBillingIOU,tblBillingEntry,tblPerson ".
    "where tblBillingIOU.BillingEntryID=tblBillingEntry.BillingEntryID and ".
      "tblBillingEntry.RecipientID=tblPerson.PersonID and ".
      "tblBillingIOU.DebtorID=$person_id and Done=0 and tblBillingIOU.Amount<>0 and Paid=0 ".
    "group by RecipientID ".
    "order by Name");
  echo mysql_error();
  
  echo "<TABLE><TR><TD>Recipient</TD><TD>Amount</TD></TR>",
    "<TR><TD COLSPAN=\"2\"><HR></TD></TR>";

  while ($row = mysql_fetch_array($rh)) {
    $bgc = $person_id == $row["RecipientID"] ? "WAMcol_bglighter" : "WAMcol_bg";
    echo "<TR>",
      "<TD BGCOLOR=\"$bgc\">",$row["Recipient"],"</TD>",
      "<TD ALIGN=\"right\">",format_num($row["Amount"]),"</TD>",
      "<TD><A HREF=\"download-qif.php?person_id=$person_id&include_assets=0&other_person_id=",$row["RecipientID"],"\">Download QIF File (debt only)</A></TD>\n",
      "<TD><A HREF=\"download-qif.php?person_id=$person_id&other_person_id=",$row["RecipientID"],"\">Download QIF File (all)</A></TD>\n",
      "</TR>";
  }
  echo "</TABLE><P>";

  // Assets --------------------------------------------------------
  echo "<B> Survey of assets: </B><HR>";
  
  $rh = mysql_query(
    "select DebtorID,concat(FirstName,' ',Name) as Debtor,Due,Store,Text,tblBillingIOU.Amount as Amount,Paid ".
    "from tblBillingIOU,tblBillingEntry,tblPerson,tblStore ".
    "where tblBillingIOU.BillingEntryID=tblBillingEntry.BillingEntryID and ".
      "DebtorID=PersonID and ".
      "tblBillingEntry.StoreID=tblStore.StoreID and ".
      "RecipientID=$person_id and Done=0 and tblBillingIOU.Amount<>0 ".
      "and DebtorID<>$person_id ".
    "order by Name,Due,Text");
  echo mysql_error();
  
  echo "<TABLE><TR><TD>Debtor</TD><TD>Due</TD><TD>Store</TD>",
    "<TD>Text</TD><TD>Amount</TD><TD>Paid</TD></TR>",
    "<TR><TD COLSPAN=\"6\"><HR></TD></TR>";

  while ($row = mysql_fetch_array($rh)) {
    echo "<TR>",
      "<TD>",$row["Debtor"],"</TD>",
      "<TD>",ger_format_datestring($row["Due"]),"</TD>",
      "<TD>",$row["Store"],"</TD>",
      "<TD>",$row["Text"],"</TD>",
      "<TD ALIGN=\"right\">",format_num($row["Amount"]),"</TD>",
      "<TD>",$row["Paid"]!=0 ? "X" : "-","</TD>",
      "</TR>";
  }
  echo "</TABLE><P>";
  
  $rh = mysql_query(
    "select DebtorID,concat(FirstName,' ',Name) as Debtor,sum(tblBillingIOU.Amount) as Amount ".
    "from tblBillingIOU,tblBillingEntry,tblPerson ".
    "where tblBillingIOU.BillingEntryID=tblBillingEntry.BillingEntryID and ".
      "DebtorID=PersonID and ".
      "RecipientID=$person_id and Done=0 and ".
      "tblBillingIOU.Amount<>0 and Paid=0 and DebtorID<>$person_id ".
    "group by DebtorID ".
    "order by Name");
  echo mysql_error();
  
  echo "<TABLE><TR><TD>Debtor</TD><TD>Amount</TD></TR>",
    "<TR><TD COLSPAN=\"2\"><HR></TD></TR>";

  while ($row = mysql_fetch_array($rh)) {
    echo "<TR>",
      "<TD>",$row["Debtor"],"</TD>",
      "<TD ALIGN=\"right\">",format_num($row["Amount"]),"</TD>",
      "<TD>",
        $person_id != $person["PersonID"] ? "" :
        html_getbutton("mark as paid (".$row["DebtorID"].")"),"</TD>",
      "<TD><A HREF=\"download-qif.php?person_id=$person_id&include_debts=0&other_person_id=",$row["DebtorID"],"\">Download QIF File (assets only)</A></TD>\n",
      "<TD><A HREF=\"download-qif.php?person_id=$person_id&other_person_id=",$row["DebtorID"],"\">Download QIF File (all)</A></TD>\n",
      "</TR>";
  }
  echo "</TABLE><P>";
?>

</FORM>

WAMendbody
WAMtail
