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
                            <a href="{{ route('tax.create') }}" class=" float-end me-1 btn btn-sm btn-primary"><i
                                    class="fa fa-plus-circle"></i>
                                {{ trans('messages.add_form_title', ['form' => trans('messages.tax')]) }}</a>
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
                            <form action="{{ route('tax.bulk-action') }}" id="quick-action-form"
                                class="form-disabled d-flex gap-3 align-items-center">
                                @csrf
                                <select name="action_type" class="form-select select2" id="quick-action-type"
                                    style="width:auto" disabled>
                                    <option value="">{{ __('messages.no_action') }}</option>
                                    <option value="change-status">{{ __('messages.status') }}</option>
                                    <option value="delete">{{ __('messages.delete') }}</option>
                                </select>

                                <div class="select-status d-none quick-action-field" id="change-status-action"
                                    style="width:100%">
                                    <select name="status" class="form-select select2" id="status"
                                        style="width:auto">
                                        <option value="1">{{ __('messages.active') }}</option>
                                        <option value="0">{{ __('messages.inactive') }}</option>
                                    </select>
                                </div>
                                <button id="quick-action-apply" class="btn btn-primary" data-ajax="true"
                                    data--submit="{{ route('tax.bulk-action') }}" data-datatable="reload"
                                    data-confirmation='true' data-title="{{ __('tax', ['form' => __('tax')]) }}"
                                    title="{{ __('tax', ['form' => __('tax')]) }}"
                                    data-message='{{ __('Do you want to perform this action?') }}'
                                    >{{ __('messages.apply') }}</button>
                        </div>

                        </form>
                    </div>
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <div class="d-flex align-items-center gap-3 justify-content-end">
                            <div class="d-flex justify-content-end gap-3">
                                <div class="datatable-filter ml-auto">
                                    <select name="column_status" id="column_status" class="select2 form-select"
                                        data-filter="select" style="width: auto">
                                        <option value="">{{ __('messages.all') }}</option>
                                        <option value="0" {{ $filter['status'] == '0' ? 'selected' : '' }}>
                                            {{ __('messages.inactive') }}</option>
                                        <option value="1" {{ $filter['status'] == '1' ? 'selected' : '' }}>
                                            {{ __('messages.active') }}</option>
                                    </select>
                                </div>
                                <div class="input-group input-group-search ms-2">
                                    <span class="input-group-text" id="addon-wrapping"><i
                                            class="fas fa-search"></i></span>
                                    <input type="text" class="form-control dt-search" placeholder="Search..."
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
        let selectedRows = [];
        
        document.addEventListener('DOMContentLoaded', (event) => {
            window.renderedDataTable = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                responsive: true,
                dom: '<"row align-items-center"><"table-responsive my-3 mt-3 mb-2 pb-1" rt><"row align-items-center data_table_widgets" <"col-md-6" <"d-flex align-items-center flex-wrap gap-3" l i>><"col-md-6" p>><"clear">',
                ajax: {
                    "type": "GET",
                    "url": '{{ route('tax.index_data') }}',
                    "data": function(d) {
                        d.search = {
                            value: $('.dt-search').val()
                        };
                        d.filter = {
                            column_status: $('#column_status').val()
                        }
                    },
                    "dataSrc": function(json) {
                        updateSelectAllCheckbox();
                        return json.data;
                    }
                },
                columns: [{
                        name: 'check',
                        data: 'check',
                        title: '<input type="checkbox" class="form-check-input" name="select_all_table" id="select-all-table" onclick="selectAllTable(this)">',
                        exportable: false,
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at',
                        title: "{{ __('product.lbl_update_at') }}",
                        orderable: true,
                        visible: false,
                    },
                    {
                        data: 'title',
                        name: 'title',
                        title: "{{ __('messages.title') }}"
                    },
                    {
                        data: 'value',
                        name: 'value',
                        title: "{{ __('messages.value') }}"
                    },
                    {
                        data: 'status',
                        name: 'status',
                        title: "{{ __('messages.status') }}"
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        title: "{{ __('messages.action') }}"
                    }

                ],
                order: [
                    ['1', 'desc']
                ],
                language: {
                    processing: "{{ __('messages.processing') }}"
                },
                drawCallback: function() {
                    const tableRows = window.renderedDataTable.rows().nodes();
                    tableRows.each(function(row) {
                        const rowId = $(row).find('input[name="table_checkbox"]').val();
                        if (selectedRows.includes(rowId)) {
                            $(row).find('input[name="table_checkbox"]').prop('checked', true);
                        }
                    });
                    updateSelectAllCheckbox();
                }
            });

            // Handle select all checkbox
            $(document).on('click', '#select-all-table', function() {
                const isChecked = $(this).prop('checked');
                const tableRows = window.renderedDataTable.rows().nodes();
                
                tableRows.each(function(row) {
                    const checkbox = $(row).find('input[name="table_checkbox"]');
                    checkbox.prop('checked', isChecked);
                    
                    const rowId = checkbox.val();
                    const index = selectedRows.indexOf(rowId);
                    
                    if (isChecked && index === -1) {
                        selectedRows.push(rowId);
                    } else if (!isChecked && index !== -1) {
                        selectedRows.splice(index, 1);
                    }
                });
                
                updateQuickActionButton();
            });

            // Handle individual checkboxes
            $(document).on('click', 'input[name="table_checkbox"]', function() {
                const rowId = $(this).val();
                const index = selectedRows.indexOf(rowId);
                
                if (this.checked && index === -1) {
                    selectedRows.push(rowId);
                } else if (!this.checked && index !== -1) {
                    selectedRows.splice(index, 1);
                }
                
                updateSelectAllCheckbox();
                updateQuickActionButton();
            });

            // Handle filter and search
            $('.dt-search, #column_status').on('change keyup', function() {
                window.renderedDataTable.draw();
            });
        });

        function updateSelectAllCheckbox() {
            const visibleCheckboxes = $('input[name="table_checkbox"]:visible');
            const checkedVisibleCheckboxes = $('input[name="table_checkbox"]:visible:checked');
            
            $('#select-all-table').prop(
                'checked',
                visibleCheckboxes.length > 0 && visibleCheckboxes.length === checkedVisibleCheckboxes.length
            );
        }

        function updateQuickActionButton() {
            const hasSelectedRows = selectedRows.length > 0;
            $('#quick-action-type').prop('disabled', !hasSelectedRows);
            if (!hasSelectedRows) {
                $('#quick-action-apply').prop('disabled', true);
                $('.quick-action-field').addClass('d-none');
            }
        }

        function selectAllTable(elem) {
            const isChecked = $(elem).prop('checked');
            const tableRows = window.renderedDataTable.rows().nodes();
            
            tableRows.each(function(row) {
                const checkbox = $(row).find('input[name="table_checkbox"]');
                checkbox.prop('checked', isChecked);
                
                const rowId = checkbox.val();
                const index = selectedRows.indexOf(rowId);
                
                if (isChecked && index === -1) {
                    selectedRows.push(rowId);
                } else if (!isChecked && index !== -1) {
                    selectedRows.splice(index, 1);
                }
            });
            
            updateQuickActionButton();
        }

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
