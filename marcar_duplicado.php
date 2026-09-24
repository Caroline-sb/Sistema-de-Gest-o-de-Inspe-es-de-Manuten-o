<?php
session_start();
require "bancodados.php";

// Segurança
if (!isset($_SESSION['id_matricula']) || empty($_SESSION['id_matricula'])) {
    header("Location: login.html");
    exit;
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_chamado = $_GET['id'];

    try {
        // Atualiza o chamado no banco, mudando o status para 'duplicado'
        // Também salva uma observação automática para manter o histórico
        $sql = "UPDATE chamados 
                SET status_chamado = 'duplicado', 
                    observacao_triagem = 'Encerrado na triagem: Chamado marcado como duplicado.' 
                WHERE id_chamados = :id";
                
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':id', $id_chamado);
        $stmt->execute();

        // Redireciona de volta para o painel
        header("Location: dashboard.php");
        exit;

    } catch (PDOException $erro) {
        die("Erro ao marcar como duplicado: " . $erro->getMessage());
    }
} else {
    die("ID do chamado não fornecido.");
}
?>