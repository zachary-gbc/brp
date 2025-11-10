<?php include('pretitle.php'); ?>
<title>Add Days</title>
<?php include('posttitle.php'); ?>

<h3 style="margin: 3px 0px 0px 5px">Add Days</h3>
<?php
	if(isset($_POST['submit']))
	{
		$days=explode(PHP_EOL,$_POST['adddays']); $values="";
		foreach($days as $day)
		{
			$items=explode(" , ",$day);
			$instance=trim(str_replace("'","''",$items[0]));
			$day=trim(str_replace("'","''",$items[1]));
			$verses=trim(str_replace("'","''",$items[2]));
			$version=trim(str_replace("'","''",$items[3]));
			$notes=trim(str_replace("'","''",$items[4]));
			$notes=trim(str_replace("\\","\\\\",$notes));

			if($instance != "" && $day != "" && $verses != "" && $version != "")
			{ $values.="('$instance', '$day', '$verses', '$version', '$notes', now()),"; }
		}

		if($values != "")
		{
			$insert=("INSERT INTO ReadingPlanDay(RP_PlanInstance, RP_Date, RP_Verses, RP_Version, RP_Notes, RP_UpdateDateTime) VALUES " . substr($values,0,-1) . ";");
			if(!mysqli_query($db,$insert)) { echo("Unable to Run Query: $insert"); exit; }
			else { echo("<h2>Days Added Successfully<br><br></h3><a href='editdays.php'>Click to View/Edit Days</a>"); }
		}
	}
	else
	{
		echo("<form method='post' action=''>\n");
		echo("Days can be added by pasting them into the textbox below. Please use the following format for all days to be added:<br>");
		echo("Instance Name , Day (formatted yyyymmdd) , Verses , Version , Notes<br>");
		echo("Example: All Church Reading Plan , 20260101 , Genesis 1:1-4 , CSB , What happened in this passage?\\nWhere would you apply this?<br>");
		echo("Notes:<br><ul><li>Make sure that each item is separated by a comma and a space before and after the comma</li>");
		echo("<li>If you need to include a line break in the notes, you must include \"\\n\"</li></ul>");
		echo("<br><br>Add Days:<br><textarea style='width:500px;height:200px' name='adddays'></textarea>");
		echo("<br><br><input type='submit' name='submit' value='Submit Changes' />\n</form>\n");
	}
?>

<?php include('footer.php'); ?>
