<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Obter informações do usuário
require_once '../config/db.php';
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Redirecionar com base no papel do usuário
if ($user['role'] === 'admin') {
    header('Location: admin_dashboard.php');
    exit;
} elseif ($user['role'] === 'agent') {
    header('Location: agent_dashboard.php');
    exit;
} else {
    header('Location: client_dashboard.php');
    exit;
}
?>
