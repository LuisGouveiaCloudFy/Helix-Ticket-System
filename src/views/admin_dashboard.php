<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Welcome, Admin <?= htmlspecialchars($_SESSION['user_name']); ?></h1>
    <nav>
        <a href="user_management.php">Manage Users</a>
        <a href="department_management.php">Manage Departments</a>
        <a href="view_statistics.php">View Statistics</a>
        <a href="../php/logout.php">Logout</a>
    </nav>
</body>
</html>
