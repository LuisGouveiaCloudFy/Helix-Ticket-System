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

        h1 {
            margin-bottom: 1.5rem;
        }

        nav {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        a {
            padding: 0.8rem 1.5rem;
            background: #2575fc;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 1rem;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.3s ease;
        }

        a:hover {
            background: #6a11cb;
        }

        @media (max-width: 480px) {
            .container {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
    <h1>Welcome, Admin <?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : ' '; ?></h1>
        <nav>
            <a href="user_management.php">Manage Users</a>
            <a href="department_management.php">Manage Departments</a>
            <a href="view_statistics.php">View Statistics</a>
            <a href="edit_profile.php">Edit Profile</a> <!-- Adicionado o botão de Edit Profile -->
            <a href="../php/logout.php">Logout</a>
        </nav>
    </div>
</body>
</html>
