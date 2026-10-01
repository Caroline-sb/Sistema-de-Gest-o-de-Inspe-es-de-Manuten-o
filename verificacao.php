<?php
session_start();
require "bancodados.php";

// Bloqueia quem não estiver logado
if (!isset($_SESSION['id_matricula']) || empty($_SESSION['id_matricula'])) {
    header("Location: login.html");
    exit;
}

// Só supervisor pode acessar
$id_utilizador = $_SESSION['id_matricula'];
$sql_user = "SELECT cargo, nome FROM funcionarios WHERE id_matricula = :id";
$stmt_user = $conexao->prepare($sql_user);
$stmt_user->bindParam(':id', $id_utilizador);
$stmt_user->execute();
$utilizador = $stmt_user->fetch(PDO::FETCH_ASSOC);

if ($utilizador['cargo'] !== 'Supervisor') {
    header("Location: dashboard.php");
    exit;
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Erro: Nenhum chamado selecionado.");
}

$id_chamado = $_GET['id'];

try {
    // Busca os dados do chamado e o nome de quem foi designado
    $sql = "SELECT c.*, f.nome AS nome_designado
            FROM chamados c
            LEFT JOIN funcionarios f ON c.id_funcionario_designado = f.id_matricula
            WHERE c.id_chamados = :id";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':id', $id_chamado);
    $stmt->execute();
    $chamado = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$chamado) {
        die("Erro: Chamado não encontrado.");
    }

    $codigo = 'OC-' . str_pad($chamado['id_chamados'], 5, '0', STR_PAD_LEFT);

    // Busca as ações corretivas vinculadas ao chamado (se houver)
    $sql_acoes = "SELECT a.*, f.nome AS nome_responsavel
                  FROM acoes a
                  LEFT JOIN funcionarios f ON a.responsavel = f.id_matricula
                  WHERE a.id_chamados = :id
                  ORDER BY a.id_acao ASC";
    $stmt_acoes = $conexao->prepare($sql_acoes);
    $stmt_acoes->bindParam(':id', $id_chamado);
    $stmt_acoes->execute();
    $acoes = $stmt_acoes->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $erro) {
    die("Erro ao buscar dados: " . $erro->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificação do supervisor</title>
    <link rel="stylesheet" href="demandas.css">
    <link rel="stylesheet" href="verificacao.css">
    <style>
        input[readonly], textarea[readonly] {
            background-color: #F5F7FA;
            color: #52627E;
            border: 1px solid #D1D9E6;
            cursor: not-allowed;
        }
        .card-acao {
            background-color: #F8FBFE;
            border: 1px solid #BFE0F5;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
        }
        .card-acao h4 {
            color: #143263;
            margin: 0 0 10px 0;
        }
        .card-acao p {
            margin: 4px 0;
            font-size: 14px;
            color: #52627E;
        }
        .card-acao strong {
            color: #17233D;
        }
        .sem-acoes {
            color: #A0AABF;
            font-size: 14px;
            padding: 10px 0;
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
        <h2>Verificação do supervisor</h2>
        <p>Confirme se o serviço foi realmente executado antes de encerrar o chamado.</p>

        <form action="salvar_verificacao.php" method="post" enctype="multipart/form-data">

            <input type="hidden" name="id_chamados" value="<?= $chamado['id_chamados'] ?>">

            <fieldset>
                <legend>Chamado de origem</legend>

                <div>
                    <label for="chamadoId">Código do chamado</label>
                    <input type="text" id="chamadoId" value="<?= htmlspecialchars($codigo) ?>" readonly>
                </div>

                <div>
                    <label for="chamadoTitulo">Título do chamado</label>
                    <input type="text" id="chamadoTitulo" value="<?= htmlspecialchars($chamado['titulo']) ?>" readonly>
                </div>

                <div>
                    <label for="chamadoResponsavel">Executado por</label>
                    <input type="text" id="chamadoResponsavel" value="<?= htmlspecialchars($chamado['nome_designado'] ?? 'N/A') ?>" readonly>
                </div>

                <div>
                    <label for="chamadoSetor">Setor</label>
                    <input type="text" id="chamadoSetor" value="<?= htmlspecialchars($chamado['setor']) ?>" readonly>
                </div>

                <div>
                    <label for="chamadoDescricao">Descrição do problema</label>
                    <textarea id="chamadoDescricao" rows="3" readonly><?= htmlspecialchars($chamado['descricao']) ?></textarea>
                </div>
            </fieldset>

            <!-- Ações corretivas registradas pelo mantenedor -->
            <fieldset>
                <legend>Ações corretivas registradas</legend>
                <?php if (count($acoes) > 0): ?>
                    <?php foreach ($acoes as $acao): ?>
                        <div class="card-acao">
                            <h4><?= htmlspecialchars($acao['titulo']) ?></h4>
                            <p><strong>Responsável:</strong> <?= htmlspecialchars($acao['nome_responsavel'] ?? 'N/A') ?></p>
                            <p><strong>Prazo:</strong> <?= htmlspecialchars($acao['prazo'] ?? 'N/A') ?></p>
                            <p><strong>Status:</strong> <?= htmlspecialchars($acao['status_acao']) ?></p>
                            <p><strong>Descrição:</strong> <?= htmlspecialchars($acao['descricao']) ?></p>
                            <?php if (!empty($acao['comentarios'])): ?>
                                <p><strong>Comentários:</strong> <?= htmlspecialchars($acao['comentarios']) ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="sem-acoes">Nenhuma ação corretiva registrada — serviço concluído diretamente.</p>
                <?php endif; ?>
            </fieldset>

            <fieldset>
                <legend>Verificação</legend>

                <div>
                    <label for="servicoRealizado">O serviço foi realizado corretamente?</label>
                    <select id="servicoRealizado" name="servicoRealizado" required>
                        <option value="">Selecione uma opção</option>
                        <option value="sim">Sim, confirmo a conclusão</option>
                        <option value="nao">Não, precisa de reavaliação</option>
                    </select>
                </div>

                <div>
                    <label for="validadoPor">Validado por</label>
                    <input type="text" id="validadoPor" name="validadoPor" value="<?= htmlspecialchars($utilizador['nome']) ?>" readonly>
                </div>

                <div>
                    <label for="observacaoSupervisor">Observação do supervisor</label>
                    <textarea id="observacaoSupervisor" name="observacaoSupervisor" rows="3"></textarea>
                </div>

                <div>
                    <label for="anexos">Foto de conclusão</label>
                    <input type="file" id="anexos" name="anexos" accept="image/*">
                </div>
            </fieldset>

            <button type="submit">Confirmar</button>
        </form>

        <nav aria-label="Ações da página">
            <button type="button" onclick="window.location.href='dashboard.php'">Voltar ao painel</button>
        </nav>
    </article>
    </main>
    <footer>
        <p>&copy; 2026 OperaCheck. Todos os direitos reservados.</p>
    </footer>
</body>
</html>