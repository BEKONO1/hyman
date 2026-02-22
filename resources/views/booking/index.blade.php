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
                            @if ($auth_user->can('booking add'))
                                <a href="{{ route('booking.create') }}" class=" float-end me-1 btn btn-sm btn-primary"><i
                                        class="fa fa-plus-circle"></i>
                                    {{ __('messages.add_form_title', ['form' => __('messages.booking')]) }}</a>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center justify-content-between gy-3">
                    <div class="col-md-6 col-lg-4 col-xl-5">
                        <div class="d-flex flex-wrap align-items-center gap-3">
                            @if (!auth()->user()->hasAnyRole(['user']))
                            <div class="">
                                <span class="fw-bold"> {{ __('messages.total_amount') }}: </span>
                                <span class="text-primary total-earnings-display fw-medium">{{ getPriceFormat($totalEarning) }}</span>
                            </div>
                           
                            <a href="#" data-bs-toggle="modal" data-bs-target="#breakdownModal"
                                class="text-success">View Breakdown</a>
                            @endif
                            <button type="button" class="btn btn-sm btn-primary ms-2" data-bs-toggle="modal"
                                data-bs-target="#Export">{{ __('messages.export') }}</button>
                        </div>
                       


                    </div>
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <div class="d-flex justify-content-end gap-2">
                            <div class="input-group input-group-search ms-2">
                                <span class="input-group-text" id="addon-wrapping"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control dt-search" placeholder="Search..."
                                    aria-label="Search" aria-describedby="addon-wrapping"
                                    aria-controls="dataTableBuilder">
                            </div>
                            <button class="btn btn-primary d-flex align-items-center gap-1 btn-group position-relative"
                                data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample"
                                aria-controls="offcanvasExample">
                                <i class="ph ph-funnel"></i>{{ __('messages.filter') }}
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger filter-count" style="display: none;">
                                    0
                                </span>
                            </button>
                        </div>

                    </div>

                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="datatable" class="table table-striped border">

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <x-advance-filter>
            <x-slot name="title">
                <h4>{{ __('messages.filter_title') }}</h4>
            </x-slot>

            <div class="form-group datatable-filter">
                <label class="form-label" for="service_name">{{ __('messages.service_label') }}</label>
                <select name="service_name[]" id="service_id" class="form-select select2" data-filter="select" multiple
                        data-ajax--url="{{ route('ajax-list', ['type' => 'service-list', 'provider_id' => auth()->user()->hasRole('provider') ? auth()->user()->id : null, 'handyman_id' => auth()->user()->hasRole('handyman') ? auth()->user()->id : null]) }}"
                        data-ajax--cache="true"
                        data-placeholder="{{ __('messages.all_services') }}">
                    <!-- <option value="">{{ __('messages.all_services') }}</option> -->
                </select>

            </div>

            <div class="form-group datatable-filter">
                <label class="form-label" for="date_range">{{ __('messages.daterange_label') }}</label>
                <input type="text" id="datepicker1" class="form-control flatpickr"
                    placeholder="{{ __('messages.select_date_range') }}" />
            </div>
            @if (!auth()->user()->hasAnyRole(['user']))
            <div class="form-group datatable-filter">
                <label class="form-label" for="customer_id">{{ __('messages.customer_label') }}</label>
                <select name="customer_name[]" id="customer_id" class="form-select select2" data-filter="select" multiple
                    data-ajax--url="{{ route('ajax-list', ['type' => 'user']) }}" data-ajax--cache="true"
                    data-placeholder="{{ __('messages.all_customers') }}">
                    <!-- <option value="">{{ __('messages.all_customers') }}</option> -->

                </select>
            </div>
            @endif
            @if (!auth()->user()->hasAnyRole(['provider','handyman']))
            <div class="form-group datatable-filter">
                <label class="form-label" for="provider_id">{{ __('messages.provider_label') }}</label>
                <select name="provider_name[]" id="provider_id" class="form-select select2" data-filter="select" multiple
                    data-ajax--url="{{ route('ajax-list', ['type' => 'provider']) }}" data-ajax--cache="true"
                    data-placeholder="{{ __('messages.all_providers') }}">
                    <!-- <option value="">{{ __('messages.all_providers') }}</option> -->

                </select>
            </div>
            @endif
            @if (!auth()->user()->hasAnyRole(['handyman']))
            <div class="form-group datatable-filter">
                <label class="form-label" for="handyman_name">{{ __('messages.handyman_label') }}</label>
                <select name="handyman_name[]" id="handyman_id" class="form-select select2" data-filter="select" multiple
                data-ajax--url="{{ route('ajax-list', ['type' => 'handyman', 'provider_id' => auth()->user()->hasRole('provider') ? auth()->user()->id : null]) }}"
             data-ajax--cache="true"
             data-placeholder="{{ __('messages.all_handymen') }}">
                    <!-- <option value="">{{ __('messages.all_handymen') }}</option> -->

                </select>
            </div>
            @endif
            <div class="form-group datatable-filter">
                <label class="form-label" for="bookingStatus">{{ __('messages.booking_status_label') }}</label>
                <div class="btn-group d-flex flex-wrap gap-3">
                    @foreach ($advanceFilter['bookingStatus'] as $value => $label)
                        <button type="button" class="btn filter-button" data-filter="booking_status"
                            data-value="{{ $value }}" data-multiple="true">
                            {{ formatString($label) }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="form-group datatable-filter">
                <label class="form-label" for="payment_status">{{ __('messages.payment_status_label') }}</label>
                <div class="btn-group d-flex flex-wrap gap-3">

                @foreach ($advanceFilter['paymentStatus'] as $status)
    {{-- Convert to match DB --}}
    <button type="button" class="btn filter-button"
        data-filter="payment_status"
        data-value="{{ strtolower($status) }}" 
        data-multiple="true">
        {{ __( 'messages.' . str_replace(' ', '_', $status) ) }}
    </button>
@endforeach
                </div>
            </div>

            <div class="form-group datatable-filter">
                <label class="form-label" for="payment_type">{{ __('messages.payment_type_label') }}</label>
                <div class="btn-group d-flex flex-wrap gap-3">
                    @foreach ($advanceFilter['paymentType'] as $type => $title)
                        <button type="button" class="btn filter-button" data-filter="payment_type"
                            data-value="{{ $type }}" data-multiple="true">
                            {{ formatString($type) }}
                        </button>
                    @endforeach
                </div>
            </div>



        </x-advance-filter>


        <div class="modal fade  modal-lg" id="breakdownModal" tabindex="-1" aria-labelledby="breakdownModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header p-0">
                        <button type="button" class="btn-close custom-close" data-bs-dismiss="modal"
                            aria-label="Close"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="d-flex justify-content-between p-4">
                        <div>
                            <h5 class="modal-title" id="breakdownModalLabel">{{ __('messages.payment_breakdown') }}</h5>
                        </div>
                        <div>
                            <h5> <span class="text-secondary">{{ __('messages.total_amount') }}</span>
                                <span class="text-primary" id="total_without_discount"></span>
                                <del class="text-secondary" id="total_with_discount"></del>
                            </h5>

                        </div>
                    </div>


                    <div class="modal-body">
                        <div class="progress-breakdown">
                            <div class="progress">
                                <div class="progress-bar admin" role="progressbar" style="background:var(--bs-pink); width: 0%"
                                    aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                <div class="progress-bar provider bg-warning" role="progressbar" style="width: 0%"
                                    aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                <div class="progress-bar handyman bg-info" role="progressbar" style="width: 0%"
                                    aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                <div class="progress-bar tax bg-danger" role="progressbar" style="width: 0%"
                                    aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                <div class="progress-bar discount bg-success" role="progressbar" style="width: 0%"
                                    aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <ul class="list-group list-group-flush mt-4" id="earningList">
                                <!-- Data will be inserted here dynamically -->
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="Export" tabindex="-1" role="dialog" aria-labelledby="exportModalTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="exportModalTitle">{{__('messages.export_data')}}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"></span>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">
                        <!-- File Type Options -->
                        <div class="form-group">
                            <label>{{__('messages.select_file_type')}}</label>
                            <div class="btn-group btn-group-toggle d-flex flex-wrap export-type" data-toggle="buttons">
                                <label class="btn btn-outline-primary active">
                                    <input type="radio" name="fileType" value="xlsx" /> XLSX
                                </label>
                                <label class="btn btn-outline-primary">
                                    <input type="radio" name="fileType" value="xls" /> XLS
                                </label>
                                <label class="btn btn-outline-primary">
                                    <input type="radio" name="fileType" value="ods" /> ODS
                                </label>
                                <label class="btn btn-outline-primary">
                                    <input type="radio" name="fileType" value="csv" /> CSV
                                </label>
                                <label class="btn btn-outline-primary">
                                    <input type="radio" name="fileType" value="pdf" checked /> PDF
                                </label>
                                <label class="btn btn-outline-primary">
                                    <input type="radio" name="fileType" value="html" /> HTML
                                </label>
                            </div>
                        </div>
                        <!-- Column Selection -->
                        <div class="form-group">
                            <label>{{__('messages.select_column')}}</label>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="colID" checked />
                                <label class="form-check-label" for="colID">{{ __('messages.id') }}</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="colPatientName" checked />
                                <label class="form-check-label" for="colPatientName">{{ __('messages.service') }}</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="colStartDateTime" checked />
                                <label class="form-check-label" for="colStartDateTime">{{ __('messages.booking_date') }}</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="colServices" checked />
                                <label class="form-check-label" for="colServices">{{ __('messages.user') }}</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="colPrice" checked />
                                <label class="form-check-label" for="colPrice">{{ __('messages.provider') }}</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="colStatus" checked />
                                <label class="form-check-label" for="colStatus">{{ __('messages.status') }}</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="colDoctor" checked />
                                <label class="form-check-label" for="colDoctor">{{ __('messages.total_amount') }}</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="colPaymentStatus" checked />
                                <label class="form-check-label" for="colPaymentStatus">{{ __('messages.payment_status') }}</label>
                            </div>
                        </div>
                    </div>


                    <!-- Modal Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                        <button type="button" class="btn btn-primary" id="downloadButton">
                            <span class="spinner-border spinner-border-sm d-none" role="status"
                                aria-hidden="true"></span>
                            <span class="button-text">{{ __('messages.export') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- <script src="{{ asset('js/bootstrap.bundle.js') }}"></script> -->
    <script>
        let selectedFilters = {
            booking_status: [],
            payment_status: [],
            payment_type: [],
            advance_paid: [],
            date_range: ''
        };
        document.addEventListener('DOMContentLoaded', (event) => {

            window.renderedDataTable = $('#datatable').DataTable({
                // processing: true,
                serverSide: true,
                autoWidth: false,
                responsive: true,
                dom: '<"row align-items-center"><"table-responsive my-3 mt-3 mb-2 pb-1" rt><"row align-items-center data_table_widgets" <"col-md-6" <"d-flex align-items-center flex-wrap gap-3" l i>><"col-md-6" p>><"clear">',
                ajax: {
                    "type": "GET",
                    "url": '{{ route('booking.index_data') }}',
                    "data": function(d) {
                        const urlParams = new URLSearchParams(window.location.search);
                        const providerIdFromUrl = urlParams.get('provider_id');
                        d.provider_id = providerIdFromUrl; 
                        d.search = {
                            value: $('.dt-search').val()
                        };
                        d.filter = {
                            column_status: $('#column_status').val()
                        }
                        d.advanceFilter = {
                            customer_id: $('#customer_id').val() ? $('#customer_id').val() : [],
                            service_id: $('#service_id').val() ? $('#service_id').val() : [],
                            provider_id: $('#provider_id').val() ? $('#provider_id').val() : [],
                            handyman_id: $('#handyman_id').val() ? $('#handyman_id').val() : [],
                            date_range: selectedFilters.date_range,
                            booking_status: selectedFilters.booking_status,
                            payment_status: selectedFilters.payment_status,
                            payment_type: selectedFilters.payment_type,
                            advance_paid: selectedFilters.advance_paid
                        }
                    }
                },
                columns: [
                    // @if (auth()->user()->hasAnyRole(['admin']))
                    //     {
                    //         name: 'check',
                    //         data: 'check',
                    //         title: '<input type="checkbox" class="form-check-input" name="select_all_table" id="select-all-table" data-type="booking" onclick="selectAllTable(this)">',
                    //         exportable: false,
                    //         orderable: false,
                    //         searchable: false,
                    //     },
                    // @endif


                     {
                        data: 'updated_at',
                        name: 'updated_at',
                        title: "{{ __('product.lbl_update_at') }}",
                        orderable: true,
                        visible: false,
                    },
                    {
                        data: 'id',
                        name: 'id',
                        title: "{{ __('messages.id') }}"
                    },
                    {
                        data: 'service_id',
                        name: 'service_id',
                        title: "{{ __('messages.service') }}"
                    },
                    {
                        data: 'date',
                        name: 'date',
                        title: "{{ __('messages.booking_date') }}"
                    },
                    {
                        data: 'customer_id',
                        name: 'customer_id',
                        title: "{{ __('messages.user') }}"
                    },
                    @if (!auth()->user()->hasAnyRole(['provider']))
                        {
                            data: 'provider_id',
                            name: 'provider_id',
                            title: "{{ __('messages.provider') }}"
                        },
                    @endif {
                        data: 'status',
                        name: 'status',
                        title: "{{ __('messages.status') }}"
                    },
                    {
                        data: 'total_amount',
                        name: 'total_amount',
                        title: "{{ __('messages.total_amount') }}"
                    },
                    {
                        data: 'payment_id',
                        name: 'payment_id',
                        title: "{{ __('messages.payment_status') }}"
                    },

                    @if ($auth_user->can('booking delete'))
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            title: "{{ __('messages.action') }}",
                            className: 'text-end',
                        }
                    @endif
                ],
                order: [
                    @if (auth()->user()->hasAnyRole(['admin']))
                        [1, 'desc']
                    @else
                        [1, 'desc']
                    @endif
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

        $(document).on('update_quick_action', function() {})

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

        document.addEventListener('DOMContentLoaded', function() {
            $("#datepicker1").flatpickr({
                mode: "range",
                dateFormat: "Y-m-d",
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length === 2 || selectedDates.length === 1) {
                        selectedFilters.date_range = dateStr;
                        $('#datatable').DataTable().ajax.reload();
                        updateTotalEarnings();
                    }
                }
            });

            // Function to handle toggle of button state and update selected filters
            function toggleMultipleFilter(button, filterType) {
                const value = button.dataset.value;
                const index = selectedFilters[filterType].indexOf(value);

                if (index === -1) {
                    // Add value to array and add active class
                    selectedFilters[filterType].push(value);
                    button.classList.remove('inactive');
                    button.classList.add('active');
                } else {
                    // Remove value from array and remove active class
                    selectedFilters[filterType].splice(index, 1);
                    button.classList.remove('active');
                    button.classList.add('inactive');
                }

                // Refresh the table and update earnings
                $('#datatable').DataTable().ajax.reload();
                updateTotalEarnings();
            }

            // Adding event listeners for filter buttons
            document.querySelectorAll('.filter-button').forEach(button => {
                button.addEventListener('click', function() {
                    const filterType = button.dataset.filter;
                    if (button.dataset.multiple === 'true') {
                        toggleMultipleFilter(button, filterType);
                    }
                });
            });


            const checkboxes = document.querySelectorAll('.form-check-input');
            const downloadButton = document.getElementById('downloadButton');

            function toggleExportButton() {
                const isChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);
                downloadButton.disabled = !isChecked; // Disable if no checkbox is checked
            }

            // Add event listener to each checkbox
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', toggleExportButton);
            });

            // Initial check on page load
            toggleExportButton();


            $('#breakdownModal').on('show.bs.modal', function() {
                // Get all current filter values
                let filterData = {
                    customer_id: $('#customer_id').val() ? $('#customer_id').val() : [],
                    service_id: $('#service_id').val() ? $('#service_id').val() : [], // Multiple service IDs
                    provider_id: $('#provider_id').val() ? $('#provider_id').val() : [],
                    handyman_id: $('#handyman_id').val() ? $('#handyman_id').val() : [],
                    date_range: $('#datepicker1').val(),
                    booking_status: selectedFilters.booking_status,
                    payment_status: selectedFilters.payment_status,
                    payment_type: selectedFilters.payment_type
                };

                // Call API with filter data
                $.ajax({
                    url: '{{ route('earning-breakdown') }}',
                    type: 'GET',
                    data: {
                        advanceFilter: filterData
                    },
                    success: function(data) {
                        // Update total earning
                        $('#totalEarning').text(data.totalEarning);

                        // Calculate percentages and update progress bars
                        let earnings = data.earnings;
console.log(data);

                        let totalEarning = 0; // Initialize the variable onc

                        if (data.userRole === 'admin' || data.userRole === 'demo_admin') {
                            totalEarning = parseFlexibleAmount(data.totalEarning);
                        } else if (data.userRole === 'provider') {
                            const adminEarning = parseFlexibleAmount(earnings.admin || '0');
                            totalEarning = parseFlexibleAmount(data.totalEarning) - adminEarning || 0;
                        } else {
                            const adminEarning = parseFlexibleAmount(earnings.admin || '0');
                            const providerEarning = parseFlexibleAmount(earnings.provider || '0');
                            totalEarning = parseFlexibleAmount(data.totalEarning) - adminEarning - providerEarning || 0;
                        }

                        function calculatePercent(amount, total) {
                            return total > 0 ? (amount / total) * 100 : 0;
                        }

                        if (data.userRole === 'admin' || data.userRole === 'demo_admin') {
                            $('.progress-bar.admin').css('width', calculatePercent(parseFlexibleAmount(earnings.admin), totalEarning) + '%');
                            $('.progress-bar.provider').css('width', calculatePercent(parseFlexibleAmount(earnings.provider), totalEarning) + '%');
                            $('.progress-bar.handyman').css('width', calculatePercent(parseFlexibleAmount(earnings.handyman), totalEarning) + '%');
                            $('.progress-bar.tax').css('width', calculatePercent(parseFlexibleAmount(earnings.tax), totalEarning) + '%');
                            $('.progress-bar.discount').css('width', calculatePercent(parseFlexibleAmount(earnings.discount), totalEarning) + '%');
                        } else if (data.userRole === 'provider') {
                            $('.progress-bar.provider').css('width', calculatePercent(parseFlexibleAmount(earnings.provider), totalEarning) + '%');
                            $('.progress-bar.handyman').css('width', calculatePercent(parseFlexibleAmount(earnings.handyman), totalEarning) + '%');
                            $('.progress-bar.tax').css('width', calculatePercent(parseFlexibleAmount(earnings.tax), totalEarning) + '%');
                            $('.progress-bar.discount').css('width', calculatePercent(parseFlexibleAmount(earnings.discount), totalEarning) + '%');
                        } else {
                            $('.progress-bar.handyman').css('width', calculatePercent(parseFlexibleAmount(earnings.handyman), totalEarning) + '%');
                            $('.progress-bar.tax').css('width', calculatePercent(parseFlexibleAmount(earnings.tax), totalEarning) + '%');
                            $('.progress-bar.discount').css('width', calculatePercent(parseFlexibleAmount(earnings.discount), totalEarning) + '%');
                        }


                        

                        // Update the earnings list
                        let earningListHtml = generateEarningsList(earnings, data.userRole);
                        $('#earningList').html(earningListHtml);

                        // Update totals
                        $('#total_without_discount').text(earnings.totalAmountWithoutDiscount);
                        console.log(parseFlexibleAmount(earnings.discount));
                        if (parseFlexibleAmount(earnings.discount) > 0) {
                            $('#total_with_discount').text(earnings.totalAmountWithDiscount).show();
                        } else {
                            $('#total_with_discount').hide();
                        }
                    },
                    error: function() {
                        console.error('Failed to fetch earning breakdown data.');
                        $('#earningList').html('<div class="alert alert-danger">Failed to load data. Please try again later.</div>');
                    }
                });
            });
            function parseFlexibleAmount(amount) {
                // Remove all currency symbols and other non-numeric characters except ., and ,
                const cleanAmount = amount.replace(/[^0-9.,-]/g, '');

                // Check if the format uses a comma as a decimal separator (e.g., German or French)
                if (cleanAmount.includes(',') && cleanAmount.includes('.')) {
                    // Assume last comma is a decimal separator, remove others
                    const parts = cleanAmount.split(',');
                    const decimals = parts.pop();
                    const number = parts.join('').replace(/\./g, '') + '.' + decimals;
                    return parseFloat(number) || 0;
                } else if (cleanAmount.includes(',')) {
                    // If no periods, assume comma as decimal separator
                    return parseFloat(cleanAmount.replace(/,/g, '.')) || 0;
                } else {
                    // Default: Period as decimal separator
                    return parseFloat(cleanAmount) || 0;
                }
            }
            function generateEarningsList(earnings, userRole) {
                let html = `
                    <li class="list-group-item d-flex justify-content-between py-2 px-0">
                        <span class="fw-bold">{{ __('messages.role_earned') }}</span>
                        <span class="fw-bold">{{ __('messages.amount') }}</span>
                    </li>`;

                if (userRole === 'admin' || userRole === 'demo_admin') {
                    html += `
                    <li class="py-2 d-flex justify-content-between">
                        <span>Admin Earned:</span>
                        <span class="fw-bold" style="color:var(--bs-pink);">${earnings.admin}</span>
                    </li>`;
                }

                if (userRole === 'admin' || userRole === 'demo_admin' || userRole === 'provider') {
                    html += `
                    <li class="py-2 d-flex justify-content-between">
                        <span>Provider Earned:</span>
                        <span class="text-warning">${earnings.provider}</span>
                    </li>`;
                }

                html += `
                    <li class="py-2 d-flex justify-content-between">
                        <span>Handyman Earned:</span>
                        <span class="fw-bold text-info">${earnings.handyman}</span>
                    </li>
                    <li class="py-2 d-flex justify-content-between">
                        <span>Tax Amount:</span>
                        <span class="text-danger">${earnings.tax}</span>
                    </li>
                    <li class="py-2 d-flex justify-content-between">
                        <span>Discount:</span>
                        <span class="text-success">${earnings.discount}</span>
                    </li>`;

                return html;
            }

            // Add reset functionality for Export modal
            function resetExportModal() {
                // Reset file type radio buttons - set PDF as default
                document.querySelectorAll('input[name="fileType"]').forEach(radio => {
                    radio.checked = radio.value === 'pdf';
                });

                // Reset all column checkboxes to checked
                document.querySelectorAll('.form-check-input').forEach(checkbox => {
                    checkbox.checked = true;
                });

                // Reset button state
                const downloadButton = document.getElementById('downloadButton');
                const spinner = document.querySelector('.spinner-border');
                const buttonText = document.querySelector('.button-text');
                
                downloadButton.disabled = false;
                spinner.classList.add('d-none');
                buttonText.textContent = "{{ __('messages.export') }}";
            }

            // Reset modal when clicking the close button or cancel button
            document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(button => {
                button.addEventListener('click', resetExportModal);
            });

            // Reset modal when clicking outside the modal (backdrop click)
            $('#Export').on('hidden.bs.modal', function () {
                resetExportModal();
            });

            // Export button click handler
            document.getElementById('downloadButton').addEventListener('click', function() {
                // Get selected file type
                const fileType = document.querySelector('input[name="fileType"]:checked').value;

                // Get selected columns (checkboxes that are checked)
                const selectedColumns = [];
                document.querySelectorAll('.form-check-input:checked').forEach((checkbox) => {
                    selectedColumns.push(checkbox.id);
                });

                // Create FormData to send to the backend
                const formData = new FormData();
                formData.append('format', fileType);
                formData.append('columns', JSON.stringify(selectedColumns));

                // Disable the button and show the loading spinner
                const buttonText = document.querySelector('.button-text');
                const spinner = document.querySelector('.spinner-border');
                const downloadButton = this;
                downloadButton.disabled = true;
                spinner.classList.remove('d-none');
                buttonText.textContent = "Loading...";

                var baseUrl = $('meta[name="baseUrl"]').attr('content');

                // Fetch request to export data
                fetch(baseUrl+'/export', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: formData,
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json();
                    }
                    return response.blob();
                })
                .then(data => {
                    if (data.error) {
                        Snackbar.show({
                            text: data.error,
                            pos: 'bottom-right',
                            backgroundColor: '#d32f2f',
                            actionTextColor: '#fff'
                        });
                        resetExportModal();
                        return;
                    }

                    // Download the file
                    const url = window.URL.createObjectURL(data);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `Bookings.${fileType}`;
                    a.click();
                    window.URL.revokeObjectURL(url);

                    // Close the modal after successful download
                    $('#Export').modal('hide');
                })
                .catch(error => {
                    console.error('Export error:', error);
                    Snackbar.show({
                        text: 'Failed to export data. Please try again.',
                        pos: 'bottom-right',
                        backgroundColor: '#d32f2f',
                        actionTextColor: '#fff'
                    });
                    resetExportModal();
                });
            });
        });

        function updateFilterCount() {
            let count = 0;
             count += selectedFilters.booking_status.length;
            count += selectedFilters.payment_status.length;
            count += selectedFilters.payment_type.length;
            if (selectedFilters.date_range) {
                count++;
            }
            const serviceIds = $('#service_id').val();
            if (serviceIds && serviceIds.length > 0) {
                count += 1;
            }
            const customerIds = $('#customer_id').val();
            if (customerIds && customerIds.length > 0) {
                count += 1;
            }
            
            const providerIds = $('#provider_id').val();
            if (providerIds && providerIds.length > 0) {
                count += 1;
            }
            
            const handymanIds = $('#handyman_id').val();
            if (handymanIds && handymanIds.length > 0) {
                count += 1;
            }

            const filterCountBadge = document.querySelector('.filter-count');
            if (count > 0) {
                filterCountBadge.style.display = 'block';
                filterCountBadge.textContent = count;
            } else {
                filterCountBadge.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.filter-button').forEach(button => {
                button.addEventListener('click', function() {
                    updateFilterCount();
                });
            });

            $('#service_id, #customer_id, #provider_id, #handyman_id').on('change', function() {
                updateFilterCount();
            });

            $("#datepicker1").on('change', function() {
                updateFilterCount();
            });

            updateFilterCount();
        });

        // Add this after the filter for total earnings
        document.addEventListener('DOMContentLoaded', function() {
            // Function to update total earnings display
            function updateTotalEarnings() {
                // Get all current filter values
                let filterData = {
                    customer_id: $('#customer_id').val(),
                    service_id: $('#service_id').val(),
                    provider_id: $('#provider_id').val(),
                    handyman_id: $('#handyman_id').val(),
                    date_range: $('#datepicker1').val(),
                    booking_status: selectedFilters.booking_status,
                    payment_status: selectedFilters.payment_status,
                    payment_type: selectedFilters.payment_type
                };

                // Call API to get updated earnings
                $.ajax({
                    url: '{{ route('earning-breakdown') }}',
                    type: 'GET',
                    data: {
                        advanceFilter: filterData
                    },
                    success: function(data) {
                        // Update total earnings display
                        $('.total-earnings-display').text(data.totalEarning);
                    },
                    error: function() {
                        console.error('Failed to fetch updated earnings data.');
                    }
                });
            }

            // Add event listeners for all filter changes
            $('.filter-button').on('click', function() {
                updateTotalEarnings();
            });

            $('#customer_id, #service_id, #provider_id, #handyman_id').on('change', function() {
                updateTotalEarnings();
            });

            $("#datepicker1").on('change', function() {
                updateTotalEarnings();
            });


            function getPriceFormat(price) {
                return '$' + parseFloat(price).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            }
            updateTotalEarnings();
        });
        document.addEventListener('DOMContentLoaded', function() {
        // Handle export file type button group (Bootstrap 5 compatibility)
        document.querySelectorAll('.export-type .btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            // Remove 'active' from all buttons
            document.querySelectorAll('.export-type .btn').forEach(function(b) {
                b.classList.remove('active');
            });
            // Add 'active' to the clicked button
            btn.classList.add('active');
            // Set the radio input as checked
            btn.querySelector('input[type="radio"]').checked = true;
        });
    });
});
    </script>


</x-master-layout>
