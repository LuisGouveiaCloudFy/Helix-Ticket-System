<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $userId = $data['id'];
    $password = $data['password'];

    // Verifica se o usuário está logado
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Usuário não está logado.']);
        exit;
    }

    // Verifica a senha do usuário ativo
    $stmt = $pdo->prepare("SELECT PASSWORD FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Adiciona log para depuração
    error_log("Tentativa de exclusão do usuário ID: " . $userId);
    error_log("Senha fornecida: " . $password);

    if ($user) {
        if (password_verify($password, $user['PASSWORD'])) { // Corrigido para PASSWORD
            // Se a senha estiver correta, exclui o usuário
            $deleteStmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $deleteStmt->execute([$userId]);

            // Adiciona log para verificar o resultado da exclusão
            error_log("Número de linhas afetadas: " . $deleteStmt->rowCount());

            if ($deleteStmt->rowCount() > 0) {
                echo json_encode(['success' => true]);
            } else {
                error_log("Erro ao excluir o usuário ID: " . $userId);
                echo json_encode(['success' => false, 'message' => 'Erro ao excluir o usuário. O usuário pode não existir ou já ter sido excluído.']);
            }
        } else {
            error_log("Senha incorreta para o usuário ID: " . $_SESSION['user_id']);
            echo json_encode(['success' => false, 'message' => 'Senha incorreta. Verifique sua senha e tente novamente.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Usuário não encontrado.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método não permitido.']);
}
?>