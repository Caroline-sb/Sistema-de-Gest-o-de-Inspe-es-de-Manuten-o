# Sistema-de-Gest-o-de-Inspe-es-de-Manuten-o
# ⚙️ OperaCheck

O **OperaCheck** é um sistema web de gestão de manutenção industrial desenvolvido para controlar, monitorizar e dar seguimento a chamados corretivos e preventivos de forma eficiente e segura.

## 🚀 Funcionalidades Principais

* **Controle de Acesso por Cargos (RBAC):**
  * **Solicitante:** Abre novos chamados e acompanha o progresso das suas próprias solicitações.
  * **Mantenedor / Supervisor:** Realiza triagens, define prazos, atribui técnicos responsáveis, gere ações corretivas e valida os encerramentos.
* **Dashboard Dinâmico:** Listagem em tempo real com auto-atualização a cada 30 segundos, filtros avançados por **Status** e **Setor**, e cartões estatísticos interativos.
* **Gestão do Fluxo de Chamados:** Ciclo completo de estados (*Aberto ➔ Em execução ➔ Aguardando verificação ➔ Concluído / Duplicado*).
* **Painel de Relatórios:** Aba dedicada com gráficos estatísticos detalhados (distribuição por status e volume por setor) gerados com Chart.js.
* **Segurança Robusta:** Autenticação por sessões PHP e consultas protegidas contra SQL Injection utilizando **PDO (PHP Data Objects)**.

---

## 🛠️ Tecnologias Utilizadas

* **Linguagem Backend:** PHP (com PDO)
* **Base de Dados:** MySQL / MariaDB
* **Frontend:** HTML5, CSS3 (com folhas de estilo modulares e responsivas)
* **Gráficos e Indicadores:** Chart.js
* **Ambiente de Servidor Local:** XAMPP / WAMP (ou servidor embutido do PHP)

---

## 📂 Estrutura Principal do Projeto

* `bancodados.php` — Arquivo central de conexão com a base de dados via PDO.
* `login.html` / `login.php` — Ecrã de autenticação de utilizadores.
* `index.php` — Painel de controlo inicial (com saudação, contadores dinâmicos e atalhos rápidos).
* `dashboard.php` — Tabela principal de gestão de chamados com filtros e alertas de sucesso.
* `triagem.php` / `salvar_triagem.php` — Ecrã e lógica de designação de técnicos e avanço de status.
* `acao.php` / `salvar_acao.php` — Registo de ações corretivas e anexos.
* `relatorios.php` — Página dedicada com gráficos visuais de desempenho.
* `demandas.css`, `dashboard.css`, `index.css` — Folhas de estilo do sistema.

---

## 🚀 Como Executar o Projeto Localmente

1. Certifique-se de ter um servidor local a correr (como o **XAMPP** com o Apache e o MySQL ativados).
2. Importe a base de dados do projeto para o seu gestor MySQL (ex: phpMyAdmin).
3. Coloque a pasta do projeto no diretório do seu servidor (ex: `htdocs` no XAMPP) ou inicie um servidor embutido do PHP na pasta raiz:
   ```bash
   php -S localhost:8000
