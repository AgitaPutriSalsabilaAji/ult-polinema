import Chart from 'chart.js/auto';

const chartDataElement = document.getElementById('dashboard-chart-data');

if (chartDataElement) {
    const unitStatistics = JSON.parse(chartDataElement.dataset.unitStatistics);
    const statusStatistics = JSON.parse(chartDataElement.dataset.statusStatistics);
    const barCanvas = document.getElementById('applicationUnitChart');
    const pieCanvas = document.getElementById('applicationStatusChart');

    if (barCanvas) {
        new Chart(barCanvas, {
            type: 'bar',
            data: {
                labels: unitStatistics.map((unit) => unit.nama_unit),
                datasets: [
                    {
                        label: 'Pengajuan Baru',
                        data: unitStatistics.map((unit) => unit.pengajuan_baru),
                        backgroundColor: '#f59e0b',
                    },
                    {
                        label: 'Sedang Diproses',
                        data: unitStatistics.map((unit) => unit.pengajuan_diproses),
                        backgroundColor: '#7c3aed',
                    },
                    {
                        label: 'Pengajuan Selesai',
                        data: unitStatistics.map((unit) => unit.pengajuan_selesai),
                        backgroundColor: '#059669',
                    },
                    {
                        label: 'Pengajuan Ditolak',
                        data: unitStatistics.map((unit) => unit.pengajuan_ditolak),
                        backgroundColor: '#ef4444',
                    },
                ],
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 10,
                            font: { size: 10 },
                        },
                    },
                    tooltip: {
                        enabled: true,
                    },
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 } },
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            font: { size: 10 },
                        },
                        title: {
                            display: true,
                            text: 'Jumlah Pengajuan',
                        },
                    },
                },
            },
        });
    }

    if (pieCanvas) {
        new Chart(pieCanvas, {
            type: 'pie',
            data: {
                labels: statusStatistics.map((status) => status.label),
                datasets: [{
                    data: statusStatistics.map((status) => status.count),
                    backgroundColor: statusStatistics.map((status) => status.color),
                    borderColor: '#ffffff',
                    borderWidth: 2,
                }],
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 10,
                            font: { size: 10 },
                        },
                    },
                    tooltip: {
                        callbacks: {
                            label(context) {
                                const total = context.dataset.data.reduce((sum, value) => sum + value, 0);
                                const count = context.parsed;
                                const percentage = total > 0 ? (count / total * 100).toFixed(1) : '0.0';

                                return `${context.label}: ${count} (${percentage}%)`;
                            },
                        },
                    },
                },
            },
        });
    }
}
