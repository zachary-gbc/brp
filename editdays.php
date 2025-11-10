<?php include('pretitle.php'); ?>
<title>Edit Days</title>
<?php include('posttitle.php'); ?>

<h3 style="margin: 3px 0px 0px 5px">Edit Days</h3>
<?php
	if(isset($_POST['submit']))
	{
		$instance=$_POST['instance']; $instances=array(); $dates=array(); $verses=array(); $versions=array(); $notes=array(); $x=0; $y=0;
		$days="SELECT * FROM ReadingPlanDay WHERE (RP_PlanInstance='$instance')"; 
		if(!$rs=mysqli_query($db,$days)) { echo("Unable to Run Query: $days"); exit; }
		while($row = mysqli_fetch_array($rs))
		{
			$instances[$row['RP_ID']]=$row['RP_PlanInstance'];
			$dates[$row['RP_ID']]=$row['RP_Date'];
			$verses[$row['RP_ID']]=$row['RP_Verses'];
			$versions[$row['RP_ID']]=$row['RP_Version'];
			$notes[$row['RP_ID']]=$row['RP_Notes'];
		}

		while(isset($_POST["id$x"]))
		{
			$id=$_POST["id$x"];
			$instance=str_replace("'","''",$_POST["instance$x"]);
			$date=str_replace("'","''",$_POST["date$x"]);
			$verse=str_replace("'","''",$_POST["verses$x"]);
			$version=str_replace("'","''",$_POST["version$x"]);
			$note1=str_replace("'","''",$_POST["notes$x"]);
			$note=trim(str_replace("\\","\\\\",$note1));

			if(isset($_POST["delete$x"]))
			{
				$delete="DELETE FROM ReadingPlanDay WHERE (RP_ID='$id')";
				if(!$rs=mysqli_query($db,$delete)) { echo("Unable to Run Query: $delete"); exit; }
			}
			else
			{
				if($instance != $instances[$id] || $date != $dates[$id] || $verse != $verses[$id] || $version != $versions[$id] || $note1 != $notes[$id])
				{
					$update="UPDATE ReadingPlanDay SET RP_PlanInstance='$instance', RP_Date='$date', RP_Verses='$verse', RP_Version='$version', RP_Notes='$note', RP_UpdateDateTime=now() WHERE (RP_ID='$id')";
					if(!$rs=mysqli_query($db,$update)) { echo("Unable to Run Query: $update"); exit; } else { $y++; }
				}
			}
			$x++;
		}

		echo("<h3>$x Rows Processed, $y Updates Made<br></h3>\n");
	}
	elseif(isset($_GET['instance']))
	{
		$instance=$_GET['instance']; $table=""; $x=0;
		$days="SELECT * FROM ReadingPlanDay WHERE (RP_PlanInstance='$instance')";
		if(!$rs=mysqli_query($db,$days)) { echo("Unable to Run Query: $days"); exit; }
		while($row = mysqli_fetch_array($rs))
		{
			if($x%2 == 0) { $table.=("<tr class='tr_odd'>\n"); } else { $table.=("<tr class='tr_even'>\n"); }
			$table.=("<th><input type='hidden' name='id$x' value='" . $row['RP_ID'] . "' />" . $row['RP_ID'] . "</th>\n");
			$table.=("<td><input type='text' name='instance$x' value='" . $row['RP_PlanInstance'] . "' /></td>\n");
			$table.=("<td><input type='text' name='date$x' value='" . $row['RP_Date'] . "' /></td>\n");
			$table.=("<td><input type='text' name='verses$x' value='" . $row['RP_Verses'] . "' /></td>\n");
			$table.=("<td><input type='text' name='version$x' value='" . $row['RP_Version'] . "' /></td>\n");
			$table.=("<td><input type='text' name='notes$x' value='" . $row['RP_Notes'] . "' style='width:500px'	 /></td>\n");
			$table.=("<th><input type='checkbox' name='delete$x' /></th>\n");
			$table.=("</tr>"); $x++;
		}

		if($table != "")
		{
			echo("<form method='post' action=''>\n<input type='hidden' name='instance' value='$instance' />\n<table>\n");
			echo("<tr><th>ID</th><th>Instance</th><th>Date</th><th>Verses</th><th>Version</th><th>Notes</th><th>Delete</th></tr>\n");
			echo("$table</table>\n<br><br><input type='submit' name='submit' value='Submit Changes' />\n</form>\n");
		}
	}
	else
	{
		$getinstances="SELECT RP_PlanInstance FROM ReadingPlanDay GROUP BY RP_PlanInstance"; $instances="";
		if(!$rs=mysqli_query($db,$getinstances)) { echo("Unable to Run Query: $getinstances"); exit; }
		while($row = mysqli_fetch_array($rs)) { $instances.=("<option value='" . $row['RP_PlanInstance'] . "'>" . $row['RP_PlanInstance'] . "</option>"); }

		echo("<form method='get' action=''>\nReading Plan Instance: <select name='instance'>$instances</select>\n");
		echo("<br><br><input type='submit' name='submit' value='View Days' />\n</form>\n");
	}
?>

<?php include('footer.php'); ?>
