<?php
session_start();
require "bancodados.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Captura tudo o que foi digitado no formulário HTML
    $titulo = $_POST['titulo'] ?? '';
    $data_visita = !empty($_POST['data']) ? $_POST['data'] : null; 
    $area = $_POST['area'] ?? '';
    $setor = $_POST['setor'] ?? '';
    $descricao = $_POST['descricao'] ?? '';
    $periodo = $_POST['periodo'] ?? '';
    
    // Status inicial padrão
    $status_chamado = 'aberto'; 

    // 2. Pega o ID de quem abriu (se não tiver sessão no momento do teste, usa o ID 1 para não dar erro de Foreign Key)
    $id_funcionario_abertura = $_SESSION['id_matricula'] ?? 1;

    try {
        // 3. Prepara o comando INSERT com os nomes exatos da sua tabela
        $sql = "INSERT INTO chamados (titulo, data_visita, area, setor, id_funcionario_abertura, descricao, periodo, status_chamado) 
                VALUES (:titulo, :data_visita, :area, :setor, :id_func, :descricao, :periodo, :status)";
        
        $stmt = $conexao->prepare($sql);
        
        // 4. Conecta as variáveis aos parâmetros de forma segura
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':data_visita', $data_visita);
        $stmt->bindParam(':area', $area);
        $stmt->bindParam(':setor', $setor);
        $stmt->bindParam(':id_func', $id_funcionario_abertura);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':periodo', $periodo);
        $stmt->bindParam(':status', $status_chamado);
        
        // 5. Executa a gravação no banco
        $stmt->execute();
        
        // 6. Descobre qual foi o ID (número do chamado) gerado
        $id_gerado = $conexao->lastInsertId();
        
        // 7. Redireciona de volta para a tela, passando o ID na URL para mostrar a mensagem verde
        header("Location: index.html?sucesso=1&id=" . $id_gerado);
        exit;

    } catch (PDOException $erro) {
        die("Erro ao registrar a demanda no banco: " . $erro->getMessage());
    }
}
?>