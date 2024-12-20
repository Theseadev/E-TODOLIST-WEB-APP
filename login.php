<?php
session_start();

// Koneksi ke database
$conn = new mysqli('localhost', 'root', '', 'e_todolist');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nim = $_POST['nim'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validasi NIM (harus 12 digit)
    if (!preg_match('/^\d{12}$/', $nim)) {
        $_SESSION['login_error'] = 'NIM tidak valid! Pastikan NIM terdiri dari 12 digit.';
        header('Location: index.html');
        exit();
    }

    // Mencari user di database
    $stmt = $conn->prepare("SELECT Password FROM users WHERE NIM = ? AND Username = ?");
    $stmt->bind_param("ss", $nim, $username);
    $stmt->execute();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();

    // Verifikasi password
    if (isset($hashed_password) && password_verify($password, $hashed_password)) {
        $_SESSION['nim'] = $nim;
        $_SESSION['username'] = $username;
        header('Location: dashboard.html');
        exit();
    } else {
        $_SESSION['login_error'] = 'NIM, Username, atau Password salah!';
        header('Location: index.html');
        exit();
    }

    $stmt->close();
}

$conn->close();
?>
