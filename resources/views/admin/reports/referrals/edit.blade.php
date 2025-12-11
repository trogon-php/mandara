<form id="edit-form" method="POST" action="{{ route('admin.reports.referrals.update', $edit_data->id) }}">
    @csrf
    @method('PUT')
    
    <div class="modal-body">
        <div class="row">
            <!-- Referrer Info (Read-only) -->
            <div class="col-md-12 mb-3">
                <label class="form-label">Referrer</label>
                <input type="text" class="form-control" value="{{ $edit_data->referrer->name ?? 'N/A' }} ({{ $edit_data->referrer->email ?? 'N/A' }})" readonly>
            </div>

            <!-- Referred User Info (Read-only) -->
            <div class="col-md-12 mb-3">
                <label class="form-label">Referred User</label>
                <input type="text" class="form-control" 
                    value="{{ $edit_data->referred ? $edit_data->referred->name . ' (' . $edit_data->referred->email . ')' : 'Not Used Yet' }}" 
                    readonly>
            </div>

            <!-- Referral Code (Read-only) -->
            <div class="col-md-12 mb-3">
                <label class="form-label">Referral Code</label>
                <input type="text" class="form-control" value="{{ $edit_data->referral_code }}" readonly>
            </div>

            <!-- Status -->
            <div class="col-md-6 mb-3">
                <label class="form-label">Status <span class="text-danger">*</span></label>
                <select name="status" class="form-select" required>
                    <option value="pending" {{ $edit_data->status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ $edit_data->status == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="rewarded" {{ $edit_data->status == 'rewarded' ? 'selected' : '' }}>Rewarded</option>
                    <option value="cancelled" {{ $edit_data->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <!-- Reward Coins -->
            <div class="col-md-6 mb-3">
                <label class="form-label">Reward Coins <span class="text-danger">*</span></label>
                <input type="number" name="reward_coins" class="form-control" value="{{ $edit_data->reward_coins }}" required min="0">
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Update Referral</button>
    </div>
</form>

<script>
$(document).ready(function() {
    $('#edit-form').on('submit', function(e) {
        e.preventDefault();
        
        let form = $(this);
        let url = form.attr('action');
        let formData = form.serialize();
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function(response) {
                showToast('success', response.message || 'Referral updated successfully');
                $('#crud-modal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                let errors = xhr.responseJSON?.errors;
                if (errors) {
                    let errorMessages = Object.values(errors).flat().join('<br>');
                    showToast('error', errorMessages);
                } else {
                    showToast('error', xhr.responseJSON?.message || 'An error occurred');
                }
            }
        });
    });
});
</script>



