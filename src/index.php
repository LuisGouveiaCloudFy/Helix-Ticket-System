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
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #fff;
        }

        .container {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 2rem;
            width: 100%;
            max-width: 600px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .header h1 {
            margin-bottom: 1rem;
        }

        .welcome-message {
            margin-bottom: 1.5rem;
        }

        .nav-links {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }

        .button {
            padding: 0.8rem 1.5rem;
            background: #2575fc; /* Azul principal */
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 1rem;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.3s ease;
        }

        .button:hover {
            background: #6a11cb; /* Azul escuro */
        }

        .logout-button {
            background: #ff4b5c; /* Vermelho vivo */
        }

        .logout-button:hover {
            background: #ff6b7f; /* Vermelho claro */
        }

        @media (max-width: 480px) {
            .container {
                padding: 1.5rem;
            }

            .nav-links {
                flex-direction: column;
                gap: 0.5rem;
            }

            .button {
                width: 100%;
                text-align: center;
            }
        }
    </style>
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
