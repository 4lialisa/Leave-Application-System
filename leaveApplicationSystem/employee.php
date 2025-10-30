<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard - Leave Application System</title>
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

.container, .employee-container {
    flex: 1; /* Both containers will take equal space */
    margin: 0 10px; /* Margin for separation */
    padding: 20px;
    background-color: #fff;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
}

.container h1, .employee-container h1 {
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

.employee-content {
    display: flex;
    justify-content: space-around;
    align-items: center;
    flex-wrap: wrap;
}

.employee-content div {
    width: calc(50% - 20px);
    margin-bottom: 10px;
    padding: 15px;
    background-color: #f1f1f1;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.employee-content div h2 {
    color: #333;
    margin: 0;
    font-size: 18px;
}

.employee-content div span {
    color: #666;
    font-weight: bold;
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

.btn-cancel {
    background-color: #666666;
    color: white;
}

.welcome-container {
	display: flex;
	justify-content: space-between;
	align-items: center;
	margin: 0px 20px 0px 20px;
}
		
.button-container button {
	margin-left: 10px; /* Adjust spacing between buttons */
}

.button-container {
    display: inline-flex;
    gap: 10px;
    margin-left: 10px;
}

button {
    padding: 12px 24px;
    background-color: #ff6f61;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

button:hover {
    background-color: #e65550;
}

.info {
	display: inline-block;
	margin: 0px 5px 0px 5px;
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
	<?php 
		$errorMessage = "";
		function statusColour($status) {
			if($status == "Approved"){
				return "#00A305";
			}
			else if($status == "Rejected") {
				return "#BF0013";
			}
			else if($status == "Cancelled") {
				return "#9B9B9B";
			}
			else {
				return "black";
			}
		}
		
		// Check if session is established and employee_id is saved in user device
		if(isset($_COOKIE["session_num"])&& isset($_COOKIE["employee_id"])) {
			$session_num = $_COOKIE["session_num"];
			$employee_id = $_COOKIE["employee_id"];
			
			$conn = new mysqli("localhost", "root", "", "leaveapplicationsystem");
		
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			}
			
			// Get session number from database
			$db_query = $conn->prepare("SELECT session_num FROM login WHERE user = ?;");
			$db_query->bind_param('s', $employee_id);
			if(!($result = $db_query->execute())) {
				$errorMessage = "Server error, please contact administrator.";
				$conn->close();
			}
			$db_query->bind_result($server_session_num);
			$db_query->fetch();
			
			// Compare the session number in user device and in database
			if($server_session_num == $session_num) {
				
				// If session number is true, get employee information
				$db_query->close();
				$db_query = $conn->prepare("SELECT employee_id, employee_name, department, position, annual_leave, sick_leave, hospitalisation_leave FROM employee WHERE employee_id = ?;");
				$db_query->bind_param('s', $employee_id);
				
				if($result = $db_query->execute()) {
					$db_query->bind_result($employeeID, $employeeName, $department, $position, $annualLeave, $sickLeave, $hospitalisationLeave);
					$db_query->fetch();
					$db_query->close();
				}
				else {
					$errorMessage = "Your record could not be retrieved. Please contact administrator to solve the problem.";
				}
				
				// Get current month and year
				$today = new DateTime();
				$currentYear = $today->format("Y");
				$currentMonth = $today->format("m");
				
				// Get total unpaid leave by employee in this year
				$db_query = $conn->prepare("SELECT SUM(unpaidLeave) FROM application WHERE employeeID = ? AND YEAR(actionDate) = ?;");
				$db_query->bind_param('ss', $employeeID, $currentYear);
				$db_query->execute();
				$db_query->bind_result($unpaidInYear);
				$db_query->fetch();
				$db_query->close();
				
				// If SUM() return null, then change it to 0
				if($unpaidInYear == "") {
					$unpaidInYear = 0;
				}
				
				// Get total unpaid leave by employee in this month
				$db_query = $conn->prepare("SELECT SUM(unpaidLeave) FROM application WHERE employeeID = ? AND MONTH(actionDate) = ?;");
				$db_query->bind_param('ss', $employeeID, $currentMonth);
				$db_query->execute();
				$db_query->bind_result($unpaidInMonth);
				$db_query->fetch();
				$db_query->close();
				
				if($unpaidInMonth == "") {
					$unpaidInMonth = 0;
				}
			}
			else {
				$errorMessage = "Your session is expired. Please login again.";
			}
		}
		else {
			$errorMessage = "Invalid cookies found in your device. Please login again.";
		}
		
	// Handle approve/reject function
	if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'], $_POST['application_id'])) {
		$action = $_POST['action'];
		$applicationId = $_POST['application_id'];
		
		// Get application date to preserve from auto-updating
		$timeQuery = $conn->prepare("SELECT applicationDate FROM application WHERE id = ?");
		$timeQuery->bind_param('i', $applicationId);
		$timeQuery->execute();
		$timeQuery->bind_result($applicationDate);
		$timeQuery->fetch();
		$timeQuery->close();
		
		if($action == "cancel") {
			$status = 'Cancelled';
			$updateSql = "UPDATE application SET status='$status', applicationDate='$applicationDate', actionDate=NOW() WHERE id=$applicationId";
			if(!($conn->query($updateSql))) {
				echo "Error updating record: " . $conn->error;
			}
		}
	}
	
	?>
</head>
<body>
	
	<?php 
		// To display PHP error message and stop loading body
		if($errorMessage != "") {
			print "<script>document.body.style.backgroundColor=\"lightgrey\";</script>";
			print "<div class=\"container\" style=\"max-width:500px; margin-left:auto; margin-right:auto; text-align:center; margin-top:20px\"><h2>Employee Dashboard</h2><p>" . $errorMessage . "</p><button style=\"background-color: #ff6f61; color: white; padding: 12px 20px; margin: 10px; border: none; border-radius: 5px; cursor: pointer\" onclick=\"window.location.href='index.php'\">Login again</button></div></body></html>";
			exit();
		}
	?>
	
	<!-- For confirmation before cancel application -->
	<script type="text/javascript">
		function confirmation() {
			return confirm("Are you sure you want to cancel the application?");
		}
	</script>
	
    <div class="wrapper">
        <header>
            <h1>Employee Dashboard</h1>
            <div class="logout-btn">
                <a href="index.php?logout=true">Logout</a>
            </div>
        </header>
		
		<div class="welcome-container">
            <h1 style="margin-left: 10px">Welcome, <?php echo $employeeName; ?>!</h1>
            <div class="button-container">
				<form action="reset.php" method="post">
					<input type="hidden" name="previousPage" value=<?php echo basename($_SERVER["PHP_SELF"]) ?>>
					<button onclick="this.form.submit()">Reset Password</button>
				</form>
            </div>
		</div>
		
        <div class="dashboard-wrapper">
            <div class="container">
				<h1>Leave Application</h1>
				<div class="content">
					<div style="width:300px";>
						<h2>Apply for Leave</h2>
						<p>Submit your leave application online.</p>
						<a href="application.php">Apply Now</a>
					</div>
				</div>
            </div>

            <div class="employee-container">
                <h1>Employee Dashboard</h1>
                <div class="employee-content">
                    <div>
                        <h2>ID: <span id="employee-id"><?php echo $employeeID; ?></span></h2>
                    </div>
                    <div>
                        <h2>Department: <span id="employee-department"><?php echo $department; ?></span></h2>
                    </div>
                    <div>
                        <h2>Position: <span id="employee-position"><?php echo $position; ?></span></h2>
                    </div>
                </div>
            </div>
        </div>
		
		<div class="container">
			<div class="info" style="width: 58%">
            <table>
				<caption><h1>Leave Balance</h1></caption>
                <thead>
                    <tr>
                        <th>Annual Leave</th>
                        <th>Medical Leave</th>
                        <th>Hospitalisation Leave</th>
                    </tr>
                </thead>
                <tbody id="application-table">
					<tr>
						<td><?php echo $annualLeave; ?> days</td>
						<td><?php echo $sickLeave; ?> days</td>
						<td><?php echo $hospitalisationLeave; ?> days</td>
					</tr>
				</tbody>
            </table>
			</div>
			<div class="info" style="width: 38%">
			<table>
				<caption><h1>Unpaid Leave Taken</h1></caption>
                <thead>
                    <tr>
                        <th>in <?php echo $today->format('F'); ?></th>
                        <th>in <?php echo $currentYear; ?></th>
                    </tr>
                </thead>
                <tbody id="application-table">
					<tr>
						<td><?php echo $unpaidInMonth; ?> days</td>
						<td><?php echo $unpaidInYear; ?> days</td>
					</tr>
				</tbody>
            </table>
			</div>
        </div>

        <div class="container">
            <h1>Employee Application Records</h1>
			<div style="max-height: 250px; overflow-y: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Application ID</th>
                        <th>Leave Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
						<th>Paid Leave</th>
						<th>Unpaid Leave</th>
                        <th>Status</th>
						<th>Action</th>
                    </tr>
                </thead>
                <tbody id="application-table">
                    <?php
						$db_query = "SELECT id, leaveType, startDate, endDate, paidLeave, unpaidLeave, status, actionDate FROM application WHERE employeeID ='{$employee_id}'";
						$stmt = $conn->prepare($db_query);

						$stmt->execute();
						$historyResult = $stmt->get_result();
	
						if ($historyResult->num_rows > 0) {
							while ($historyRow = $historyResult->fetch_assoc()) {
								echo "<tr>
									<td>{$historyRow['id']}</td>
									<td>{$historyRow['leaveType']}</td>
									<td>{$historyRow['startDate']}</td>
									<td>{$historyRow['endDate']}</td>
									<td>{$historyRow['paidLeave']}</td>
									<td>{$historyRow['unpaidLeave']}</td>
									<td style=\"color: " . statusColour($historyRow['status']) . "; font-weight: bold\">{$historyRow['status']}</td>";
								
								// Add "Cancel" button if status is pending
								if($historyRow['status'] == "Pending") {
									echo '<td class="actions">
											<form method="post" onsubmit="return confirmation();">
												<input type="hidden" name="application_id" value="' . $historyRow["id"] . '">
												<button class="btn btn-cancel" type="submit" name="action" value="cancel">Cancel</button>
											</form>
										  </td>';
								}
								else {
									echo "<td>{$historyRow['status']} at {$historyRow['actionDate']}</td>";
								}
							
								echo "</tr>";
							}
						} else {
							echo "<tr><td colspan='8'>No application history found.</td></tr>";
						}
					?>
                </tbody>
            </table>
			</div>
        </div>
    </div>

	<?php $conn->close(); ?>

</body>
</html>
