<!-- Assignment Modal -->
<div class="modal fade" id="assignOrderModal" tabindex="-1" aria-labelledby="assignOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white border-0">
                <h5 class="modal-title d-flex align-items-center" id="assignOrderModalLabel">
                    <i class="ri-user-add-line me-2"></i>
                    Assign Order to Delivery Staff
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="assignOrderForm" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <!-- Order Info Card -->
                    <div class="card border-0 bg-light mb-4">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="mb-1 fw-semibold">Order Information</h6>
                                    <p class="mb-0 text-muted small">
                                        <span class="fw-bold" id="orderIdDisplay"></span> | 
                                        <span id="customerNameDisplay"></span>
                                    </p>
                                </div>
                                <div class="text-end">
                                    <i class="ri-shopping-bag-3-line fs-3 text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Delivery Staff Selection -->
                    <div class="mb-3">
                        <label for="delivery_staff_id" class="form-label fw-semibold">
                            <i class="ri-user-line me-1"></i> Select Delivery Staff <span class="text-danger">*</span>
                        </label>
                        <select class="form-select select2-ajax" 
                                id="delivery_staff_id" 
                                name="delivery_staff_id" 
                                data-url="{{ route('admin.estore-delivery-staff.select2-ajax-options') }}"
                                required
                                style="width: 100%;">
                            <option value="">Search and select delivery staff...</option>
                        </select>
                        <div class="form-text">
                            <i class="ri-information-line me-1"></i>
                            Start typing to search for delivery staff
                        </div>
                    </div>

                    <!-- Delivery Room -->
                    <div class="mb-3">
                        <label for="delivery_room" class="form-label fw-semibold">
                            <i class="ri-home-line me-1"></i> Delivery Room <span class="text-muted">(Optional)</span>
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="delivery_room" 
                               name="delivery_room" 
                               placeholder="e.g., Room 302, Room 405"
                               maxlength="50">
                        <div class="form-text">
                            <i class="ri-information-line me-1"></i>
                            Enter the room number where the order should be delivered
                        </div>
                    </div>

                    <!-- Info Alert -->
                    <div class="alert alert-info border-0 d-flex align-items-start mb-0" role="alert">
                        <i class="ri-information-line me-2 mt-1"></i>
                        <div class="small">
                            <strong>Note:</strong> Once assigned, the delivery staff will receive a notification and can start processing the order.
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer border-0 bg-light p-3">
                    <button type="button" class="btn btn-light waves-effect" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light" id="assignSubmitBtn">
                        <i class="ri-check-line me-1"></i> Assign Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// let assignOrderModal;
// let deliveryStaffSelect;

function openAssignModal(orderId, orderNumber, customerName, currentStaffId = null) {
    // Set order info
    document.getElementById('orderIdDisplay').textContent = orderNumber;
    document.getElementById('customerNameDisplay').textContent = customerName;
    
    // Reset form
    document.getElementById('assignOrderForm').reset();
    $('#delivery_staff_id').val(null).trigger('change');
    document.getElementById('delivery_room').value = '';
    
    // Set form action
    document.getElementById('assignOrderForm').action = `/admin/estore-orders/${orderId}/assign`;
    
    // Pre-select current staff if reassigning
    if (currentStaffId) {
        // Fetch and set the current staff
        fetch(`/admin/estore-delivery-staff/${currentStaffId}`)
            .then(response => response.json())
            .then(data => {
                if (data && data.name) {
                    const option = new Option(data.name + (data.phone ? ` [+${data.country_code} ${data.phone}]` : ''), currentStaffId, true, true);
                    deliveryStaffSelect.append(option).trigger('change');
                }
            })
            .catch(error => console.error('Error fetching staff:', error));
    }
    const modal = new bootstrap.Modal(document.getElementById('assignOrderModal'));
    // Show modal
    modal.show();
    initializeSelect2Ajax();
}

// Handle form submission
document.getElementById('assignOrderForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('assignSubmitBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="ri-loader-4-line me-1 spin"></i> Assigning...';
    
    const formData = new FormData(this);
    const url = this.action;
    
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log(data.status);
        if (data.status == 'success') {
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message || 'Order assigned successfully',
                timer: 2000,
                showConfirmButton: false
            });
            
            // Close modal - get modal instance properly
            const modalElement = document.getElementById('assignOrderModal');
            const modalInstance = bootstrap.Modal.getInstance(modalElement);
            if (modalInstance) {
                modalInstance.hide();
            }
            
            // Reload table or page
            setTimeout(() => {
                window.location.reload();
            }, 500);
        } else {
            // Show error
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: data.message || 'Failed to assign order',
                confirmButtonText: 'OK'
            });
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'An error occurred while assigning the order',
            confirmButtonText: 'OK'
        });
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});

// Add spin animation
const style = document.createElement('style');
style.textContent = `
    .spin {
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);
</script>