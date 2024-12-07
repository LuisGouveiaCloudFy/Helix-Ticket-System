<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validações básicas
    if (empty($name) || empty($username) || empty($email) || empty($password)) {
        die("Todos os campos são obrigatórios!");
    }

    // Hash da senha
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    try {
        // Insere o usuário no banco
        $stmt = $pdo->prepare("INSERT INTO users (name, username, email, password) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $username, $email, $hashedPassword]);

        echo "Registro bem-sucedido! <a href='../index.php'>Faça login aqui.</a>";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Código 23000 para duplicatas
            die("O e-mail ou nome de usuário já está em uso.");
        }
        die("Erro no banco de dados: " . $e->getMessage());
    }
}
?>
