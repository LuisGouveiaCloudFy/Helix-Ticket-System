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
            height: 100vh;
            color: #fff;
        }

        h1 {
            text-align: center;
            margin-bottom: 1rem;
        }

        form {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 2rem;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 0.8rem;
            margin-bottom: 1rem;
            border: none;
            border-radius: 5px;
            outline: none;
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
    </style>
</head>
<body>
    <form action="../php/login.php" method="POST">
        <h1>Login</h1>
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required placeholder="Enter your email">
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required placeholder="Enter your password">
        <button type="submit">Login</button>
    </form>
</body>
</html>
