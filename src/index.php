<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    // Se não estiver logado, redireciona para a página de login
    header('Location: views/login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Helix Ticket System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>Welcome, <?= htmlspecialchars($_SESSION['user_name']); ?>!</h1>
        </header>

        <section class="welcome-message">
            <p>You are logged in as <strong><?= htmlspecialchars($_SESSION['user_role']); ?></strong>.</p>
            <p>Manage your tickets or go to your dashboard to take action!</p>
        </section>

        <nav class="nav-links">
            <a href="views/dashboard.php" class="button">Go to Dashboard</a>
            <a href="php/logout.php" class="button logout-button">Logout</a>
        </nav>
    </div>
</body>
</html>
