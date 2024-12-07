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
    <title>Client Dashboard</title>
</head>
<body>
    <h1>Welcome, <?= htmlspecialchars($_SESSION['user_name']); ?></h1>
    <nav>
        <a href="submit_ticket.php">Submit a Ticket</a>
        <a href="my_tickets.php">View My Tickets</a>
        <a href="../php/logout.php">Logout</a>
    </nav>
</body>
</html>
