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
                tableBody.append(
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                            No reports found
                        </td>
                    </tr>
                );
                return;
            }

            response.data.forEach(report => {
                // console.log('Processing report:', report);
                const row = 
                    `<tr data-status="${report.status}" data-report-id="${report.id}">
                        <td>${report.date}</td>
                        <td>${report.reporter_name}</td>
                        <td>${report.location}</td>
                        <td>${report.issue_type}</td>
                        <td><span class="view-full-description" data-full-description="${report.full_description}">${report.description}</span></td>
                        <td>
                            <select class="form-select form-select-sm status-select" data-report-id="${report.id}" data-status="${report.status}" style="width: 130px;">
                                <option value="pending" ${report.status === 'pending' ? 'selected' : ''}>Pending</option>
                                <option value="in_progress" ${report.status === 'in_progress' ? 'selected' : ''}>In Progress</option>
                                <option value="resolved" ${report.status === 'resolved' ? 'selected' : ''}>Resolved</option>
                            </select>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-info btn-sm d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#viewReportModal${report.id}">
                                    <i class="bi bi-eye"></i> View Details
                                </button>
                                <button class="btn btn-danger btn-sm d-flex align-items-center gap-1 delete-report" data-report-id="${report.id}">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                ;`
                tableBody.append(row);

                 // Append modal content dynamically for each report
                 // Append modal content dynamically for each report
                 const modalContent = `
                 <div class="modal fade" id="viewReportModal${report.id}" tabindex="-1" aria-labelledby="viewReportModalLabel${report.id}" aria-hidden="true">
                     <div class="modal-dialog modal-lg">
                         <div class="modal-content">
                             <div class="modal-header">
                                 <h5 class="modal-title" id="viewReportModalLabel${report.id}">Report Details</h5>
                                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                             </div>
                             <div class="modal-body">
                                 <div class="mb-3">
                                     <h5>Location:</h5>
                                     <p>${report.location}</p>
                                 </div>
                                 <div class="mb-3">
                                     <h5>Issue Type:</h5>
                                     <p>${report.issue_type}</p>
                                 </div>
                                 <div class="mb-3">
                                     <h5>Description:</h5>
                                     <p>${report.description}</p>
                                 </div>
                                 <div class="mb-3">
                                     <h5>Status:</h5>
                                     <span class="badge bg-${report.status}">
                                         ${report.status}
                                     </span>
                                 </div>
                                 ${report.image_path ? `
                                     <div class="mb-3">
                                         <h5>Attached Image:</h5>
                                         <img src="../${report.image_path}" alt="Report Image" class="img-fluid">
                                     </div>
                                 ` : ''}
                             </div>
                             <div class="modal-footer">
                                 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                             </div>
                         </div>
                     </div>
                 </div>
             `;
             $('body').append(modalContent);
            });

            updateStats(response.stats);

            // Update pagination
            const pagination = $('#pagination');
            pagination.empty();

            const totalPages = response.pages;
            $('#totalRecords').text(Showing ${response.data.length} of ${response.total} records);

            // Previous button
            pagination.append(
                <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${currentPage - 1}">Previous</a>
                </li>
            );

            // Page numbers
            for (let i = 1; i <= totalPages; i++) {
                pagination.append(
                    <li class="page-item ${currentPage === i ? 'active' : ''}">
                        <a class="page-link " href="#" data-page="${i}">${i}</a>
                    </li>
                );
            }

            // Next button
            pagination.append(
                <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${currentPage + 1}">Next</a>
                </li>
            );
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
    $('#reportStatusFilter button').click(function() {
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

    // Handle status changes
    $(document).on('change', '.status-select', function() {
        const reportId = $(this).data('report-id');
        const newStatus = $(this).val();
        
        $.post('backend/update-report-status.php', {
            report_id: reportId,
            status: newStatus
        }, function(response) {
            console.log('Response:', response);
            if (response.success) {
                alert("Status updated successfully!");
                loadReports();
            } else {
                alert('Failed to update status');
            }
        });
    });


    function fetchNotifications() {
        $.ajax({
            url: 'backend/fetch-notifications.php',
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                let notificationList = $("#notificationList");
                let notificationCount = $("#notificationCount");
                let unreadCount = 0;

                notificationList.empty();
                if (data.length > 0) {
                    console.log("New notifications received:", data.length);
                    
                    data.forEach(notification => {
                        if (!notification.is_read) {
                            unreadCount++;
                        }
                        
                        notificationList.append(
                            <li>
                                <a class="dropdown-item ${notification.is_read ? 'text-muted' : 'fw-bold'}" href="#" data-id="${notification.id}">
                                    <div class="small text-muted">${notification.formatted_date}</div>
                                    ${notification.message}
                                </a>
                            </li>
                        );
                    });

                    notificationCount.text(unreadCount);
                    if (unreadCount > 0) {
                        notificationCount.show();
                    } else {
                        notificationCount.hide();
                    }
                } else {
                    console.log("No notifications.");
                    notificationCount.hide();
                    notificationList.append('<li class="text-center"><small>No notifications</small></li>');
                }
            },
            error: function (xhr, status, error) {
                console.error("Failed to fetch notifications:", error);
            }
        });
    }

    // Handle notification click
    $(document).on('click', '#notificationList a', function(e) {
        e.preventDefault();
        let notificationId = $(this).data('id');
        let $this = $(this);
        
        // Mark notification as read
        $.ajax({
            url: 'backend/mark-notification-read.php',
            method: 'POST',
            data: { notification_id: notificationId },
            success: function() {
                $this.removeClass('fw-bold').addClass('text-muted');
                let count = parseInt($("#notificationCount").text()) - 1;
                if (count <= 0) {
                    $("#notificationCount").hide();
                } else {
                    $("#notificationCount").text(count);
                }
            }
        });
    });

    fetchNotifications();
    setInterval(fetchNotifications, 1000); // Check every 30 seconds
});
