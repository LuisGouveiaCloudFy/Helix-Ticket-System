<?php
require_once '../config/db.php';  // Includes the database configuration file

session_start();

// Check if the user is logged in and is an agent
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'agent') {
    die("You need to be logged in as an agent to view the tickets.");
}

$user_id = $_SESSION['user_id'];

// Initialize filter variables
$department_id = isset($_GET['department_id']) ? intval($_GET['department_id']) : null;
$status = isset($_GET['status']) ? $_GET['status'] : null;
$priority = isset($_GET['priority']) ? $_GET['priority'] : null;
$assigned_agent = isset($_GET['assigned_agent']) ? $_GET['assigned_agent'] : null;
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : null;
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : null;

try {
    // Fetch departments for the filter
    $departments_stmt = $pdo->query("SELECT DISTINCT id, name FROM departments");
    $departments = $departments_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch agents for the filter
    $agents_stmt = $pdo->query("SELECT id, username FROM users WHERE role = 'agent'");
    $agents = $agents_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Prepare the ticket query with filters
    $query = "SELECT t.id, t.title, t.status, t.priority, t.created_at, d.name AS department_name, u.username AS assigned_agent
              FROM tickets t
              LEFT JOIN departments d ON t.department_id = d.id
              LEFT JOIN users u ON t.assigned_agent_id = u.id";

    // Initialize filter conditions
    $conditions = [];
    $params = [];

    // Add filters to the query
    if ($department_id) {
        $conditions[] = "t.department_id = :department_id";
        $params[':department_id'] = $department_id;
    }
    if ($status) {
        $conditions[] = "t.status = :status";
        $params[':status'] = $status;
    }
    if ($priority) {
        $conditions[] = "t.priority = :priority";
        $params[':priority'] = $priority;
    }
    if ($assigned_agent) {
        $conditions[] = "t.assigned_agent_id = :assigned_agent";
        $params[':assigned_agent'] = $assigned_agent;
    }
    if ($date_from) {
        $conditions[] = "t.created_at >= :date_from";
        $params[':date_from'] = $date_from;
    }
    if ($date_to) {
        $conditions[] = "t.created_at <= :date_to";
        $params[':date_to'] = $date_to;
    }

    // Combine conditions into the query
    if ($conditions) {
        $query .= " WHERE " . implode(' AND ', $conditions);
    }

    $query .= " ORDER BY t.created_at DESC";

    // Execute the query
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
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
    <title>Tickets by Department</title>
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

        .filter-form {
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 10px; /* Espaçamento entre os filtros */
        }

        .filter-form label {
            flex: 1 1 100px; /* Largura mínima para os rótulos */
            margin: 5px 0;
            text-align: left;
        }

        .filter-form select, 
        .filter-form input[type="date"] {
            flex: 1 1 150px; /* Largura mínima para os campos de entrada */
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .date-range {
            display: flex; /* Exibe os campos de data lado a lado */
            gap: 10px; /* Espaçamento entre os campos de data */
        }

        .filter-form button {
            flex: 1 1 100%;
            padding: 10px;
            background: #2575fc;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .filter-form button:hover {
            background: #6a11cb;
        }

        .ticket-list {
            margin-top: 20px;
            width: 100%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px; /* Cantos arredondados */
            overflow: hidden; /* Para aplicar o arredondamento */
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
            color: inherit; /* Remove o azul dos links */
            text-decoration: none; /* Remove o sublinhado dos links */
        }

        .action-button {
            display: inline-block;
            padding: 8px 16px;
            background: #2575fc;
            color: white;
            border-radius: 5px;
            text-align: center;
            transition: background 0.3s ease;
        }

        .action-button:hover {
            background: #6a11cb;
        }

        .no-tickets {
            margin-top: 1rem;
            font-size: 1.2rem;
        }

        .toggle-filters {
            cursor: pointer;
            background: #2575fc;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
            transition: background 0.3s ease;
        }

        .toggle-filters:hover {
            background: #6a11cb;
        }

        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Tickets by Department</h1>

        <button class="toggle-filters" onclick="toggleFilters()">Show/Hide Filters</button>

        <form action="" method="GET" class="filter-form hidden" id="filterForm">
            <label for="department_id">Department:</label>
            <select name="department_id" id="department_id">
                <option value="">All</option>
                <?php foreach ($departments as $department): ?>
                    <option value="<?= htmlspecialchars($department['id']); ?>" <?= $department_id == $department['id'] ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($department['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="status">Status:</label>
            <select name="status" id="status">
                <option value="">All</option>
                <option value="open" <?= $status == 'open' ? 'selected' : ''; ?>>Open</option>
                <option value="assigned" <?= $status == 'assigned' ? 'selected' : ''; ?>>Assigned</option>
                <option value="closed" <?= $status == 'closed' ? 'selected' : ''; ?>>Closed</option>
            </select>

            <label for="priority">Priority:</label>
            <select name="priority" id="priority">
                <option value="">All</option>
                <option value="low" <?= $priority == 'low' ? 'selected' : ''; ?>>Low</option>
                <option value="medium" <?= $priority == 'medium' ? 'selected' : ''; ?>>Medium</option>
                <option value="high" <?= $priority == 'high' ? 'selected' : ''; ?>>High</option>
            </select>

            <label for="assigned_agent">Assigned Agent:</label>
            <select name="assigned_agent" id="assigned_agent">
                <option value="">All</option>
                <?php foreach ($agents as $agent): ?>
                    <option value="<?= htmlspecialchars($agent['id']); ?>" <?= $assigned_agent == $agent['id'] ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($agent['username']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <div class="date-range">
                <div>
                    <label for="date_from">Date From:</label>
                    <input type="date" name="date_from" id="date_from" value="<?= htmlspecialchars($date_from); ?>">
                </div>
                <div>
                    <label for="date_to">Date To:</label>
                    <input type="date" name="date_to" id="date_to" value="<?= htmlspecialchars($date_to); ?>">
                </div>
            </div>

            <button type="submit" class="button">Filter</button>
        </form>

        <section class="ticket-list">
            <?php if (empty($tickets)): ?>
                <p class="no-tickets">No tickets found.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Department</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Created At</th>
                            <th>Assigned Agent</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tickets as $ticket): ?>
                            <tr>
                                <td><a href="view_ticket_agent.php?id=<?= htmlspecialchars($ticket['id']); ?>"><?= htmlspecialchars($ticket['title']); ?></a></td>
                                <td><?= htmlspecialchars($ticket['department_name']); ?></td>
                                <td><?= htmlspecialchars($ticket['status']); ?></td>
                                <td><?= htmlspecialchars($ticket['priority']); ?></td>
                                <td><?= htmlspecialchars($ticket['created_at']); ?></td>
                                <td><?= htmlspecialchars($ticket['assigned_agent']); ?></td>
                                <td>
                                    <a href="view_ticket_agent.php?id=<?= htmlspecialchars($ticket['id']); ?>" class="action-button">View Ticket</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>

        <div style="margin-top: 20px;">
            <a href="agent_dashboard.php" class="action-button">Back to Dashboard</a>
        </div>
    </div>

    <script>
        function toggleFilters() {
            const filterForm = document.getElementById('filterForm');
            filterForm.classList.toggle('hidden');
        }
    </script>
</body>
</html>