<x-master-layout>
    <div class="container-fluid">
    @include('partials._provider')
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3">
                            <h5 class="fw-bold">{{ $pageTitle ?? trans('messages.list') }}</h5>
                        </div>

                    </div>
                </div>
                <div class="card">


                        <div class="card-body">
                        <div class="row justify-content-end">
                                <div class="col-md-3">
                                <div class="d-flex justify-content-end">

                                    <div class="input-group input-group-search ml-auto">
                                        <span class="input-group-text" id="addon-wrapping"><i class="fas fa-search"></i></span>
                                        <input type="text" class="form-control dt-search" placeholder="Search..." aria-label="Search" aria-describedby="addon-wrapping" aria-controls="dataTableBuilder">
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
<script>
    document.addEventListener('DOMContentLoaded', (event) => {

window.renderedDataTable = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        responsive: true,
        dom: '<"row align-items-center"><"table-responsive my-3" rt><"row align-items-center" <"col-md-6" l><"col-md-6" p><"col-md-6" i>><"clear">',
        ajax: {
          "type"   : "GET",
          "url"    : '{{ route("providerpayout.index_data",['id' => $id]) }}',
          "data"   : function( d ) {
            d.search = {
              value: $('.dt-search').val()
            };
            d.filter = {
              column_status: $('#column_status').val()
            }
          },
        },
        columns: [
            // {
            //     name: 'check',
            //     data: 'check',
            //     exportable: false,
            //     title: '<input type="checkbox" class="form-check-input" name="select_all_table" id="select-all-table" onclick="selectAllTable(this)">',
            //     searchable: false,
            //     orderable: false,
            // },
            {
                        data: 'id',
                        name: 'id',
                        title: "{{__('messages.id')}}",
                        orderable: false,
            },
            {
                data: 'provider_id',
                name: 'provider_id',
                title: "{{ __('messages.provider') }}",
                orderable: false,
            },
            {
                data: 'payment_method',
                name: 'payment_method',
                title: "{{ __('messages.payment_method') }}"
            },
            {
                data: 'bank_name',
                name: 'bank_name',
                title: "{{ __('messages.bank_name') }}",
                orderable: false,
            },
            {
                data: 'description',
                name: 'description',
                title: "{{ __('messages.description') }}"
            },
            {
                data: 'created_at',
                name: 'created_at',
                title: "{{ __('messages.paid_date') }}"
            },
            {
                data: 'amount',
                name: 'amount',
                title: "{{ __('messages.amount') }}"
            },
            // {
            //     data: 'action',
            //     name: 'action',
            //     orderable: false,
            //     searchable: false,
            //     title: "{{ __('messages.action') }}",
            //     className: 'text-end'
            // }


        ],
        order: [],
        language: {
          processing: "{{ __('messages.processing') }}" // Set your custom processing text
        }
    });
});
</script>
</x-master-layout>
