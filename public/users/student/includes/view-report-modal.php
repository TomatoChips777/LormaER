<!-- View Report Modal -->
<div class="modal fade" id="viewReportModal<?php echo $report['id']; ?>" tabindex="-1" aria-labelledby="viewReportModalLabel<?php echo $report['id']; ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewReportModalLabel<?php echo $report['id']; ?>">Report Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <h5>Location:</h5>
                    <p><?php echo htmlspecialchars($report['location']); ?></p>
                </div>
                <div class="mb-3">
                    <h5>Issue Type:</h5>
                    <p><?php echo ucfirst(htmlspecialchars($report['issue_type'])); ?></p>
                </div>
                <div class="mb-3">
                    <h5>Description:</h5>
                    <p><?php echo htmlspecialchars($report['description']); ?></p>
                </div>
                <div class="mb-3">
                    <h5>Status:</h5>
                    <?php
                    $statusClass = [
                        'pending' => 'warning',
                        'in_progress' => 'primary',
                        'resolved' => 'success'
                    ][$report['status']];
                    ?>
                    <span class="badge bg-<?php echo $statusClass; ?>">
                        <?php echo ucfirst(str_replace('_', ' ', $report['status'])); ?>
                    </span>
                </div>
                <?php if ($report['image_path']): ?>
                    <div class="mb-3">
                        <h5>Attached Image:</h5>
                        <img src="../<?php echo htmlspecialchars($report['image_path']); ?>" alt="Report Image" class="img-fluid">
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>