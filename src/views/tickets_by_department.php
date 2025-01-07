<?php
require_once '../config/db.php';  // Inclui o arquivo de configuração do banco de dados

session_start();

// Verifica se o usuário está logado e se é um agente
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'agent') {
    die("Você precisa estar logado como agente para ver os tickets.");
}

$user_id = $_SESSION['user_id'];

// Inicializa variáveis de filtro
$department_id = isset($_GET['department_id']) ? intval($_GET['department_id']) : null;
$status = isset($_GET['status']) ? $_GET['status'] : null;
$created_after = isset($_GET['created_after']) ? $_GET['created_after'] : null;

try {
    // Busca os departamentos para o filtro
    $departments_stmt = $pdo->query("SELECT id, name FROM departments");
    $departments = $departments_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Prepara a consulta de tickets com filtros
    $query = "SELECT t.id, t.title, t.status, t.priority, t.created_at, d.name AS department_name
              FROM tickets t
              LEFT JOIN departments d ON t.department_id = d.id
              WHERE 1=1"; // Condição verdadeira para facilitar a adição de filtros

    $params = [];

    // Adiciona filtros à consulta
    if ($department_id) {
        $query .= " AND t.department_id = :department_id";
        $params[':department_id'] = $department_id;
    }
    if ($status) {
        $query .= " AND t.status = :status";
        $params[':status'] = $status;
    }
    if ($created_after) {
        $query .= " AND t.created_at >= :created_after";
        $params[':created_after'] = $created_after;
    }

    $query .= " ORDER BY t.created_at DESC";

    // Executa a consulta
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
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
    <title>Tickets por Departamento</title>
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

        form {
            margin-bottom: 2rem;
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
    </style>
</head>
<body>
    <div class="container">
        <h1>Tickets por Departamento</h1>

        <form action="" method="GET">
            <label for="department_id">Departamento:</label>
            <select name="department_id" id="department_id">
                <option value="">Todos</option>
                <?php foreach ($departments as $department): ?>
                    <option value="<?= htmlspecialchars($department['id']); ?>" <?= $department_id == $department['id'] ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($department['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="status">Status:</label>
            <select name="status" id="status">
                <option value="">Todos</option>
                <option value="open" <?= $status == 'open' ? 'selected' : ''; ?>>Aberto</option>
                <option value="in_progress" <?= $status == 'in_progress' ? 'selected' : ''; ?>>Em Progresso</option>
                <option value="closed" <?= $status == 'closed' ? 'selected' : ''; ?>>Fechado</option>
            </select>

            <label for="created_after">Criado Após:</label>
            <input type="date" name="created_after" id="created_after" value="<?= htmlspecialchars($created_after); ?>">

            <button type="submit">Filtrar</button>
        </form>

        <section class="ticket-list">
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
                    <?php if (empty($tickets)): ?>
                        <tr>
                            <td colspan="5">Nenhum ticket encontrado.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($tickets as $ticket): ?>
                            <tr>
                                <td><a href="view_ticket_agent.php?id=<?= htmlspecialchars($ticket['id']); ?>"><?= htmlspecialchars($ticket['title']); ?></a></td>
                                <td><?= htmlspecialchars($ticket['department_name']); ?></td>
                                <td><?= htmlspecialchars($ticket['status']); ?></td>
                                <td><?= htmlspecialchars($ticket['priority']); ?></td>
                                <td><?= htmlspecialchars($ticket['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </div>
</body>
</html>