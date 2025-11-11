<?php
  if(isset($_GET['buildfile']))
  {
    include('dblogin.php');

    $content=""; $timeframe="";
    if(isset($_GET['planinstance'])) { $planinstance=$_GET['planinstance']; } else { $planinstance=""; }
    if(isset($_GET['timeframe']) && $_GET['timeframe'] != "all")
    {
      $startday=date("Ymd");
      $endday=date("Ymd",mktime(1,1,1,(date("m")+$_GET['timeframe']),date("d"),date("Y")));
      $timeframe="AND (RP_Date > '$startday') AND (RP_Date < '$endday')";
    }

    $days="SELECT * FROM ReadingPlanDay WHERE (RP_PlanInstance='$planinstance') $timeframe";
    if(!$rs=mysqli_query($db,$days)) { echo("Unable to Run Query: $days"); exit; }
    while($row = mysqli_fetch_array($rs))
    {
      $content.=("<div id='reading-plan-" . $row['RP_Date'] . "' style='border-bottom:1px dashed black;margin-bottom:15px'>\n");
      $content.=("<h4 style='margin:0px;font-family:Source Sans Pro'>" . date("F jS", strtotime($row['RP_Date'])) . "</h4>\n");
      $content.=("<p style='margin:5px;font-family:Source Sans Pro'><a href='https://www.biblegateway.com/passage/?search=" . str_replace(" ","%20",$row['RP_Verses']) . "&version=" . $row['RP_Version'] . "'>" . $row['RP_Verses'] . "</a></p>\n");
      $content.=("<p style='font-family:Source Sans Pro'>" . str_replace("\\n","<br>",($row['RP_Notes'])) . "</p>\n");
      $content.=("</div>\n");
    }

    if($content != "")
    {
      header("Content-type: text/plain");
      header("Content-Disposition: attachment; filename=reading-plan.txt");
      echo($content);

    }
    else { echo("<h2>No Items Available</h2>\n"); }
  }
  else
  {
    include('pretitle.php');
    echo("<title>Create HTML File</title>");
    include('posttitle.php');

    $getinstances="SELECT RP_PlanInstance FROM ReadingPlanDay GROUP BY RP_PlanInstance ORDER BY RP_PlanInstance"; $instances="";
    if(!$rs=mysqli_query($db,$getinstances)) { echo("Unable to Run Query: $getinstances"); exit; }
    while($row = mysqli_fetch_array($rs)) { $instances.=("<option value='" . $row['RP_PlanInstance'] . "'>" . $row['RP_PlanInstance'] . "</option>"); }

    echo("<h3 style='margin: 3px 0px 0px 5px'>Download HTML File</h3><br>");
    echo("<form method='get' action=''>\n");
    echo("Reading Plan Instance: <select name='planinstance'>$instances</select>\n");
    echo("Time Limit: <select name='timelimit'><option value='all'>All</option><option value='1'>One Month</option>");
    echo("<option value='2'>Two Months</option><option value='4'>Four Months</option><option value='6'>Six Months</option></select>\n");
    echo("<br><input name='buildfile' type='submit' value='Download HTML File' />\n");
    echo("</form>\n");

    include('footer.php');
  }
?>
