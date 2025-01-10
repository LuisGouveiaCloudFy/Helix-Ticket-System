<?php
require_once '../config/db.php';  // Includes the database configuration file

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You need to be logged in to view the ticket details.");
}

// Check if the ticket ID is provided
if (!isset($_GET['id'])) {
    die("Ticket ID not specified.");
}

$ticket_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

try {
    // Fetch ticket details
    $stmt = $pdo->prepare("SELECT t.id, t.title, t.status, t.priority, t.created_at, d.name AS department_name
                            FROM tickets t
                            LEFT JOIN departments d ON t.department_id = d.id
                            WHERE t.id = :ticket_id AND t.client_id = :client_id");
    $stmt->execute([':ticket_id' => $ticket_id, ':client_id' => $user_id]);
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the ticket exists
    if (!$ticket) {
        die("Ticket not found or you do not have permission to view it.");
    }

    // Process ticket actions
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        $action = $_POST['action'];

        try {
            if ($action === 'close') {
                // Close the ticket
                $stmt = $pdo->prepare("UPDATE tickets SET status = 'closed' WHERE id = :ticket_id AND client_id = :client_id");
                $stmt->execute([':ticket_id' => $ticket_id, ':client_id' => $user_id]);
                header("Location: view_ticket.php?id=" . $ticket_id); // Redirect to the same page
                exit;
            } elseif ($action === 'reopen') {
                // Reopen the ticket
                $stmt = $pdo->prepare("UPDATE tickets SET status = 'open' WHERE id = :ticket_id AND client_id = :client_id");
                $stmt->execute([':ticket_id' => $ticket_id, ':client_id' => $user_id]);
                header("Location: view_ticket.php?id=" . $ticket_id); // Redirect to the same page
                exit;
            }
        } catch (PDOException $e) {
            die("Error updating ticket status: " . $e->getMessage());
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['response'])) {
        // Add new response
        $response = $_POST['response'];

        try {
            $stmt = $pdo->prepare("INSERT INTO resp_tickets (ticket_id, user_id, response) VALUES (:ticket_id, :user_id, :response)");
            $stmt->execute([':ticket_id' => $ticket_id, ':user_id' => $user_id, ':response' => $response]);
            header("Location: view_ticket.php?id=" . $ticket_id); // Redirect to the same page
            exit;
        } catch (PDOException $e) {
            die("Error adding response: " . $e->getMessage());
        }
    }

    // Fetch responses to the ticket
    $stmt = $pdo->prepare("SELECT r.id, r.response, r.created_at, u.username, u.role
                            FROM resp_tickets r
                            LEFT JOIN users u ON r.user_id = u.id
                            WHERE r.ticket_id = :ticket_id
                            ORDER BY r.created_at ASC");
    $stmt->execute([':ticket_id' => $ticket_id]);
    $responses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching ticket details: " . $e->getMessage());
}

// Fetch departments for dropdown
$departments = ['Support', 'Sales', 'HR', 'Agent']; // Add 'Agent' to the list
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Ticket</title>
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
            max-width: 600px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: left; /* Align text to the left for better readability */
        }

        h1 {
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        p {
            margin: 0.5rem 0;
        }

        .response-form {
            margin-top: 2rem;
        }

        .response-form textarea {
            width: calc(100% - 20px); /* Ajusta a largura para compensar o padding */
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            resize: none; /* Prevent resizing */
            margin-bottom: 10px;
        }

        .response {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
            padding: 10px; /* Mantém o padding existente */
            margin: 10px 0;
            width: 100%; /* Garante que as respostas ocupem 100% da largura do contêiner */
            box-sizing: border-box; /* Inclui o padding na largura total */
        }

        .button {
            padding: 10px;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.2s ease; /* Adiciona transição para transformação */
            text-align: center; /* Alinha o texto ao centro */
            width: 100%; /* Faz com que todos os botões ocupem toda a largura */
            margin: 10px 0; /* Espaço igual entre os botões */
        }

        .button:hover {
            transform: scale(1.05); /* Aumenta ligeiramente o tamanho do botão ao passar o mouse */
        }

        .button.send-response {
            background: #2575fc; /* Cor do botão de enviar resposta */
        }

        .button.send-response:hover {
            background: #1a5bb8; /* Cor mais escura ao passar o mouse */
        }

        .button.close {
            background: red; /* Cor do botão de fechar */
        }

        .button.close:hover {
            background: darkred; /* Cor mais escura ao passar o mouse */
        }

        .button.reopen {
            background: green; /* Cor do botão de reabrir */
        }

        .button.reopen:hover {
            background: darkgreen; /* Cor mais escura ao passar o mouse */
        }

        .button.back {
            background: #2575fc; /* Cor azul padrão */
        }

        .button.back:hover {
            background: #1a5bb8; /* Cor mais escura ao passar o mouse */
        }

        .action-buttons {
            display: flex;
            flex-direction: column; /* Coloca os botões em colunas */
            margin-top: 20px;
        }

        .no-underline {
            text-decoration: none; /* Remove o sublinhado */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Ticket: <?= htmlspecialchars($ticket['title']); ?></h1>
        <p><strong>Department:</strong> <?= htmlspecialchars($ticket['department_name']); ?></p>
        <p><strong>Status:</strong> <span class="status"><?= htmlspecialchars($ticket['status']); ?></span></p>
        <p><strong>Priority:</strong> <?= htmlspecialchars($ticket['priority']); ?></p>
        <p><strong>Created At:</strong> <?= htmlspecialchars($ticket['created_at']); ?></p>

        <h2>Responses</h2>
        <?php if (empty($responses)): ?>
            <p>No responses yet.</p>
        <?php else: ?>
            <?php foreach ($responses as $response): ?>
                <div class="response <?= $response['role'] === 'agent' ? 'agent-response' : ''; ?>">
                    <p><strong><?= htmlspecialchars($response['username']); ?>:</strong></p>
                    <p><?= htmlspecialchars($response['response']); ?></p>
                    <p><em><?= htmlspecialchars($response['created_at']); ?></em></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="response-form">
            <h3>Add Response</h3>
            <form action="" method="POST">
                <textarea name="response" rows="4" required placeholder="Type your response here..." class="textarea"></textarea>
                <br>
                <button type="submit" class="button send-response">Send Response</button>
            </form>
        </div>

        <div class="action-buttons">
            <form action="" method="POST" style="flex: 1;">
                <input type="hidden" name="ticket_id" value="<?= htmlspecialchars($ticket['id']); ?>">
                <?php if ($ticket['status'] === 'closed'): ?>
                    <button type="submit" name="action" value="reopen" class="button reopen">Reopen Ticket</button>
                <?php else: ?>
                    <button type="submit" name="action" value="close" class="button close">Close Ticket</button>
                <?php endif; ?>
            </form>
            <a href="my_tickets.php" class="button back no-underline">Back to My Tickets</a>
        </div>
    </div>
</body>
</html>