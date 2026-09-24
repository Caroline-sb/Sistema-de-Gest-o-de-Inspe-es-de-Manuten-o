<?php
session_start();
require "bancodados.php";

// Segurança: Se não estiver logado, volta para o login
if (!isset($_SESSION['id_matricula']) || empty($_SESSION['id_matricula'])) {
    header("Location: login.html");
    exit;
}

// Procura qual é o cargo da pessoa que fez o login
$id_utilizador = $_SESSION['id_matricula'];
$sql = "SELECT nome, cargo FROM funcionarios WHERE id_matricula = :id";
$stmt = $conexao->prepare($sql);
$stmt->bindParam(':id', $id_utilizador);
$stmt->execute();
$utilizador = $stmt->fetch(PDO::FETCH_ASSOC);

$nome = $utilizador['nome'] ?? 'Utilizador';
$cargo = $utilizador['cargo'] ?? 'Solicitante';
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OperaCheck - Início</title>
    <link rel="stylesheet" href="demandas.css">
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <header>
        <h1><span class="logo-opera">Opera</span><span class="logo-check">Check</span></h1>
        <a href="logout.php" class="botao-sair">Sair</a>
    </header>
    <main>
    <article>
        <h2 style="margin-bottom: 5px;">Bem-vindo, <?= htmlspecialchars($nome) ?>!</h2>
        
        <?php if ($cargo == 'Mantenedor' || $cargo == 'Supervisor'): ?>
            <!-- ========================================== -->
            <!-- VISÃO DA EQUIPA TÉCNICA (Mantenedor/Supervisor) -->
            <!-- ========================================== -->
            <p>Painel de controlo da equipa de manutenção.</p>

            <section class="indicadores" aria-label="Indicadores gerais">
                <div class="indicador">
                    <span class="indicador-numero">2</span>
                    <span class="indicador-texto">Ações atrasadas</span>
                </div>
                <div class="indicador">
                    <span class="indicador-numero">5</span>
                    <span class="indicador-texto">Total de chamados</span>
                </div>
                <div class="indicador">
                    <span class="indicador-numero">0</span>
                    <span class="indicador-texto">Aguardando Triagem</span>
                </div>
            </section>

            <h3 style="color: #143263; font-size: 16px; margin-top: 30px;">Acesso Rápido</h3>
            <nav class="acoes-rapidas" aria-label="Ações rápidas" style="justify-content: flex-start; flex-wrap: wrap;">
                <a href="dashboard.php" class="botao botao-primario">Dashboard Completo</a>
                <!-- Como Triagem e Ação dependem de um chamado específico, levamos o técnico para o painel para ele escolher o chamado na lista -->
                <a href="dashboard.php?filtroStatus=aberto" class="botao botao-secundario">Fila de Triagem</a>
                <a href="dashboard.php?filtroStatus=execucao" class="botao botao-secundario">Minhas Ações</a>
            </nav>

        <?php else: ?>
            <!-- ========================================== -->
            <!-- VISÃO DO SOLICITANTE (Trabalhador comum)   -->
            <!-- ========================================== -->
            <p>O que deseja fazer hoje?</p>
            
            <nav class="acoes-rapidas" aria-label="Ações rápidas" style="justify-content: flex-start; margin-top: 30px;">
                <a href="teladedemandas.html" class="botao botao-primario">Nova Demanda</a>
                <a href="dashboard.php" class="botao botao-secundario">Ver Meus Chamados</a>
            </nav>
        <?php endif; ?>

    </article>
    </main>
    <footer>
        <p>&copy; 2026 OperaCheck. Todos os direitos reservados.</p>
    </footer>
</body>
</html>