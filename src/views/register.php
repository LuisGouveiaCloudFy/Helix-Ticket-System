<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>Register</title>
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
            padding: 2rem;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin: 0 auto;
            position: relative; /* Para posicionar o botão de voltar */
        }

        h1 {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1rem;
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
            margin-bottom: 0.5rem;
            border: none;
            border-radius: 8px;
            outline: none;
            background: rgba(255, 255, 255, 0.9);
            font-size: 1rem;
        }

        .buttons {
            display: flex;
            margin-top: 1rem;
        }

        button {
            width: 100%;
            padding: 1rem;
            background: #2575fc;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background: #6a11cb;
        }

        .back-link {
            position: absolute; /* Para posicionar no canto superior esquerdo */
            top: 1rem; /* Ajustado para ficar no topo */
            left: 1rem;
            padding: 0.5rem 1rem;
            background: #ff4081; /* Cor ajustada para melhor contraste */
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 1rem;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2); /* Sombra para dar profundidade */
        }

        .back-link:hover {
            background: #c60055; /* Cor de hover mais escura */
        }

        @media (max-width: 480px) {
            form {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <form action="../php/register.php" method="POST">
            <a href="javascript:history.back()" class="back-link">Voltar</a>
            <h1>Register</h1>
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" name="name" id="name" required placeholder="Enter your name">
            </div>
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" required placeholder="Enter your username">
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required placeholder="Enter your email">
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required placeholder="Enter your password">
            </div>
            <div class="buttons">
                <button type="submit">Register</button>
            </div>
        </form>
    </div>
</body>
</html>