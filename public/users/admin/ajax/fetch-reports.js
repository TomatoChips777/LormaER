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
                    `<tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                            No reports found
                        </td>
                    </tr>`
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
                    </tr>`;
                tableBody.append(row);

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
                    </div>`;
                $('body').append(modalContent);
            });

            updateStats(response.stats);

            // Update pagination
            const pagination = $('#pagination');
            pagination.empty();

            const totalPages = response.pages;
            $('#totalRecords').text(`Showing ${response.data.length} of ${response.total} records`);

            // Previous button
            pagination.append(
                `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${currentPage - 1}">Previous</a>
                </li>`
            );

            // Page numbers
            for (let i = 1; i <= totalPages; i++) {
                pagination.append(
                    `<li class="page-item ${currentPage === i ? 'active' : ''}">
                        <a class="page-link " href="#" data-page="${i}">${i}</a>
                    </li>`
                );
            }

            // Next button
            pagination.append(
                `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${currentPage + 1}">Next</a>
                </li>`
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
    let lastNotificationCount = 0; // Store previous count

    function fetchNotifications() {
        $.ajax({
            url: 'backend/fetch-notifications.php',
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                let notificationList = $("#notificationList");
                let notificationCount = $("#notificationCount");
                let unreadCount = 0;
        
                notificationList.empty(); // Clear the list before appending new items
        
                if (data.length > 0) {
                    data.forEach(notification => {
                        if (!notification.is_read) {
                            unreadCount++;
                        }
        
                        // Include `data-report-id` for navigation
                        notificationList.append(
                            `<li class="p-2 border-bottom">
                                <a href="#" class="mark-as-read d-block text-decoration-none ${notification.is_read ? 'text-muted' : 'fw-bold'}" 
                                   data-id="${notification.id}" 
                                   data-report-id="${notification.report_id}">
                                    <div class="card shadow-sm p-2">
                                        <div class="d-flex align-items-start">
                                            <img src="assets/profile-placeholder.png" class="rounded-circle me-2" width="40" height="40" alt="User">
                                            <div>
                                                <p class="mb-1">${notification.message}</p>
                                                <small class="text-muted">${notification.formatted_date}</small>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </li>`
                        );
                    });
    
                    notificationCount.text(unreadCount);
                    notificationCount.toggle(unreadCount > 0); // Show/hide count
    
                    // 🔄 **Refresh the table if new notifications are detected**
                    if (unreadCount > lastNotificationCount) {
                        console.log("New notifications detected, refreshing table...");
                        loadReports(); // Reload reports
                    }
    
                    // Update the last known notification count
                    lastNotificationCount = unreadCount;
                } else {
                    notificationList.append('<li class="text-center text-muted py-2"><small>No notifications</small></li>');
                    notificationCount.hide();
                }
            },
            error: function (xhr, status, error) {
                console.error("Failed to fetch notifications:", error);
            }
        });
    }
    
    // function fetchNotifications() {
    //     $.ajax({
    //         url: 'backend/fetch-notifications.php',
    //         method: 'GET',
    //         dataType: 'json',
    //         success: function (data) {
    //             let notificationList = $("#notificationList");
    //             let notificationCount = $("#notificationCount");
    //             let unreadCount = 0;
    
    //             notificationList.empty(); // Clear the list before appending new items
    
    //             if (data.length > 0) {
    //                 data.forEach(notification => {
    //                     if (!notification.is_read) {
    //                         unreadCount++;
    //                     }
    
    //                     // Include `data-report-id` for navigation
    //                     notificationList.append(
    //                         `<li class="p-2 border-bottom">
    //                             <a href="#" class="mark-as-read d-block text-decoration-none ${notification.is_read ? 'text-muted' : 'fw-bold'}" 
    //                                data-id="${notification.id}" 
    //                                data-report-id="${notification.report_id}">
    //                                 <div class="card shadow-sm p-2">
    //                                     <div class="d-flex align-items-start">
    //                                         <img src="assets/profile-placeholder.png" class="rounded-circle me-2" width="40" height="40" alt="User">
    //                                         <div>
    //                                             <p class="mb-1">${notification.message}</p>
    //                                             <small class="text-muted">${notification.formatted_date}</small>
    //                                         </div>
    //                                     </div>
    //                                 </div>
    //                             </a>
    //                         </li>`
    //                     );
    //                 });
    
    //                 notificationCount.text(unreadCount);
    //                 notificationCount.toggle(unreadCount > 0); // Show/hide count
    //             } else {
    //                 notificationList.append('<li class="text-center text-muted py-2"><small>No notifications</small></li>');
    //                 notificationCount.hide();
    //             }
    //         },
    //         error: function (xhr, status, error) {
    //             console.error("Failed to fetch notifications:", error);
    //         }
    //     });
    // }
    

    $(document).on('click', '.mark-as-read', function (e) {
        e.preventDefault();
    
        let notificationId = $(this).data('id');
        let reportId = $(this).data('report-id');
        let $this = $(this);
        let notificationItem = $this.closest("li"); // Find the closest parent `<li>`
    
        if (!notificationId || !reportId) {
            console.error("Missing notification or report ID.");
            return;
        }
    
        // Mark notification as read in the backend
        $.ajax({
            url: 'backend/mark-notification-read.php',
            method: 'POST',
            data: { notification_id: notificationId },
            success: function () {
                console.log("Notification marked as read:", notificationId);
    
                // Remove the notification from the list
                notificationItem.fadeOut(300, function () {
                    $(this).remove();
                    
                    // Update unread count
                    let count = parseInt($("#notificationCount").text()) - 1;
                    if (count <= 0) {
                        $("#notificationCount").hide();
                    } else {
                        $("#notificationCount").text(count);
                    }
                    
                    // If no notifications left, show "No notifications" message
                    if ($("#notificationList li").length === 0) {
                        $("#notificationList").append('<li class="text-center text-muted py-2"><small>No notifications</small></li>');
                    }
                });
    
                // 🔹 Scroll to the report in the table
                let reportRow = $(`tr[data-report-id="${reportId}"]`);
                if (reportRow.length) {
                    $('html, body').animate({
                        scrollTop: reportRow.offset().top - 100
                    }, 800);
    
                    // Highlight the row for visibility
                    reportRow.addClass('table-warning');
                    setTimeout(() => reportRow.removeClass('table-warning'), 2000);
                }
            },
            error: function (xhr, status, error) {
                console.error("Error marking notification as read:", error);
            }
        });
    });
    

    fetchNotifications();
    setInterval(fetchNotifications, 30000); // Check every 30 seconds
});
