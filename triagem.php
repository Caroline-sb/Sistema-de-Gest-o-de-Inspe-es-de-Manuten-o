<?php
session_start();
require "bancodados.php";

// Bloqueia quem não estiver logado
if (!isset($_SESSION['id_matricula']) || empty($_SESSION['id_matricula'])) {
    header("Location: login.html");
    exit;
}

// Verifica se o ID do chamado foi enviado
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Erro: Nenhum chamado selecionado para triagem.");
}

$id_chamado = $_GET['id'];

// 1. Busca as informações do chamado
try {
    $sql = "SELECT c.*, f.nome AS responsavel 
            FROM chamados c 
            LEFT JOIN funcionarios f ON c.id_funcionario_abertura = f.id_matricula 
            WHERE c.id_chamados = :id";
            
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':id', $id_chamado);
    $stmt->execute();
    $chamado = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$chamado) {
        die("Erro: Chamado não encontrado na base de dados.");
    }

    // 2. Busca a lista de funcionários (Mantenedores e Supervisores) para preencher o campo "Designado para"
    $sql_tecnicos = "SELECT id_matricula, nome FROM funcionarios WHERE cargo IN ('Mantenedor', 'Supervisor') ORDER BY nome ASC";
    $stmt_tecnicos = $conexao->prepare($sql_tecnicos);
    $stmt_tecnicos->execute();
    $tecnicos = $stmt_tecnicos->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $erro) {
    die("Erro ao buscar dados: " . $erro->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Triagem de chamado</title>
    <link rel="stylesheet" href="demandas.css">
    <link rel="stylesheet" href="triagem.css">
</head>
<body>
    <header>
        <h1><span class="logo-opera">Opera</span><span class="logo-check">Check</span></h1>
        <a href="logout.php" class="botao-sair" style="float: right; margin-top: -35px; color: white; text-decoration: none;">Sair</a>
    </header>
    <main>
    <article>
        <h2>Triagem de chamado</h2>
        <p>Confira as informações abaixo, verifique se não é um chamado duplicado e defina o encaminhamento.</p>

        <form action="salvar_triagem.php" method="post">
            
            <input type="hidden" name="id_chamados" value="<?= $chamado['id_chamados'] ?>">

            <fieldset>
                <legend>Dados do chamado</legend>

                <div>
                    <label for="titulo">Título</label>
                    <input type="text" id="titulo" name="titulo" value="<?= htmlspecialchars($chamado['titulo']) ?>" readonly>
                </div>

                <div>
                    <label for="data">Data</label>
                    <input type="date" id="data" name="data" value="<?= htmlspecialchars($chamado['data_visita']) ?>" readonly>
                </div>

                <div>
                    <label for="area">Área</label>
                    <input type="text" id="area" name="area" value="<?= htmlspecialchars($chamado['area']) ?>" readonly>
                </div>

                <div>
                    <label for="setor">Setor</label>
                    <input type="text" id="setor" name="setor" value="<?= htmlspecialchars($chamado['setor']) ?>" readonly>
                </div>

                <div>
                    <label for="responsavel">Responsável pela Solicitação</label>
                    <input type="text" id="responsavel" name="responsavel" value="<?= htmlspecialchars($chamado['responsavel'] ?? '') ?>" readonly>
                </div>

                <div>
                    <label for="descricao">Descrição</label>
                    <textarea id="descricao" name="descricao" rows="4" readonly><?= htmlspecialchars($chamado['descricao']) ?></textarea>
                </div>

                <div>
                    <label for="participantes">Período</label>
                    <input type="text" id="participantes" name="participantes" value="<?= htmlspecialchars($chamado['periodo'] ?? '') ?>" readonly>
                </div>

                <div>
                    <label for="anexos">Anexos</label>
                    <input type="text" id="anexos" name="anexos" value="<?= $chamado['anexos'] ? 'Possui anexo' : 'Nenhum anexo' ?>" readonly>
                </div>
            </fieldset>

            <fieldset>
                <legend>Possíveis chamados duplicados</legend>
                <ul id="lista-duplicados">
                    <li>Nenhum chamado parecido encontrado até o momento.</li>
                </ul>
            </fieldset>

            <fieldset>
                <legend>Decisão da triagem</legend>

                <div>
                    <label for="tipo">Tipo de atendimento</label>
                    <select id="tipo" name="tipo" required>
                        <option value="">Selecione o tipo de atendimento</option>
                        <option value="mecanico">Mecânico</option>
                        <option value="eletronico">Eletrônico</option>
                        <option value="ambos">Ambos</option>
                        <option value="nao_procede">Não é da manutenção</option>
                    </select>
                </div>

                <div>
                    <label for="designado">Designado para</label>
                    <!-- Agora é um select que mostra o nome mas envia o ID pro banco! -->
                    <select id="designado" name="designado" required>
                        <option value="">Selecione o técnico responsável</option>
                        <?php foreach ($tecnicos as $tecnico): ?>
                            <option value="<?= $tecnico['id_matricula'] ?>"><?= htmlspecialchars($tecnico['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="observacaoTriagem">Observação da triagem</label>
                    <textarea id="observacaoTriagem" name="observacaoTriagem" rows="3" required></textarea>
                </div>
            </fieldset>

            <button type="submit">Confirmar triagem</button>
            <button type="button" class="botao-duplicado" onclick="if(confirm('Tem certeza que este chamado é duplicado? Ele será encerrado imediatamente.')) { window.location.href='marcar_duplicado.php?id=<?= $chamado['id_chamados'] ?>'; }">Marcar como duplicado</button>
        </form>

        <nav aria-label="Ações do chamado">
            <button type="button" onclick="window.location.href='dashboard.php'">Voltar</button>
        </nav>
    </article>
    </main>
    <footer>
        <p>&copy; 2026 OperaCheck. Todos os direitos reservados.</p>
    </footer>
</body>
</html>