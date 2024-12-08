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
    <title>Agent Dashboard</title>
</head>
<body>
    <h1>Welcome, Agent <?= htmlspecialchars($_SESSION['username']); ?></h1>
    <nav>
        <a href="tickets_by_department.php">Tickets in My Department</a>
        <a href="manage_tickets.php">Manage Tickets</a>
        <a href="../php/logout.php">Logout</a>
    </nav>
</body>
</html>
