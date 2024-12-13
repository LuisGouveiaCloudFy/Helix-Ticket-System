<?php
// Inclui o arquivo de configuração com as informações do banco de dados
require_once '../config/db.php';  // Ajuste o caminho conforme necessário

try {
    // Prepara os dados para inserir
    $departments = [
        'Accounting',
        'Technical Support',
        'HR',
        'Sales'
    ];

    // Prepara o comando SQL de inserção
    $stmt = $pdo->prepare("INSERT INTO departments (name) VALUES (:name)");

    // Insere cada departamento
    foreach ($departments as $department) {
        // Executa o comando SQL para inserir o departamento
        $stmt->execute([':name' => $department]);
    }

    echo "Departamentos inseridos com sucesso!";
} catch (PDOException $e) {
    // Exibe mensagem de erro caso algo dê errado
    echo "Erro ao inserir departamentos: " . $e->getMessage();
}
?>
