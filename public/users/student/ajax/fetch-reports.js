$(document).ready(function() {
    let currentPage = 1;
    let pageSize = 10;
    let currentStatus = 'all';
    let currentSearch = '';

    function loadReports() {
        console.log('Loading reports with:', {
            page: currentPage,
            limit: pageSize,
            status: currentStatus,
            search: currentSearch
        });

        $.get('backend/fetch-reports.php', {
            page: currentPage,
            limit: pageSize,
            status: currentStatus,
            search: currentSearch
        }, function(response) {
            console.log('Response received:', response);

            const tableBody = $('#reportsTableBody');
            tableBody.empty();

            if (!response.data || response.data.length === 0) {
                tableBody.append(`
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                            No reports found
                        </td>
                    </tr>
                `);
                return;
            }

            response.data.forEach(report => {
                // console.log('Processing report:', report);
                const row = `
                    <tr data-status="${report.status}" data-report-id="${report.id}">
                        <td>${report.date}</td>
                        <td>${report.location}</td>
                        <td>${report.issue_type}</td>
                        <td><span class="view-full-description" data-full-description="${report.full_description}">${report.description}</span></td>
                        <td>
                            <span class="badge bg-${report.status === 'pending' ? 'warning' : report.status === 'in_progress' ? 'primary' : 'success'}">
                                ${report.status === 'pending' ? 'Pending' : report.status === 'in_progress' ? 'In Progress' : 'Resolved'}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-info btn-sm d-flex align-items-center gap-1 text-white" data-bs-toggle="modal" data-bs-target="#viewReportModal${report.id}">
                                    <i class="bi bi-eye"></i> View Details
                                </button>
                                <button class="btn btn-danger btn-sm d-flex align-items-center gap-1 delete-report" data-report-id="${report.id}">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </div>
                        </td>
                    </tr>

                    
                `;
                tableBody.append(row);

               
            });

            updateStats(response.stats);
            // Update pagination
            const pagination = $('#pagination');
            pagination.empty();

            const totalPages = response.pages;
            $('#totalRecords').text(`Showing ${response.data.length} of ${response.total} records`);

            // Previous button
            pagination.append(`
                <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <a class="page-link " href="#" data-page="${currentPage - 1}">Previous</a>
                </li>
            `);

            // Page numbers
            for (let i = 1; i <= totalPages; i++) {
                pagination.append(`
                    <li class="page-item ${currentPage === i ? 'active ' : ''}">
                        <a class="page-link " href="#" data-page="${i}">${i}</a>
                    </li>
                `);
            }

            // Next button
            pagination.append(`
                <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${currentPage + 1}">Next</a>
                </li>
            `);
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error('AJAX Error:', textStatus, errorThrown);
            console.log('Response:', jqXHR.responseText);
        });
    }
    // Update stats in the quick stats section
    function updateStats(stats) {
        $('#pendingCount').text(stats.pending);
        $('#inProgressCount').text(stats.in_progress);
        $('#resolvedCount').text(stats.resolved);
    }
    // Initial load
    loadReports();


    
    
    // Event handlers
    $('#reportStatusFilter button').click(function(e) {
        e.preventDefault();
        $('#reportStatusFilter button').removeClass('active');
        $(this).addClass('active');
        currentStatus = $(this).val();
        currentPage = 1;
        loadReports();
    });

    $('#searchInput').on('input', function() {
        currentSearch = $(this).val();
        currentPage = 1;
        loadReports();
    });

    $('#pageSizeSelect').change(function() {
        pageSize = parseInt($(this).val());
        currentPage = 1;
        loadReports();
    });

    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        const page = parseInt($(this).data('page'));
        if (page && !isNaN(page)) {
            currentPage = page;
            loadReports();
        }
    });

     

});