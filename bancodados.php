<?php
/* =========================================================
   OperaCheck — Conexão com o banco de dados
   Este arquivo só cria a conexão (PDO) e é incluído no
   início de cada script PHP que precisa falar com o banco.
   ========================================================= */

$host = "localhost";
$banco = "Operacheck";
$usuario = "root";
$senha = ""; // troque pela sua senha real do MySQL

try {
    $conexao = new PDO(
        "mysql:host=$host;dbname=$banco;charset=utf8mb4",
        $usuario,
        $senha
    );

    // Faz o PDO avisar com erro de verdade se algo der errado,
    // em vez de falhar silenciosamente
    $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $erro) {
    // Se a conexão falhar, para tudo e mostra a mensagem
    die("Erro ao conectar no banco de dados: " . $erro->getMessage());
}
