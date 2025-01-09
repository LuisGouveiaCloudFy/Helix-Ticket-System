<?php
session_start();
require_once '../config/db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Initialize variables
$error = '';
$success = '';

// Get user data
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT name, username, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Process the edit form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Redefinir a variável de sucesso ao processar o formulário
    $success = '';

    // Basic validations
    if (empty($name) || empty($username) || empty($email)) {
        $error = "All fields are required!";
    } else {
        try {
            // Update user data
            $stmt = $pdo->prepare("UPDATE users SET name = ?, username = ?, email = ? WHERE id = ?");
            $stmt->execute([$name, $username, $email, $user_id]);

            // Update password if provided
            if (!empty($password)) {
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashedPassword, $user_id]);
            }

            $success = "Profile updated successfully!";
        } catch (PDOException $e) {
            $error = "Error updating profile: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>Edit Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: #fff;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .error-message, .success-message {
            padding: 15px; /* Adiciona espaço interno */
            border-radius: 5px; /* Bordas arredondadas */
            margin: 10px 0; /* Espaço acima e abaixo */
            font-weight: bold; /* Texto em negrito */
        }
        .error-message {
            background-color: /* rgba(255, 0, 0, 0.1); */ rgba(0, 107, 12, 0.8);/* Fundo vermelho claro */
            color: #d8000c; /* Cor do texto vermelho */
        }
        .success-message {
            background-color: rgba(0, 255, 0, 0.1); /* Fundo verde claro */
            color: #FFFFFF; /* Cor do texto verde */
        }

        .container {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 2rem;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
       
        /*
        .error-message {
            color: red;
        }
        .success-message {
            color: green;
        }
            */
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: calc(100% - 20px); /* Adjust width to compensate for padding */
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            font-size: 1rem;
            transition: background 0.3s ease;
        }
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            background: rgba(255, 255, 255, 0.3);
            outline: none;
        }
        button {
            padding: 10px;
            background: #2575fc;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s ease;
            width: 100%; /* Make the button full width */
            margin-bottom: 10px; /* Space below the button */
        }
        button:hover {
            background: #6a11cb;
        }
        .back-button {
            display: inline-block; /* Make it inline-block for better alignment */
            padding: 10px;
            background: #ff4081; /* Color of the back button */
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s ease;
            text-decoration: none; /* Remove underline */
            width: 100%; /* Make the back button full width */
            margin-top: 0; /* Remove margin to align with the button above */
            margin-bottom: 0; /* Ensure no extra margin below */
        }
        .back-button:hover {
            background: #c60055; /* Hover color for the back button */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Profile</h1>
        <?php if ($error): ?>
            <div class="error-message"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success-message"><?= htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <form action="" method="POST">
            <div>
                <label for="name">Name:</label>
                <input type="text" name="name" id="name" value="<?= htmlspecialchars($user['name']); ?>" required>
            </div>
            <div>
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" value="<?= htmlspecialchars($user['username']); ?>" required>
            </div>
            <div>
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']); ?>" required>
            </div>
            <div>
                <label for="password">New Password (leave blank to keep current):</label>
                <input type="password" name="password" id="password">
            </div>
            <button type="submit">Update Profile</button>
            <button type="button" class="back-button" onclick="window.location.href='dashboard.php'">Back to Dashboard</button> <!-- Back button -->
        </form>
    </div>
</body>
</html>