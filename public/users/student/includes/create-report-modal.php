 <!-- New Report Modal -->
 <div class="modal fade" id="newReportModal" tabindex="-1">
     <div class="modal-dialog modal-lg">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title">Submit New Report</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
             </div>
             <form id="reportForm" method="POST" enctype="multipart/form-data">
                 <div class="modal-body">
                     <div class="mb-3">
                         <label class="form-label">Location</label>
                         <input type="text" class="form-control" name="location" required placeholder="e.g., Building A, Room 101">
                     </div>
                     <div class="mb-3">
                         <label class="form-label">Issue Type</label>
                         <select class="form-select" name="issue_type" required>
                             <option value="">Select issue type</option>
                             <option value="plumbing">Plumbing Issue</option>
                             <option value="electrical">Electrical Problem</option>
                             <option value="structural">Structural Damage</option>
                             <option value="cleaning">Cleaning Required</option>
                             <option value="safety">Safety Concern</option>
                             <option value="other">Other</option>
                         </select>
                     </div>
                     <div class="mb-3">
                         <label class="form-label">Description</label>
                         <textarea class="form-control" name="description" rows="4" required placeholder="Please provide detailed description of the issue..."></textarea>
                     </div>
                     <div class="mb-3">
                         <label class="form-label">Upload Image</label>
                         <input type="file" class="form-control" name="image" accept="image/*" capture="camera">
                         <div class="form-text">You can take a photo or upload an existing image</div>
                     </div>
                 </div>
                 <div class="modal-footer">
                     <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                     <button type="submit" class="btn btn-success">Submit Report</button>
                 </div>
             </form>
         </div>
     </div>
 </div>