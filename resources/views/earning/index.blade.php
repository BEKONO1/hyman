<x-master-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card card-block card-stretch">
                            <div class="d-flex card-body">
                                <h5 class="card-title me-1">{{ __('messages.earning') }}</h5>
                                <span class=""> ({{ __('messages.tax_not_included') }})</span>

                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div class="row justify-content-end">
                                    <div class="col-md-3">
                                        <div class="d-flex justify-content-end">

                                            <div class="input-group input-group-search ml-auto">
                                                <span class="input-group-text" id="addon-wrapping"><i
                                                        class="fas fa-search"></i></span>
                                                <input type="text" class="form-control dt-search"
                                                    placeholder="Search..." aria-label="Search"
                                                    aria-describedby="addon-wrapping" aria-controls="dataTableBuilder">
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
                    "url": '{{ route('earningData') }}',
                    "data": function(d) {
                        d.search = {
                            value: $('.dt-search').val()
                        };
                        d.filter = {
                            column_status: $('#column_status').val()
                        };
                    },
                },
                columns: [{
                        data: 'provider_name',
                        name: 'provider_name',
                        title: "{{ __('messages.provider') }}"
                    },
                    {
                        data: 'total_bookings',
                        name: 'total_bookings',
                        title: "{{ __('messages.booking') }}",
                        orderable: false,
                        searchable : false
                    },
                    {
                        data: 'total_earning',
                        name: 'total_earning',
                        title: "{{ __('messages.total_earning') }}",
                        orderable: false,
                        searchable : false
                    },

                    {
                        data: 'admin_earning',
                        name: 'admin_earning',
                        title: "{{ __('messages.admin_earning') }}",
                        orderable: false,
                        searchable : false
                    },
                    {
                        data: 'provider_earning',
                        name: 'provider_earning',
                        title: "{{ __('messages.provider_due_earning') }}",
                        orderable: false,
                        searchable : false
                    },

                    {
                        data: 'provider_paid_earning',
                        name: 'provider_paid_earning',
                        title: "{{ __('messages.provider_paid_earning') }}",
                        orderable: false, // Disable sorting
                        searchable : false
                    },
                    {
                        data: 'handyman_total_earning',
                        name: 'handyman_total_earning',
                        title: "{{ __('messages.hadyman_total_earning') }}",
                        orderable: false, // Disable sorting
                        searchable : false
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
                    [0, 'desc']
                ],
                language: {
                    processing: "{{ __('messages.processing') }}" // Set your custom processing text
                }
            });
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
