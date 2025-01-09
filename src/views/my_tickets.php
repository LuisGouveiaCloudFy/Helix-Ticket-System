<?php
require_once '../config/db.php';  // Includes the database configuration file

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You need to be logged in to view your tickets.");
}

$user_id = $_SESSION['user_id'];

try {
    // Prepare the SQL command to fetch the user's tickets
    $stmt = $pdo->prepare("SELECT t.id, t.title, t.status, t.priority, t.created_at, d.name AS department_name
                           FROM tickets t
                           LEFT JOIN departments d ON t.department_id = d.id
                           WHERE t.client_id = :client_id
                           ORDER BY t.created_at DESC");
    $stmt->execute([':client_id' => $user_id]);

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

        .button {
            padding: 10px;
            background: #2575fc;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s ease;
            margin: 10px 5px;
            text-decoration: none; /* Remove underline */
            width: 100%; /* Set button width to 100% */
            box-sizing: border-box; /* Include padding and border in the element's total width */
        }

        .button:hover {
            background: #6a11cb;
        }

        .view-button {
            background: #28a745; /* Green for view button */
            margin: 0; /* No margin for view button */
            width: 100%; /* Set view button width to 100% */
            white-space: nowrap; /* Prevent text from wrapping */
        }

        .view-button:hover {
            background: #5cb85c; /* Darker green on hover */
        }

        .button-container {
            margin-top: 20px; /* Space above the buttons */
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
                <p class="no-tickets">You have no submitted tickets.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Department</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Created At</th>
                            <th>Action</th>
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
                                <td>
                                    <a href="view_ticket.php?id=<?= htmlspecialchars($ticket['id']); ?>" class="button view-button">View Ticket</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>

        <div class="button-container">
            <a href="submit_ticket.php" class="button">Create New Ticket</a>
            <a href="dashboard.php" class="button">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>