$(document).ready(function() {
    let currentPage = 1;
    let pageSize = 10;
    let currentRole = 'all';
    let currentSearch = '';
    let loggedInUserId = $('#user_id').val();
    function loadUsers() {
        console.log('Loading users with:', {
            page: currentPage,
            limit: pageSize,
            role: currentRole,
            search: currentSearch
        });

        $.get('backend/fetch-users.php', {
            page: currentPage,
            limit: pageSize,
            role: currentRole,
            search: currentSearch
        }, function(response) {
            console.log('Response received:', response);

            const tableBody = $('#usersTableBody');
            tableBody.empty();

            if (!response.data || response.data.length === 0) {
                tableBody.append(`
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <i class="bi bi-person-x fs-1 text-muted d-block mb-2"></i>
                            No users found
                        </td>
                    </tr>
                `);
                return;
            }

            response.data.forEach(user => {

                const isCurrentUser = user.id == loggedInUserId;
                const disabledAttr = isCurrentUser ? 'disabled' : '';
                const row = `
                    <tr data-role="${user.role}" data-user-id="${user.id}">
                        <td>${user.id}</td>
                        <td>${user.name}</td>
                        <td>${user.email}</td>
                        <td>${user.created_at}</td>
                        <td>
                            <select class="form-select form-select-sm role-select" data-user-id="${user.id}" style="width: 130px;" ${disabledAttr}>
                                <option value="admin" ${user.role === 'admin' ? 'selected' : ''}>Admin</option>
                                <option value="student" ${user.role === 'student' ? 'selected' : ''}>Student</option>
                                <option value="other" ${user.role === '' ? 'selected' : ''}>Other</option>
                                </select>
                        </td>
                        <td>${user.report_count}</td>
                        <td>
                             <div class="d-flex gap-2">
                                <button class="btn btn-info text-white btn-sm d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#viewReportModal${user.id}">
                                    <i class="bi bi-eye"></i> View 
                                </button>
                                <button class="btn btn-danger btn-sm d-flex align-items-center gap-1 delete-report" data-report-id="${user.id}">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                tableBody.append(row);
            });
            updateRoleCounts(response.role_counts);
            // Update pagination
            const pagination = $('#pagination');
            pagination.empty();

            const totalPages = response.pages;
            $('#totalRecords').text(`Showing ${response.data.length} of ${response.total} users`);

            pagination.append(`
                <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${currentPage - 1}">Previous</a>
                </li>
            `);

            for (let i = 1; i <= totalPages; i++) {
                pagination.append(`
                    <li class="page-item ${currentPage === i ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>
                `);
            }

            pagination.append(`
                <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${currentPage + 1}">Next</a>
                </li>
            `);
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error('AJAX Error:', textStatus, errorThrown);
            alert('Error loading users. Please try again.');
        });
    }

    // Initial load
    loadUsers();


    function updateRoleCounts(role_count) {
        $('#adminCount').text(role_count.admin);
        $('#studentCount').text(role_count.student);
        $('#otherCount').text(role_count.other);
    }
    // Event handlers
    $('#userRoleFilter button').click(function() {
        $('#userRoleFilter button').removeClass('active');
        $(this).addClass('active');
        currentRole = $(this).val();
        currentPage = 1;
        loadUsers();
    });

    $('#searchInput').on('input', function() {
            currentSearch = $(this).val();
            currentPage = 1;
            loadUsers();
        
    });

    $('#pageSizeSelect').change(function() {
        pageSize = parseInt($(this).val());
        currentPage = 1;
        loadUsers();
    });

    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        const page = parseInt($(this).data('page'));
        if (page && !isNaN(page)) {
            currentPage = page;
            loadUsers();
        }
    });

    // Handle role updates
    $(document).on('change', '.role-select', function() {
        const userId = $(this).data('user-id');
        const newRole = $(this).val();

        $.post('backend/update-user-role.php', {
            user_id: userId,
            role: newRole
        }, function(response) {
            console.log('Response:', response);
            if (response.success) {
                alert("Role updated successfully!");
                loadUsers();
            } else {
                alert('Failed to update role');
            }
        });
    });

    // Handle user deletion
    $(document).on('click', '.delete-user', function() {
        const userId = $(this).data('user-id');

        if (confirm('Are you sure you want to delete this user?')) {
            $.post('backend/delete-user.php', { user_id: userId }, function(response) {
                if (response.success) {
                    alert("User deleted successfully!");
                    loadUsers();
                } else {
                    alert('Failed to delete user');
                }
            });
        }
    });

    // Handle user view (example: show modal or redirect)
    $(document).on('click', '.view-user', function() {
        const userId = $(this).data('user-id');
        window.location.href = `user-profile.php?id=${userId}`;
    });
});
