<?php
  if(isset($_GET['buildfile']))
  {
    include('dblogin.php');

    $content="";
    $prodid="Church Name";
    $dayortime="day";
    if(isset($_GET['planinstance'])) { $planinstance=$_GET['planinstance']; } else { $planinstance=""; }
    if(isset($_GET['starttimeofday']) && $_GET['starttimeofday'] != "0") { $starttimeofday=$_GET['starttimeofday']; } else { $starttimeofday="090000"; }
    if(isset($_GET['endtimeofday']) && $_GET['endtimeofday'] != "0") { $endtimeofday=$_GET['endtimeofday']; } else { $endtimeofday="100000"; }

    $getvar="SELECT Var_Value FROM Variables WHERE (Var_Name='PRODID')";
    if(!$rs=mysqli_query($db,$getvar)) { echo("Unable to Run Query: $getvar"); exit; }
    while($row = mysqli_fetch_array($rs)) { $prodid=$row['Var_Value']; }
    
    $getvar="SELECT Var_Value FROM Variables WHERE (Var_Name='Day-or-Time')";
    if(!$rs=mysqli_query($db,$getvar)) { echo("Unable to Run Query: $getvar"); exit; }
    while($row = mysqli_fetch_array($rs)) { $dayortime=strtolower($row['Var_Value']); }

    $days="SELECT * FROM ReadingPlanDay WHERE (RP_PlanInstance='$planinstance')";
    if(!$rs=mysqli_query($db,$days)) { echo("Unable to Run Query: $days"); exit; }
    while($row = mysqli_fetch_array($rs))
    {
      $content.=("BEGIN:VEVENT\n");
      $content.=("SUMMARY:$planinstance - " . $row['RP_Verses'] . "\n");
      if($dayortime == "day") { $content.=("DTSTART;VALUE=DATE:" . $row['RP_Date'] . "\n"); }
      else { $content.=("DTSTART:" . $row['RP_Date'] . "T" . $starttimeofday . "00\nDTEND:" . $row['RP_Date'] . "T" . $endtimeofday . "00\n"); }
      $content.=("LOCATION:https://www.biblegateway.com/passage/?search=" . str_replace(" ","%20",$row['RP_Verses']) . "&version=" . $row['RP_Version'] . "\n");
      $content.=("DESCRIPTION:" . $row['RP_Notes'] . "\n");
      $content.=("END:VEVENT\n");
    }

    if($content != "")
    {
      header("Content-type: text/plain");
      header("Content-Disposition: attachment; filename=reading-plan.txt");

      echo("BEGIN:VCALENDAR\n");
      echo("PRODID:-//$prodid/NONSGML $planinstance//EN\n");
      echo("VERSION:2.0\n");
      echo("BEGIN:VTIMEZONE\n");
      echo("TZID:America/New_York\n");
      echo("END:VTIMEZONE\n");
      echo($content);
      echo("END:VCALENDAR\n");
    }
    else { echo("<h2>No Items Available</h2>\n"); }
  }
  else
  {
    include('pretitle.php');
    echo("<title>Create Calendar File</title>");
    include('posttitle.php');

    $getinstances="SELECT RP_PlanInstance FROM ReadingPlanDay GROUP BY RP_PlanInstance"; $instances="";
    if(!$rs=mysqli_query($db,$getinstances)) { echo("Unable to Run Query: $getinstances"); exit; }
    while($row = mysqli_fetch_array($rs))
    { $instances.=("<option value='" . $row['RP_PlanInstance'] . "'>" . $row['RP_PlanInstance'] . "</option>"); }

    echo("<h3>Download Calendar File</h3>\n");
    echo("<form method='get' action=''><table>");
    echo("<tr><th style='text-align:right'>Reading Plan Instance: </th><td><select name='planinstance'>$instances</select></td></tr>\n");
    echo("<tr><th style='text-align:right'>Full Day or Time Slot: </th><td><select value='timeorday'>");
    echo("<option value='day'>Full Day</option><option value='timeslot'>Time Slot</option></select></td></tr>\n");
    echo("<tr><th style='text-align:right'>Start of Time Slot (hhmm): </th><td><input name='starttimeofday' type='text' style='width:75px' placeholder='0900'</td></tr>\n");
    echo("<tr><th style='text-align:right'>End of Time Slot (hhmm): </th><td><input name='endtimeofday' type='text' style='width:75px' placeholder='1000'</td></tr>\n");
    echo("<tr><th colspan='2'><input name='buildfile' type='submit' value='Download Calendar' /></th></tr>\n");
    echo("</table></form>\n");

    include('footer.php');
  }
?>
