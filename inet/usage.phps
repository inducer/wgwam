m4_include(WAMroot/mac.inc)

WAMdtd
WAMhead
WAMbody

WAMtitle(`WAMconnect usage statistics')

<FORM NAME="usage" METHOD="post">

<?php
  include("WAMroot/wamlib.php");
  
  function get_cost($start,$seconds)
  {
    $rh = mysql_query(
      "select Seconds,Cost from tblPhoneTariff ".
      "where ".$start["wday"]." between StartWDay and EndWDay and ".
      "'".iso_format_time($start)."' between Start and End");

    if (mysql_num_rows($rh) != 1) { 
      echo "<B>ERROR!</B> Tariff table invalid, no tariff for wd ",
        $start[wday]," time ",iso_format_time($start),"<BR>";
      return 0;
    }
    
    $row = mysql_fetch_array($rh);
    $unit_sec = intval($row["Seconds"]);
    $cost = doubleval($row["Cost"]);
    $result = intval(($seconds+$unit_sec-1) / $unit_sec) * $cost;
    // echo iso_format_time($start)," ",$seconds," sec (1 unit = $unit_sec sec)=> ",$result," units<BR>";
    return $result;
  }
  
  WAMprime();
  
  if (isset($frm_from)) {
    $from = ger_parse_date($frm_from);
    $to = ger_parse_date($frm_to);
  }
  else {
    $now = getdate(time());
    $from = getdate(mktime(0,0,0,$now["mon"]-1,$now["mday"],$now["year"]));
    $to = $now;
  }
  $iso_from = iso_format_date($from);
  $iso_to = iso_format_date($to);
  
  if (html_getcommand() == "mark as paid") {
    mysql_query(
      "update tblConnect set Done=1 ".
      "where Done=0 and Start>='$iso_from' and Start<='$iso_to' ".
      "and End is not null"
    );
    echo "[mark] successful<BR>";
  }

  echo "<TABLE>",
    "<TR><TD>From</TD><TD>",html_getinput("frm_from",ger_format_date($from),20),"</TD></TR>",
    "<TR><TD>To</TD><TD>",html_getinput("frm_to",ger_format_date($to),20),"</TD></TR>",
    "<TR><TD COLSPAN=\"2\">",
    html_getbutton("redisplay"),html_getbutton("mark as paid"),
    "</TD></TR>",
    "</TABLE>";
  
  $persons = mysql_query("select PersonID,concat(FirstName,' ',Name) as CName from tblPerson order by Name");
  while ($row = mysql_fetch_array($persons)) $cost[intval($row["PersonID"])] = 0;
  
  $rh = mysql_query(
    "select PersonID,Start,unix_timestamp(end)-unix_timestamp(start) as Seconds ".
    "from tblConnect where Done=0 and Start>='$iso_from' and Start<='$iso_to' ".
    "and End is not null"
  );
  
  while ($row = mysql_fetch_array($rh)) { 
    if (!isset($row["PersonID"])) $row["PersonID"] = "daemon";
    $cost[$row["PersonID"]] += 
      get_cost(iso_parse_timestamp($row["Start"]),
        intval($row["Seconds"]));
  }
     
  mysql_rewind($persons);
  echo "<TABLE>",
    "<TR><TD>Name</TD><TD>Cost</TD></TR><TR><TD COLSPAN=\"2\"><HR></TD></TR>";
  while ($row = mysql_fetch_array($persons)) {
    echo "<TR><TD>",$row["CName"],"</TD><TD ALIGN=\"right\">",format_num($cost[intval($row["PersonID"])]),"</TD>";
  }
  echo "<TR><TD>daemon user</TD><TD ALIGN=\"right\">",format_num($cost["daemon"]),"</TD>";
  echo "</TABLE>";
?>

</FORM>

WAMendbody
WAMtail
