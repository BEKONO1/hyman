<x-master-layout>

    <head>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    </head>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3 flex-wrap gap-3">
                            <h5 class="fw-bold">{{ $pageTitle ?? trans('messages.list') }}</h5>
                            {{-- @if ($auth_user->can('service add') && Route::currentRouteName() !== 'servicepackage.service')
                                <a href="{{ route('service.create') }}" class="float-end me-1 btn btn-sm btn-primary "><i
                                        class="fa fa-plus-circle"></i>
                                    {{ __('messages.add_form_title', ['form' => __('messages.service')]) }}</a>
                            @endif --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="row justify-content-between gy-3">
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <div class="col-md-12">
                            <form action="{{ route('request.bulk-action') }}" id="quick-action-form"
                                class="form-disabled d-flex gap-3 align-items-center">
                                @csrf
                                @if ($auth_user->user_type !== 'provider')
                                <select name="action_type" class="form-select select2" id="quick-action-type"
                                    style="width:100%" disabled>
                                    <option value="">{{ __('messages.no_action') }}</option>
                                    <option value="change-status">{{ __('messages.status') }}</option>
                                    @if ($auth_user->can('service delete'))
                                        <option value="delete">{{ __('messages.delete') }}</option>
                                        <option value="restore">{{ __('messages.restore') }}</option>
                                        <option value="permanently-delete">{{ __('messages.permanent_dlt') }}</option>
                                    @endif
                                </select>

                                <div class="select-status d-none quick-action-field" id="change-status-action"
                                    style="width:100%">
                                    <select name="status" class="form-control select2" id="status">
                                        <option value="pending">{{ __('messages.pending') }}</option>
                                        <option value="reject">{{ __('messages.reject') }}</option>                                        
                                        <option value="approve">{{ __('messages.approve') }}</option>                                        
                                    </select>
                                </div>
                                <button id="quick-action-apply" class="btn btn-primary" data-ajax="true"
                                    data--submit="{{ route('request.bulk-action') }}" data-datatable="reload"
                                    data-confirmation='true'
                                    data-title="{{ __('service', ['form' => __('service')]) }}"
                                    title="{{ __('service', ['form' => __('service')]) }}"
                                    data-message="{{ __('messages.perform_action_confirmation') }}"
                                    data-confirm-button="{{ __('messages.yes') }}"
                                    data-cancel-button="{{ __('messages.no') }}"
                                    >{{ __('messages.apply') }}</button>
                                    @endif
                        </div>
                        </form>
                    </div>
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <div class="d-flex align-items-center gap-3 justify-content-end">
                            <div class="d-flex justify-content-end gap-3">
                                <div class="datatable-filter ml-auto">
                                <select name="column_status" id="column_status" class="select2 form-control"
                                    data-filter="select" style="width: 100%">
                                    <option value="">{{ __('messages.all') }}</option>
                                    <option value="pending" {{ $filter['status'] == 'pending' ? 'selected' : '' }}>
                                        {{ __('messages.pending') }}</option>
                                    <option value="reject" {{ $filter['status'] == 'reject' ? 'selected' : '' }}>
                                        {{ __('messages.reject') }}</option>
                                    <option value="approve" {{ $filter['status'] == 'approve' ? 'selected' : '' }}>
                                        {{ __('messages.approve') }}</option>
                                </select>
                                </div>
                                <div class="input-group input-group-search ms-2">
                                    <span class="input-group-text" id="addon-wrapping"><i
                                            class="fas fa-search"></i></span>
                                    <input type="text" class="form-control dt-search" placeholder="{{ __('messages.search') }}..."
                                        aria-label="Search" aria-describedby="addon-wrapping"
                                        aria-controls="dataTableBuilder">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="datatable" class="table table-striped border">

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {

            window.renderedDataTable = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                responsive: true,
                dom: '<"row align-items-center"><"table-responsive my-3 mt-3 mb-2 pb-1" rt><"row align-items-center data_table_widgets" <"col-md-6" <"d-flex align-items-center flex-wrap gap-3" l i>><"col-md-6" p>><"clear">',
                ajax: {
                    "type": "GET",
                    "url": '{{ route('service.request-index-data', ) }}',
                    "data": function(d) {
                        d.search = {
                            value: $('.dt-search').val()
                        };
                        d.filter = {
                            column_status: $('#column_status').val()
                        }
                    },
                },

                columns: [
                    @if ($auth_user->user_type !== 'provider')
                    {
                    name: 'check',
                        data: 'check',
                        title: '<input type="checkbox" class="form-check-input" name="select_all_table" id="select-all-table" data-type="service" onclick="selectAllTable(this)">',
                        exportable: false,
                        orderable: false,
                        searchable: false,
                    },        @endif

                    {
                        data: 'updated_at',
                        name: 'updated_at',
                        title: "{{ __('product.lbl_update_at') }}",
                        orderable: true,
                        visible: false,
                    },
                    {
                        data: 'name',
                        name: 'name',
                        title: "{{ __('messages.name') }}"
                    },
                        {
                            data: 'provider_id',
                            name: 'provider_id',
                            title: "{{ __('messages.provider') }}"
                        },
                     {
                        data: 'category_id',
                        name: 'category_id',
                        title: "{{ __('messages.category') }}"
                    },
                    {
                        data: 'price',
                        name: 'price',
                        title: "{{ __('messages.price') }}"
                    },
                    {
                        data: 'service_request_status',
                        name: 'service_request_status',
                        title: "{{ __('messages.status') }}"
                    },
                   {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        title: "{{ __('messages.action') }}",
                        className: 'text-end'
                    }

                ],
                order: [
                    [1, 'desc']
                ],
                language: {
                    processing: "{{ __('messages.processing') }}" // Set your custom processing text
                }
            });
        });

        function resetQuickAction() {
            const actionValue = $('#quick-action-type').val();
            console.log(actionValue)
            if (actionValue != '') {
                $('#quick-action-apply').removeAttr('disabled');

                if (actionValue == 'change-status') {
                    $('.quick-action-field').addClass('d-none');
                    $('#change-status-action').removeClass('d-none');
                } else {
                    $('.quick-action-field').addClass('d-none');
                }
            } else {
                $('#quick-action-apply').attr('disabled', true);
                $('.quick-action-field').addClass('d-none');
            }
        }

        $('#quick-action-type').change(function() {
            resetQuickAction()
        });
        $(document).on('update_quick_action', function() {

        })



        $(document).on('click', '[data-ajax="true"]', function(e) {
            e.preventDefault();
            const button = $(this);
            const confirmation = button.data('confirmation');

            if (confirmation === 'true') {
                const message = button.data('message');
                if (confirm(message)) {
                    Swal.fire({
                            title: '{{ __("messages.please_wait") }}',
                            text: '{{ __("messages.saving_data") }}',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    const submitUrl = button.data('submit');
                    const form = button.closest('form');
                    form.submit();
                }
            } else {
                const submitUrl = button.data('submit');
                const form = button.closest('form');
                form.attr('action', submitUrl);
                form.submit();
            }
        });

        $(document).ready(function () {

// Handle approve button click
$(document).on('click', '.approve-btn', function () {
    var serviceId = $(this).data('id');
    showApprovalConfirmation(serviceId, 'approved'); // Show confirmation for approval
});

$(document).on('click', '.trash-btn', function () {
        var serviceId = $(this).data('id');
        showDeleteConfirmation(serviceId); // Show confirmation for deletion
    });

// Handle reject button click
$(document).on('click', '.reject-btn', function () {
    var serviceId = $(this).data('id');
    showRejectionConfirmation(serviceId); // Show confirmation for rejection
});

// Function to show SweetAlert with Cancel and Approve options (for approval)
function showApprovalConfirmation(serviceId, status) {
    Swal.fire({
        icon: 'success', // Slightly smaller checkmark
        title: '',
        html: '<span style="color: #333; font-weight: 550; font-size: 20px;">' + 
              '{{ __("messages.are_you_sure_you_want_to") }} ' + 
              (status === "approved" 
                ? '{{ __("messages.approve_this_service_into_list") }}' 
                : '{{ __("messages.reject_this_service_into_list") }}') +
              '</span>', // Darker gray text (not fully black) and medium weight
        showCancelButton: true,
        cancelButtonText: '<span style="color: black; font-weight: 500;">{{ __("messages.cancel") }}</span>', // Black text, medium weight
        confirmButtonText: '{{ __("messages.approve") }}',
        confirmButtonColor: '#6366F1', // Purple confirm button
        cancelButtonColor: '#E5E7EB', // Light gray cancel button
        reverseButtons: true // Reverse button positions
    }).then((result) => {
        if (result.isConfirmed) {
            updateServiceStatus(serviceId, status); // Update the service status
        }
    });
}


// Function to show SweetAlert with input for rejection reason (no second confirmation)
function showRejectionConfirmation(serviceId) {
    Swal.fire({
        title: `<h2 style="font-size: 20px; font-weight: bold; margin-bottom: 15px;">{{ __('messages.reject_service_confirmation_title') }}</h2>`,
        text: '{{ __("messages.provide_rejection_reason") }}', html: `
            <div style="text-align: left; margin-top: 5px; background-color: #f0f0f0; padding: 20px; border-radius: 10px;">
                <label for="reject-reason" style="font-size: 14px; font-weight: bold; display: block; margin-bottom: 5px;">
                {{ __('messages.provide_rejection_reason') }}
                </label>
                <textarea id="reject-reason" placeholder="{{ __('messages.rejection_reason_aria') }}"  
                    style="width: 100%; height: 100px; background-color: #ffffff; border: 1px solid #ccc; 
                    border-radius: 8px; padding: 10px; font-size: 14px; resize: none;"></textarea>
            </div>
        `,
        icon: 'error',
        inputAttributes: {
            'aria-label': '{{ __("messages.rejection_reason_aria") }}'
        },
        showCancelButton: true,
        confirmButtonText: '<span style="font-size: 14px; font-weight: bold;">{{ __("messages.reject") }}</span>',
        cancelButtonText: '<span style="font-size: 14px; font-weight: bold; color: black;">{{ __("messages.cancel") }}</span>',
        cancelButtonColor: '#f0f0f0', 
        reverseButtons: true 
    }).then((result) => {
    if (result.isConfirmed) {
        var rejectionReason = document.getElementById('reject-reason').value;
        if (rejectionReason.trim() !== "") {
            updateServiceStatus(serviceId, 'rejected', rejectionReason);
        } else {
            Swal.fire({
                title: '{{ __("messages.error") }}',
                text: '{{ __("messages.rejection_reason_required") }}',
                icon: 'error',
                confirmButtonText: '{{ __("messages.okay") }}'
            });
        }
    }
});
}

// Function to update service status after confirmation
function updateServiceStatus(serviceId, status, reason = '') {
    Swal.fire({
        title: '{{ __("messages.please_wait") }}',
        text: '{{ __("messages.processing_request") }}',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    $.ajax({
        url: '{{ route("service.updateStatus") }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            id: serviceId,
            status: status, 
            reason: reason 
        },
        success: function (response) {
            Swal.close();
            if (response.success) {
                var badge = (status === 'approved') 
                    ? '<span class="badge badge-success">Approved</span>' 
                    : '<span class="badge badge-danger">Rejected</span>';

                var row = $('#datatable-row-' + serviceId); 
                row.find('.service-status').html(badge); 

                renderedDataTable.ajax.reload();

                Swal.fire({
                    title: '{{ __("messages.success") }}',
                    text: (status === 'approved') 
                        ? '{{ __("messages.service_approved_successfully") }}' 
                        : '{{ __("messages.service_rejected_successfully") }}',
                    icon: 'success',
                    confirmButtonText: '{{ __("messages.okay") }}'
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: 'An error occurred while updating the status.',
                    icon: 'error',
                    confirmButtonText: 'Try Again'
                });
            }
        },
        error: function () {
            // If the AJAX call failed
            Swal.fire({
                title: 'Error!',
                text: 'An error occurred while processing the request.',
                icon: 'error',
                confirmButtonText: 'Try Again'
            });
        }
    });
}



function showDeleteConfirmation(serviceId) {
        Swal.fire({
            text: '{{ __("messages.confirm_delete_service") }}',
            icon: 'warning',
            showCancelButton: true,
            cancelButtonText: '{{ __("messages.cancel") }}',
            confirmButtonText: '{{ __("messages.delete") }}',
            confirmButtonColor: '#dc3545', 
            cancelButtonColor: '#6c757d', 
            reverseButtons: true 
        }).then((result) => {
            if (result.isConfirmed) {
                deleteService(serviceId); 
            }
        });
    }
    function deleteService(serviceId) {
        Swal.fire({
        title: '{{ __("messages.please_wait") }}',
        text: '{{ __("messages.service_deleted_successfully") }}',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    $.ajax({
        url: '{{ route("service.destroy", "") }}/' + serviceId,
        type: 'DELETE',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function (response) {
            Swal.close();
            console.log('Response from server:', response);  
                Swal.fire({
                    title: '{{ __("messages.delete") }}',
                    text: '{{ __("messages.service_deleted_successfully") }}',
                    icon: 'success',
                    confirmButtonText: '{{ __("messages.okay") }}'
                });

            renderedDataTable.ajax.reload();
        },
        error: function(xhr, status, error) {
            Swal.close();
            let errorMessage = xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred while processing the request.';
            Swal.fire({
                title: 'Error!',
                text: errorMessage,
                icon: 'error',
                confirmButtonText: 'Try Again'
            });
        }
    });
}
});
    </script>
<script src="{{ asset('js/sweetalert2.min.js') }}"></script>
</x-master-layout>
