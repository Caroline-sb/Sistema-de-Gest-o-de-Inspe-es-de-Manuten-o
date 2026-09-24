<?php
session_start();
require "bancodados.php";

// Bloqueia quem não estiver logado
if (!isset($_SESSION['id_matricula']) || empty($_SESSION['id_matricula'])) {
    header("Location: login.html");
    exit;
}

// Verifica se o ID do chamado foi enviado na URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Erro: Nenhum chamado selecionado para a ação.");
}

$id_chamado = $_GET['id'];

// Busca as informações do chamado e a lista de funcionários (para o responsável)
try {
    $sql = "SELECT * FROM chamados WHERE id_chamados = :id";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':id', $id_chamado);
    $stmt->execute();
    $chamado = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$chamado) {
        die("Erro: Chamado não encontrado.");
    }

    $codigo = 'OC-' . str_pad($chamado['id_chamados'], 5, '0', STR_PAD_LEFT);

    // Busca os funcionários para preencher o campo de responsável
    $sql_func = "SELECT id_matricula, nome FROM funcionarios WHERE cargo IN ('Mantenedor', 'Supervisor') ORDER BY nome ASC";
    $stmt_func = $conexao->prepare($sql_func);
    $stmt_func->execute();
    $funcionarios = $stmt_func->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $erro) {
    die("Erro ao buscar dados: " . $erro->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ação corretiva</title>
    <link rel="stylesheet" href="demandas.css">
    <link rel="stylesheet" href="acao.css">
    <style>
        input[readonly] {
            background-color: #F5F7FA;
            color: #52627E;
            border: 1px solid #D1D9E6;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <header>
        <h1><span class="logo-opera">Opera</span><span class="logo-check">Check</span></h1>
        <a href="logout.php" class="botao-sair" style="float: right; margin-top: -35px; color: white; text-decoration: none;">Sair</a>
    </header>
    <main>
    <article>
        <h2>Ação corretiva</h2>
        <p>Use esta página quando não for possível concluir o chamado agora (ex: falta de peça ou equipamento).</p>

        <!-- O formulário aponta para salvar_acao.php e aceita ficheiros (enctype) -->
        <form action="salvar_acao.php" method="post" enctype="multipart/form-data">
            
            <!-- Passa o ID do chamado oculto -->
            <input type="hidden" name="id_chamados" value="<?= $chamado['id_chamados'] ?>">

            <fieldset>
                <legend>Chamado de origem</legend>

                <div>
                    <label for="chamadoId">Código do chamado</label>
                    <input type="text" id="chamadoId" name="chamadoId" value="<?= htmlspecialchars($codigo) ?>" readonly>
                </div>

                <div>
                    <label for="chamadoTitulo">Título do chamado</label>
                    <input type="text" id="chamadoTitulo" name="chamadoTitulo" value="<?= htmlspecialchars($chamado['titulo']) ?>" readonly>
                </div>
            </fieldset>

            <fieldset>
                <legend>Dados da ação</legend>

                <div>
                    <label for="titulo">Título da ação</label>
                    <input type="text" id="titulo" name="titulo" required>
                </div>

                <div>
                    <label for="prazo">Prazo</label>
                    <input type="date" id="prazo" name="prazo" required>
                </div>

                <div>
                    <label for="statusAcao">Status da ação</label>
                    <select id="statusAcao" name="statusAcao" required>
                        <option value="">Selecione o status</option>
                        <option value="aberta">Aberta</option>
                        <option value="aguardando_peca">Aguardando peça/equipamento</option>
                        <option value="em_andamento">Em andamento</option>
                        <option value="concluida">Concluída</option>
                    </select>
                </div>

                <div>
                    <label for="responsavel">Responsável</label>
                    <!-- Transformado em select para puxar os técnicos reais do banco e evitar erros -->
                    <select id="responsavel" name="responsavel" required>
                        <option value="">Selecione o responsável</option>
                        <?php foreach ($funcionarios as $func): ?>
                            <option value="<?= $func['id_matricula'] ?>"><?= htmlspecialchars($func['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="descricaoAcao">Descrição da ação</label>
                    <textarea id="descricaoAcao" name="descricaoAcao" rows="3" required></textarea>
                </div>

                <div>
                    <label for="comentarios">Comentários</label>
                    <textarea id="comentarios" name="comentarios" rows="3"></textarea>
                </div>

                <div>
                    <label for="anexos">Anexos</label>
                    <input type="file" id="anexos" name="anexos">
                </div>
            </fieldset>

            <button type="submit">Salvar</button>
            <button type="reset">Cancelar</button>
        </form>

        <nav aria-label="Ações da página">
            <button type="button" id="botao-voltar" onclick="window.location.href='dashboard.php'">Voltar ao painel</button>
        </nav>
    </article>
    </main>
    <footer>
        <p>&copy; 2026 OperaCheck. Todos os direitos reservados.</p>
    </footer>
</body>
</html>