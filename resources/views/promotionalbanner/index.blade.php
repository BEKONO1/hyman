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
                            <h5 class="fw-bold">{{ $pageTitle ?? trans('messages.provider_promotional_banner') }}</h5>

                            @if(!auth()->user()->hasAnyRole(['admin', 'demo_admin']))
                            <a href="{{ route('promotional-banner.create') }}" class="float-end me-1 btn btn-sm btn-primary">
                                <i class="fa fa-plus-circle"></i>
                                {{ __('messages.add_new', ['form' => __('messages.promotional_banner')]) }}
                            </a>
                            @endif

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
                            <form action="{{ route('promotionalbanner.bulk-action') }}" id="quick-action-form" class="form-disabled d-flex gap-3 align-items-center">
                                @csrf
                                <select name="action_type" class="form-select select2" id="quick-action-type" style="width:100%" disabled>
                                    <option value="">{{ __('messages.no_action') }}</option>
                                    <option value="change-status">{{ __('messages.status') }}</option>
                                    <option value="delete">{{ __('messages.delete') }}</option>
                                </select>

                                <div class="select-status d-none quick-action-field" id="change-status-action" style="width:100%">
                                    <select name="status" class="form-select select2" id="status">
                                        <option value="accepted">{{ __('messages.accepted') }}</option>
                                        <option value="pending">{{ __('messages.pending') }}</option>
                                        <option value="rejected">{{ __('messages.rejected') }}</option>
                                    </select>
                                </div>
                                <button id="quick-action-apply" class="btn btn-primary" data-ajax="true" data--submit="{{ route('promotionalbanner.bulk-action') }}" data-datatable="reload" data-confirmation='true' data-title="{{ __('messages.promotional_banner') }}" title="{{ __('messages.promotional_banner') }}" data-message='{{ __('messages.confirm_action') }}' >{{ __('messages.apply') }}</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <div class="d-flex align-items-center gap-3 justify-content-end">
                            <div class="d-flex justify-content-end gap-3">
                                <div class="datatable-filter ml-auto">
                                    <select name="column_status" id="column_status" class="select2 form-select" data-filter="select" style="width: 100%">
                                        <option value="">{{ __('messages.all') }}</option>
                                        <option value="accepted">{{ __('messages.accepted') }}</option>
                                        <option value="pending">{{ __('messages.pending') }}</option>
                                        <option value="rejected">{{ __('messages.rejected') }}</option>
                                    </select>
                                </div>
                                <div class="input-group input-group-search ms-2">
                                    <span class="input-group-text" id="addon-wrapping"><i
                                            class="fas fa-search"></i></span>
                                    <input type="text" class="form-control dt-search" placeholder="{{ __('messages.search') }}"
                                        aria-label="{{ __('messages.search') }}" aria-describedby="addon-wrapping"
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
                    url: "{{ route('promotional-banner.index_data') }}",
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

                    {
                        name: 'check',
                        data: 'check',
                        title: '<input type="checkbox" class="form-check-input" name="select_all_table" id="select-all-table" onclick="selectAllTable(this)">',
                        exportable: false,
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'id',
                        name: 'id',
                        title: "{{ __('messages.book_id') }}",
                        orderable: true,
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at',
                        title: "{{ __('product.lbl_update_at') }}",
                        orderable: true,
                        visible: false,
                    },
                    {
                        data: 'banner',
                        name: 'banner',
                        orderable: false,
                        title: "{{ __('messages.banner') }}"
                    },
                  @if(auth()->user()->hasAnyRole(['admin', 'demo_admin']))
                    {
                        data: 'display_name',
                        name: 'display_name',
                        title: "{{ __('messages.provider') }}",
                        orderable: true,
                    },
                    @endif
                    // @if(auth()->user()->hasRole('admin'))
                    // {
                    //     data: 'provider',
                    //     name: 'provider',
                    //     orderable: false,
                    //     title: "{{ __('messages.provider') }}"
                    // },
                    // @endif

                    {
                        data: 'date_range',
                        name: 'date_range',
                        orderable: true,
                        title: "{{ __('messages.date_range') }}"
                    },

                    {
                        data: 'price',
                        name: 'price',
                        orderable: true,
                        title: "{{ __('messages.price') }}"
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: true,
                        title: "{{ __('messages.status') }}"
                    },
                    @if(auth()->user()->hasAnyRole(['admin', 'demo_admin']))
                    {
                        data: 'payment_status',
                        name: 'payment_status',
                        orderable: true,
                        title: "{{ __('messages.payment_status') }}"
                    },
                    @endif
                   @if(!auth()->user()->hasAnyRole(['admin', 'demo_admin']))
                    {
                        data: 'reason',
                        name: 'reason',
                        orderable: false,
                        title: "{{ __('messages.reason') }}"
                    },
                    @endif
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        title: "{{ __('messages.action') }}"
                    },



                ],
                order: [
                    [1, 'desc']
                ],
                language: {
                    processing: "{{ __('messages.processing') }}" // Set your custom processing text
                }
            });

            // Trigger DataTable reload on status change
            $('#column_status').change(function() {
                window.renderedDataTable.ajax.reload();
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
        </script>

        <script>


    // Approve Banner
    $(document).on('click', '.approve-banner', function() {
    let id = $(this).data('id');

    Swal.fire({
        icon: 'success',
        title: `<h2 class="swal-title">{{ __('messages.approve_banner_confirmation') }}</h2>`,
        showCancelButton: true,
        cancelButtonText: '{{ __('messages.cancel') }}',
        confirmButtonText: '{{ __('messages.approve') }}',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-alert'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `promotional-banner/${id}/status`,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    status: 'accepted'
                },
                success: function(response) {
                    Swal.fire('{{ __('messages.approved') }}', '{{ __('messages.banner_approved_success') }}', 'success');
                    $('#datatable').DataTable().ajax.reload();
                },
                error: function() {
                    Swal.fire('{{ __('messages.error') }}', '{{ __('messages.something_went_wrong') }}', 'error');
                }
            });
        }
    });
});

    // Reject Banner
    $(document).on('click', '.reject-banner', function() {
    let id = $(this).data('id');

    Swal.fire({
        icon: "error",
        title: `<h2 style="font-size: 20px; font-weight: bold; margin-bottom: 15px;">{{ __('messages.reject_banner_confirmation') }}</h2>`,
        html: `
            <div style="text-align: left; margin-top: 5px; background-color: #f0f0f0; padding: 20px; border-radius: 10px;">
                <label for="reject-reason" style="font-size: 14px; font-weight: bold; display: block; margin-bottom: 5px;">
                    Provide the reason for rejection
                </label>
                <textarea id="reject-reason" placeholder="e.g. Insufficient details"
                    style="width: 100%; height: 100px; background-color: #ffffff; border: 1px solid #ccc;
                    border-radius: 8px; padding: 10px; font-size: 14px; resize: none;"></textarea>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: '<span style="font-size: 14px; font-weight: bold;">Reject & Refund</span>',
        cancelButtonText: '<span style="font-size: 14px; font-weight: bold;">Cancel</span>',
        reverseButtons: true,
        padding: '25px', // Adds padding for a better look
        width: '450px', // Adjusts popup width
        customClass: {
            popup: 'swal2-popup'
        },
        preConfirm: () => {
            const reason = document.getElementById('reject-reason').value.trim();
            if (!reason) {
                Swal.showValidationMessage("Rejection reason is required!");
                return false;
            }
            return $.ajax({
                url: `promotional-banner/${id}/status`,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    status: 'rejected',
                    reject_reason: reason
                }
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire('Rejected!', 'The banner has been rejected.', 'success');
            $('#datatable').DataTable().ajax.reload();
        }
    });
});

    // Handle payment status change
    $(document).on('change', '.payment-status-dropdown', function() {
        const bannerId = $(this).data('id');
        const paymentStatus = $(this).val();

        $.ajax({
            url: `promotional-banner/${bannerId}/update-payment-status`,
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                payment_status: paymentStatus
            },
            success: function(response) {
                if (response.status) {
                    Swal.fire('Success!', 'Payment status updated successfully.', 'success');
                    $('#datatable').DataTable().ajax.reload();
                }
            },
            error: function(xhr) {
                Swal.fire('Error!', 'Something went wrong!', 'error');
            }
        });
    });

</script>

</x-master-layout>
