<?php
require_once '../config/db.php';  // Inclui o arquivo de configuração do banco de dados

session_start();

// Verifica se o usuário está logado e se é um agente
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'agent') {
    die("Você precisa estar logado como agente para ver os detalhes do ticket.");
}

// Verifica se o ID do ticket foi passado
if (!isset($_GET['id'])) {
    die("ID do ticket não especificado.");
}

$ticket_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

try {
    // Busca os detalhes do ticket
    $stmt = $pdo->prepare("SELECT t.id, t.title, t.status, t.priority, t.created_at, d.name AS department_name, u.name AS client_name, u.email AS client_email
                            FROM tickets t
                            LEFT JOIN departments d ON t.department_id = d.id
                            LEFT JOIN users u ON t.client_id = u.id
                            WHERE t.id = :ticket_id");
    $stmt->execute([':ticket_id' => $ticket_id]);
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica se o ticket existe
    if (!$ticket) {
        die("Ticket não encontrado.");
    }

    // Busca as respostas do ticket
    $stmt = $pdo->prepare("SELECT r.id, r.response, r.created_at, u.username
                            FROM resp_tickets r
                            LEFT JOIN users u ON r.user_id = u.id
                            WHERE r.ticket_id = :ticket_id
                            ORDER BY r.created_at ASC");
    $stmt->execute([':ticket_id' => $ticket_id]);
    $responses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar os detalhes do ticket: " . $e->getMessage());
}

// Processa a resposta ao ticket
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = trim($_POST['response']);
    $status = trim($_POST['status']);

    if (empty($response) && empty($status)) {
        die("A resposta ou o status devem ser preenchidos.");
    }

    try {
        // Atualiza o status do ticket se fornecido
        if (!empty($status)) {
            $stmt = $pdo->prepare("UPDATE tickets SET status = :status WHERE id = :ticket_id");
            $stmt->execute([':status' => $status, ':ticket_id' => $ticket_id]);
        }

        // Insere a resposta na tabela resp_tickets se fornecida
        if (!empty($response)) {
            $stmt = $pdo->prepare("INSERT INTO resp_tickets (ticket_id, user_id, response) VALUES (:ticket_id, :user_id, :response)");
            $stmt->execute([':ticket_id' => $ticket_id, ':user_id' => $user_id, ':response' => $response]);
        }

        // Redireciona para a mesma página para evitar reenvio do formulário
        header("Location: view_ticket_agent.php?id=" . $ticket_id);
        exit;
    } catch (PDOException $e) {
        die("Erro ao adicionar a resposta ou atualizar o status: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Ticket - Agente</title>
    <link rel="stylesheet" href="../css/style.css">
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

        .container {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 2rem;
            width: 90%;
            max-width: 800px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h1 {
            margin-bottom: 1.5rem;
        }

        .response-form {
            margin-top: 2rem;
        }

        .response {
            background: rgba(255, 255, 255, 0.2);
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            text-align: left;
        }

        .response p {
            margin: 0.5rem 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Ticket: <?= htmlspecialchars($ticket['title']); ?></h1>
        <p><strong>Departamento:</strong> <?= htmlspecialchars($ticket['department_name']); ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($ticket['status']); ?></p>
        <p><strong>Prioridade:</strong> <?= htmlspecialchars($ticket['priority']); ?></p>
        <p><strong>Criado Em:</strong> <?= htmlspecialchars($ticket['created_at']); ?></p>
        <p><strong>Cliente:</strong> <?= htmlspecialchars($ticket['client_name']); ?> (<?= htmlspecialchars($ticket['client_email']); ?>)</p>

        <h2>Respostas</h2>
        <?php if (empty($responses)): ?>
            <p>Nenhuma resposta ainda.</p>
        <?php else: ?>
            <?php foreach ($responses as $response): ?>
                <div class="response">
                    <p><strong><?= htmlspecialchars($response['username']); ?>:</strong></p>
                    <p><?= htmlspecialchars($response['response']); ?></p>
                    <p><em><?= htmlspecialchars($response['created_at']); ?></em></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="response-form">
            <h3>Adicionar Resposta</h3>
            <form action="" method="POST">
                <textarea name="response" rows="4" placeholder="Digite sua resposta aqui..."></textarea>
                <br>
                <label for="status">Mudar Status:</label>
                <select name="status">
                    <option value="">Selecione um status</option>
                    <option value="open">Aberto</option>
                    <option value="in_progress">Em Progresso</option>
                    <option value="closed">Fechado</option>
                </select>
                <br>
                <button type="submit">Enviar Resposta e Mudar Status</button>
            </form>
        </div>
    </div>
</body>
</html>
