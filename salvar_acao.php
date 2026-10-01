<?php
/* =========================================================
   OperaCheck — Salva a ação corretiva no banco
   Recebe os dados do acao.php, insere na tabela Acoes
   e atualiza o status do chamado para "aguardando_verificacao".
   ========================================================= */

session_start();
require "bancodados.php";

// Bloqueia quem não estiver logado
if (!isset($_SESSION['id_matricula']) || empty($_SESSION['id_matricula'])) {
    header("Location: login.html");
    exit;
}

// Recebe os dados do formulário
$id_chamados    = $_POST['id_chamados'];
$titulo         = $_POST['titulo'];
$prazo          = $_POST['prazo'];
$status_acao    = $_POST['statusAcao'];
$responsavel    = $_POST['responsavel'];
$descricao      = $_POST['descricaoAcao'];
$comentarios    = $_POST['comentarios'];

// Trata o anexo (se houver)
$nome_anexo = null;
if (isset($_FILES['anexos']) && $_FILES['anexos']['error'] === UPLOAD_ERR_OK) {
    $pasta_uploads = "uploads/";

    // Cria a pasta de uploads se não existir
    if (!is_dir($pasta_uploads)) {
        mkdir($pasta_uploads, 0777, true);
    }

    $nome_arquivo  = time() . "_" . str_replace(" ", "_", basename($_FILES['anexos']['name']));
    $caminho_final = $pasta_uploads . $nome_arquivo;

    if (move_uploaded_file($_FILES['anexos']['tmp_name'], $caminho_final)) {
        $nome_anexo = $caminho_final;
    }
}

try {
    // Insere a ação na tabela Acoes
    $sql = "INSERT INTO Acoes (id_chamados, titulo, descricao, prazo, status_acao, responsavel, comentarios, anexos)
            VALUES (:id_chamados, :titulo, :descricao, :prazo, :status_acao, :responsavel, :comentarios, :anexos)";

    $stmt = $conexao->prepare($sql);
    $stmt->execute([
        'id_chamados' => $id_chamados,
        'titulo'      => $titulo,
        'descricao'   => $descricao,
        'prazo'       => $prazo,
        'status_acao' => $status_acao,
        'responsavel' => $responsavel,
        'comentarios' => $comentarios,
        'anexos'      => $nome_anexo
    ]);

    // Atualiza o status do chamado para "aguardando_verificacao"
    $sql_update = "UPDATE Chamados SET status_chamado = 'aguardando_verificacao' WHERE id_chamados = :id";
    $stmt_update = $conexao->prepare($sql_update);
    $stmt_update->execute(['id' => $id_chamados]);

    // Redireciona pro dashboard com aviso de sucesso
    header("Location: dashboard.php?sucesso=acao");
    exit;

} catch (PDOException $erro) {
    die("Erro ao salvar ação: " . $erro->getMessage());
}