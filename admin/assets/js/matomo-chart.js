document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('countryChart')) {
        var ctx = document.getElementById('countryChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: countryData.labels,
                datasets: [{ 
                    label: 'Visits by Country', 
                    data: countryData.data, 
                    backgroundColor: getColors(countryData.data.length) // Get colors based on data length
                }]
            }
        });
    }

    if (document.getElementById('browserChart')) {
        var ctx = document.getElementById('browserChart').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: browserData.labels,
                datasets: [{ 
                    label: 'Browser Usage', 
                    data: browserData.data, 
                    backgroundColor: getColors(browserData.data.length) // Get colors based on data length
                }]
            }
        });
    }

    if (document.getElementById('visitsSummaryChart')) {
        var ctx = document.getElementById('visitsSummaryChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: visitsSummaryData.labels,
                datasets: [{ 
                    label: 'Visits Summary', 
                    data: visitsSummaryData.data, 
                    backgroundColor: getColors(visitsSummaryData.data.length) // Get colors based on data length
                }]
            }
        });
    }
});

// Function to convert hex color to RGBA
function hexToRGBA(hex, alpha = 0.2) {
    hex = hex.replace(/^#/, '');
    var r = parseInt(hex.substring(0, 2), 16);
    var g = parseInt(hex.substring(2, 4), 16);
    var b = parseInt(hex.substring(4, 6), 16);
    return `rgba(${r}, ${g}, ${b}, ${alpha})`;
}

// Function to get an array of colors based on input length
function getColors(length) {
    // Include all provided hex colors in this array
    var hexColors = [
        '#ec4899', // Pink 500
        '#10b981', // Emerald 500
        '#8b5cf6', // Violet 500
        '#06b6d4', // Cyan 500
        '#22c55e', // Green 500
        '#f97316', // Orange 500
        '#a855f7', // Purple 500
        '#3b82f6', // Blue 500
        '#ef4444', // Red 500
        '#f43f5e', // Rose 500
        '#eab308', // Yellow 500
        '#84cc16', // Lime 500
        '#f59e0b', // Amber 500
        '#d946ef', // Fuchsia 500
        '#6366f1', // Indigo 500
        '#78716c', // Stone 500
        '#14b8a6', // Teal 500
        '#0ea5e9', // Sky 500
    ];
    

    // Map each hex color to its RGBA equivalent
    var rgbaColors = hexColors.map(hex => hexToRGBA(hex));

    // Return the sliced array based on the required length
    return rgbaColors.slice(0, length);
}
