<?php

// Database information
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "leaveapplicationsystem";

// Create connection
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error_message = "";

// Check the form is submitted or not
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get username and password 
    $input_username = $_POST['username'];
    $input_password = $_POST['password'];
    
	// Prepare to get password from database
	$username = trim($input_username);
    $stmt = $conn->prepare("SELECT password FROM login WHERE user = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();   
    $stmt->bind_result($db_password);
        
    // Fetch the result
    if ($stmt->fetch()) {
        // Verify the password
        if ($input_password === $db_password) {
            $stmt->close();
             
            // Establish session by creating session number in database
            $session_num = rand(0,100000);
			$db_query = $conn->prepare("UPDATE login SET session_num =? WHERE user =?;");
			$db_query->bind_param('ss', $session_num, $username);
				
			try {
				// If session number saved in database, set cookie in user device
				if($result = $db_query->execute()) {
					
					// If username is "glamadmin" (administrator)
					if($username == "glamadmin") {
						// Set admin cookie
						setcookie("admin", $username);
						setcookie("admin_session", $session_num);
						
						// Then, direct user to admin dashboard
						header("Location: admin.php");
						exit();
					}
					// else the user is an employee
					else {
						// Set employee cookie
						setcookie("employee_id", $username);
						setcookie("session_num", $session_num);
						
						// Then, direct user to employee.php
						header("Location: employee.php");
						exit();
					}
				}
				else {
					$error_message = "Server error:" . $conn->error . "<br>Please login again later.";
				}
			}
			catch(mysqli_sql_exception $e){
				$errorMessage = $e->errorMessage();
			}
			
		} else {
            $error_message = "Oops! You have typed your username or password incorrectly. Please try again.";
        }
    } else {
        $error_message = "Oops! You have typed your username or password incorrectly. Please try again.";
    }
    $stmt->close();
}


// If the page is loaded from logout, clear cookies
if ($_SERVER["REQUEST_METHOD"] == "GET") {
	if(isset($_GET['logout'])) {
		if(isset($_COOKIE["session_num"])&& isset($_COOKIE["employee_id"])) {
			// Clear session number from database
			$employee_id = $_COOKIE["employee_id"];
			$stmt = $conn->prepare("UPDATE login SET session_num = NULL WHERE user = ?;");
			$stmt->bind_param('s', $employee_id);
			$stmt->execute();
			
			// Clear session number from user device
			setcookie("session_num", "", time()-3600);
			setcookie("employee_id", "", time()-3600);
		}
		if(isset($_COOKIE["admin_session"])&& isset($_COOKIE["admin"])) {
			// Clear session number from database
			$employee_id = $_COOKIE["admin"];
			$stmt = $conn->prepare("UPDATE login SET session_num = NULL WHERE user = ?;");
			$stmt->bind_param('s', $employee_id);
			$stmt->execute();
			
			// Clear session number from user device
			setcookie("admin_session", "", time()-3600);
			setcookie("admin", "", time()-3600);
		}
	}
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Leave Application System</title>

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f0f0f0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }
    .login-container {
        background-color: #ff6f61;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        padding: 20px;
        width: 500px;
    }
    h2 {
        text-align: center;
        margin-bottom: 20px;
        color: black;
    }
    .form-group {
        margin-bottom: 20px;
        margin-left: 1px;
        margin-right: 10px;
    }
    .form-group label {
        display: block;
        margin-bottom: 5px;
        color: #333;
        font-weight: bold;
    }
    .form-group input {
        width: 100%;
        padding: 8px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
    .form-group button {
        background-color: white;
        color: black;
        border: 1px solid #ccc;
        padding: 8px 16px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.3s ease;
    }
    .form-group button:hover {
        background-color: #ddd;
    }
    .form-footer {
        margin-top: 10px;
        text-align: center;
        font-size: 14px;
    }
    .form-footer a {
        color: #007bff;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    .form-footer a:hover {
        color: #0056b3;
    }
    .error-message {
        color: white;
        text-align: center;
        margin-top: 10px;
		background-color: #FF424E;
		border-radius: 20px;
		padding: 12px 20px;
		max-width: 500px;
		font-weight: bold;
    }
	p:first-letter {
		font-size: 20pt;
	}
</style>
</head>
<body>
<div class="login-container">
    <h2>Login to Leave Application System</h2>
    
    <form action="index.php" method="post">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="form-group">
            <button type="submit">Login</button>
        </div>
    </form>
    <?php
    if (!empty($error_message)) {
        echo '<p class="error-message">&#9888;<br>' . $error_message . '</p>';
    }
    ?>
</div>
</body>
</html>
