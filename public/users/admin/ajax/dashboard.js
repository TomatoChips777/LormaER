let chartInstance = null; // To hold the current chart instance
let chartInstance2 = null; // For second chart (status report chart)

// Function to fetch report data by a filter
function fetchReports(filter) {
    fetch(`backend/fetch-report-year.php?filter=${filter}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const labels = Object.keys(data.data); // Get report types or categories
                const counts = Object.values(data.data); // Get the counts for each category

                const ctx = document.getElementById('issueChart').getContext('2d');

                // Destroy the previous chart before creating a new one
                if (chartInstance) {
                    chartInstance.destroy();
                }

                chartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Report Count',
                            data: counts,
                            backgroundColor: 'rgba(54, 162, 235, 0.6)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            }
                        }
                    }
                });
            } else {
                console.error("Failed to fetch reports:", data.message);
            }
        })
        .catch(error => console.error("Error fetching reports:", error));
}

// Function to fetch report data based on status filter
function fetchReportsByStatus(filter) {
    fetch(`backend/fetch-report-status.php?issue_type_filter=${filter}`)
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                console.error("Failed to fetch report data:", data.message);
                return;
            }

            const totalCounts = data.total_counts; // Get total status counts
            const labels = Object.keys(totalCounts); // Status categories (e.g., "pending", "in_progress", "resolved")
            const statusCounts = Object.values(totalCounts); // Corresponding counts

            // Destroy previous status chart before creating a new one
            if (chartInstance2) {
                chartInstance2.destroy();
            }

            const ctx = document.getElementById("reportChart").getContext("2d");
            chartInstance2 = new Chart(ctx, {
                type: "doughnut",
                data: {
                    labels: labels,
                    datasets: [{
                        label: "Report Status",
                        data: statusCounts,
                        backgroundColor: [
                            "rgba(255, 99, 132, 0.6)", // Pending
                            "rgba(255, 206, 86, 0.6)", // In Progress
                            "rgba(75, 192, 192, 0.6)"  // Resolved
                        ],
                        borderColor: [
                            "rgba(255, 99, 132, 1)",
                            "rgba(255, 206, 86, 1)",
                            "rgba(75, 192, 192, 1)"
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true
                }
            });
        })
        .catch(error => console.error("Error fetching reports:", error));
}

// Event listener for the report type filter
document.getElementById('reportTypeFilter').addEventListener('change', function() {
    fetchReportsByStatus(this.value);
});

// Event listener for the date filter
document.getElementById('dateFilter').addEventListener('change', function() {
    fetchReports(this.value);
});

// Initial data load based on default filters
document.addEventListener('DOMContentLoaded', () => {
    // Fetch initial report data (for current year or default filter)
    fetchReports('current_year');
    // Fetch initial status data based on default report type filter
    fetchReportsByStatus(document.getElementById('reportTypeFilter').value);
});
