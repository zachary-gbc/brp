<?php include('pretitle.php'); ?>
<title>Delete Days</title>
<?php include('posttitle.php'); ?>

<h3 style="margin: 3px 0px 0px 5px">Delete Days</h3><br>
<?php
	if(isset($_POST['confirm']))
	{
		$instance=$_POST['deleteinstance']; $items=$_POST['items']; $startdate=""; $enddate="";
		if($_POST['startdate'] != "") { $startdate=(" AND RP_Date >= '" . date("Ymd",strtotime($_POST['startdate'])) . "'"); }
		if($_POST['enddate'] != "") { $enddate=(" AND RP_Date <= '" . date("Ymd",strtotime($_POST['enddate'])) . "'"); }

		$delete="DELETE FROM ReadingPlanDays WHERE RP_PlanInstance='$instance' $startdate $enddate";
		if(!mysqli_query($db,$delete)) { echo("Unable to Run Query: $delete"); exit; }

		echo("<h3>$items Deleted</h3>\n");
	}
	elseif(isset($_POST['submit']))
	{
		$instance=$_POST['deleteinstance']; $startdate=""; $enddate="";
		if($_POST['startdate'] != "") { $start=$_POST['startdate']; $startdate=(" AND RP_Date >= '" . date("Ymd",strtotime($_POST['startdate'])) . "'"); }
		if($_POST['enddate'] != "") { $end=$_POST['enddate']; $enddate=(" AND RP_Date <= '" . date("Ymd",strtotime($_POST['enddate'])) . "'"); }

		$itemstodelete="SELECT COUNT(RP_ID) AS NumItems FROM ReadingPlanDays WHERE RP_PlanInstance='$instance' $startdate $enddate"; $items=0;
		if(!$rs=mysqli_query($db,$itemstodelete)) { echo("Unable to Run Query: $itemstodelete"); exit; }
		while($row = mysqli_fetch_array($rs)) { $items=$row['NumItems']; }

		echo("<form method='post' action=''>\n");
		echo("<h3 style='color:red'>Be Extremely Careful, Deletes Cannot Be Undone!</h3><br>\n");
		echo("<h3>$items Items will be permanently deleted, are you sure?</h3>\n");
		echo("<input type='hidden' name='deleteinstance' value='$instance' /><input type='hidden' name='startdate' value='$start' />");
		echo("<input type='hidden' name='enddate' value='$end' /><input type='hidden' name='items' value='$items' />\n");
		echo("<br><input type='submit' name='confirm' value='Yes Delete Days' />\n</form>\n");

	}
	else
	{
		$getinstances="SELECT RP_PlanInstance FROM ReadingPlanDays GROUP BY RP_PlanInstance ORDER BY RP_PlanInstance"; $instances="";
		if(!$rs=mysqli_query($db,$getinstances)) { echo("Unable to Run Query: $getinstances"); exit; }
		while($row = mysqli_fetch_array($rs)) { $instances.=("<option value='" . $row['RP_PlanInstance'] . "'>" . $row['RP_PlanInstance'] . "</option>"); }

		echo("<form method='post' action=''>\n");
		echo("<h3 style='color:red'>Be Extremely Careful, Deletes Cannot Be Undone!</h3><br>\n");
		echo("Instance: <select name='deleteinstance'><option value='Select Instance to Delete'>Select Instance to Delete</option>$instances</select><br>\n");
		echo("Date Range Start: <input type='date' name='startdate' /><br>\n");
		echo("Date Range End: <input type='date' name='enddate' /><br>\n");
		echo("<br><br><input type='submit' name='submit' value='Delete Days' />\n</form>\n");
	}
?>

<?php include('footer.php'); ?>
