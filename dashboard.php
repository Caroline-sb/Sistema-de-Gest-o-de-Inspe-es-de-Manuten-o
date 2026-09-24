<?php
session_start();
require "bancodados.php";

// Bloqueia quem não estiver logado
if (!isset($_SESSION['id_matricula']) || empty($_SESSION['id_matricula'])) {
    header("Location: login.html");
    exit;
}

// Descobre o ID e o cargo de quem está logado
$id_utilizador = $_SESSION['id_matricula'];
$sql_user = "SELECT cargo FROM funcionarios WHERE id_matricula = :id";
$stmt_user = $conexao->prepare($sql_user);
$stmt_user->bindParam(':id', $id_utilizador);
$stmt_user->execute();
$utilizador = $stmt_user->fetch(PDO::FETCH_ASSOC);
$cargo_logado = $utilizador['cargo'] ?? 'Solicitante';

// Captura os filtros enviados pelo formulário
$filtroStatus = $_GET['filtroStatus'] ?? '';
$filtroSetor = $_GET['filtroSetor'] ?? '';

try {
    // Começa a construir a consulta base
    $sql = "SELECT c.id_chamados, c.titulo, c.setor, c.status_chamado, f.nome AS responsavel 
            FROM chamados c 
            LEFT JOIN funcionarios f ON c.id_funcionario_abertura = f.id_matricula 
            WHERE 1=1";
    
    $parametros = [];

    // REGRA DE SEGURANÇA: Se for Solicitante, filtra restritamente apenas pelos chamados dele
    if ($cargo_logado == 'Solicitante') {
        $sql .= " AND c.id_funcionario_abertura = :meu_id";
        $parametros[':meu_id'] = $id_utilizador;
    }

    // Se escolheu um Status no filtro da tela
    if (!empty($filtroStatus)) {
        $sql .= " AND c.status_chamado = :status";
        $parametros[':status'] = $filtroStatus;
    }

    // Se escolheu um Setor no filtro da tela
    if (!empty($filtroSetor)) {
        $sql .= " AND c.setor = :setor";
        $parametros[':setor'] = $filtroSetor;
    }

    // Ordena do mais novo para o mais antigo
    $sql .= " ORDER BY c.id_chamados DESC";
    
    // Prepara e executa a busca com os parâmetros de forma segura
    $stmt = $conexao->prepare($sql);
    $stmt->execute($parametros);
    $chamados = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $erro) {
    die("Erro ao buscar chamados no banco de dados: " . $erro->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="30">
    <title>Dashboard de chamados</title>
    <link rel="stylesheet" href="demandas.css">
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <header>
        <h1><span class="logo-opera">Opera</span><span class="logo-check">Check</span></h1>
        <a href="logout.php" class="botao-sair">Sair</a>
    </header>
    <main>
    <article>
        <h2>Chamados</h2>
        <p>Acompanhe todos os chamados cadastrados e o status atual de cada um.</p>

        <form action="dashboard.php" method="get" class="filtros">
            <div>
                <label for="filtroStatus">Status</label>
                <select id="filtroStatus" name="filtroStatus">
                    <option value="">Todos</option>
                    <option value="aberto" <?= $filtroStatus == 'aberto' ? 'selected' : '' ?>>Aberto</option>
                    <option value="triagem" <?= $filtroStatus == 'triagem' ? 'selected' : '' ?>>Em triagem</option>
                    <option value="execucao" <?= $filtroStatus == 'execucao' ? 'selected' : '' ?>>Em execução</option>
                    <option value="verificacao" <?= $filtroStatus == 'verificacao' ? 'selected' : '' ?>>Aguardando verificação</option>
                    <option value="concluido" <?= $filtroStatus == 'concluido' ? 'selected' : '' ?>>Concluído</option>
                    <option value="reavaliacao" <?= $filtroStatus == 'reavaliacao' ? 'selected' : '' ?>>Reavaliação</option>
                    <option value="duplicado" <?= $filtroStatus == 'duplicado' ? 'selected' : '' ?>>Duplicado</option>
                </select>
            </div>

            <div>
                <label for="filtroSetor">Setor</label>
                <select id="filtroSetor" name="filtroSetor">
                    <option value="">Todos</option>
                    <option value="setorA" <?= $filtroSetor == 'setorA' ? 'selected' : '' ?>>Setor A</option>
                    <option value="setorB" <?= $filtroSetor == 'setorB' ? 'selected' : '' ?>>Setor B</option>
                    <option value="setorC" <?= $filtroSetor == 'setorC' ? 'selected' : '' ?>>Setor C</option>
                    <option value="setorD" <?= $filtroSetor == 'setorD' ? 'selected' : '' ?>>Setor D</option>
                </select>
            </div>

            <div class="filtros-botao">
                <button type="submit">Filtrar</button>
            </div>
        </form>

        <table>
            <caption>Lista de chamados cadastrados</caption>
            <thead>
                <tr>
                    <th scope="col">Código</th>
                    <th scope="col">Título</th>
                    <th scope="col">Setor</th>
                    <th scope="col">Responsável</th>
                    <th scope="col">Status</th>
                    
                    <?php if ($cargo_logado == 'Mantenedor' || $cargo_logado == 'Supervisor'): ?>
                        <th scope="col">Triagem</th>
                    <?php endif; ?>
                    
                    <th scope="col">Ação</th>
                </tr>
            </thead>
            <tbody id="corpo-tabela-chamados">
                <?php if (count($chamados) > 0): ?>
                    <?php foreach ($chamados as $chamado): ?>
                        <?php 
                            $codigo = 'OC-' . str_pad($chamado['id_chamados'], 5, '0', STR_PAD_LEFT);
                            $classeStatus = 'status-' . strtolower($chamado['status_chamado']);
                            
                            if ($chamado['status_chamado'] == 'execucao') {
                                $textoStatus = 'Execução';
                            } else {
                                $textoStatus = ucfirst($chamado['status_chamado']);
                            }
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($codigo) ?></td>
                            <td><?= htmlspecialchars($chamado['titulo']) ?></td>
                            <td><?= htmlspecialchars($chamado['setor']) ?></td>
                            <td><?= htmlspecialchars($chamado['responsavel'] ?? 'N/A') ?></td>
                            <td><span class="status <?= $classeStatus ?>"><?= htmlspecialchars($textoStatus) ?></span></td>
                            
                            <?php if ($cargo_logado == 'Mantenedor' || $cargo_logado == 'Supervisor'): ?>
                                <td>
                                    <?php if ($chamado['status_chamado'] == 'aberto'): ?>
                                        <a href="triagem.php?id=<?= $chamado['id_chamados'] ?>" class="botao-tabela" style="background-color: #6A1B9A; color: white; border: none;">Fazer Triagem</a>
                                    <?php else: ?>
                                        <span style="color: #A0AABF; font-size: 12px; font-weight: 600;">Concluída</span>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                            
                            <td><a href="acao.php?id=<?= $chamado['id_chamados'] ?>" class="botao-tabela">Ver</a></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="<?= ($cargo_logado == 'Mantenedor' || $cargo_logado == 'Supervisor') ? '7' : '6' ?>" style="text-align: center; padding: 20px;">Nenhum chamado encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <nav aria-label="Ações da página">
            <a href="index.php" class="botao-voltar">Voltar</a>
        </nav>
    </article>
    </main>
    <footer>
        <p>&copy; 2026 OperaCheck. Todos os direitos reservados.</p>
    </footer>
</body>
</html>