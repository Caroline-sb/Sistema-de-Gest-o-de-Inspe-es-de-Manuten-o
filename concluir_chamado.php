<?php
/* =========================================================
   OperaCheck — Conclui o chamado sem ação corretiva
   Apenas muda o status para aguardando_verificacao para
   o supervisor fazer o 2º check.
   ========================================================= */

session_start();
require "bancodados.php";

if (!isset($_SESSION['id_matricula']) || empty($_SESSION['id_matricula'])) {
    header("Location: login.html");
    exit;
}

$id_chamados = $_POST['id_chamados'];

try {
    $sql = "UPDATE Chamados SET status_chamado = 'aguardando_verificacao' WHERE id_chamados = :id";
    $stmt = $conexao->prepare($sql);
    $stmt->execute(['id' => $id_chamados]);

    header("Location: dashboard.php?sucesso=concluido");
    exit;

} catch (PDOException $erro) {
    die("Erro ao concluir chamado: " . $erro->getMessage());
}