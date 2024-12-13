<?php
require_once '../config/db.php';  // Inclui o arquivo de configuração do banco de dados

session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: ../views/login.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Obtém os dados do formulário
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $department = trim($_POST['department']);
        $priority = trim($_POST['priority']);
        $status = 'open'; // Status padrão

        // Verifica se todos os campos obrigatórios estão preenchidos
        if (empty($title) || empty($description) || empty($department) || empty($priority)) {
            $error = "All fields are required.";
        } else {
            // Obtém o ID do usuário logado
            $client_id = $_SESSION['user_id'];

            // Prepara a inserção no banco de dados
            $stmt = $pdo->prepare("
                INSERT INTO tickets (client_id, department_id, title, status, priority, description) 
                VALUES (:client_id, :department_id, :title, :status, :priority, :description)
            ");
            $stmt->execute([
                ':client_id' => $client_id,
                ':department_id' => $department,
                ':title' => $title,
                ':status' => $status,
                ':priority' => $priority,
                ':description' => $description
            ]);

            $success = "Ticket successfully submitted.";
        }
    } catch (PDOException $e) {
        $error = "Error submitting the ticket: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit a Ticket</title>
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
            max-width: 600px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        header h1 {
            margin: 0;
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        label {
            font-weight: bold;
        }

        input, textarea, select {
            padding: 0.8rem;
            border: none;
            border-radius: 5px;
            width: 100%;
            box-sizing: border-box;
        }

        textarea {
            resize: none;
        }

        button {
            padding: 0.8rem;
            background: #2575fc;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background: #6a11cb;
        }

        .message {
            margin: 1rem 0;
            padding: 1rem;
            border-radius: 5px;
            text-align: center;
        }

        .error-message {
            background: rgba(255, 0, 0, 0.2);
            color: #ff4d4d;
        }

        .success-message {
            background: rgba(0, 255, 0, 0.2);
            color: #4dff4d;
        }

        .actions {
            display: flex;
            justify-content: space-between;
            margin-top: 1.5rem;
        }

        .btn {
            display: inline-block;
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
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Submit a New Ticket</h1>
        </header>

        <?php if (!empty($error)): ?>
            <div class="message error-message">
                <?= htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="message success-message">
                <?= htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <form action="submit_ticket.php" method="POST">
            <label for="title">Title:</label>
            <input type="text" name="title" id="title" required>

            <label for="description">Description:</label>
            <textarea name="description" id="description" rows="5" required></textarea>

            <label for="department">Department:</label>
            <select name="department" id="department" required>
                <option value="1">Accounting</option>
                <option value="2">Technical Support</option>
                <option value="3">HR</option>
                <option value="4">Sales</option>
            </select>

            <label for="priority">Priority:</label>
            <select name="priority" id="priority" required>
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
            </select>

            <button type="submit">Submit Ticket</button>
        </form>

        <div class="actions">
            <a href="../views/dashboard.php" class="btn">Back to Dashboard</a>
            <a href="my_tickets.php" class="btn">View My Tickets</a>
        </div>
    </div>
</body>
</html>
