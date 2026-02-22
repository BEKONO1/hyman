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
                            @if ($list_status != 'pending')
                                @if ($auth_user->can('provider add'))
                                    <a href="{{ route('provider.create') }}"
                                        class=" float-end me-1 btn btn-sm btn-primary"><i class="fa fa-plus-circle"></i>
                                        {{ __('messages.add_form_title', ['form' => __('messages.provider')]) }}</a>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="row justify-content-between gy-3">
                            <div class="col-md-6 col-lg-4 col-xl-3">
                                <div class="col-md-12">
                                    <form action="{{ route('provider.bulk-action') }}" id="quick-action-form"
                                        class="form-disabled d-flex gap-3 align-items-center">
                                        @csrf
                                        <select name="action_type" class="form-select select2" id="quick-action-type"
                                            style="width:100%" disabled>
                                            <option value="">{{ __('messages.no_action') }}</option>
                                            <option value="change-status">{{ __('messages.status') }}</option>
                                            <option value="delete">{{ __('messages.delete') }}</option>
                                            <option value="restore">{{ __('messages.restore') }}</option>
                                            <option value="permanently-delete">{{ __('messages.permanent_dlt') }}
                                            </option>
                                        </select>

                                        <div class="select-status d-none quick-action-field" id="change-status-action"
                                            style="width:100%">
                                            <select name="status" class="form-select select2" id="status"
                                                style="width:100%">
                                                @if ($list_status == 'pending')
                                                    <option value="1">{{ __('messages.approve') }}</option>
                                                @else
                                                    <option value="1">{{ __('messages.active') }}</option>
                                                    <option value="0">{{ __('messages.inactive') }}</option>
                                                @endif
                                            </select>
                                        </div>
                                        <button id="quick-action-apply" class="btn btn-primary" data-confirmation='true'
                                            data-ajax="true" data-submit="{{ route('provider.bulk-action') }}"
                                            data-datatable="reload"
                                            data-title="{{ __('provider', ['form' => __('provider')]) }}"
                                            title="{{ __('provider', ['form' => __('provider')]) }}"
                                            data-message='{{ __('Do you want to perform this action?') }}'
                                            >{{ __('messages.apply') }}</button>
                                    </form>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-3">
                                <div class="d-flex align-items-center gap-3 justify-content-end">
                                    <div class="input-group input-group-search ms-2">
                                        <span class="input-group-text" id="addon-wrapping"><i
                                                class="fas fa-search"></i></span>
                                        <input type="text" class="form-control dt-search" placeholder="Search..."
                                            aria-label="Search" aria-describedby="addon-wrapping"
                                            aria-controls="dataTableBuilder">
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="datatable" class="table table-striped border"></table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            const canEdit = {{ json_encode($auth_user->can('provider edit')) }};
            const canDelete = {{ json_encode($auth_user->can('provider delete')) }};
            const canChangePassword = {{ json_encode($auth_user->can('provider changepassword')) }};
            const zone_id = @json($zone_id);
            const columns = [{
                    name: 'check',
                    data: 'check',
                    title: '<input type="checkbox" class="form-check-input" name="select_all_table" id="select-all-table" data-type="user" onclick="selectAllTable(this)">',
                    searchable: false,
                    exportable: false,
                    orderable: false,
                },
                {
                    data: 'updated_at',
                    name: 'updated_at',
                    title: "{{ __('product.lbl_update_at') }}",
                    orderable: true,
                    visible: false,
                },
                {
                    data: 'display_name',
                    name: 'display_name',
                    title: "{{ __('messages.name') }}",
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    title: "{{ __('messages.joining_date') }}"
                },
                {
                    data: 'providertype_id',
                    name: 'providertype_id',
                    title: "{{ __('messages.providertype') }}"
                },
                {
                    data: 'contact_number',
                    name: 'contact_number',
                    title: "{{ __('messages.contact_number') }}"
                },
                {
                    data: 'wallet',
                    name: 'wallet',
                    title: "{{ __('messages.wallet_amt') }}",
                    searchable: false,
                    orderable: false,
                },
                {
                    data: 'status',
                    name: 'status',
                    title: "{{ __('messages.status') }}"
                },
            ];

            // Add the action column if the user has edit or delete permissions
            if (canEdit || canDelete || canChangePassword) {
                columns.push({
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    title: "{{ __('messages.action') }}",
                    className: 'text-end'
                });
            }

            window.renderedDataTable = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                responsive: true,
                dom: '<"row align-items-center"><"table-responsive my-3 mt-3 mb-2 pb-1" rt><"row align-items-center data_table_widgets" <"col-md-6" <"d-flex align-items-center flex-wrap gap-3" l i>><"col-md-6" p>><"clear">',
                ajax: {
                    "type": "GET",
                    "url": '{{ route('provider.index_data', ['list_status' => $list_status]) }}',
                    "data": function(d) {
                        d.search = {
                            value: $('.dt-search').val()
                        };
                        d.filter = {
                            column_status: $('#column_status').val()
                        };
                        d.zone_id = zone_id;
                    },
                },
                columns: columns,
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
            resetQuickAction();
        });

        $(document).on('click', '[data-ajax="true"]', function(e) {
            e.preventDefault();
            const button = $(this);
            const confirmation = button.data('confirmation');

            if (confirmation === 'true') {
                const message = button.data('message');
                if (confirm(message)) {
                    const submitUrl = button.data('submit');
                    const form = button.closest('form');
                    form.attr('action', submitUrl);
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
    
</x-master-layout>
