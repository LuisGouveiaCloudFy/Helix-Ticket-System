<?php
require_once '../config/db.php';  // Inclui o arquivo de configuração do banco de dados

session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    die("Você precisa estar logado para submeter um ticket.");
}

$error = '';
$success = '';

try {
    // Obtém os dados do formulário
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $department = trim($_POST['department']);
    $status = 'open';  // Definido como padrão
    $priority = trim($_POST['priority']);

    // Verifica se os dados estão preenchidos
    if (empty($title) || empty($description) || empty($department) || empty($priority)) {
        $error = "Todos os campos são obrigatórios!";
    } else {
        // Obtém o ID do usuário logado
        $client_id = $_SESSION['user_id'];

        // Prepara o comando SQL de inserção
        $stmt = $pdo->prepare("INSERT INTO tickets (client_id, department_id, title, status, priority, description) VALUES (:client_id, :department_id, :title, :status, :priority, :description)");

        // Executa a inserção no banco de dados
        $stmt->execute([
            ':client_id' => $client_id,
            ':department_id' => $department,
            ':title' => $title,
            ':status' => $status,
            ':priority' => $priority,
            ':description' => $description
        ]);

        $success = "Ticket submetido com sucesso!";
    }
} catch (PDOException $e) {
    // Exibe o erro caso ocorra
    $error = "Erro ao submeter o ticket: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit a Ticket</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Submit a New Ticket</h1>
        </header>

        <section class="ticket-form">
            <?php if (!empty($error)): ?>
                <div class="error-message"><?= htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="success-message"><?= htmlspecialchars($success); ?></div>
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
                    <!-- Adicione mais departamentos conforme necessário -->
                </select>

                <label for="priority">Priority:</label>
                <select name="priority" id="priority" required>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>

                <button type="submit">Submit Ticket</button>
            </form>
        </section>
    </div>
</body>
</html>