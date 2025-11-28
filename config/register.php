<?php
session_start();
include_once $_SERVER['DOCUMENT_ROOT'] . "/config/config.php";

// ✅ Ensure database connection exists
if (!$con) {
    die("<script>alert('❌ Database connection error!'); window.location.href='/register.php';</script>");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $birth_date = $_POST['birth_date'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // ✅ Check if fields are empty
    if (empty($first_name) || empty($last_name) || empty($email) || empty($birth_date) || empty($password)) {
        echo "<script>alert('❌ Please fill in all fields!'); window.location.href='/register.php';</script>";
        exit();
    }

    // ✅ Check if passwords match
    if ($password !== $confirm_password) {
        echo "<script>alert('❌ Passwords do not match!'); window.location.href='/register.php';</script>";
        exit();
    }

    // ✅ Check password length
    if (strlen($password) < 6) {
        echo "<script>alert('❌ Password must be at least 6 characters!'); window.location.href='/register.php';</script>";
        exit();
    }

    // ✅ Check if email already exists
    $check_sql = "SELECT id_admin FROM admin WHERE email = ?";
    $check_stmt = $con->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo "<script>alert('❌ Email already registered!'); window.location.href='/register.php';</script>";
        exit();
    }
    $check_stmt->close();

    // ✅ Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // ✅ Insert new admin (default role: normal)
    $sql = "INSERT INTO admin (first_name, last_name, email, birth_date, password, role) VALUES (?, ?, ?, ?, ?, 'normal')";
    $stmt = $con->prepare($sql);

    if (!$stmt) {
        die("<script>alert('❌ SQL Error: " . $con->error . "'); window.location.href='/register.php';</script>");
    }

    $stmt->bind_param("sssss", $first_name, $last_name, $email, $birth_date, $hashed_password);

    if ($stmt->execute()) {
        // ✅ Auto-login after registration
        $new_id = $stmt->insert_id;
        $_SESSION['id_admin'] = $new_id;
        $_SESSION['first_name'] = $first_name;
        $_SESSION['last_name'] = $last_name;
        $_SESSION['email'] = $email;
        $_SESSION['birth_date'] = $birth_date;

        echo "<script>alert('✅ Account created successfully! Welcome, " . htmlspecialchars($first_name) . "!'); window.location.href='/index.php';</script>";
        exit();
    } else {
        echo "<script>alert('❌ Registration failed! Please try again.'); window.location.href='/register.php';</script>";
        exit();
    }

    $stmt->close();
}

// If accessed directly without POST, redirect to register page
header("Location: /register.php");
exit();
?>
