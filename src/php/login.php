<?php
require_once '../config/db.php'; // Inclui a configuração do banco de dados
session_start(); // Inicia a sessão

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    var_dump($_POST); // Para depuração, mostra os dados enviados

    // Captura e limpa os dados do formulário
    $email = trim($_POST['email']);
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    // Verifica se os campos estão vazios
    if (empty($email) || empty($password)) {
        die("Todos os campos são obrigatórios!"); // Mensagem de erro se campos estiverem vazios
    }

    try {
        // Busca o usuário pelo email
        $stmt = $pdo->prepare("SELECT id, NAME, username, email, PASSWORD, role, created_at FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC); // Obtém os dados do usuário

        // Verifica se o usuário existe
        if ($user) {
            // Verifica se a senha está correta
            if (password_verify($password, $user['PASSWORD'])) {
                // Armazena informações do usuário na sessão
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['NAME'];
                $_SESSION['user_role'] = $user['role'];

                // Redireciona para o painel
                header("Location: ../index.php");
                exit;
            } else {
                die("Credenciais inválidas."); // Mensagem de erro se a senha estiver incorreta
            }
        } else {
            die("Credenciais inválidas."); // Mensagem de erro se o usuário não existir
        }
    } catch (PDOException $e) {
        die("Erro no banco de dados: " . $e->getMessage()); // Mensagem de erro em caso de falha na consulta
    }
}
?>