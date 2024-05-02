document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('countryChart')) {
        var ctx = document.getElementById('countryChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: countryData.labels,
                datasets: [{ label: 'Visits by Country', data: countryData.data, backgroundColor: 'rgba(75, 192, 192, 0.2)' }]
            }
        });
    }

    if (document.getElementById('browserChart')) {
        var ctx = document.getElementById('browserChart').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: browserData.labels,
                datasets: [{ label: 'Browser Usage', data: browserData.data, backgroundColor: 'rgba(255, 99, 132, 0.2)' }]
            }
        });
    }

    if (document.getElementById('visitsSummaryChart')) {
        var ctx = document.getElementById('visitsSummaryChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: visitsSummaryData.labels,
                datasets: [{ label: 'Visits Summary', data: visitsSummaryData.data, backgroundColor: 'rgba(153, 102, 255, 0.2)' }]
            }
        });
    }
    
});
