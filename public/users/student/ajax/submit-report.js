$(document).ready(function () {
    $('#reportForm').on('submit', function (e) {
        e.preventDefault();  // Prevent the default form submission

        // Prepare form data (including file)
        var formData = new FormData(this);

        // Send the form data via AJAX
        $.ajax({
            url: 'backend/submit-report.php',  // The PHP file to handle the request
            type: 'POST',
            data: formData,
            contentType: false, // Tell jQuery not to set content type
            processData: false, // Tell jQuery not to process the data
            success: function (response) {
                console.log("Raw Response: ", response); // Log the raw response to check its contents
                let res;
                try {
                    res = JSON.parse(response); // Attempt to parse it
                } catch (e) {
                    console.error("Error parsing JSON:", e);
                    alert('Invalid response from the server');
                    return;
                }
            
                // Continue processing only if the JSON is valid
                if (res.success) {
                    alert('Report submitted successfully!');
                    $('#newReportModal').modal('hide');
                    const newReport = res.report;

                    // Get the current filter status 
                    var filterStatus = $('#reportStatusFilter button.active').val();  // For example, "all", "pending", "in_progress", "resolved"
                    
                    // Only append if the filter is "all" or the report's status matches the filter
                    if (filterStatus === 'all' || newReport.status === filterStatus) {
                        const row = `
                            <tr data-status="${newReport.status}" data-report-id="${newReport.id}">
                                <td>${newReport.created_at}</td>
                                <td>${newReport.location}</td>
                                <td>${newReport.issue_type}</td>
                                <td><span class="view-full-description" data-full-description="${newReport.full_description}">${newReport.description}</span></td>
                                <td>
                                    <span class="badge bg-${newReport.status === 'pending' ? 'warning' : newReport.status === 'in_progress' ? 'primary' : 'success'}">
                                        ${newReport.status === 'pending' ? 'Pending' : newReport.status === 'in_progress' ? 'In Progress' : 'Resolved'}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-info btn-sm d-flex align-items-center gap-1 text-white" data-bs-toggle="modal" data-bs-target="#viewReportModal${newReport.id}">
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
                    updateStats(res.stats);
                } else {
                    alert('Error: ' + res.message);
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error: ' + status + ' - ' + error);
                console.log(xhr.responseText); // Log the server response
                alert('An error occurred while submitting the report.');
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
