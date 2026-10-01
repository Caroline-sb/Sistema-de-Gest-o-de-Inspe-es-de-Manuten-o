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
// Pega apenas o primeiro nome para uma saudação mais amigável
$primeiro_nome = explode(' ', trim($nome))[0];
$cargo = $utilizador['cargo'] ?? 'Solicitante';

// Variáveis para os contadores do painel
$acoes_atrasadas = 0;
$total_pendentes = 0;
$aguardando_triagem = 0;

// Se for da equipe técnica, busca os números no banco
if ($cargo == 'Mantenedor' || $cargo == 'Supervisor') {
    try {
        // 1. Conta chamados aguardando triagem (status 'aberto')
        $sql_triagem = "SELECT COUNT(*) FROM chamados WHERE status_chamado = 'aberto'";
        $aguardando_triagem = $conexao->query($sql_triagem)->fetchColumn();

        // 2. Conta total de chamados pendentes (tudo que não está concluído ou duplicado)
        $sql_pendentes = "SELECT COUNT(*) FROM chamados WHERE status_chamado NOT IN ('concluido', 'duplicado')";
        $total_pendentes = $conexao->query($sql_pendentes)->fetchColumn();

        // 3. Ações atrasadas: Mantido 0 por enquanto até finalizarmos a tela de "Ações" e seus prazos.
        // $sql_atrasadas = "SELECT COUNT(*) FROM acoes WHERE status_acao != 'concluida' AND prazo < CURDATE()";
        // $acoes_atrasadas = $conexao->query($sql_atrasadas)->fetchColumn();
        
    } catch (PDOException $erro) {
        // Apenas previne que a página quebre caso falhe a contagem
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
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
        <h2 style="margin-bottom: 5px;">Bem-vindo, <?= htmlspecialchars($primeiro_nome) ?>!</h2>
        
        <?php if ($cargo == 'Mantenedor' || $cargo == 'Supervisor'): ?>
            <!-- ========================================== -->
            <!-- VISÃO DA EQUIPE TÉCNICA (Mantenedor/Supervisor) -->
            <!-- ========================================== -->
            <p>Painel de controlo da equipe de manutenção.</p>

            <section class="indicadores" aria-label="Indicadores gerais">
                
                <!-- Redireciona para Minhas Ações -->
                <div class="indicador" onclick="window.location.href='dashboard.php?filtroStatus=execucao'" style="cursor: pointer;" title="Ver ações em execução">
                    <span class="indicador-numero"><?= $acoes_atrasadas ?></span>
                    <span class="indicador-texto">Ações atrasadas</span>
                </div>
                
                <!-- Redireciona para Dashboard Completo -->
                <div class="indicador" onclick="window.location.href='dashboard.php'" style="cursor: pointer;" title="Ver todos os chamados">
                    <span class="indicador-numero"><?= $total_pendentes ?></span>
                    <span class="indicador-texto">Total de chamados pendentes</span>
                </div>
                
                <!-- Redireciona para Fila de Triagem -->
                <div class="indicador" onclick="window.location.href='dashboard.php?filtroStatus=aberto'" style="cursor: pointer;" title="Ir para a fila de triagem">
                    <span class="indicador-numero"><?= $aguardando_triagem ?></span>
                    <span class="indicador-texto">Aguardando Triagem</span>
                </div>
                
            </section>

            <h3 style="color: #143263; font-size: 16px; margin-top: 30px;">Acesso Rápido</h3>
            <nav class="acoes-rapidas" aria-label="Ações rápidas" style="justify-content: flex-start; flex-wrap: wrap;">
                <a href="dashboard.php" class="botao botao-primario">Dashboard Completo</a>
                
                <!-- BOTÃO DE RELATÓRIOS DESTACADO -->
                <a href="relatorios.php" class="botao botao-primario" style="background-color: #2ECC71; border-color: #27AE60;">Ver Relatórios</a>
                
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