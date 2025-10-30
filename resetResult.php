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
    .form-group {
        margin-bottom: 20px;
        text-align: left;
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

<div class="container">
    <?php 
	if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["newPassword"])) {
		// Get neccessary information submitted
		$previousPage = $_POST['previousPage'];
		$username = $_POST['employeeID'];
		$oldPassword = $_POST['oldPassword'];
		$newPassword = $_POST['newPassword'];
		
		// Get old password from the system
		$conn = new mysqli("localhost", "root", "", "leaveapplicationsystem");
		$db_query = $conn->prepare("SELECT password FROM login WHERE user = ?;");
		$db_query->bind_param('s', $username);
		$db_query->execute();
		$db_query->bind_result($old);
		$db_query->fetch();
		$db_query->close();
		
		if ($old != $oldPassword) {
			echo '<img src="cross.png" alt="Not successful" width=50px height=50px>' ;
			echo '<h2>Your password does not reset successfully</h2>';
			echo 'You did not entered your old password correctly. Please try to reset your password again.<br><br><br><br>';
			echo '<div class="form-group"><button type="button" style="float: left" onclick="window.location.href=\'' . $previousPage . '\'">Back to Dashboard</button></div>';
		}
		else {
			$db_query = $conn->prepare("UPDATE login SET password='$newPassword' WHERE user='$username'");

			$db_query->execute();
			echo '<img src="tick.png" alt="Successful" width=50px height=50px>';
			echo '<h2>Your password has been reset successfully</h2><br><br><br><br>';
			echo '<div class="form-group"><button type="button" style="float: left" onclick="window.location.href=\'' . $previousPage . '\'">Back to Dashboard</button></div>';
		}
		
		$conn->close();
	}
	else {
		print "<h2>Reset Password</h2><p>Unexpected problem is met. Try to login again to perform password reset</p><button type=\"button\" onclick=\"window.location.href='index.php'\">Login again</button>";
		exit();
	}
	
	?>
</div>

	
</body>
</html>


