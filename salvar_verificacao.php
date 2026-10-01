<?php
/* =========================================================
   OperaCheck — Salva a verificação do supervisor
   Recebe os dados do verificacao.php, insere na tabela
   Verificacoes e atualiza o status do chamado para
   "concluido" ou "reavaliacao" conforme a decisão.
   ========================================================= */

session_start();
require "bancodados.php";

// Bloqueia quem não estiver logado
if (!isset($_SESSION['id_matricula']) || empty($_SESSION['id_matricula'])) {
    header("Location: login.html");
    exit;
}

// Recebe os dados do formulário
$id_chamados        = $_POST['id_chamados'];
$servico_realizado  = $_POST['servicoRealizado'];
$validado_por       = $_POST['validadoPor'];
$observacao         = $_POST['observacaoSupervisor'];

// Trata o anexo (foto de conclusão)
$nome_foto = null;
if (isset($_FILES['anexos']) && $_FILES['anexos']['error'] === UPLOAD_ERR_OK) {
    $pasta_uploads = "uploads/";

    if (!is_dir($pasta_uploads)) {
        mkdir($pasta_uploads, 0777, true);
    }

    $nome_arquivo  = time() . "_" . str_replace(" ", "_", basename($_FILES['anexos']['name']));
    $caminho_final = $pasta_uploads . $nome_arquivo;

    if (move_uploaded_file($_FILES['anexos']['tmp_name'], $caminho_final)) {
        $nome_foto = $caminho_final;
    }
}

// Define o novo status do chamado conforme a decisão do supervisor
$novo_status = ($servico_realizado === 'sim') ? 'concluido' : 'reavaliacao';

try {
    // Insere o registro na tabela Verificacoes
    $sql = "INSERT INTO Verificacoes (id_chamados, servico_realizado, validado_por, observacao, foto_conclusao, data_verificacao)
            VALUES (:id_chamados, :servico_realizado, :validado_por, :observacao, :foto_conclusao, CURDATE())";

    $stmt = $conexao->prepare($sql);
    $stmt->execute([
        'id_chamados'       => $id_chamados,
        'servico_realizado' => $servico_realizado,
        'validado_por'      => $validado_por,
        'observacao'        => $observacao,
        'foto_conclusao'    => $nome_foto
    ]);

    // Atualiza o status do chamado
    $sql_update = "UPDATE Chamados SET status_chamado = :status WHERE id_chamados = :id";
    $stmt_update = $conexao->prepare($sql_update);
    $stmt_update->execute([
        'status' => $novo_status,
        'id'     => $id_chamados
    ]);

    // Redireciona pro dashboard com aviso
    header("Location: dashboard.php?sucesso=verificacao");
    exit;

} catch (PDOException $erro) {
    die("Erro ao salvar verificação: " . $erro->getMessage());
}