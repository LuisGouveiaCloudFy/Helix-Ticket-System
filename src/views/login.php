<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #fff;
        }

        .form-container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            padding: 1rem;
        }

        form {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 2.5rem;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin: 0 auto;
        }

        h1 {
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: normal;
            color: rgba(255, 255, 255, 0.9);
        }

        input {
            width: calc(100% - 2rem);
            padding: 1rem;
            margin-bottom: 1.5rem;
            border: none;
            border-radius: 8px;
            outline: none;
            background: rgba(255, 255, 255, 0.9);
            font-size: 1rem;
        }

        input:focus {
            box-shadow: 0 0 5px rgba(255, 255, 255, 0.8);
        }

        button {
            width: 100%;
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

        @media (max-width: 480px) {
            form {
                padding: 1.5rem;
            }
        }

        .register-link {
            width: 100%;
            padding: 0.8rem;
            background: #6a11cb;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s ease;
            text-decoration: none;
            text-align: center;
        }

        .register-link:hover {
            background: #5610a5;
        }
    </style>
</head>
<body>
    <div class="form-container">
    <form action="../php/login.php" method="POST">
    <h1>Login</h1>
    <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required placeholder="Enter your email">
    </div>
    <div class="form-group">
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required placeholder="Enter your password"> <!-- Verifique se o name está correto -->
    </div>
    <div class="buttons">
        <button type="submit">Login</button>
        <a href="register.php" class="register-link">Register</a>
    </div>
</form>
    </div>
</body>
</html>