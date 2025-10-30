<?php
// Include file to call its function
include 'google-calendar-event.php';

// Database details
$host = "localhost";
$username = "root"; 
$password = "";
$dbname = "leaveapplicationsystem";

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$errorMessage = "";

// Check if session is established and employee_id is saved in user device
if(isset($_COOKIE["admin_session"])&& isset($_COOKIE["admin"])) {
	$session_num = $_COOKIE["admin_session"];
	$employee_id = $_COOKIE["admin"];
	if($employee_id != "glamadmin") {
		$errorMessage = "Oops! Your session cookie is invalid. Please try login again.";
	}
	
	// Get session number from database
	$db_query = $conn->prepare("SELECT session_num FROM login WHERE user = ?;");
	$db_query->bind_param('s', $employee_id);
	$db_query->execute();
	$db_query->bind_result($server_session_num);
	$db_query->fetch();
	$db_query->close();
	
	//Compare if the session number is valid
	if($server_session_num != $session_num) {
		$errorMessage = "Oops! Your session cookie is invalid. Please try login again.";
	}
}
else{
	$errorMessage = "Oops! Necessary cookies are not found in your device. Please try login again.";
}

// Handle approve/reject function
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'], $_POST['application_id'])) {
    $action = $_POST['action'];
    $applicationId = $_POST['application_id'];
	$leaveType = "";
	
	// Get leave type of the application
	$db_query = $conn->prepare("SELECT leaveType, paidLeave, employeeID, applicationDate, status FROM application WHERE id=?");
	$db_query->bind_param('s', $applicationId);
	$db_query->execute();
	$db_query->bind_result($leaveType, $paidLeave, $employeeID, $applicationDate, $status);
	$db_query->fetch();
	$db_query->close();
	
	if($status == "Approved") {
		try {
			// Remove leave from the productivity calendar
			remove_google_calendar_events($applicationId, $conn);
		}
		catch (\Exception $e) {
			echo "You are offline. Try to connect to internet.";
			exit();
		}
	
		// Determine whether any paid leave should be recovered
		if($leaveType == "Annual" || $leaveType == "Emergency") {
			$columnChange = "annual_leave";
		}
		else if($leaveType == "Medical") {
			$columnChange = "sick_leave";
		}
		else if($leaveType == "Hospitalisation") {
			$columnChange = "hospitalisation_leave";
		}
		else {
			$columnChange = "";
		}
		
		if($columnChange != "" && $paidLeave != 0) {
			$recoverSql = "UPDATE employee SET $columnChange = $columnChange + $paidLeave WHERE employee_id='$employeeID'";
			$conn->query($recoverSql);
		}
	}
	
    // Update status, reset paid and unpaid leave count, action date, and preserved application date from update automatically
    $status = 'Pending';
    $updateSql = "UPDATE application SET status='$status', paidLeave = NULL, unpaidLeave = NULL, applicationDate='$applicationDate', actionDate=NULL WHERE id=$applicationId";
	if(!($conn->query($updateSql))) {
		echo "Error updating record: " . $conn->error;
	}
}

// Default tab is leave application history
$tab = "history";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["tab"])) {
	$tab = $_POST["tab"];
}

// SQL statement to select data from application
$sql = "SELECT * FROM application WHERE status = 'Approved' OR status = 'Rejected'";
$result = $conn->query($sql);

function statusColour($status) {
	if($status == "Approved"){
		return "#00A305";
	}
	else if($status == "Rejected") {
		return "#BF0013";
	}
	else {
		return "black";
	}
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Leave Application System</title>
    <style>
body {
    font-family: 'Arial', sans-serif;
    margin: 0;
    padding: 0;
    background: #f8f8f8;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    min-height: 100vh;
}

.dashboard-link  {
    display: inline-block;
    margin-top: 20px;
    padding: 10px 20px;
    background-color: #ff6f61;
    color: white;
    text-decoration: none;
    border-radius: 5px;
}
.dashboard-link:hover {
    background-color: #e65550;
}

.wrapper {
    max-width: 1000px;
    width: 100%;
    background: #fff;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    overflow: hidden;
    padding-bottom: 20px; /* Added padding */
}

header {
    background-color: #ff6f61;
    color: white;
    padding: 20px;
    text-align: center;
}

header h1 {
    margin: 0;
    font-size: 32px;
    margin-top: -5px;
    margin-bottom: 15px;
}

.logout-btn {
    text-align: right;
    margin-top: -40px; 
}

.logout-btn a {
    color: white;
    padding: 8px 16px;
    text-decoration: none;
    background-color: #e65550;
    border-radius: 5px;
    transition: background-color 0.3s;
}

.logout-btn a:hover {
    background-color: #cc4a47;
}

.dashboard-wrapper {
    display: flex;
    justify-content: space-between;
    padding: 20px;
    margin-bottom: 20px; /* Added margin for separation */
}

.container {
    flex: 1; 
    margin: 0 10px; /* Margin for separation */
    padding: 20px;
    background-color: #fff;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
}

.container h1 {
    color: #333;
    text-align: center;
    margin-bottom: 20px;
    font-size: 24px;
}

.content {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
}

.content div {
    width: 100%;
    margin: 10px 0;
    padding: 20px;
    background-color: #f1f1f1;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.content div h2 {
    color: #333;
    margin-bottom: 10px;
    font-size: 20px;
}

.content div p {
    color: #666;
    margin-bottom: 20px;
    font-size: 16px;
}

.content div a {
    display: inline-block;
    margin-top: 10px;
    padding: 12px 24px;
    background-color: #ff6f61;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    transition: background-color 0.3s;
}

.content div a:hover {
    background-color: #e65550;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
}

table, th, td {
    border: 1px solid #ddd;
    padding: 12px;
    text-align: left;
}

th {
    background-color: #f2f2f2;
	position: sticky;
    top: 0;
    z-index: 2; /* Ensure the header stays above the table body */
    font-weight: bold;
}

td {
    color: #333;
}

th, td {
    padding: 15px;
}

th:first-child, td:first-child {
    padding-left: 20px;
}

th:last-child, td:last-child {
    padding-right: 20px;
}

.actions {
    justify-content: center;
}

.btn {
    padding: 8px 16px;
    margin: 0 4px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn-undo {
    background-color: #666666;
    color: white;
}

.navigation {
	margin: 10px 10px 10px 0px;
	padding: 12px 24px;
	color: #828282;
	background-color: white;
	font-weight: bold;
	border-radius: 10px;
	border: none;
	cursor: pointer;
    transition: background-color 0.3s;
}

.naviBar button.navigation:hover {
	color: black;
}

.selected {
	
	color: black;
	box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
}

.naviBar {
	margin-left: 10px; 
	display:inline-flex;
}

@media (max-width: 768px) {
    .dashboard-wrapper {
        flex-direction: column;
    }
    .container, .employee-container {
        width: calc(100% - 30px);
        margin: 15px;
    }
    .employee-content div {
        width: 100%;
        margin-bottom: 10px;
    }
}


    </style>
	
	<script type="text/javascript">
		function confirmation() {
			return confirm("Are you sure you want to undo the approval/rejection of the application?");
		}
	</script>
</head>
<body>
	<?php 
		// To display PHP error message and stop loading body
		if($errorMessage != "") {
			print "<script>document.body.style.backgroundColor=\"lightgrey\";</script>";
			print "<div class=\"container\" style=\"max-width:500px; margin-left:auto; margin-right:auto; text-align:center; margin-top: 20px\"><h2>Admin Dashboard</h2><p>" . $errorMessage . "</p><button style=\"background-color: #ff6f61; color: white; padding: 12px 20px; margin: 10px; border: none; border-radius: 5px; cursor: pointer\" onclick=\"window.location.href='index.php'\">Login again</button></div></body></html>";
			exit();
		}
		
	?>
		
    <div class="wrapper">
        <header>
            <h1>Admin Dashboard</h1>
            <div class="logout-btn">
                <a href="index.php?logout=true">Logout</a>
            </div>
        </header>
		<div class="naviBar">
			<form method="post">
				<input type="hidden" name="tab" value="history">
				<button onClick="this.form.submit()" id="history" class="navigation selected">Leave Application History</button>
			</form>
			<form method="post">
				<input type="hidden" name="tab" value="calendar">
				<button onClick="this.form.submit()" id="calendar" class="navigation">Productivity Calendar</button>
			</form>
		</div>
		
	<?php
	if($tab == "history") {
	echo '<script>document.getElementById("history").className = "navigation selected";
				  document.getElementById("calendar").className= "navigation";</script>';
	echo '<div class="container">
            <h1>Leave Applications History</h1>
			<div style="max-height: 500px; overflow-y: auto;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
						<th>Employee ID</th>
						<th>Start Date</th>
						<th>End Date</th>
						<th>Leave Type</th>
						<th>Duration</th>
						<th>Paid Leave</th>
						<th>Unpaid Leave</th>
						<th>Status</th>
						<th>Action</th>
                    </tr>
                </thead>
                <tbody id="application-table">';
                    
					if ($result->num_rows > 0) {
						// Display data of each row
						while($row = $result->fetch_assoc()) {
							echo "<tr>";
                            echo "<td><a href='appdetails.php?id=" . $row["id"] . "'>" . $row["id"] . "</a></td>";
							echo "<td>" . $row["employeeID"] . "</td>";
							echo "<td>" . $row["startDate"] . "</td>";
							echo "<td>" . $row["endDate"] . "</td>";
							echo "<td>" . $row["leaveType"] . "</td>";
							echo "<td>" . $row["duration"] . "</td>";
							echo "<td>" . $row["paidLeave"] . "</td>";
							echo "<td>" . $row["unpaidLeave"] . "</td>";
							echo "<td style=\"color:" . statusColour($row["status"]) . "; font-weight: bold\">" . $row["status"] . "</td>";
							echo '<td class="actions">';
							echo '<form method="post" onsubmit="return confirmation();">';
							echo '<input type="hidden" name="application_id" value="' . $row["id"] . '">';
							echo '<button class="btn btn-undo" type="submit" name="action" value="undo">Undo</button>';
							echo '</form>';
							echo '</td>';
							echo "</tr>";
						}
					} else {
						echo '<tr><td colspan="10">No applications found</td></tr>';
					}
                
                echo '</tbody>
            </table>
			<a href="admin.php" class="dashboard-link">Back to Dashboard</a>
			</div>
        </div>	
    </div>';
	}
	else {
		echo '<script>document.getElementById("history").className = "navigation";
				      document.getElementById("calendar").className= "navigation selected";</script>';
		echo '<div class="container content">
		
		<iframe src="https://calendar.google.com/calendar/embed?src=89ada0166e4e105cf755b09267e8667a63582168898f7b403aeeaecc10fa134b%40group.calendar.google.com&ctz=Asia%2FKuala_Lumpur" style="border: 0" width="800" height="600" frameborder="0" scrolling="no"></iframe>
		
		<a href="admin.php" class="dashboard-link">Back to Dashboard</a>
		
		</div>';
	}
	$conn->close();
?>

</body>
</html>
