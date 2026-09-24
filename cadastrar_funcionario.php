<?php
/* =========================================================
   OperaCheck — Cadastra um novo funcionário (com login)
   ========================================================= */

require "bancodados.php";

$nome = $_POST["nome"];
$cargo = $_POST["cargo"];
$usuario = $_POST["usuario"];
$senha = $_POST["senha"];

$senhaCriptografada = password_hash($senha, PASSWORD_DEFAULT);

$comando = $conexao->prepare(
    "INSERT INTO Funcionarios (nome, cargo, usuario, senha)
     VALUES (:nome, :cargo, :usuario, :senha)"
);

$comando->execute([
    "nome" => $nome,
    "cargo" => $cargo,
    "usuario" => $usuario,
    "senha" => $senhaCriptografada
]);

// Redireciona pro login com aviso de sucesso
header("Location: login.html?sucesso=1");
exit;