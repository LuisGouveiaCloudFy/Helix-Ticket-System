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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
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

        .no-tickets {
            margin-top: 1rem;
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Meus Tickets</h1>
        </header>

        <section class="ticket-list">
            <?php if (empty($tickets)): ?>
                <p class="no-tickets">Você não tem tickets submetidos.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Departamento</th>
                            <th>Status</th>
                            <th>Prioridade</th>
                            <th>Criado Em</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tickets as $ticket): ?>
                            <tr>
                                <td><a href="view_ticket.php?id=<?= htmlspecialchars($ticket['id']); ?>"><?= htmlspecialchars($ticket['title']); ?></a></td>
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
    </div>
</body>
</html>