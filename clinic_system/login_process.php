<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role = $_POST['role'];
    $identifier = $_POST['identifier'];
    $password = $_POST['password'];

    session_unset();

    if ($role === 'staff') {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE staff_id = ? AND role = 'staff'");
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'patient'");
    }

    $stmt->execute([$identifier]);
    $user = $stmt->fetch();

    // CHANGE THIS LINE: Use === instead of password_verify()
    if ($user && $password === $user['password']) {
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];
        
        // Note: Make sure 'profile_pic' exists in your DB table
        $_SESSION['profile_pic'] = $user['profile_pic'] ?? null; 

        if ($user['role'] === 'staff') {
            header("Location: staff_dashboard.php");
        } else {
            header("Location: patient_dashboard.php");
        }
        exit();
    } else {
        // Redirect back to your login page (login.php)
        echo "<script>alert('Invalid credentials!'); window.location='login.php';</script>";
    }
}
?>