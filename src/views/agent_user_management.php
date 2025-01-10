<?php
require_once '../config/db.php';  // Includes the database configuration file

session_start();

// Check if the user is logged in and is an agent
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
    die("Session variables are not set.");
}

if ($_SESSION['user_role'] !== 'agent') {
    die("You do not have permission to access this page.");
}

$user_id = $_SESSION['user_id'];

try {
    // Fetch users (excluding admins)
    $stmt = $pdo->prepare("SELECT id, username, email, role, created_at FROM users WHERE role != 'admin'");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching users: " . $e->getMessage());
}

// Handle user update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $update_id = $_POST['user_id'];
    $new_username = $_POST['username'];
    $new_email = $_POST['email'];

    try {
        $stmt = $pdo->prepare("UPDATE users SET username = :username, email = :email WHERE id = :id");
        $stmt->execute([':username' => $new_username, ':email' => $new_email, ':id' => $update_id]);
        header("Location: agent_user_management.php"); // Redirect to the same page to see changes
        exit;
    } catch (PDOException $e) {
        die("Error updating user: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
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
        }

        h1 {
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            overflow: hidden;
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

        .action-button {
            display: inline-block;
            padding: 10px 20px;
            background: #2575fc;
            color: white;
            border: none; /* Remover contorno */
            border-radius: 5px;
            text-align: center;
            transition: background 0.3s ease;
            cursor: pointer; /* Mudar o cursor para indicar que é clicável */
            font-size: 1rem; /* Tamanho da fonte */
        }

        .action-button:hover {
            background: #6a11cb;
        }

        .modal {
            display: flex;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.6);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
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

        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
            color: #333;
        }

        input[type="text"],
        input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .modal-buttons {
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>User Management</h1>
        <button class="action-button" onclick="window.location.href='dashboard.php'">Back to Dashboard</button>
        <?php if (empty($users)): ?>
            <p>No users found.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['id']); ?></td>
                            <td><?= htmlspecialchars($user['username']); ?></td>
                            <td><?= htmlspecialchars($user['email']); ?></td>
                            <td><?= htmlspecialchars($user['role']); ?></td>
                            <td><?= htmlspecialchars($user['created_at']); ?></td>
                            <td>
                                <button class="action-button" onclick="openModal('<?= $user['id']; ?>', '<?= htmlspecialchars($user['username']); ?>', '<?= htmlspecialchars($user['email']); ?>')">Edit</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Modal -->
    <div id="modal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <form id="modal-form" action="" method="POST">
                <input type="hidden" name="user_id" id="modal-user-id">
                <label for="username">Username:</label>
                <input type="text" name="username" id="modal-username" required>
                <label for="email">Email:</label>
                <input type="email" name="email" id="modal-email" required>
                <div class="modal-buttons">
                    <button type="submit" name="update_user" class="action-button">Update</button>
                    <button type="button" class="action-button" onclick="closeModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(userId, username, email) {
            document.getElementById('modal-user-id').value = userId;
            document.getElementById('modal-username').value = username;
            document.getElementById('modal-email').value = email;

            document.getElementById('modal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('modal').style.display = 'none';
        }
    </script>
</body>
</html>