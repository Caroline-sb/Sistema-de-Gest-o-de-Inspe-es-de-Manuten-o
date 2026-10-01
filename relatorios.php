<?php
session_start();
require "bancodados.php";

// Bloqueia quem não estiver logado
if (!isset($_SESSION['id_matricula']) || empty($_SESSION['id_matricula'])) {
    header("Location: login.html");
    exit;
}

// Descobre o cargo de quem está logado (Apenas equipa técnica deve aceder)
$id_utilizador = $_SESSION['id_matricula'];
$sql_user = "SELECT cargo FROM funcionarios WHERE id_matricula = :id";
$stmt_user = $conexao->prepare($sql_user);
$stmt_user->bindParam(':id', $id_utilizador);
$stmt_user->execute();
$utilizador = $stmt_user->fetch(PDO::FETCH_ASSOC);
$cargo_logado = $utilizador['cargo'] ?? 'Solicitante';

if ($cargo_logado == 'Solicitante') {
    die("Acesso negado. Apenas a equipa de manutenção tem acesso aos relatórios.");
}

// Busca dos dados para os gráficos
$dados_grafico_status = [];
$dados_grafico_setor = [];

try {
    // Agrupa por status
    $sql_status = "SELECT status_chamado, COUNT(*) as quantidade FROM chamados GROUP BY status_chamado";
    $dados_grafico_status = $conexao->query($sql_status)->fetchAll(PDO::FETCH_ASSOC);

    // Agrupa por setor
    $sql_setor = "SELECT setor, COUNT(*) as quantidade FROM chamados GROUP BY setor";
    $dados_grafico_setor = $conexao->query($sql_setor)->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $erro) {
    die("Erro ao carregar dados: " . $erro->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios - OperaCheck</title>
    <link rel="stylesheet" href="demandas.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .area-graficos {
            display: flex;
            gap: 20px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        .grafico-container {
            flex: 1;
            min-width: 350px;
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border: 1px solid #E1E8ED;
        }
        .grafico-container h3 {
            margin-top: 0;
            color: #143263;
            font-size: 18px;
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #F0F4F8;
        }
        .canvas-wrapper {
            position: relative;
            height: 300px;
            width: 100%;
        }
    </style>
</head>
<body>
    <header>
        <h1><span class="logo-opera">Opera</span><span class="logo-check">Check</span></h1>
        <a href="logout.php" class="botao-sair">Sair</a>
    </header>
    <main>
    <article>
        <h2>Painel de Relatórios</h2>
        <p>Visão geral dos indicadores de manutenção e chamados.</p>

        <section class="area-graficos">
            <div class="grafico-container">
                <h3>Distribuição por Status</h3>
                <div class="canvas-wrapper">
                    <canvas id="graficoStatus"></canvas> 
                </div>
            </div>
            
            <div class="grafico-container">
                <h3>Volume de Chamados por Setor</h3>
                <div class="canvas-wrapper">
                    <canvas id="graficoSetor"></canvas>
                </div>
            </div>
        </section>

        <nav aria-label="Ações da página" style="margin-top: 30px;">
            <a href="index.php" class="botao-voltar">Voltar ao Início</a>
        </nav>
    </article>
    </main>
    <footer>
        <p>&copy; 2026 OperaCheck. Todos os direitos reservados.</p>
    </footer>

    <script>
        // Dados vindos do PHP
        const dadosStatusBrutos = <?= json_encode($dados_grafico_status) ?>;
        const dadosSetorBrutos = <?= json_encode($dados_grafico_setor) ?>;

        // Renderizar Gráfico de Status (Donut)
        const labelsStatus = dadosStatusBrutos.map(item => item.status_chamado.toUpperCase());
        const valoresStatus = dadosStatusBrutos.map(item => item.quantidade);
        
        const ctxStatus = document.getElementById('graficoStatus').getContext('2d');
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: labelsStatus,
                datasets: [{
                    data: valoresStatus,
                    backgroundColor: ['#3498DB', '#E74C3C', '#2ECC71', '#F1C40F', '#9B59B6', '#95A5A6', '#E67E22']
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // Renderizar Gráfico de Setores (Barras)
        const labelsSetor = dadosSetorBrutos.map(item => item.setor.toUpperCase());
        const valoresSetor = dadosSetorBrutos.map(item => item.quantidade);

        const ctxSetor = document.getElementById('graficoSetor').getContext('2d');
        new Chart(ctxSetor, {
            type: 'bar',
            data: {
                labels: labelsSetor,
                datasets: [{
                    label: 'Chamados',
                    data: valoresSetor,
                    backgroundColor: '#6C5CE7',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { 
                    y: { beginAtZero: true, ticks: { stepSize: 1 } } 
                },
                plugins: { 
                    legend: { display: false } 
                }
            }
        });
    </script>
</body>
</html>