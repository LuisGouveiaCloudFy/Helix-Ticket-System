<?php
require_once '../config/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        die("Todos os campos são obrigatórios!");
    }

    try {
        // Busca o usuário pelo email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Armazena informações do usuário na sessão
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name']; // Adicionando o nome do usuário à sessão
            $_SESSION['user_role'] = $user['role'];

            // Redireciona para o painel
            header("Location: ../index.php");
            exit;
        } else {
            die("Credenciais inválidas.");
        }
    } catch (PDOException $e) {
        die("Erro no banco de dados: " . $e->getMessage());
    }
}
?>
