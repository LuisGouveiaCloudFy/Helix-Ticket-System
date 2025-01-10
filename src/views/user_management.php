<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Verifica se o usuário está logado e se é um administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php'); // Redireciona para a página de login se não for admin
    exit;
}

// Inclui o arquivo de configuração do banco de dados
require_once '../config/db.php';

try {
    // Lê os dados dos usuários usando a variável $pdo para a conexão PDO
    $query = "SELECT id, name, username, email, role, created_at FROM users ORDER BY created_at DESC";
    $result = $pdo->query($query); // Usa $pdo que é definido em db.php
} catch (PDOException $e) {
    die("Erro ao executar a consulta: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>User Management</title>
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
            width: 95%; /* Aumenta a largura da janela */
            max-width: 1000px; /* Aumenta a largura máxima da janela */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            table-layout: auto; /* Permite que as colunas se ajustem ao conteúdo */
        }

        th, td {
            padding: 0.8rem;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
            vertical-align: middle; /* Alinhamento vertical */
            overflow: hidden; /* Esconde o conteúdo que excede a largura da célula */
            text-overflow: ellipsis; /* Adiciona reticências para texto longo */
            white-space: nowrap; /* Impede a quebra de linha */
        }

        th {
            background: rgba(255, 255, 255, 0.2);
        }

        tr:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 50px; /* Altura consistente */
            gap: 0.5rem; /* Aumenta o espaço entre os botões */
        }

        .btn {
            padding: 0.4rem 0.6rem; /* Ajusta o padding para caber melhor */
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.8rem; /* Ajusta o tamanho da fonte */
            transition: background 0.3s ease;
        }

        .btn-edit {
            background: #2575fc;
        }

        .btn-delete {
            background: #ff4d4d;
        }

        .btn-dashboard {
            background: #28a745; /* Cor verde para o botão de dashboard */
            padding: 0.6rem 1.2rem; /* Estilo do botão */
            border-radius: 5px; /* Bordas arredondadas */
            text-decoration: none; /* Remove sublinhado */
        }

        .btn:hover {
            opacity: 0.8;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            margin: 0;
        }

        .header a {
            padding: 0.6rem 1.2rem;
            background: #2575fc;
            border: none;
            border-radius: 5px;
            color: white;
            text-decoration: none;
            font-size: 1rem;
            transition: background 0.3s ease;
        }

        .header a:hover {
            background: #6a11cb;
        }

        /* Estilos do Modal */
        .modal {
            display: none; /* Oculto por padrão */
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.7); /* Fundo escuro e opaco */
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe; /* Cor de fundo do conteúdo do modal */
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Sombra para o conteúdo do modal */
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        input[type="password"] {
            width: 100%; /* Garante que o campo ocupe toda a largura disponível */
            padding: 0.6rem;
            margin: 0.5rem 0; /* Margem igual em todos os lados */
            border: 2px solid black; /* Contorno preto */
            border-radius: 5px;
            background-color: #f0f0f0; /* Cor de fundo mais clara para melhor contraste */
            color: black; /* Texto em preto */
            box-sizing: border-box; /* Inclui padding e border na largura total */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>User Management</h1>
            <a href="dashboard.php" class="btn btn-dashboard">Voltar ao Dashboard</a>
        </div>

        <?php if ($result && $result->rowCount() > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = $result->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['id']); ?></td>
                            <td><?= htmlspecialchars($user['name']); ?></td>
                            <td><?= htmlspecialchars($user['username']); ?></td>
                            <td><?= htmlspecialchars($user['email']); ?></td>
                            <td><?= htmlspecialchars($user['role']); ?></td>
                            <td><?= htmlspecialchars($user['created_at']); ?></td>
                            <td class="actions">
                                <a href="edit_user.php?id=<?= $user['id']; ?>" class="btn btn-edit">Edit</a>
                                <button class="btn btn-delete" onclick="openModal(<?= $user['id']; ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No users found.</p>
        <?php endif; ?>
    </div>

    <!-- Modal -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 style="font-weight: bold; color: black;">Confirm Delete</h2>
            <p style="font-weight: bold; color: black;">Enter your password to confirm deletion:</p>
            <input type="password" id="password" placeholder="Password" style="margin: 0.5rem 0; background-color: #f0f0f0; color: black; border: 2px solid black; border-radius: 5px; width: calc(100% - 1rem);">
            <button id="confirmDelete" class="btn btn-delete" disabled style="background-color: #ff4d4d; color: white;">Confirm Delete</button>
            <p id="timer" style="font-weight: bold; color: black;">Please wait <span id="countdown">10</span> seconds before confirming.</p>
        </div>
    </div>

    <script>
        let currentUserId;
        let countdownTimer;

        function openModal(userId) {
            currentUserId = userId; // Armazena o ID do usuário a ser excluído
            document.getElementById("myModal").style.display = "block";
            startCountdown(); // Inicia o cronômetro
        }

        function closeModal() {
            document.getElementById("myModal").style.display = "none";
            document.getElementById("password").value = ""; // Limpa o campo de senha
            clearInterval(countdownTimer); // Limpa o cronômetro
            document.getElementById("confirmDelete").disabled = true; // Desabilita o botão
            document.getElementById("countdown").innerText = "10"; // Reseta o cronômetro
        }

        function startCountdown() {
            let timeLeft = 10;
            document.getElementById("confirmDelete").disabled = true; // Desabilita o botão inicialmente
            document.getElementById("timer").style.display = "block"; // Exibe o timer

            countdownTimer = setInterval(function() {
                timeLeft--;
                document.getElementById("countdown").innerText = timeLeft;

                if (timeLeft <= 0) {
                    clearInterval(countdownTimer);
                    document.getElementById("confirmDelete").disabled = false; // Habilita o botão após 10 segundos
                    document.getElementById("timer").style.display = "none"; // Esconde o timer
                }
            }, 1000);
        }

        // Confirmar a exclusão do usuário
        document.getElementById("confirmDelete").onclick = function() {
    const password = document.getElementById("password").value;

    // Verifica se a senha foi inserida
            if (!password) {
                alert("Please enter your password.");
                return;
            }

            // Enviar a senha para o servidor para validação
            fetch('user_delete.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: currentUserId, password: password })
            })
            .then(response => response.json())
            .then(data => {
                console.log(data); // Verifica a resposta do servidor
                if (data.success) {
                    // Redireciona para a página de gerenciamento de usuários após a exclusão
                    window.location.reload();
                } else {
                    alert(data.message); // Exibe mensagem de erro
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                alert("An error occurred. Please try again.");
            });
        };
    </script>
</body>
</html>