m4_include(WAMroot/`mac.inc')

WAMdtd
WAMhead
WAMbody

WAMtitle(`WAM billing editor')

<FORM METHOD="POST">

<?php
  include('WAMroot/wamlib.php');
  
  // Detail mode ----------------------------------------------
  function out_debtor($id,$name,$factor,$paid,$paidtime) {
    echo "<TR><TD>$name</TD><TD>",
      "<INPUT TYPE=\"checkbox\" NAME=\"js_participate\" onClick=",
        "\"if (this.checked) this.form.frm_".$id."_factor.value='",
	format_num($factor*100,1),
	"'; else this.form.frm_".$id."_factor.value='0';\"",
	$factor ? " CHECKED" : "",">",
      html_getinput("frm_".$id."_factor",format_num($factor*100,1),6),
      "</TD><TD> paid:",
      "<INPUT TYPE=\"checkbox\" NAME=\"frm_".$id."_paid\"",$paid ?" CHECKED":"",">",
      "</TD><TD>",$paid?ger_format_timestampstring($paidtime) :"",
      html_gethidden("frm_".$id."_paidtime",$paid ? $paidtime : "NULL"),
      "</TD></TR>\n";
    }
  
  function dm_outentry($id) {
    global $stores,$persons,$person;
        
    $empty_entry = !is_integer($id);
    
    if (!$empty_entry) {
      $rh = mysql_query("select * ".
        "from tblBillingEntry ".
        "where BillingEntryID=$id");
      $entry = mysql_fetch_array($rh);
      
      if (intval($entry["Done"])!=0) 
        echo "This item is marked as <B>DONE</B>. You will not be allowed to change it.<P>";
      }
    echo "<TABLE BORDER=\"1\">";
    
    echo "<TR><TD>Recipient</TD><TD>",
      html_getselect("frm_recipient",$persons,$empty_entry ? $person["PersonID"] : $entry["RecipientID"]),
      "</TD></TR>";
    echo "<TR><TD>Text</TD><TD>",
      html_getinput("frm_text",$empty_entry ? "" : $entry["Text"]),
      "</TD></TR>";
    echo "<TR><TD>Store</TD><TD>",
      html_getselect("frm_store",$stores,$empty_entry ? "" : $entry["StoreID"]),
      "</TD></TR>";
    
    echo "<TR><TD>Quantity/Amount</TD><TD>",
      html_getinput("frm_qty",$empty_entry ? "1" : format_num($entry["Quantity"],1),6)," x ",
      html_getinput("frm_amount",$empty_entry ? "" : format_num($entry["Amount"],2),9),
      "</TD></TR>";

    echo "<TR><TD>Tax</TD><TD>",
      html_getinput("frm_tax",$empty_entry ? WAMget_config_value("NormalTax","0") : format_num($entry["Tax"],1),6)," %",
      "</TD></TR>";
      
    $due = $empty_entry ? getdate(time()): iso_parse_date($entry["Due"]);
      
    echo "<TR><TD>Due</TD><TD>",
       html_getinput("frm_dueday",$due["mday"],2)," . ",
       html_getinput("frm_duemon",$due["mon"],2)," . ",
       html_getinput("frm_dueyear",$due["year"],4),
      "</TD></TR>";

    echo "<TR><TD>Debtors</TD><TD><TABLE>";
    if ($empty_entry) {
      mysql_rewind($persons);
      while ($row = mysql_fetch_array($persons)) 
        out_debtor(intval($row["PersonID"]),$row["CName"],1-intval($row["Inactive"]),0,"");
      }
    else {
      mysql_rewind($persons);
      while ($person_row = mysql_fetch_array($persons)) {
	$iou_rid = mysql_query("select tblBillingIOU.*,concat(FirstName,' ',Name) as CName ".
          "from tblPerson,tblBillingIOU ".
          "where DebtorID=PersonID and PersonID=".$person_row["PersonID"]." ".
	  "and BillingEntryID=$id;");
	if (mysql_num_rows($iou_rid) == 0) 
          out_debtor(intval($person_row["PersonID"]),$person_row["CName"],0,0,"");
        else {
          $iou_row = mysql_fetch_array($iou_rid);
          out_debtor(intval($iou_row["DebtorID"]),$iou_row["CName"],
            doubleval($iou_row["Amount"])/(doubleval($entry["Quantity"])*doubleval($entry["Amount"])),
            intval($iou_row["Paid"]),$iou_row["PaidTime"]);
	  }
	}
      }
             
    echo "</TABLE></TD></TR>";
    
    echo "<TR><TD>Entered</TD><TD>",
      $empty_entry ? "N/A" : ger_format_timestampstring($entry["Entered"]),
      "</TD></TR>";

    echo "</TABLE>";
    }
  
  function dm_delentry($id) {
    if (WAMis_done($id)) {
      echo "[delete] This item is marked as <B>DONE</B>. You may not change or delete it.<P>";
      return;
      }
    
    mysql_query("delete from tblBillingEntry where BillingEntryID=$id");
    mysql_query("delete from tblBillingIOU where BillingEntryID=$id");
    }
  
  function dm_inentry($id) {    
    global $HTTP_POST_VARS,$frm_recipient,$frm_store,$frm_text,
      $frm_dueyear,$frm_duemon,$frm_dueday,$frm_qty,$frm_amount,$frm_tax;
    
    $total = parse_num($frm_qty)*parse_num($frm_amount)*(1+parse_num($frm_tax)/100);
    if ($total == "0") {
      echo "<B>WHOOPS?</B> Why insert an entry with zero balance?";
      return $id;
      }
    
    $empty_entry = !is_integer($id);
    if (!$empty_entry) {
      if (WAMis_done($id)) {
        echo "[modify] This item is marked as <B>DONE</B>. You may not change or delete it.<P>";
        return $id;
        }
      
      mysql_query("delete from tblBillingEntry where BillingEntryID=$id");
      mysql_query("delete from tblBillingIOU where BillingEntryID=$id");
      }

    $rh = mysql_query("insert into tblBillingEntry ".
      "(BillingEntryID,RecipientID,Text,StoreID,Due,Quantity,Amount,Tax,Done) values ".
      "(NULL,$frm_recipient,\"$frm_text\",$frm_store,\"$frm_dueyear-$frm_duemon-$frm_dueday\",".
      parse_num($frm_qty).",".parse_num($frm_amount).",".parse_num($frm_tax).",0)");
    $newid = mysql_insert_id();
    
    $total_factors = 0;
    reset($HTTP_POST_VARS);
    do {
      $formvar = key($HTTP_POST_VARS);
      
      if (ereg("frm_[0-9]*_factor",$formvar,$regs))
        $total_factors += parse_num($HTTP_POST_VARS[$regs[0]]);
    } while (each($HTTP_POST_VARS));
    
    reset($HTTP_POST_VARS);   
    do {
      $formvar = key($HTTP_POST_VARS);
      
      if (ereg("frm_([0-9]*)_factor",$formvar,$regs)) {
        $person_id = intval($regs[1]);
      
        $paid = isset($HTTP_POST_VARS["frm_".$person_id."_paid"]);
        if ($paid) {
          $paidtime = $HTTP_POST_VARS["frm_".$person_id."_paidtime"];
          if ($paidtime == "NULL") $paidtime="now()";
	  $paid_str = "1";
          }
        else {
	  $paidtime = "NULL";
	  $paid_str = "0";
	  }
        
        mysql_query("insert into tblBillingIOU ".
          "(BillingEntryID,DebtorID,Amount,Paid,PaidTime) values ".
          "($newid,$person_id,".
          parse_num($HTTP_POST_VARS["frm_".$person_id."_factor"])/$total_factors*$total.
          ",$paid_str,$paidtime)"
        );
        }
    } while (each($HTTP_POST_VARS));
    
    WAMdone_if_complete($newid);
    
    return $newid;
    }
  
  function dm_outbuttons() {
    echo "<TABLE><TR><TD>",
      html_getbutton("browse mode"),
      html_getbutton("new"),
      html_getbutton("insert"),
      html_getbutton("modify"),
      html_getbutton("delete"),
      "</TD></TR></TABLE>";      
    }  
  
  function detail_mode() {
    global $frm_id;
    $id = intval($frm_id);
    
    if ((html_getcommand()=="new") || (html_getcommand()=="")) {
      dm_outentry("new");
      echo html_gethidden("frm_id","new");  
      }
    if (html_getcommand()=="edit") {
      if (isset($frm_id)) {
        dm_outentry($id);
        echo html_gethidden("frm_id",$id);  
        }
      else {
        dm_outentry("new");
        echo html_gethidden("frm_id","new");  
        }
      }
    if (html_getcommand()=="insert") {
      $id = dm_inentry("new");
      dm_outentry("new");
      echo html_gethidden("frm_id","new");  
      }
    if (html_getcommand()=="modify") {
      if ($frm_id == "new") {
        $id = dm_inentry("new");
        dm_outentry($id);
        echo html_gethidden("frm_id",$id);  
        }
      else {
        $id = dm_inentry($id);
        dm_outentry($id);
        echo html_gethidden("frm_id",$id);  
        }
      }
    if (html_getcommand()=="delete") {
      dm_delentry($id);
      dm_outentry("new");
        echo html_gethidden("frm_id","new");  
      }
    dm_outbuttons();
    }
 
    
  
  
  // Browse mode ----------------------------------------------
  function bm_outlist() {
    $rh = mysql_query("select BillingEntryID,".
      "concat(FirstName,' ',Name) as Recipient,".
      "Text,Store,Due,Quantity,Amount,Tax ".
      "from tblBillingEntry,tblPerson,tblStore ".
      "where tblBillingEntry.RecipientID=tblPerson.PersonID and ".
        "tblBillingEntry.StoreID=tblStore.StoreID and Done=0 ".
      "order by Due,Text"
    );
    
    echo "<TABLE BORDER=\"1\">",
      "<TR><TD></TD><TD>Recipient</TD><TD>Store</TD><TD>Text</TD><TD>Due</TD><TD>Amount</TD><TD>Tax</TD></TR>";
    $first = " CHECKED";
    
    while ($row = mysql_fetch_array($rh)) {
      echo "<TR><TD><INPUT TYPE=\"radio\" NAME=\"frm_id\" VALUE=\"".$row["BillingEntryID"]."\" $first></TD>";
      echo "<TD>",$row["Recipient"],"</TD>";
      echo "<TD>",$row["Store"],"</TD>";
      echo "<TD>",$row["Text"],"</TD>";
      echo "<TD>",ger_format_datestring($row["Due"]),"</TD>";
      echo "<TD>",format_num($row["Quantity"],1)," x ",format_num($row["Amount"]),"</TD>";
      echo "<TD>",format_num($row["Tax"],1)," % </TD>";
      echo "</TR>";
      $first = "";
      }
    echo "</TABLE>";
    }

  function bm_outbuttons() {
    echo "<TABLE><TR><TD>",
      html_getbutton("edit"),
      html_getbutton("new"),
      "</TD></TR></TABLE>";      
    }  

  function browse_mode() {
    bm_outlist();
    bm_outbuttons();
    }




  // Initialization -------------------------------------------
  WAMprime();
  
  $persons = mysql_query("select PersonID,concat(FirstName,' ',Name) as CName,Inactive from tblPerson order by Name");
  $stores = mysql_query("select StoreID,Store from tblStore order by Store");
  
  if (html_getcommand()=="browse mode")
    browse_mode();
  else 
    detail_mode();
?>

</FORM>

WAMendbody
WAMtail
