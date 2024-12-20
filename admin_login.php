<?php
session_start(); // Start the session

// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'e_todolist'); // Change to your database credentials

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query to get the user based on Username
    $stmt = $conn->prepare("SELECT Password FROM admin WHERE Username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    // Check if the user is found
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($stored_password);
        $stmt->fetch();

        // Verify the password
        if ($password === $stored_password) { // Direct comparison for plain text password
            // Save information in the session
            $_SESSION['username'] = $username;

            // Redirect to the dashboard
            header("Location: dashboard_admin.html");
            exit(); // Stop script execution after redirect
        } else {
            // Password incorrect
            header("Location: loginadmin.html");
            exit();
        }
    } else {
        // Account not registered
        header("Location: loginadmin.html");
        exit();
    }

    $stmt->close();
}

$conn->close();
?>
