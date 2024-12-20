<?php
header('Content-Type: application/json'); // Mengatur tipe konten menjadi JSON
ob_start(); // Mulai output buffering

// Koneksi ke database
$conn = new mysqli('localhost', 'root', '', 'e_todolist'); // 'login_system' adalah nama database kamu

// Cek koneksi
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Koneksi gagal: ' . $conn->connect_error]));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nim = $_POST['nim'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Cek apakah NIM sudah ada di database
    $stmt = $conn->prepare("SELECT * FROM users WHERE NIM = ?");
    $stmt->bind_param("s", $nim);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // NIM sudah terdaftar
        echo json_encode(['success' => false, 'message' => 'NIM sudah terdaftar!']);
    } else {
        // Hash password sebelum disimpan ke database
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Masukkan data pengguna baru ke database
        $stmt = $conn->prepare("INSERT INTO users (NIM, Username, Password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nim, $username, $hashed_password);

        if ($stmt->execute()) {
            // Registrasi berhasil
            echo json_encode(['success' => true, 'message' => 'Registrasi berhasil!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan saat registrasi!']);
        }
    }

    $stmt->close();
}

$conn->close();
ob_end_flush(); // Hentikan output buffering dan kirim output ke browser
?>
