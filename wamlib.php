<?php

// ------------------------------------------------------------
// String functions
// ------------------------------------------------------------
function format_num($number,$precision = 2) {
  $str = sprintf("%.".$precision."f",$number);
  // $str = ereg_replace("\.",",",$str);
  return $str;
  }




function parse_num($str) {
  // \$str = ereg_replace("\.","",$str);
  $str = ereg_replace("\,",".",$str);
  return doubleval($str);
  }




// ------------------------------------------------------------
// German date functions
// ------------------------------------------------------------
function ger_parse_date($datestring) {
  ereg("([0-9]*)[^0-9]([0-9]*)[^0-9]([0-9]*)",$datestring,$regs);
  $result["mday"] = intval($regs[1]);
  $result["mon"] = intval($regs[2]);
  $result["year"] = intval($regs[3]);
  return $result;
  }




function us_format_date($date) {
  return $date["mon"]."/".$date["mday"]."/".$date["year"];
  }




function ger_format_date($date) {
  return $date["mday"].".".$date["mon"].".".$date["year"];
  }




function us_format_datestring($datestring) {
  return us_format_date(iso_parse_date($datestring));
  }




function ger_format_datestring($datestring) {
  return ger_format_date(iso_parse_date($datestring));
  }




function ger_format_timestampstring($timestamp) {
  $values = iso_parse_timestamp($timestamp);
  
  return sprintf("%02d.%02d.%04d %02d:%02d:%02d",
    $values["mday"],$values["mon"],$values["year"],$values["hours"],$values["minutes"],$values["seconds"]);
  }




// ------------------------------------------------------------
// ISO date functions
// ------------------------------------------------------------
function iso_parse_date($datestring) {
  ereg("([0-9]*)[^0-9]([0-9]*)[^0-9]([0-9]*)",$datestring,$regs);
  $result["mday"] = intval($regs[3]);
  $result["mon"] = intval($regs[2]);
  $result["year"] = intval($regs[1]);
  return $result;
  }




function iso_format_date($date) {
  return $date["year"]."-".$date["mon"]."-".$date["mday"];
  }




function iso_parse_timestamp($timestamp) {
  ereg("([0-9]{4})\-{0,1}([0-9]{2})\-{0,1}([0-9]{2}) {0,1}([0-9]{2})\:{0,1}([0-9]{2})\:{0,1}([0-9]{2})",$timestamp,$regs);
  return getdate(mktime(intval($regs[4]),intval($regs[5]),intval($regs[6]),
    intval($regs[2]),intval($regs[3]),intval($regs[1])));
  }




function iso_format_time($timestamp) {
  return sprintf("%02d:%02d:%02d",
    $timestamp["hours"],$timestamp["minutes"],$timestamp["seconds"]);
  }




// ------------------------------------------------------------
// Database tool functions
// ------------------------------------------------------------
function mysql_rewind($res_id) {
  mysql_data_seek($res_id,0);
  }




// ------------------------------------------------------------
// HTML tool functions
// ------------------------------------------------------------
function html_getselect($name,$resultid,$selected) {
  $result = "<SELECT NAME=\"".$name."\">";
  mysql_rewind($resultid);
  while ($row = mysql_fetch_row($resultid)) {
    $is_selected = $selected == $row[0];
    $result .= "<OPTION VALUE=\"".$row[0]."\"".($is_selected ? " SELECTED" : "").">".
      $row[1]."</OPTION>";
  }
  $result .= "</SELECT>";
  return $result;
  }




function html_getinput($name,$value,$size=50) {
  return "<INPUT TYPE=\"text\" NAME=\"$name\" SIZE=\"$size\" VALUE=\"$value\">";
  }




function html_gethidden($name,$value) {
  return "<INPUT TYPE=\"hidden\" NAME=\"$name\" VALUE=\"$value\">";
  }




function html_getcommand() {
  global $frm_command;
  return $frm_command;
  }




function html_getbutton($command) {
  return "<INPUT TYPE=\"submit\" NAME=\"frm_command\" VALUE=\"$command\">";
  }




// ------------------------------------------------------------
// Database interface functions
// ------------------------------------------------------------
function WAMget_config_value($key,$default = "") {
  $res_id = mysql_query("select ConfigValue from tblConfiguration where ConfigKey='$key';");
  if (mysql_error() != '') return $default;
  if (mysql_num_rows($res_id) == 0) return $default;
  $row = mysql_fetch_array($res_id);
  return $row['ConfigValue'];
  }



function WAMset_config_value($key,$value) {
  mysql_query("delete from tblConfiguration where ConfigKey='$key';");
  mysql_query("insert into tblConfiguration values ('$key','$value');");
  }



function WAMconnect() {
  $result = mysql_connect('localhost','wam');
  if (!$result) {
    echo "<B>INTERNAL ERROR</B> Failed to connect to database.";
    exit();
  }
  mysql_select_db('wam');
  if (mysql_errno() != 0)
    mysql_select_db('wg');
  
  $res_id = mysql_query("select * from tblConfiguration;");
  if (mysql_errno() != 0) {
    // apparently, this database doesn't yet have the configuration
    // info table, so go create it.
    
    mysql_query("create table tblConfiguration (ConfigKey text,ConfigValue text);");
    mysql_query("insert into tblConfiguration values ('DBVersion','1');");
    }
  
  if (WAMget_config_value("DBVersion") == "1") {
    mysql_query("alter table tblBillingEntry add column Tax double default 0 after Amount;");
    echo mysql_error();
    WAMset_config_value("DBVersion","2");
    }
  
  return 0;
  }




function WAMprime($exit_on_failure = 1) {
  global $person,$ck_login;

  if (WAMconnect()) exit();
  
  if (!$exit_on_failure) return;
  if (($ck_login == '') && $exit_on_failure) {
    echo "<B>AUTHORIZATION ERROR</B> Cookie not set. Please log in again.";
    exit();
    }
    
  $res_id = mysql_query("select Login from tblPerson");

  $valid_login = '';
  while ($person = mysql_fetch_array($res_id)) {
    if (md5($person['Login']) == $ck_login) $valid_login = $person['Login'];
    }
  
  if ($valid_login == '') {    
    echo '<B>AUTHORIZATION ERROR</B> Could not resolve cookie to valid login.';
    exit();
    }

  $res_id = mysql_query("select * from tblPerson where login='$valid_login'");
  $person = mysql_fetch_array($res_id);
  }




function WAMdisconnect() {
  mysql_close();
  }




// ------------------------------------------------------------
// Billing support functions
// ------------------------------------------------------------
function WAMis_done($id) {
  $rh = mysql_query("select Done from tblBillingEntry where BillingEntryID=$id");
  $entry = mysql_fetch_array($rh);
  $result = intval($entry["Done"])!=0;
  "$id done: $result";
  return intval($entry["Done"])!=0;
  }
  



function WAMdone_if_complete($billingentry) {
  $rh = mysql_query(
    "select BillingIOUID from tblBillingIOU,tblBillingEntry ".
    "where tblBillingIOU.BillingEntryID=tblBillingEntry.BillingEntryID and ".
    "tblBillingIOU.BillingEntryID=$billingentry and Paid=0 and tblBillingIOU.Amount<>0 and DebtorID<>RecipientID");
  echo mysql_error();
  if (mysql_num_rows($rh)==0) {
    echo "[done_if_complete] marking entry $billingentry as done.<BR>";
    mysql_query("update tblBillingEntry set Done=1 where BillingEntryID=$billingentry");
    }
  }




function WAMmark_paid($recipient,$debtor) {
  $rh = mysql_query(
    "select tblBillingIOU.BillingEntryID as BillingEntryID,BillingIOUID from tblBillingIOU,tblBillingEntry ".
    "where tblBillingIOU.BillingEntryID=tblBillingEntry.BillingEntryID and ".
      "Paid=0 and DebtorID<>RecipientID and tblBillingIOU.Amount<>0 ".
      "and RecipientID=$recipient and DebtorID=$debtor");
  echo mysql_error();
  while ($row = mysql_fetch_array($rh)) {
    mysql_query("update tblBillingIOU set Paid=1 where BillingIOUID=".$row["BillingIOUID"]);
    WAMdone_if_complete(intval($row["BillingEntryID"]));
    }
  }



?>
