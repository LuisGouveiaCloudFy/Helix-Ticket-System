<?php
require_once '../config/db.php';  // Includes the database configuration file

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You need to be logged in to view your tickets.");
}

$user_id = $_SESSION['user_id'];

try {
    // Fetch tickets assigned to the logged-in agent
    $stmt = $pdo->prepare("SELECT t.id, t.title, t.status, t.priority, t.created_at, d.name AS department_name
                            FROM tickets t
                            LEFT JOIN departments d ON t.department_id = d.id
                            WHERE t.assigned_agent_id = :user_id
                            ORDER BY t.created_at DESC");
    $stmt->execute([':user_id' => $user_id]);
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching tickets: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tickets</title>
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
            align-items: center; /* Centraliza verticalmente */
            min-height: 100vh; /* Garante que ocupa toda a altura da tela */
        }

        .container {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 2rem;
            width: 90%;
            max-width: 800px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        h1 {
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #fff;
        }

        th {
            background: #2575fc;
            color: white;
            font-weight: bold;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .action-button {
            display: inline-block;
            padding: 8px 16px;
            background: #2575fc; /* Cor azul para o botão de visualizar */
            color: white;
            border-radius: 5px;
            text-align: center;
            transition: background 0.3s ease;
        }

        .action-button:hover {
            background: #6a11cb; /* Cor mais escura ao passar o mouse */
        }

        .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #1a5bb8; /* Cor azul que se encaixa no tema */
            color: white;
            border-radius: 5px;
            text-align: center;
            transition: background 0.3s ease;
        }

        .back-button:hover {
            background: #0e4da4; /* Cor mais escura ao passar o mouse */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Your Tickets</h1>
        <?php if (empty($tickets)): ?>
            <p>No tickets assigned to you.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Department</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets as $ticket): ?>
                        <tr>
                            <td><?= htmlspecialchars($ticket['title']); ?></td>
                            <td><?= htmlspecialchars($ticket['status']); ?></td>
                            <td><?= htmlspecialchars($ticket['priority']); ?></td>
                            <td><?= htmlspecialchars($ticket['department_name']); ?></td>
                            <td><?= htmlspecialchars($ticket['created_at']); ?></td>
                            <td>
                                <a href="view_ticket_agent_self.php?id=<?= htmlspecialchars($ticket['id']); ?>" class="action-button">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <a href="dashboard.php" class="back-button">Back to Dashboard</a> <!-- Botão de voltar ao dashboard -->
    </div>
</body>
</html>