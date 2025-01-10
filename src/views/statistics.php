<?php
session_start();
require_once '../config/db.php';

// Verifica se o usuário está logado e se é um administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Inicializa variáveis
$filterStatus = isset($_POST['filter_status']) ? $_POST['filter_status'] : 'all';

// Consulta para obter o número total de usuários
$totalUsersQuery = $pdo->query("SELECT COUNT(*) as total FROM users");
$totalUsers = $totalUsersQuery->fetch(PDO::FETCH_ASSOC)['total'];

// Consulta para obter o número total de tickets
$totalTicketsQuery = $pdo->query("SELECT COUNT(*) as total FROM tickets");
$totalTickets = $totalTicketsQuery->fetch(PDO::FETCH_ASSOC)['total'];

// Consulta para obter o número de tickets por status
$ticketsByStatusQuery = $pdo->query("SELECT STATUS, COUNT(*) as count FROM tickets GROUP BY STATUS");
$ticketsByStatus = $ticketsByStatusQuery->fetchAll(PDO::FETCH_ASSOC);

// Consulta para obter o número de tickets por prioridade
$ticketsByPriorityQuery = $pdo->query("SELECT priority, COUNT(*) as count FROM tickets GROUP BY priority");
$ticketsByPriority = $ticketsByPriorityQuery->fetchAll(PDO::FETCH_ASSOC);

// Consulta para obter o número de departamentos
$totalDepartmentsQuery = $pdo->query("SELECT COUNT(*) as total FROM departments");
$totalDepartments = $totalDepartmentsQuery->fetch(PDO::FETCH_ASSOC)['total'];
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estatísticas</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            max-width: 1200px; /* Ajustado para um layout 16:9 */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .charts {
            display: flex;
            justify-content: space-around; /* Para colocar os gráficos lado a lado */
            flex-wrap: wrap; /* Para permitir que os gráficos se ajustem em telas menores */
        }
        canvas {
            margin: 20px 0;
        }
        .status-chart {
            width: 600px; /* Aumentado para o gráfico de status */
            height: 400px; /* Aumentado para o gráfico de status */
        }
        .priority-chart {
            width: 400px; /* Ajustado para o gráfico de prioridade */
            height: 400px; /* Ajustado para o gráfico de prioridade */
        }
        .button {
            padding: 10px;
            background: #2575fc;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin: 10px;
        }
        .button:hover {
            background: #6a11cb;
        }
        .filter {
            margin: 20px 0;
        }
        select {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            background: rgba(255, 255, 255, 0.8);
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Estatísticas</h1>
        <p>Total de Usuários: <?= htmlspecialchars($totalUsers); ?></p>
        <p>Total de Tickets: <?= htmlspecialchars($totalTickets); ?></p>
        <p>Total de Departamentos: <?= htmlspecialchars($totalDepartments); ?></p>

        <div class="filter">
            <form method="POST">
                <label for="filter_status">Filtrar por Status:</label>
                <select name="filter_status" id="filter_status">
                    <option value="all">Todos</option>
                    <option value="open" <?= $filterStatus === 'open' ? 'selected' : ''; ?>>Abertos</option>
                    <option value="assigned" <?= $filterStatus === 'assigned' ? 'selected' : ''; ?>>Atribuídos</option>
                    <option value="closed" <?= $filterStatus === 'closed' ? 'selected' : ''; ?>>Fechados</option>
                </select>
                <button type="submit" class="button">Aplicar Filtro</button>
                <button type="button" class="button" onclick="recalculate()">Recalcular</button>
            </form>
        </div>

        <div class="charts">
            <div>
                <h2>Tickets por Status</h2>
                <canvas id="ticketsByStatusChart" class="status-chart"></canvas>
            </div>

            <div>
                <h2>Tickets por Prioridade</h2>
                <canvas id="ticketsByPriorityChart" class="priority-chart"></canvas>
            </div>
        </div>

        <div style="margin-top: 20px;">
            <a href="admin_dashboard.php" class="button">Voltar ao Dashboard</a>
        </div>
    </div>

    <script>
        // Gráfico de Tickets por Status
        const ticketsByStatusCtx = document.getElementById('ticketsByStatusChart').getContext('2d');
        const ticketsByStatusData = {
            labels: <?= json_encode(array_column($ticketsByStatus, 'STATUS')); ?>,
            datasets: [{
                label: 'Número de Tickets',
                data: <?= json_encode(array_column($ticketsByStatus, 'count')); ?>,
                backgroundColor: [
                    'rgba(75, 192, 192, 0.6)',
                    'rgba(255, 99, 132, 0.6)',
                    'rgba(255, 206, 86, 0.6)',
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(255, 206, 86, 1)',
                ],
                borderWidth: 1
            }]
        };
        const ticketsByStatusChart = new Chart(ticketsByStatusCtx, {
            type: 'bar',
            data: ticketsByStatusData,
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: 'white' // Melhor contraste
                        }
                    },
                    x: {
                        ticks: {
                            color: 'white' // Melhor contraste
                        }
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            color: 'white' // Melhor contraste
                        }
                    }
                }
            }
        });

        // Gráfico de Tickets por Prioridade
        const ticketsByPriorityCtx = document.getElementById('ticketsByPriorityChart').getContext('2d');
        const ticketsByPriorityData = {
            labels: <?= json_encode(array_column($ticketsByPriority, 'priority')); ?>,
            datasets: [{
                label: 'Número de Tickets',
                data: <?= json_encode(array_column($ticketsByPriority, 'count')); ?>,
                backgroundColor: [
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(255, 159, 64, 0.6)',
                    'rgba(255, 99, 132, 0.6)',
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(255, 99, 132, 1)',
                ],
                borderWidth: 1
            }]
        };
        const ticketsByPriorityChart = new Chart(ticketsByPriorityCtx, {
            type: 'pie',
            data: ticketsByPriorityData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            color: 'white' // Melhor contraste
                        }
                    },
                    title: {
                        display: true,
                        text: 'Distribuição de Tickets por Prioridade',
                        color: 'white' // Melhor contraste
                    }
                }
            }
        });

        function recalculate() {
            // Função para recalcular as estatísticas
            location.reload(); // Recarrega a página para atualizar os dados
        }
    </script>
</body>
</html>