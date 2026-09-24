<?php
/* =========================================================
   OperaCheck — Processa o login
   Recebe usuário/senha do login.html, confere no banco e,
   se estiver certo, guarda os dados na sessão e redireciona
   cada cargo pra tela certa.
   ========================================================= */

session_start();
require "bancodados.php";

$usuarioDigitado = $_POST["usuario"] ?? "";
$senhaDigitada = $_POST["senha"] ?? "";

// Busca o funcionário pelo usuário informado
$consulta = $conexao->prepare("SELECT * FROM Funcionarios WHERE usuario = :usuario");
$consulta->execute(["usuario" => $usuarioDigitado]);
$funcionario = $consulta->fetch(PDO::FETCH_ASSOC);

// Confere se o funcionário existe E se a senha digitada bate com o hash salvo
if ($funcionario && password_verify($senhaDigitada, $funcionario["senha"])) {

    // Guarda os dados do funcionário logado na sessão
    $_SESSION["id_matricula"] = $funcionario["id_matricula"];
    $_SESSION["nome"] = $funcionario["nome"];
    $_SESSION["cargo"] = $funcionario["cargo"];

    // Redireciona conforme o cargo
    if ($funcionario["cargo"] === "Solicitante") {
        header("Location: index.html");
    } else {
        // Mantenedor, Supervisor ou qualquer outro cargo interno
        header("Location: painel.html");
    }
    exit;

} else {
    // Usuário ou senha errados: volta pro login com um aviso
    header("Location: login.html?erro=1");
    exit;
}