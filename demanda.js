/* =========================================================
   OperaCheck — 
   Versão inicial: só testa se o JavaScript está conectado
   ao formulário. Ainda NÃO salva em lugar nenhum (isso vem
   na próxima etapa, com o banco de dados).
   ========================================================= */

// Pega o formulário pelo elemento <form> da página
const formulario = document.querySelector("form");

// Escuta o momento em que o formulário é enviado (botão "Cadastrar")
formulario.addEventListener("submit", function (evento) {

    // Impede o navegador de tentar enviar/recarregar a página
    // (senão perderíamos os dados antes de conseguir vê-los)
    evento.preventDefault();

    // Pega o valor digitado em cada campo, usando o "id" do HTML
    const titulo = document.getElementById("titulo").value;
    const area = document.getElementById("area").value;
    const setor = document.getElementById("setor").value;
    const responsavel = document.getElementById("responsavel").value;
    const descricao = document.getElementById("descricao").value;

    // Não precisamos validar campo vazio aqui: o atributo "required"
    // no HTML já faz o navegador bloquear o envio automaticamente,
    // antes mesmo do JavaScript rodar.

    // Mostra no console (F12 > Console) os dados capturados,
    // só para conferirmos que o JavaScript está lendo tudo certo
    console.log("Dados capturados do formulário:");
    console.log({ titulo, area, setor, responsavel, descricao });

    // Mostra um resumo simples pro usuário, como confirmação visual
    alert(
        "Teste de leitura do formulário:\n\n" +
        "Título: " + titulo + "\n" +
        "Área: " + area + "\n" +
        "Setor: " + setor + "\n" +
        "Responsável: " + responsavel
    );
});