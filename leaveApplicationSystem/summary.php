<?php
// Check the form is submitted / or submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data using $_POST or $_FILES
    $employeeID = $_POST['employeeID'];
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];
    $leaveType = $_POST['leaveType'];
    $remarks = $_POST['remarks'];
    $duration = $_POST['duration'];
    
    // Handle file upload (if employee attached the file)
    $attachmentName = $_FILES['attachment']['name'];
    $attachmentTempName = $_FILES['attachment']['tmp_name'];
	$target_Path = "attachments/";
	$target_Path = $target_Path.basename( $attachmentName );
	move_uploaded_file( $attachmentTempName, $target_Path );
    
    $host = "localhost";
    $username = "root"; // Replace with your database username
    $password = ""; // Replace with your database password
    $dbname = "leaveapplicationsystem";

    // Create connection
    $conn = new mysqli($host, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // SQL to insert data into application table
    $sql = "INSERT INTO application (employeeID, startDate, endDate, leaveType, remarks, duration, attachmentName, paidLeave, unpaidLeave) 
            VALUES ('$employeeID', '$startDate', '$endDate', '$leaveType', '$remarks', '$duration', '$attachmentName', NULL, NULL)";

    // Execute SQL statement and check whether it is successful or not
    if ($conn->query($sql) === TRUE) {
        //Summary of employee application details
        echo '<!DOCTYPE html>';
        echo '<html lang="en">';
        echo '<head>';
        echo '<meta charset="UTF-8">';
        echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
        echo '<title>Leave Application Summary</title>';
        echo '<style>';
        echo '    body {';
        echo '        font-family: \'Arial\', sans-serif;';
        echo '        background: lightgrey;';
        echo '        display: flex;';
        echo '        justify-content: center;';
        echo '        align-items: center;';
        echo '        height: 100vh;';
        echo '        margin: 0;';
        echo '    }';
        echo '    .container {';
        echo '        background-color: #fff;';
        echo '        border-radius: 10px;';
        echo '        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);';
        echo '        max-width: 500px;';
        echo '        width: 100%;';
        echo '        padding: 30px;';
        echo '        text-align: center;';
        echo '        margin-top: 200px;'; // Adjust margin as needed
        echo '    }';
        echo '    h2 {';
        echo '        color: #333;';
        echo '        margin-bottom: 20px;';
        echo '        font-size: 24px;';
        echo '    }';
        echo '    .form-group {';
        echo '        margin-bottom: 20px;';
        echo '        text-align: left;';
        echo '    }';
        echo '    .form-group label {';
        echo '        display: block;';
        echo '        font-weight: bold;';
        echo '        margin-bottom: 5px;';
        echo '        color: #333;';
        echo '    }';
        echo '    .form-group span {';
        echo '        display: block;';
        echo '        padding: 10px;';
        echo '        margin: 5px 0;';
        echo '        border-radius: 5px;';
        echo '        background-color: #f1f1f1;';
        echo '        font-size: 16px;';
        echo '    }';
        echo '    .form-group span a {';
        echo '        text-decoration: underline;';
        echo '        color: #007bff;';
        echo '        cursor: pointer;';
        echo '    }';
        echo '    .form-group button {';
        echo '        background-color: #ff6f61;';
        echo '        color: white;';
        echo '        padding: 12px 20px;';
        echo '        border: none;';
        echo '        border-radius: 5px;';
        echo '        font-size: 16px;';
        echo '        cursor: pointer;';
        echo '        transition: background-color 0.3s ease;';
        echo '        margin-top: 20px;';
        echo '    }';
        echo '    .form-group button:hover {';
        echo '        background-color: #e65550;';
        echo '    }';
        echo '</style>';
        echo '</head>';
        echo '<body>';
        echo '<div class="container">';
		echo '    <h2 style="font-size: 16pt; background-color: #9BFFA8; padding: 20px; border-radius: 20px">Your application form is successfully submitted!</h2>';
        echo '    <h2>Leave Application Summary</h2>';
        echo '    <div class="summary-card">';
        echo '        <div class="form-group">';
        echo '            <label>Employee ID:</label>';
        echo '            <span>' . $employeeID . '</span>';
        echo '        </div>';
        echo '        <div class="form-group">';
        echo '            <label>Start Date:</label>';
        echo '            <span>' . $startDate . '</span>';
        echo '        </div>';
        echo '        <div class="form-group">';
        echo '            <label>End Date:</label>';
        echo '            <span>' . $endDate . '</span>';
        echo '        </div>';
        echo '        <div class="form-group">';
        echo '            <label>Leave Type:</label>';
        echo '            <span>' . $leaveType . '</span>';
        echo '        </div>';
        echo '        <div class="form-group">';
        echo '            <label>Remarks:</label>';
        echo '            <span>' . $remarks . '</span>';
        echo '        </div>';
        echo '        <div class="form-group">';
        echo '            <label>Leave Duration (days):</label>';
        echo '            <span>' . $duration . '</span>';
        echo '        </div>';
        echo '        <div class="form-group">';
        echo '            <label>Attachment:</label>';
        if (!empty($attachmentName)) {
            echo '            <span><a href="attachments/' . $attachmentName . '">' . $attachmentName . '</a></span>';
        } else {
            echo '            <span>No attachment</span>';
        }
        echo '        </div>';
        echo '        <div class="form-group">';
        echo '            <button type="button" onclick="goBack()">Back to Employee Dashboard</button>';
        echo '        </div>';
        echo '    </div>';
        echo '</div>';
        echo '<script>';
        echo 'function goBack() {';
        echo '    window.location.href = "employee.php";';
        echo '}';
        echo '</script>';
        echo '</body>';
        echo '</html>';
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close connection
    $conn->close();
}
?>
