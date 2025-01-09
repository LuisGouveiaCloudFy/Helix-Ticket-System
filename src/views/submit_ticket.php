<?php
require_once '../config/db.php';  // Includes the database configuration file

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You need to be logged in to submit a ticket.");
}

$error = '';
$success = '';

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get the form data with checks
        $title = isset($_POST['title']) ? trim($_POST['title']) : '';
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        $department = isset($_POST['department']) ? trim($_POST['department']) : '';
        $status = 'open';  // Default status
        $priority = isset($_POST['priority']) ? trim($_POST['priority']) : '';

        // Check if the data is filled
        if (empty($title) || empty($description) || empty($department) || empty($priority)) {
            $error = "All fields are required!";
        } else {
            // Get the logged-in user's ID
            $client_id = $_SESSION['user_id'];

            // Prepare the SQL insert command
            $stmt = $pdo->prepare("INSERT INTO tickets (client_id, department_id, title, status, priority, description) VALUES (:client_id, :department_id, :title, :status, :priority, :description)");

            // Execute the insertion into the database
            $stmt->execute([
                ':client_id' => $client_id,
                ':department_id' => $department,
                ':title' => $title,
                ':status' => $status,
                ':priority' => $priority,
                ':description' => $description
            ]);

            $success = "Ticket submitted successfully!";
        }
    } catch (PDOException $e) {
        // Display the error if it occurs
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
            max-width: 600px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h1 {
            margin-bottom: 1.5rem;
        }

        .error-message {
            background-color: rgba(255, 0, 0, 0.1);
            color: #d8000c;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 1rem;
        }

        .success-message {
            background-color: rgba(0, 255, 0, 0.1);
            color: #FFFFFF;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 1rem;
        }

        label {
            display: block;
            margin: 0.5rem 0;
        }

        input[type="text"],
        textarea,
        select {
            width: 100%; /* Set all fields to the same width */
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            font-size: 1rem;
            transition: background 0.3s ease;
            box-sizing: border-box; /* Include padding and border in the element's total width */
        }

        input[type="text"]:focus,
        textarea:focus,
        select:focus {
            background: rgba(255, 255, 255, 0.3);
            outline: none;
        }

        button {
            padding: 10px;
            background: #2575fc;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s ease;
            width: 100%;
            margin-top: 10px;
        }

        button:hover {
            background: #6a11cb;
        }

        .nav-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .nav-buttons a {
            text-decoration: none;
            color: white;
            background: #2575fc;
            padding: 10px;
            border-radius: 5px;
            flex: 1;
            margin: 0 5px;
            text-align: center;
        }

        .nav-buttons a:hover {
            background: #6a11cb;
        }
    </style>
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
                    <!-- Add more departments as needed -->
                </select>

                <label for="priority">Priority:</label>
                <select name="priority" id="priority" required>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>

                <button type="submit">Submit Ticket</button>
            </form>

            <div class="nav-buttons">
                <a href="dashboard.php">Back to Dashboard</a>
                <a href="my_tickets.php">View My Tickets</a>
            </div>
        </section>
    </div>
</body>
</html>