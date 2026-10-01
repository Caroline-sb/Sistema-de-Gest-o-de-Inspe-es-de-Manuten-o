// graficos.js

// Verifica se os dados foram enviados pelo PHP antes de tentar desenhar
if (typeof dadosStatusBrutos !== 'undefined' && typeof dadosSetorBrutos !== 'undefined') {

    // --- GRÁFICO DE STATUS (DONUT) ---
    const labelsStatus = dadosStatusBrutos.map(item => item.status_chamado.toUpperCase());
    const valoresStatus = dadosStatusBrutos.map(item => item.quantidade);
    
    const canvasStatus = document.getElementById('graficoStatus');
    if (canvasStatus) {
        const ctxStatus = canvasStatus.getContext('2d');
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: labelsStatus,
                datasets: [{
                    data: valoresStatus,
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#C9CBCF', '#FF9F40']
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }

    // --- GRÁFICO DE SETORES (BARRAS) ---
    const labelsSetor = dadosSetorBrutos.map(item => item.setor.toUpperCase());
    const valoresSetor = dadosSetorBrutos.map(item => item.quantidade);

    const canvasSetor = document.getElementById('graficoSetor');
    if (canvasSetor) {
        const ctxSetor = canvasSetor.getContext('2d');
        new Chart(ctxSetor, {
            type: 'bar',
            data: {
                labels: labelsSetor,
                datasets: [{
                    label: 'Quantidade de Chamados',
                    data: valoresSetor,
                    backgroundColor: '#6C5CE7',
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
                plugins: { legend: { display: false } }
            }
        });
    }
}