m4_include(WAMroot/`mac.inc')m4_dnl
<?php
  
  include("WAMroot/wamlib.php");
  
  // Initialization -----------------------------------------------
  WAMprime();

  if ( !isset($person_id)) 
  {
    echo "person_id is unset. cannot continue.\n";
  }
  if ( !isset($other_person_id)) 
  {
    $other_person_id = -1;
  }
  if ( !isset($include_debts)) 
  {
    $include_debts = 1;
  }
  if ( !isset($include_assets)) 
  {
    $include_assets = 1;
  }
  
  // Debts --------------------------------------------------------
  header("Content-Type: text/plain");
  header("Content-Disposition: filename=wam-transactions.qif");
  echo( "!Type:Bank\n");

  $rh = mysql_query(
    "select tblBillingIOU.BillingEntryID as ID,tblBillingEntry.RecipientID,concat(FirstName,' ',Name) as Recipient,Due,Store,Text,tblBillingIOU.Amount as Amount,Paid ".
    "from tblBillingIOU,tblBillingEntry,tblPerson,tblStore ".
    "where tblBillingIOU.BillingEntryID=tblBillingEntry.BillingEntryID and ".
      "tblBillingEntry.RecipientID=tblPerson.PersonID and ".
      "tblBillingEntry.StoreID=tblStore.StoreID and ".
      "tblBillingIOU.DebtorID=$person_id and ".
      "Done=0 and ".
      "tblBillingIOU.Paid=0 and ".
      "tblBillingIOU.Amount<>0 and ".
      "tblBillingIOU.Amount<>0 ".
    "order by Name,Due,Text");
  
  while ($include_debts && $row = mysql_fetch_array($rh)) {
    if ( $other_person_id >= 0 )
    {
      if ( $other_person_id != $row["RecipientID"] )
        continue;
    }

    echo "D", us_format_datestring($row["Due"]),"\n";
    echo "T", -$row["Amount"], "\n";
    echo "N", $row["ID"], "\n";
    echo "P", $row["Recipient"], ": ", $row["Text"]," (", $row["Store"], ")\n";
    echo "^\n";
  }
  
  // Assets --------------------------------------------------------
  $rh = mysql_query(
    "select tblBillingIOU.BillingEntryID as ID,DebtorID,concat(FirstName,' ',Name) as Debtor,Due,Store,Text,tblBillingIOU.Amount as Amount,Paid ".
    "from tblBillingIOU,tblBillingEntry,tblPerson,tblStore ".
    "where tblBillingIOU.BillingEntryID=tblBillingEntry.BillingEntryID and ".
      "DebtorID=PersonID and ".
      "tblBillingEntry.StoreID=tblStore.StoreID and ".
      "RecipientID=$person_id and ".
      "tblBillingIOU.Amount<>0 and ".
      "tblBillingIOU.Paid=0 and ".
      "Done=0 and ".
      "DebtorID<>$person_id ".
    "order by Name,Due,Text");
  
  while ($include_assets && $row = mysql_fetch_array($rh)) {
    if ( $other_person_id >= 0 )
    {
      if ( $other_person_id != $row["DebtorID"] )
        continue;
    }

    echo "D", us_format_datestring($row["Due"]),"\n";
    echo "T", $row["Amount"], "\n";
    echo "N", $row["ID"], "\n";
    echo "P", $row["Debtor"], ": ", $row["Text"]," (", $row["Store"], ")\n";
    echo "^\n";
  }
?>

