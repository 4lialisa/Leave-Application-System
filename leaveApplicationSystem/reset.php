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
        margin-top: 10px;
    }
    .container {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        max-width: 500px;
        width: 100%;
        padding: 30px;
        text-align: center;
        margin-top: 20px;
    }
    h2 {
        color: #333;
        margin-bottom: 20px;
        font-size: 24px;
    }
	h3 {
        color: #727475;
        margin-bottom: 20px;
        font-size: 14px;
		text-align: left;
    }
	table {
		margin: 0px 30px 0px 30px;
	}
	td {
		font-size: 11px;
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
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        border-color: #ff6f61;
        outline: none;
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
	.green{
		color: #01C141;
	}
	.gray{
		color: #AAAEAF;
	}
</style>
</head>
<body>

<?php 
	if($_SERVER["REQUEST_METHOD"] == "POST") {
		// Save previous page link to go back
		$previousPage = $_POST['previousPage'];
	}
	
	$errorMessage = "";
	$conn = new mysqli("localhost", "root", "", "leaveapplicationsystem");
	
	// Display login button to user if no cookie is set 
	if(isset($_COOKIE["employee_id"]) || isset($_COOKIE["admin"])) {
		$uesrname = "";
		$session_num = "";
		
		// Check if this page is accessed from employee dashboard or admin dashboard
		if($previousPage == "employee.php") {
			$username = $_COOKIE["employee_id"];
			$session_num = $_COOKIE["session_num"];
						
			// Retrieve employee name for display purpose
			$db_query = $conn->prepare("SELECT employee_name FROM employee WHERE employee_id = ?;");
			$db_query->bind_param('s', $username);
			$db_query->execute();
			$db_query->bind_result($name);
			$db_query->fetch();
			$db_query->close();
		}	
		else {
			$username = $_COOKIE["admin"];
			$session_num = $_COOKIE["admin_session"];
			$name = "Glamadmin";
		}
		
		
		$db_query = $conn->prepare("SELECT session_num FROM login WHERE user = ?;");
		$db_query->bind_param('s', $username);
		$db_query->execute();
		$db_query->bind_result($server_session_num);
		$db_query->fetch();
		$db_query->close();
		
		if ($server_session_num != $session_num) {
			$errorMessage = "Oops! Invalid cookies found in your device. Please login again to reset the password.";
		}
	}
	else {
		$errorMessage = "The necessary information is missing from your device. Please login again to reset the password.";
	}
	
	$conn->close();
	
	if ($errorMessage != "") {
		print "<script>document.body.style.height=\"20vh\";</script>";
		print "<div class=\"container\" style=\"margin-top: 150px; \"><h2>Reset Password</h2><p>" . $errorMessage . "</p><button type=\"button\" onclick=\"window.location.href='index.php'\">Login again</button></div></body></html>";
		exit();
	}	
?>

<div class="container">
    <h2>Reset Password</h2>
    <form id="resetForm" action="resetResult.php" method="post" onSubmit="return validate()">
		<input type="hidden" name="employeeID" value="<?php echo $username?>">
		<input type="hidden" name="previousPage" value="<?php echo $previousPage ?>">
        <h3>Hi, <?php echo $name ?>. Please key in your old password and new password for resetting purpose.</h3>
		<br>
        <div class="form-group">
            <label for="oldPassword">Old Password:</label>
            <input type="password" id="oldPassword" name="oldPassword" required>
        </div>
        <div class="form-group" style="margin-bottom: 5px">
            <label for="newPassword">New Password:</label>
            <input type="password" id="newPassword" name="newPassword" onInput="strengthCheck()" required>
        </div>
        <div class="form-group">
            <table>
				<tr>
					<td id="check1" class="gray">&nbsp;&nbsp;&nbsp;</td>
					<td id="checkName1" class="gray">Password length should be 8 to 15 characters<td>
				</tr>
				<tr>
					<td id="check2" class="gray">&nbsp;&nbsp;&nbsp;</td>
					<td id="checkName2" class="gray">Password should have a combination of Lowercase and Uppercase letters<td>
				</tr>
				<tr>
					<td id="check3" class="gray">&nbsp;&nbsp;&nbsp;</td>
					<td id="checkName3" class="gray">Password should consist of at least a number<td>
				</tr>
				<tr>
					<td id="check4" class="gray">&nbsp;&nbsp;&nbsp;</td>
					<td id="checkName4" class="gray">Password should consist of at least a special character<td>
				</tr>
			</table>
        </div>
		<div class="form-group">
            <label for="retypePassword">Confirm New Password:</label>
            <input type="password" id="retypePassword" name="retypePassword" placeholder="Retyped your new password here" required>
        </div>
		<div>
			<div class="form-group">
				<button type="submit" style="float: right">Reset Password</button>
			</div>
			<div class="form-group">
				<button type="button" style="float: left" onclick="window.location.href='<?php echo $previousPage ?>'">Back</button>
			</div>
		</div>
    </form>
</div>

<script>
	function setCondition(element1, element2, condition) {
		if(condition == 1) {
			document.getElementById(element1).innerHTML = "&check;"
			document.getElementById(element1).className = "green";
			document.getElementById(element2).className = "green";
		}
		else {
			document.getElementById(element1).innerHTML = "&nbsp;&nbsp;&nbsp;"
			document.getElementById(element1).className = "gray";
			document.getElementById(element2).className = "gray";
		}
	}
	
	function isLower(ch) {
		return (ch >= "a" && ch <= "z");
	}
	
	function isUpper(ch) {
		return (ch >= "A" && ch <= "Z");
	}
	
	function passwordLength(password) {
		var bool = 0;
		if (password.length >= 8 && password.length <= 15) {
			bool = 1;
		}
		setCondition("check1", "checkName1", bool);
		return bool;
	}
	
	function lowerAndUpper(password) {
		var bool = 0;
		for (var i = 0; i < password.length; i++) {
			if (isLower(password.charAt(i))) {
				bool += 1;
				break;
			}
		}
		for (var i = 0; i < password.length; i++) {
			if (isUpper(password.charAt(i))) {
				bool += 1;
				break;
			}
		}
		bool /= 2;
		setCondition("check2", "checkName2", bool);
		return bool;
	}
	
	function number(password) {
		var bool = 0;
		for (var i = 0; i < password.length; i++) {
			if (!isNaN(password.charAt(i))) {
				bool = 1;
				break;
			}
		}
		setCondition("check3", "checkName3", bool);
		return bool;
	}
	
	function special(password) {
		var bool = 0;
		var ch = "";
		for (var i = 0; i < password.length; i++) {
			ch = password.charAt(i);
			if (isNaN(ch) && !isLower(ch) && !isUpper(ch)) {
				bool = 1;
				break;
			}
		}
		setCondition("check4", "checkName4", bool);
		return bool;
	}
	
	function strengthCheck() {
		var password = document.getElementById("newPassword").value;
		
		var conditionMet = 0;
		conditionMet += passwordLength(password);
		conditionMet += lowerAndUpper(password);
		conditionMet += number(password);
		conditionMet += special(password);
		
		return conditionMet;
	}
	
	function resetNewField() {
		document.getElementById("newPassword").value = "";
		document.getElementById("retypePassword").value = "";
		setCondition("check1", "checkName1", 0);
		setCondition("check2", "checkName2", 0);
		setCondition("check3", "checkName3", 0);
		setCondition("check4", "checkName4", 0);
	}
	
	function validate() {
		if (strengthCheck() < 4) {
			alert("The new password does not meet all requirements. Please change your new password accordingly.");
			resetNewField();
			return false;
		}
		else {
			var newPassword = document.getElementById("newPassword").value;
			var retypePassword = document.getElementById("retypePassword").value;
			if(retypePassword != newPassword) {
				alert("The retyped password does not matched with the new password entered. Please type and retype the new password again.");
				resetNewField();
				return false;
			}
			else {
				return true;
			}
		}
	}
</script>
</body>
</html>


