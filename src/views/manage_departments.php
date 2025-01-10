<?php
session_start();
require_once '../config/db.php';

// Verifica se o usuário está logado e se é um administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Inicializa variáveis
$error = '';
$success = '';

// Processa a exclusão de um departamento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_department'])) {
    $department_id = intval($_POST['department_id']);
    try {
        $stmt = $pdo->prepare("DELETE FROM departments WHERE id = ?");
        $stmt->execute([$department_id]);
        $success = "Departamento excluído com sucesso!";
    } catch (PDOException $e) {
        $error = "Erro ao excluir departamento: " . $e->getMessage();
    }
}

// Processa a adição de um novo departamento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_department'])) {
    $department_name = trim($_POST['department_name']);
    if (!empty($department_name)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO departments (NAME) VALUES (?)"); // Respeitando a ordem
            $stmt->execute([$department_name]);
            $success = "Departamento adicionado com sucesso!";
        } catch (PDOException $e) {
            $error = "Erro ao adicionar departamento: " . $e->getMessage();
        }
    } else {
        $error = "O nome do departamento é obrigatório!";
    }
}

// Busca todos os departamentos
try {
    $stmt = $pdo->query("SELECT * FROM departments");
    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erro ao buscar departamentos: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Departamentos</title>
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
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #fff;
        }
        th {
            background: #2575fc;
            color: white;
        }
        .button {
            padding: 10px;
            background: #2575fc;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .button:hover {
            background: #6a11cb;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group input {
            padding: 10px;
            width: calc(100% - 22px);
            border: 1px solid #ccc;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gerenciar Departamentos</h1>

        <?php if ($error): ?>
            <div class="error-message"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success-message"><?= htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <input type="text" name="department_name" placeholder="Nome do Departamento" required>
            </div>
            <button type="submit" name="add_department" class="button">Adicionar Departamento</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($departments as $department): ?>
                    <tr>
                        <td><?= htmlspecialchars($department['id']); ?></td>
                        <td><?= htmlspecialchars($department['NAME']); ?></td> <!-- Respeitando a ordem -->
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="department_id" value="<?= $department['id']; ?>">
                                <button type="submit" name="delete_department" class="button">Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            <a href="admin_dashboard.php" class="button">Voltar ao Dashboard</a>
        </div>
    </div>
</body>
</html>