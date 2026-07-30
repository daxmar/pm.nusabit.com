<?php
require_once '.config/config.php';
require_once '.config/password.php';

// Check if admin exists
$sql = "SELECT COUNT(*) as count FROM users WHERE role = 'admin'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
if ($row['count'] > 0) {
    echo "Admin user already exists.";
} else {
    $admin_username = 'admin';
    $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
    $admin_email = 'admin@example.com';
    $sql = "INSERT INTO users (username, password, nama_lengkap, role, email) VALUES (?, ?, 'Administrator', 'admin', ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'sss', $admin_username, $admin_password, $admin_email);
    if (mysqli_stmt_execute($stmt)) {
        echo "Admin user created successfully! Username: admin, Password: admin123";
    } else {
        echo "Error creating admin: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
}
mysqli_close($conn);
?>

