$(document).ready(function() {
    let currentPage = 1;
    let pageSize = 10;
    let currentStatus = 'all';
    let currentSearch = '';

    function loadReports() {
        $.get('backend/fetch-reports.php', {
            page: currentPage,
            limit: pageSize,
            status: currentStatus,
            search: currentSearch
        }, function(response) {
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
                const row = 
                    `<tr data-status="${report.status}" data-report-id="${report.id}">
                        <td>${report.date}</td>
                        <td>${report.reporter_name}</td>
                        <td>${report.location}</td>
                        <td>${report.issue_type}</td>
                        <td><span class="view-full-description" data-full-description="${report.description}">${report.description.length > 50 ? report.description.substring(0, 50) + '...' : report.description}</span></td>
                        <td>
                            <select class="form-select form-select-sm status-select" data-report-id="${report.id}">
                                <option value="pending" ${report.status === 'pending' ? 'selected' : ''}>Pending</option>
                                <option value="in_progress" ${report.status === 'in_progress' ? 'selected' : ''}>In Progress</option>
                                <option value="resolved" ${report.status === 'resolved' ? 'selected' : ''}>Resolved</option>
                            </select>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-info btn-sm d-flex align-items-center gap-1 text-white view-report" data-report-id="${report.id}">
                                    <i class="bi bi-eye"></i> View Details
                                </button>
                                <button class="btn btn-danger btn-sm d-flex align-items-center gap-1 delete-report" data-report-id="${report.id}">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </div>
                        </td>
                    </tr>`;
                tableBody.append(row);
            });

            // Add a single reusable modal to the page if it doesn't exist
            if (!$('#viewReportModal').length) {
                $('body').append(`
                    <div class="modal fade" id="viewReportModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Report Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Reporter:</strong> <span id="modalReporterName"></span></p>
                                            <p><strong>Date:</strong> <span id="modalDate"></span></p>
                                            <p><strong>Location:</strong> <span id="modalLocation"></span></p>
                                            <p><strong>Issue Type:</strong> <span id="modalIssueType"></span></p>
                                            <p><strong>Status:</strong> <span id="modalStatus"></span></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Description:</strong></p>
                                            <p id="modalDescription"></p>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <p><strong>Image:</strong></p>
                                            <div id="modalImage" class="text-center"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `);
            }

            updateStats(response.stats);

            const pagination = $('#pagination');
            pagination.empty();

            const totalPages = response.pages;
            $('#totalRecords').text(`Showing ${response.data.length} of ${response.total} records`);

            pagination.append(
                `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${currentPage - 1}">Previous</a>
                </li>`
            );

            for (let i = 1; i <= totalPages; i++) {
                pagination.append(
                    `<li class="page-item ${currentPage === i ? 'active' : ''}">
                        <a class="page-link " href="#" data-page="${i}">${i}</a>
                    </li>`
                );
            }

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

    function updateStats(stats) {
        $('#pendingCount').text(stats.pending);
        $('#inProgressCount').text(stats.in_progress);
        $('#resolvedCount').text(stats.resolved);
    }

    loadReports();

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

    $(document).on('change', '.status-select', function() {
        const reportId = $(this).data('report-id');
        const newStatus = $(this).val();
        
        $.post('backend/update-report-status.php', {
            report_id: reportId,
            status: newStatus
        }, function(response) {
            if (response.success) {
                alert("Status updated successfully!");
                loadReports();
            } else {
                alert('Failed to update status');
            }
        });
    });

    $(document).on('click', '.view-report', function() {
        const reportId = $(this).data('report-id');
        
        // Fetch report details
        $.ajax({
            url: 'backend/get-report-details.php',
            method: 'GET',
            data: { report_id: reportId },
            success: function(report) {
                // Update modal content
                $('#modalReporterName').text(report.reporter_name);
                $('#modalDate').text(report.date);
                $('#modalLocation').text(report.location);
                $('#modalIssueType').text(report.issue_type);
                $('#modalStatus').text(report.status);
                $('#modalDescription').text(report.description);
                
                // Handle image
                if (report.image_path) {
                    $('#modalImage').html(`<img src="../student/${report.image_path}" class="img-fluid" alt="Report Image">`);
                } else {
                    $('#modalImage').html('<p class="text-muted">No image attached</p>');
                }

                // Show the modal
                $('#viewReportModal').modal('show');
            },
            error: function() {
                toastr.error('Failed to load report details');
            }
        });
    });

    let lastNotificationCount = 0; 

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
                    data.forEach(notification => {
                        if (!notification.is_read) {
                            unreadCount++;
                        }
        
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
                    notificationCount.toggle(unreadCount > 0); 
    
                    if (unreadCount > lastNotificationCount) {
                        loadReports(); 
                    }
                    
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
    

    $(document).on('click', '.mark-as-read', function (e) {
        e.preventDefault();
    
        let notificationId = $(this).data('id');
        let reportId = $(this).data('report-id');
        let $this = $(this);
        let notificationItem = $this.closest("li"); 
        
        if (!notificationId || !reportId) {
            console.error("Missing notification or report ID.");
            return;
        }
    
        $.ajax({
            url: 'backend/mark-notification-read.php',
            method: 'POST',
            data: { notification_id: notificationId },
            success: function () {
                notificationItem.fadeOut(300, function () {
                    $(this).remove();
                    
                    let count = parseInt($("#notificationCount").text()) - 1;
                    if (count <= 0) {
                        $("#notificationCount").hide();
                    } else {
                        $("#notificationCount").text(count);
                    }
                    
                    if ($("#notificationList li").length === 0) {
                        $("#notificationList").append('<li class="text-center text-muted py-2"><small>No notifications</small></li>');
                    }
                });
    
                let reportRow = $(`tr[data-report-id="${reportId}"]`);
                if (reportRow.length) {
                    $('html, body').animate({
                        scrollTop: reportRow.offset().top - 100
                    }, 800);
    
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
    setInterval(fetchNotifications, 30000); 
});
