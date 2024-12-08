<?php
require_once '../config/db.php';  // Inclui o arquivo de configuração do banco de dados

session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    die("Você precisa estar logado para submeter um ticket.");
}

try {
    // Obtém os dados do formulário (supondo que 'title' e 'description' sejam os campos do ticket)
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $department_id = (int)$_POST['department_id'];  // Supondo que o ID do departamento seja passado no formulário

    // Verifica se os dados estão preenchidos
    if (empty($title) || empty($description) || empty($department_id)) {
        die("Todos os campos são obrigatórios!");
    }

    // Obtém o ID do usuário logado
    $user_id = $_SESSION['user_id'];

    // Prepara o comando SQL de inserção
    $stmt = $pdo->prepare("INSERT INTO tickets (user_id, department_id, title, description) VALUES (:user_id, :department_id, :title, :description)");

    // Executa a inserção no banco de dados
    $stmt->execute([
        ':user_id' => $user_id,
        ':department_id' => $department_id,
        ':title' => $title,
        ':description' => $description
    ]);

    echo "Ticket submetido com sucesso!";
} catch (PDOException $e) {
    // Exibe o erro caso ocorra
    die("Erro ao submeter o ticket: " . $e->getMessage());
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
            <?php if (isset($error)): ?>
                <div class="error-message"><?= htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div class="success-message"><?= htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <form action="submit_ticket.php" method="POST">
                <label for="title">Title:</label>
                <input type="text" name="title" id="title" required>

                <label for="description">Description:</label>
                <textarea name="description" id="description" rows="5" required></textarea>

                <label for="department">Department:</label>
                <select name="department" id="department">
                    <option value="Accounting">Accounting</option>
                    <option value="Technical Support">Technical Support</option>
                    <option value="HR">HR</option>
                    <option value="Sales">Sales</option>
                    <!-- Adicione mais departamentos conforme necessário -->
                </select>

                <button type="submit">Submit Ticket</button>
            </form>
        </section>
    </div>
</body>
</html>
