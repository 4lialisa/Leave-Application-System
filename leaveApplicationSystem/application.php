<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Apply for Leave</title>
<style>
    body {
        font-family: 'Arial', sans-serif;
        background: lightgrey;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin-top: 150px;
    }
    .container {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        max-width: 500px;
        width: 100%;
        padding: 30px;
        text-align: center;
        margin-top: 140px;
    }
    h2 {
        color: #333;
        margin-bottom: 20px;
        font-size: 24px;
    }
    .form-group {
        margin-bottom: 20px;
        text-align: left;
    }
    .form-group label {
        display: block;
        font-weight: bold;
        margin-bottom: 5px;
        color: #333;
    }
    .form-group input,
    .form-group select,
    .form-group textarea {
        width: calc(100% - 20px);
        padding: 10px;
        margin: 5px 0;
        border-radius: 5px;
        border: 1px solid #ccc;
        font-size: 16px;
        box-sizing: border-box;
    }
    .form-group input[type="file"] {
        padding: 8px;
        border: 1px solid #ccc;
    }
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        border-color: #ff6f61;
        outline: none;
    }
    .form-group input[readonly] {
        background-color: #f1f1f1;
    }
    button {
        background-color: #ff6f61;
        color: white;
        padding: 12px 20px;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        margin: 10px 0;
        transition: background-color 0.3s ease;
    }
    button:hover {
        background-color: #e65550;
    }
</style>
</head>
<body>

<?php 

	$conn = new mysqli("localhost", "root", "", "leaveapplicationsystem");
	$errorMessage = "";
	
	// Display login button to user if no cookie is set
	if(isset($_COOKIE["session_num"])&& isset($_COOKIE["employee_id"])) {
		$session_num = $_COOKIE["session_num"];
		$employee_id = $_COOKIE["employee_id"];
	
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
	else {
		$errorMessage = "Oops! Necessary cookies are not found in your device. Please try login again.";
	}
	
	if($errorMessage != "") {
		print "<script>document.body.style.height=\"0vh\";</script>";
		print "<div class=\"container\" style=\"margin-top: 0px; \"><h2>Leave Application Form</h2><p>" . $errorMessage . "</p><button type=\"button\" onclick=\"window.location.href='index.php'\">Login again</button></div></body></html>";
		exit();
	}
	
	// Print added days to current date
	function addDays($num) {
		$today = new DateTime();
		$today->modify('+' . $num . ' days');
		echo $today->format('Y-m-d');
	}
	
	// Get employee's annual, medical and hospitalisation leave balance
	$db_query = $conn->prepare("SELECT annual_leave, sick_leave, hospitalisation_leave, employee_gender, TIMESTAMPDIFF(month, employment_date, NOW()) FROM employee WHERE employee_id = ?;");
	$db_query->bind_param('s', $_COOKIE["employee_id"]);
	$db_query->execute();
	$db_query->bind_result($emp_annual, $emp_medical, $emp_hospital, $emp_gender, $emp_employedMonth);
	$db_query->fetch();
?>

<div class="container">
    <h2>Leave Application Form</h2>
    <form id="leaveForm" enctype="multipart/form-data" action="summary.php" method="post" onsubmit="return validate()">
        <div class="form-group">
            <label for="employeeID">Employee ID:</label>
            <input type="text" id="employeeID" name="employeeID" value=<?php echo $_COOKIE["employee_id"]?> readonly>
        </div>
        <div class="form-group">
            <label for="startDate">Start Date:</label>
            <input type="date" id="startDate" name="startDate" min=<?php echo date("Y-m-d")?> value=<?php echo date("Y-m-d")?> onblur="calculateDuration()" required>
        </div>
        <div class="form-group">
            <label for="endDate">End Date:</label>
            <input type="date" id="endDate" name="endDate" min=<?php echo date("Y-m-d")?> onblur="calculateDuration()" required>
        </div>
        <div class="form-group">
            <label for="leaveType">Leave Type:</label>
            <select id="leaveType" name="leaveType" onChange="restrict(); calculateDuration()" required>
                <option value="">Select Leave Type</option>
                <option value="Annual">Annual Leave</option>
                <option value="Medical">Medical Leave</option>
                <option value="Hospitalisation">Hospitalisation Leave</option>
				<?php 
					if($emp_gender == 'F') {
						echo '<option value="Maternity">Maternity Leave</option>';
					}
					else {
						echo '<option value="Paternity">Paternity Leave</option>';
					}
				?>
                <option value="Emergency">Emergency Leave</option>
            </select>
        </div>
        <div class="form-group">
            <label for="remarks">Remarks (Optional):</label>
            <textarea id="remarks" name="remarks" rows="4"></textarea>
        </div>
        <div class="form-group">
            <label for="attachment">Attachment (Optional):</label>
            <input type="file" id="attachment" name="attachment">
        </div>
        <div class="form-group">
            <label for="duration" id="duration-label">Leave Duration (working days):</label>
            <input type="text" id="duration" name="duration" readonly>
        </div>
		<div class="form-group" style="width: 49%; display: inline-block">
            <label for="duration">Estimated Paid Leave:</label>
            <input type="text" id="paid" name="paid" readonly>
        </div>
		<div class="form-group" style="width: 49%; display: inline-block">
            <label for="duration">Estimated Unpaid Leave:</label>
            <input type="text" id="unpaid" name="unpaid" readonly>
        </div>
        <div class="form-group">
            <button type="submit">Submit Application</button>
        </div>
        <div class="form-group">
            <button type="button" onclick="window.location.href='employee.php'">Back</button>
        </div>
    </form>
</div>

<script>
// Check if the start date is today or later
function checkStartDate(startDate, todayDate) {
	if(startDate != null) {
		if(startDate < todayDate) {
			document.getElementById('startDate').value = "";
			alert("Your application's start date has past. Please set a new date.");
			return false;
		}
	}
	return true;
}

// Check if the end date is today or later
function checkEndDate(endDate, todayDate) {
	if(endDate != null) {
		if(endDate < todayDate) {
			document.getElementById('endDate').value = "";
			alert("Your application's end date has past. Please set a new date.");
			return false;
		}
	}
	return true;
}

function calculateDuration() {
	// Get the date value
    const startDate = new Date(document.getElementById('startDate').value);
    const endDate = new Date(document.getElementById('endDate').value);
	const now = new Date();
	const todayDate = new Date(now.getFullYear(), now.getMonth(), now.getDate());
	
	// Check the only filled field
	if(isNaN(startDate) || isNaN(endDate)) {
		
		if(!isNaN(startDate)) {
			checkStartDate(startDate, todayDate);
		}
		if(!isNaN(endDate)) {
			checkEndDate(endDate, todayDate);
		}
		return;
	}
    
	// Check valid date when both fields are filled
	if(!(checkStartDate(startDate, todayDate) && checkEndDate(endDate, todayDate))) {
		return;
	}
	
	// Check if the start date is later than the end date
	if(startDate > endDate) {
		document.getElementById('endDate').value = "";
		alert("Your application's end date is earlier than the start date. Please change a new end date for your application.");
		return;
	}
	
    // Calculate duration
    let daysDifference = Math.floor((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1;
    
    // Calculate weekends duration
    let startDay = startDate.getDay();
    let endDay = endDate.getDay();
    let weekendCount = 0;

    // Counting Saturdays and Sundays between startDate and endDate
    for (let i = startDate; i <= endDate; i.setDate(i.getDate() + 1)) {
        if (i.getDay() === 0 || i.getDay() === 6) {
            weekendCount++;
        }
    }
    
    // Exclude weekends
    let workingDays = daysDifference - weekendCount;
    
    // Update the duration 
    document.getElementById('duration').value = workingDays;
	
	// Calculate estimation of paid and unpaid leave
	var annualLeave = <?php echo $emp_annual; ?>;
	var sickLeave = <?php echo $emp_medical; ?>;
	var hospitalisationLeave = <?php echo $emp_hospital; ?>;
	var leaveType = document.getElementById("leaveType").value;
	var paidLeave = 0;
	var unpaidLeave = 0;
	var label = document.getElementById("duration-label");
	
	// Calculate paid and unpaid leave for different leave type
	if(leaveType == "Annual" || leaveType == "Emergency") {
		if(annualLeave >= workingDays) {
			paidLeave = workingDays;
		}
		else {
			paidLeave = annualLeave;
		}
		unpaidLeave = workingDays - paidLeave;
		label.innerHTML = "Leave Duration (working days):";
	}
	else if(leaveType == "Medical") {
		if(sickLeave >= workingDays) {
			paidLeave = workingDays;
		}
		else {
			paidLeave = sickLeave;
		}
		unpaidLeave = workingDays - paidLeave;
		label.innerHTML = "Leave Duration (working days):";
	}
	else if(leaveType == "Hospitalisation") {
		if(hospitalisationLeave >= workingDays) {
			paidLeave = workingDays;
		}
		else {
			paidLeave = hospitalisationLeave;
		}
		unpaidLeave = workingDays - paidLeave;
		label.innerHTML = "Leave Duration (working days):";
	}
	else if(leaveType == "Maternity" || leaveType == "Paternity") {
		// Maternity and Paternity leave duration includes Saturdays and Sundays
		document.getElementById('duration').value = daysDifference;
		// But paid leave exclude weekends
		paidLeave = workingDays;
		unpaidLeave = 0;
		
		label.innerHTML = "Leave Duration (including weekends):";
	}
	else {
		// For "Select Leave Type" with empty value
		unpaidLeave = 0;
		paidLeave =0;
		label.innerHTML = "Leave Duration (working days):";
	}
	
	// Update both values
	document.getElementById('paid').value = paidLeave;
	document.getElementById('unpaid').value = unpaidLeave;
}

function restrict() {
	// Get leave type
	var ind = document.getElementById("leaveType").selectedIndex;
	var leaveType = document.getElementById("leaveType").options[ind].value;
	
	if(leaveType == "Maternity" || leaveType == "Paternity") {
		
		var employedMonth = <?php echo $emp_employedMonth; ?>;
		
		if(leaveType == "Maternity") {
			if(employedMonth < 4) {
				document.getElementById("leaveType").selectedIndex = 0;
				alert("You are not eligible to take maternity leave as you are employed not more than or equal to 4 months.");
				return;
			}
		}
		else {
			if(employedMonth < 12){
				document.getElementById("leaveType").selectedIndex = 0;
				alert("You are not eligible to take paternity leave as you are employed not more than or equal to 12 months.");
				return;
			}
			
		}
		
		// Start date must be 30 days or more later than today
		document.getElementById("startDate").min ="<?php addDays(30); ?>";
		document.getElementById("startDate").value ="<?php addDays(30); ?>";
		document.getElementById("endDate").min ="<?php addDays(30); ?>";
	}
	else {
		// For other types of leave, start and end date must not already pass
		document.getElementById("startDate").min ="<?php echo date("Y-m-d"); ?>";
		//document.getElementById("startDate").value ="<?php echo date("Y-m-d"); ?>";
		document.getElementById("endDate").min ="<?php echo date("Y-m-d"); ?>";
	}	
}

function validate() {
	// Get leave type
	var ind = document.getElementById("leaveType").selectedIndex;
	var leaveType = document.getElementById("leaveType").options[ind].value;
	
	// Calculate days difference
	const startDate = new Date(document.getElementById('startDate').value);
    const endDate = new Date(document.getElementById('endDate').value);
	let daysDifference = Math.floor((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1;
	
	// Check if the duration for maternity leave is less than or equal to 98 days
	if(leaveType == "Maternity") {
		if(daysDifference > 98) {
			document.getElementById("endDate").value="";
			alert("The duration for maternity leave have a maximum limit of 98 days, including Saturdays and Sundays. Please change the start or end date accordingly.");
			return false;
		}
		return true;
	}
	// Check if the duration for paternity leave is less than or equal to 7 days
	else if(leaveType == "Paternity") {
		if(daysDifference > 7) {
			document.getElementById("endDate").value="";
			alert("The duration for paternity leave have a maximum limit of 7 days, including Saturdays and Sundays. Please change the start or end date accordingly.");
			return false;
		}
		return true;
	}
	// Else no more checking to be done
	else {
		return true;
	}
}
</script>

	<?php $conn->close(); ?>
</body>
</html>


