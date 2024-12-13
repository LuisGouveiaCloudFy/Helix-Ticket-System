<?php
require_once '../config/db.php';  // Inclui o arquivo de configuração do banco de dados

session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    die("Você precisa estar logado para ver seus tickets.");
}

$user_id = $_SESSION['user_id'];

try {
    // Prepara o comando SQL para buscar os tickets do usuário logado
    $stmt = $pdo->prepare("SELECT t.id, t.title, t.status, t.priority, t.created_at, d.name AS department_name
                           FROM tickets t
                           LEFT JOIN departments d ON t.department_id = d.id
                           WHERE t.client_id = :client_id
                           ORDER BY t.created_at DESC");
    $stmt->execute([':client_id' => $user_id]);

    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar os tickets: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Tickets</title>
    <link rel="stylesheet" href="../css/style.css">
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
            max-width: 800px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        header h1 {
            margin: 0;
            text-align: center;
        }

        .ticket-list {
            margin-top: 1rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 0.8rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        }

        th {
            background: rgba(255, 255, 255, 0.2);
        }

        tr:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        p {
            text-align: center;
            font-size: 1.2rem;
            margin: 1rem 0;
        }

        .btn {
            display: inline-block;
            margin-top: 1rem;
            padding: 0.6rem 1.2rem;
            background: #2575fc;
            border: none;
            border-radius: 5px;
            color: white;
            text-decoration: none;
            text-align: center;
            font-size: 1rem;
            transition: background 0.3s ease;
        }

        .btn:hover {
            background: #6a11cb;
        }

        .actions {
            display: flex;
            justify-content: space-between;
            margin-top: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>My Tickets</h1>
        </header>

        <section class="ticket-list">
            <?php if (empty($tickets)): ?>
                <p>Você não tem tickets submetidos.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Department</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tickets as $ticket): ?>
                            <tr>
                                <td><?= htmlspecialchars($ticket['title']); ?></td>
                                <td><?= htmlspecialchars($ticket['department_name'] ?? 'N/A'); ?></td>
                                <td><?= htmlspecialchars($ticket['status']); ?></td>
                                <td><?= htmlspecialchars($ticket['priority']); ?></td>
                                <td><?= htmlspecialchars($ticket['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>

        <div class="actions">
            <a href="../views/dashboard.php" class="btn">Back to Dashboard</a>
            <a href="submit_ticket.php" class="btn">Create New Ticket</a>
        </div>
    </div>
</body>
</html>
