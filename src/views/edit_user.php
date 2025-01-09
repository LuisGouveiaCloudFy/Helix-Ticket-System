<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Verifica se o usuário está logado e se é um administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php'); // Redireciona para a página de login se não for admin
    exit;
}

// Inclui o arquivo de configuração do banco de dados
require_once '../config/db.php';

// Verifica se o ID do usuário foi passado na URL
if (!isset($_GET['id'])) {
    header('Location: user_management.php'); // Redireciona se não houver ID
    exit;
}

$user_id = $_GET['id'];

try {
    // Busca os dados do usuário
    $stmt = $pdo->prepare("SELECT id, name, username, email, role FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica se o usuário existe
    if (!$user) {
        header('Location: user_management.php'); // Redireciona se o usuário não for encontrado
        exit;
    }

    // Processa o formulário quando enviado
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['name']);
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $role = trim($_POST['role']);

        // Atualiza os dados do usuário
        $stmt = $pdo->prepare("UPDATE users SET name = ?, username = ?, email = ?, role = ? WHERE id = ?");
        $stmt->execute([$name, $username, $email, $role, $user_id]);

        // Redireciona para a página de gerenciamento de usuários após a atualização
        header('Location: user_management.php');
        exit;
    }
} catch (PDOException $e) {
    die("Erro ao executar a consulta: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>Edit User</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: white;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .container {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 2rem;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        input, select {
            width: 100%;
            padding: 0.6rem;
            margin: 0.5rem 0;
            border: none;
            border-radius: 5px;
            box-sizing: border-box;
        }

        button {
            padding: 0.6rem 1.2rem;
            background: #2575fc;
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.3s ease;
            width: 100%;
            margin-top: 1rem;
        }

        button:hover {
            background: #6a11cb;
        }

        .cancel-link {
            display: block;
            text-align: center;
            color: white;
            text-decoration: underline;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit User</h1>
        <form method="POST">
            <input type="text" name="name" value="<?= htmlspecialchars($user['name']); ?>" required placeholder="Name">
            <input type="text" name="username" value="<?= htmlspecialchars($user['username']); ?>" required placeholder="Username">
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required placeholder="Email">
            <select name="role" required>
                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                <option value="user" <?= $user['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                <option value="agent" <?= $user['role'] === 'agent' ? 'selected' : ''; ?>>Agent</option>
            </select>
            <button type="submit">Update User</button>
        </form>
        <a href="user_management.php" class="cancel-link">Cancel</a>
    </div>
</body>
</html>
