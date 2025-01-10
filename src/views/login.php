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

        /* Estilo do modal */
        .modal {
            display: none; /* Escondido por padrão */
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
            border-radius: 10px;
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

        /* Ajuste da cor do texto da mensagem de erro */
        #errorMessage {
            color: #d8000c; /* Cor vermelha para a mensagem de erro */
            text-align: center; /* Centraliza a mensagem */
            font-weight: bold; /* Coloca a mensagem em negrito */
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
                <input type="password" name="password" id="password" required placeholder="Enter your password">
            </div>
            <div class="buttons" style="display: flex; justify-content: space-between;">
                <button type="submit" style="margin-right: 10px;">Login</button>
                <a href="register.php" class="register-link">Register</a>
            </div>
        </form>
    </div>

    <!-- Modal -->
    <div id="errorModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <p id="errorMessage"></p>
        </div>
    </div>

    <script>
        // Função para abrir o modal
        function openModal(message) {
            document.getElementById('errorMessage').innerText = message;
            document.getElementById('errorModal').style.display = "block";
        }

        // Função para fechar o modal
        function closeModal() {
            document.getElementById('errorModal').style.display = "none";
        }

        // Verifica se há uma mensagem de erro na URL
        const urlParams = new URLSearchParams(window.location.search);
        const errorMessage = urlParams.get('error');
        if (errorMessage) {
            openModal(decodeURIComponent(errorMessage));
        }
    </script>
</body>
</html>