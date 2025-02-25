$(document).ready(function () {
    $('#reportForm').on('submit', function (e) {
        e.preventDefault();  // Prevent the default form submission

        // Prepare form data (including file)
        var formData = new FormData(this);

        // Send the form data via AJAX
        $.ajax({
            url: 'backend/submit-report.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json', 
            success: function (response) {
                if (response.success) {
                    $('#newReportModal').modal('hide');
                    const newReport = response.report;

                    // Get the current filter status 
                    var filterStatus = $('#reportStatusFilter button.active').val();  
                    
                    // Only append if the filter is "all" or the report's status matches the filter
                    if (filterStatus === 'all' || newReport.status === filterStatus) {
                        const row = `
                            <tr data-status="${newReport.status}" data-report-id="${newReport.id}">
                                <td>${newReport.created_at}</td>
                                <td>${newReport.location}</td>
                                <td>${newReport.issue_type}</td>
                                <td><span class="view-full-description" data-full-description="${newReport.description}">${newReport.description.length > 50 ? newReport.description.substring(0, 50) + '...' : newReport.description}</span></td>
                                <td>
                                    <span class="badge bg-${newReport.status === 'pending' ? 'warning' : newReport.status === 'in_progress' ? 'primary' : 'success'}">
                                        ${newReport.status === 'pending' ? 'Pending' : newReport.status === 'in_progress' ? 'In Progress' : 'Resolved'}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-info btn-sm d-flex align-items-center gap-1 text-white view-report" data-report-id="${newReport.id}">
                                            <i class="bi bi-eye"></i> View Details
                                        </button>
                                        <button class="btn btn-danger btn-sm d-flex align-items-center gap-1 delete-report" data-report-id="${newReport.id}">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                        $('#reportsTableBody').prepend(row);
                    }
                    $('#reportForm')[0].reset();
                    if (response.stats) {
                        updateStats(response.stats);
                    }
                    alert(response.message || 'Report submitted successfully!');
                } else {
                    alert(response.message || 'Failed to submit report');
                }
            },
            error: function (xhr, status, error) {
                let errorMessage = 'Failed to submit report';
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        errorMessage = response.message;
                    }
                } catch (e) {}
                alert(errorMessage);
            }
        });
    });

    // Update stats in the quick stats section
    function updateStats(stats) {
        $('#pendingCount').text(stats.pending);
        $('#inProgressCount').text(stats.in_progress);
        $('#resolvedCount').text(stats.resolved);
    }
});
