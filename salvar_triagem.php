<?php
session_start();
require "bancodados.php";

// Verifica se os dados vieram do formulário (método POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Captura os dados preenchidos no formulário da triagem
    $id_chamado = $_POST['id_chamados'] ?? null;
    $tipo = $_POST['tipo'] ?? '';
    $designado = $_POST['designado'] ?? '';
    $observacao = $_POST['observacaoTriagem'] ?? '';

    // Confirma se o ID do chamado existe
    if ($id_chamado) {
        try {
            // Atualiza o chamado: grava os dados da triagem e muda o status para "execucao"
            $sql = "UPDATE chamados 
                    SET tipo_atendimento = :tipo, 
                        id_funcionario_designado = :designado, 
                        observacao_triagem = :observacao, 
                        status_chamado = 'execucao' 
                    WHERE id_chamados = :id";
            
            $stmt = $conexao->prepare($sql);
            
            // Liga as variáveis ao comando SQL
            $stmt->bindParam(':tipo', $tipo);
            $stmt->bindParam(':designado', $designado);
            $stmt->bindParam(':observacao', $observacao);
            $stmt->bindParam(':id', $id_chamado);
            
            // Executa a atualização
            $stmt->execute();

            // Redireciona de volta ao painel após salvar com sucesso
            header("Location: dashboard.php");
            exit;

        } catch (PDOException $erro) {
            die("Erro ao salvar a triagem na base de dados: " . $erro->getMessage());
        }
    } else {
        die("Erro: ID do chamado não foi recebido pelo sistema.");
    }
} else {
    // Se alguém tentar aceder a esta página diretamente, é devolvido ao dashboard
    header("Location: dashboard.php");
    exit;
}
?>