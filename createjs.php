<?php
  if(isset($_GET['buildfile']))
  {
    include('dblogin.php');

    $content="";
    if(isset($_GET['planinstance'])) { $planinstance=$_GET['planinstance']; } else { $planinstance=""; }

    $days="SELECT * FROM ReadingPlanDay WHERE (RP_PlanInstance='$planinstance')";
    if(!$rs=mysqli_query($db,$days)) { echo("Unable to Run Query: $days"); exit; }
    while($row = mysqli_fetch_array($rs))
    {
      $content.=("<div id='reading-plan-" . $row['RP_Date'] . "' style='display:none'>\n");
      $content.=("<h4 style='font-family:Source Sans Pro'>" . date("F jS", strtotime($row['RP_Date'])) . "</h4>\n");
      $content.=("<p style='font-family:Source Sans Pro'><a href='https://www.biblegateway.com/passage/?search=" . str_replace(" ","%20",$row['RP_Verses']) . "&version=" . $row['RP_Version'] . "'>" . $row['RP_Verses'] . "</a></p>\n");
      $content.=("<p style='font-family:Source Sans Pro'>" . str_replace("\\n","<br>",($row['RP_Notes'])) . "</p>\n");
      $content.=("</div>\n");
    }

    if($content != "")
    {
      header("Content-type: text/plain");
      header("Content-Disposition: attachment; filename=reading-plan.txt");

      echo($content);
      echo("<script type='text/javascript'>\n");
      echo("var today = new Date();\nvar dd = today.getDate();\nvar mm = today.getMonth() + 1;\nvar yyyy = today.getFullYear();\n");
      echo("if (dd < 10) dd = '0' + dd;\nif (mm < 10) mm = '0' + mm;\n");
      echo("todayformatted = yyyy.toString() + mm.toString() + dd.toString();\ndivid=\"reading-plan-\" + todayformatted\n");
      echo("document.getElementById(divid).style.display='block';\n");
      echo("</script>\n");

    }
    else { echo("<h2>No Items Available</h2>\n"); }
  }
  else
  {
    include('pretitle.php');
    echo("<title>Create Javascript File</title>");
    include('posttitle.php');

    $getinstances="SELECT RP_PlanInstance FROM ReadingPlanDay GROUP BY RP_PlanInstance ORDER BY RP_PlanInstance"; $instances="";
    if(!$rs=mysqli_query($db,$getinstances)) { echo("Unable to Run Query: $getinstances"); exit; }
    while($row = mysqli_fetch_array($rs))
    { $instances.=("<option value='" . $row['RP_PlanInstance'] . "'>" . $row['RP_PlanInstance'] . "</option>"); }

    echo("<h3>Download Javascript File</h3>\n");
    echo("<form method='get' action=''>");
    echo("Reading Plan Instance: <select name='planinstance'>$instances</select>");
    echo("<br><input name='buildfile' type='submit' value='Download Javascript File' />\n");
    echo("</form>\n");

    include('footer.php');
  }
?>
