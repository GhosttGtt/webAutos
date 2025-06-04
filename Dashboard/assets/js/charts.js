const chart = (id, type, labels, data, title) => new Chart(document.getElementById(id), {
    type,
    data: {
        labels,
        datasets: [{
            label: title,
            data,
            backgroundColor: [
                '#845EC2', '#00C9A7', '#FFC75F', '#FF6F91', '#0081CF', '#B0A8B9',
                '#4B4453', '#2C73D2', '#008E9B', '#C34A36', '#FF8066', '#B39CD0'
            ],
            borderColor: "#fff",
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            title: {
                display: true,
                text: title,
                font: { size: 16 }
            },
            legend: {
                position: 'bottom'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.dataset.label || '';
                        if (label) label += ': ';
                        label += context.parsed.y ? context.parsed.y : context.parsed;
                        return label;
                    }
                }
            }
        },
        scales: type === "bar" ? {
            y: {
                beginAtZero: true
            }
        } : {}
    }
});
