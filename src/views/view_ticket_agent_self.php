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
    $stmt = $pdo->prepare("SELECT t.id, t.title, t.status, t.priority, t.created_at, d.name AS department_name, t.assigned_agent_id, u.username AS assigned_agent_name
                            FROM tickets t
                            LEFT JOIN departments d ON t.department_id = d.id
                            LEFT JOIN users u ON t.assigned_agent_id = u.id
                            WHERE t.id = :ticket_id");
    $stmt->execute([':ticket_id' => $ticket_id]);
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
                $stmt = $pdo->prepare("UPDATE tickets SET status = 'closed' WHERE id = :ticket_id");
                $stmt->execute([':ticket_id' => $ticket_id]);
                header("Location: view_ticket_agent.php?id=" . $ticket_id); // Redirect to view_ticket_agent.php
                exit;
            } elseif ($action === 'reopen') {
                // Reopen the ticket
                $stmt = $pdo->prepare("UPDATE tickets SET status = 'open' WHERE id = :ticket_id");
                $stmt->execute([':ticket_id' => $ticket_id]);
                header("Location: view_ticket_agent.php?id=" . $ticket_id); // Redirect to view_ticket_agent.php
                exit;
            } elseif ($action === 'delete') {
                // Delete the ticket
                $stmt = $pdo->prepare("DELETE FROM tickets WHERE id = :ticket_id");
                $stmt->execute([':ticket_id' => $ticket_id]);
                header("Location: tickets_by_department.php"); // Redirect to tickets_by_department.php
                exit;
            } elseif ($action === 'auto_assign') {
                // Auto assign ticket to the current user (the logged-in agent)
                $stmt = $pdo->prepare("UPDATE tickets SET assigned_agent_id = :current_user WHERE id = :ticket_id");
                $stmt->execute([':current_user' => $user_id, ':ticket_id' => $ticket_id]);
                header("Location: view_ticket_agent.php?id=" . $ticket_id); // Redirect to view_ticket_agent.php
                exit;
            }
        } catch (PDOException $e) {
            die("Error updating ticket status: " . $e->getMessage());
        }
    }

    // Process adding a new response
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['response'])) {
        $response = $_POST['response'];

        try {
            $stmt = $pdo->prepare("INSERT INTO resp_tickets (ticket_id, user_id, response) VALUES (:ticket_id, :user_id, :response)");
            $stmt->execute([':ticket_id' => $ticket_id, ':user_id' => $user_id, ':response' => $response]);
            header("Location: view_ticket_agent.php?id=" . $ticket_id); // Redirect to view_ticket_agent.php
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

    // Fetch agents for assignment
    $stmt = $pdo->prepare("SELECT id, username FROM users WHERE role = 'agent' AND id != :current_user");
    $stmt->execute([':current_user' => $user_id]);
    $agents = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching ticket details: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Ticket</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* CSS styles here */
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: #fff;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: flex-start; /* Alinha o conteúdo ao topo */
            min-height: 100vh;
        }

        .container {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 2rem;
            width: 90%;
            max-width: 800px; /* Aumenta a largura máxima para acomodar o painel */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: flex; /* Usar flexbox para o layout */
            flex-direction: row; /* Colocar o painel e o conteúdo lado a lado */
        }

        .content {
            flex: 3; /* O conteúdo principal ocupa mais espaço */
            margin-right: 20px; /* Espaço entre o conteúdo e o painel */
        }

        .agent-panel {
            flex: 1; /* O painel do agente ocupa menos espaço */
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            padding: 1rem;
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
            width: calc(100% - 20px);
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            resize: none;
            margin-bottom: 10px;
        }

        .response {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
            padding: 10px;
            margin: 10px 0;
            width: 100%;
            box-sizing: border-box;
        }

        .button {
            padding: 12px; /* Aumenta a altura do botão */
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.2s ease;
            text-align: center;
            width: 100%; /* Mantém a largura do botão */
            margin: 10px 0; /* Distância entre os botões */
        }

        .button:hover {
            transform: scale(1.05);
        }

        .button.send-response {
            background: #2575fc;
        }

        .button.send-response:hover {
            background: #1a5bb8;
        }

        .button.close {
            background: red;
        }

        .button.close:hover {
            background: darkred;
        }

        .button.reopen {
            background: green;
        }

        .button.reopen:hover {
            background: darkgreen;
        }

        .button.assign {
            background: #ff9800; /* Cor laranja para melhor contraste */
        }

        .button.assign:hover {
            background: #e68a00; /* Cor laranja mais escura ao passar o mouse */
        }

        .button.back {
            background: #2575fc;
        }

        .button.back:hover {
            background: #1a5bb8;
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            margin-top: 20px;
        }

        .no-underline {
            text-decoration: none;
        }

        /* Estilo para o seletor de agentes */
        select {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            background: rgba(255, 255, 255, 0.8);
            color: #333; /* Cor do texto */
            margin-bottom: 10px;
        }

        select:focus {
            outline: none;
            border-color: #2575fc; /* Cor do contorno ao focar */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="content">
            <h1>Ticket: <?= htmlspecialchars($ticket['title']); ?></h1>
            <p><strong>Assigned Agent:</strong> <?= htmlspecialchars($ticket['assigned_agent_name']); ?></p> <!-- Nome do agente atribuído -->
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
        </div>

        <div class="agent-panel">
            <h2>Agent Panel</h2>
            <form action="" method="POST">
                <label for="assign_agent">Assign to Other Agent:</label>
                <select name="assign_agent" id="assign_agent" required>
                    <option value="">Select an agent</option>
                    <?php foreach ($agents as $agent): ?>
                        <option value="<?= htmlspecialchars($agent['id']); ?>"><?= htmlspecialchars($agent['username']); ?></option>
                    <?php endforeach; ?>
                </select>
                <br>
                <button type="submit" name="action" value="assign" class="button assign">Assign Ticket</button> <!-- Botão de Assign com melhor contraste -->
            </form>

            <div class="action-buttons">
                <form action="" method="POST" style="flex: 1;">
                    <input type="hidden" name="ticket_id" value="<?= htmlspecialchars($ticket['id']); ?>">
                    <button type="submit" name="action" value="<?= $ticket['status'] === 'closed' ? 'reopen' : 'close'; ?>" class="button <?= $ticket['status'] === 'closed' ? 'reopen' : 'close'; ?>">
                        <?= $ticket['status'] === 'closed' ? 'Reopen Ticket' : 'Close Ticket'; ?>
                    </button>
                </form>
                <form action="" method="POST" style="flex: 1;">
                    <input type="hidden" name="ticket_id" value="<?= htmlspecialchars($ticket['id']); ?>">
                    <button type="submit" name="action" value="delete" class="button close">Delete Ticket</button>
                </form>
                <form action="" method="POST" style="flex: 1;">
                    <input type="hidden" name="ticket_id" value="<?= htmlspecialchars($ticket['id']); ?>">
                    <button type="submit" name="action" value="auto_assign" class="button send-response">Auto Assign</button>
                </form>
                <form action="manage_tickets.php" method="GET" style="flex: 1;">
                    <button type="submit" class="button send-response">Return</button> <!-- Novo botão "Return" -->
                </form>
            </div>
        </div>
    </div>
</body>
</html>