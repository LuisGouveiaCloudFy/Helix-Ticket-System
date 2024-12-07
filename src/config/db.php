<?php
// Configuração do banco de dados
$host = 'localhost';
$dbname = 'helix';
$user = 'root'; // Ajuste o usuário se necessário
$password = ''; // Ajuste a senha se necessário

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}
?>
