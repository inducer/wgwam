m4_include(WAMroot/`mac.inc')

WAMdtd
WAMhead
WAMbody

  WAMtitle(`Service plan')
  
  <?php
    include('WAMroot/wamlib.php');
    
    $weekdays = array('Sunday','Monday','Tuesday','Wednesday','Thursday',
      'Friday','Saturday');
    
    function FormatDate($date)
    {
      return $date["mday"].".".$date["mon"].".".$date["year"];
    }
    
    function IncDate($date)
    {
      return getdate(mktime(0,0,0,$date["mon"],$date["mday"]+1,$date["year"]));
    }

    function CompareDate($date1,$date2)
    {
      return ($date1["mday"]==$date2["mday"]) && ($date1["mon"]==$date2["mon"]) && 
        ($date1["year"]==$date2["year"]);
    }
     
    WAMprime(0);
    
    $origdate = getdate(time());
    $real_today = getdate(mktime(0,0,0,$origdate["mon"],$origdate["mday"],$origdate["year"]));
    
    if ($httpget_date == "") {
      $origdate = getdate(time());
      $today = $real_today;
    }
    else {
      ereg("([0-9]*) ([0-9]*) ([0-9]*)",$httpget_date,$regs);
      $today = getdate(mktime(0,0,0,intval($regs[2]),intval($regs[1]),intval($regs[3])));
    }
        
    $sow = getdate(mktime(0,0,0,$today["mon"],$today["mday"]-($today["wday"] == 0?6:$today["wday"]-1),$today["year"]));
    $eow = getdate(mktime(0,0,0,$sow["mon"],$sow["mday"]+6,$sow["year"]));

    $rules = mysql_query("select Rule,tblServiceRules.PersonID,Login,ServiceName,IconPath ".
      "from tblServiceRules,tblService,tblPerson ".
      "where tblServiceRules.ServiceID=tblService.ServiceID and ".
      "tblServiceRules.PersonID=tblPerson.PersonID");

    if (mysql_num_rows($rules) == 0) {
      echo "<B>OOPS!</B> There seem to be no service rules. Go and make some!";
      exit();
    }      

    $persons = mysql_query("select PersonID,Name,FirstName from tblPerson ".
      "order by Name,FirstName");    

    if (mysql_num_rows($persons) == 0) {
      echo "<B>OOPS!</B> There seem to be no persons.";
      exit();
    }      
  ?>

  <TABLE CELLSPACING="0px" CELLPADDING="0px" ALIGN="center">

  <?php        
    $days = 0;
    $runday = $sow;
    
    echo "<TR><TD BGCOLOR=\"WAMcol_bglight\">Day</TD>",
      "<TD BGCOLOR=\"WAMcol_bglight\">",'WAMspacev(`20px')</TD>',
      "<TD BGCOLOR=\"WAMcol_bglight\">&nbsp;</TD>";    
    while ($person = mysql_fetch_array($persons))
      echo "<TD BGCOLOR=\"WAMcol_bglight\">",
        $person["FirstName"]," ",$person["Name"],"</TD>";
          
    while ($days < 7) {
      mysql_rewind($rules);
      
      if ($days%2 == 0) $bgcol = "WAMcol_bglight";
      else $bgcol = "WAMcol_bg";
      
      $hitcount = 0;
      
      $wday = $runday["wday"];
      $dayno = 365*($runday["year"]-1)
        + floor(($runday["year"]-1)/4)
        - floor(($runday["year"]-1)/100)
        + floor(($runday["year"]-1)/400)
        + $runday["yday"];
      $weekno = floor($dayno/7);
      
      $person_hits = array();
      
      echo "<TR>";
      if (CompareDate($runday,$real_today)) $daybgcol = "#633A63";
      else $daybgcol = $bgcol;
      echo "<TD BGCOLOR=\"$daybgcol\">",$weekdays[$runday["wday"]],"</TD>";
      echo "<TD BGCOLOR=\"$daybgcol\">",FormatDate($runday),"</TD>";
      echo "<TD BGCOLOR=\"$daybgcol\">",'WAMspacev(`60px')</TD>';
      
      while ($rule = mysql_fetch_array($rules)) {
        $my_rule = $rule["Rule"];
        eval('$rule_hit = '.$rule["Rule"].';');

        if ($rule_hit) 
          $person_hits[$rule["PersonID"]] .= 
            "<IMG SRC=\""."WAMimg_path/".$rule["IconPath"]."\" ALT=\"".$rule["ServiceName"]."\">";
      }
    
      mysql_rewind($persons);
      
      while ($person = mysql_fetch_array($persons))
        echo "<TD BGCOLOR=\"$bgcol\">&nbsp;",$person_hits[$person["PersonID"]],"</TD>";
        
      $days++;
      $runday = IncDate($runday);
    }
    
  ?> 
  
  <TR><TD COLSPAN="6">
  
  <?php
  
    $prevweek = getdate(mktime(0,0,0,$sow["mon"],$sow["mday"]-7,$sow["year"]));
    $nextweek = getdate(mktime(0,0,0,$sow["mon"],$sow["mday"]+7,$sow["year"]));
    
    echo "<A HREF=\"/services/serviceplan.php?httpget_date=",
      $prevweek["mday"],"+",$prevweek["mon"],"+",$prevweek["year"],
      "\">see previous week</A> &middot; ";

    echo "<A HREF=\"/services/serviceplan.php?httpget_date=",
      $nextweek["mday"],"+",$nextweek["mon"],"+",$nextweek["year"],
      "\">see next week</A>";
  
  ?>
  
  </TD></TR></TABLE>
  
WAMendbody
WAMtail
