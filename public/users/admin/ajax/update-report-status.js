$(document).ready(function() {
    // Initialize the DataTable
    $('#reportsTable').DataTable({
        "paging": true,      
        "searching": true,   
        "ordering": true,   
        "info": true,        
        "lengthMenu": [10, 25, 50, 100],
        "autoWidth": false,  
        
    });
$('.status-select').change(function() {
    var reportId = $(this).data('report-id');
    var newStatus = $(this).val();

    var selectElement = $(this); // Get the current select element
    selectElement.prop('disabled', true); //Temp disable

    $.ajax({
        url: 'backend/update-report-status.php',
        type: 'POST',
        data: {
            report_id: reportId,
            status: newStatus
        },
        success: function(response) {
            var data = JSON.parse(response);
        },
        error: function(xhr, status, error) {
            alert('An error occurred while updating the report status. Please try again.');
            selectElement.prop('disabled', false);
        }
    });
});

});
