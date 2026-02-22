{{ html()->hidden('id', null)->attribute('placeholder', 'id')->class('form-control') }}
{{ html()->hidden('type', 'hjg')->attribute('placeholder', 'id')->class('form-control') }}
<h5 class="mb-2">{{__('messages.plan')}}</h5>

<div class="row justify-content-end">
    <div class="col-md-3">
        <div class="d-flex justify-content-end">
            <div class="input-group input-group-search ml-auto">
                <span class="input-group-text" id="addon-wrapping"><i class="fas fa-search"></i></span>
                <input type="text" class="form-control dt-search" placeholder="Search..." aria-label="Search" aria-describedby="addon-wrapping">
            </div>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="table data-table mb-0">
        <thead class="table-color-heading">
            <tr class="text-secondary">
                <th scope="col">{{__('messages.id')}}</th>
                <th scope="col">{{ __('messages.provider') }}</th>
                <th scope="col">{{__('messages.plan')}}</th>
                <th scope="col">{{__('messages.type')}}</th>
                <th scope="col">{{__('messages.amount')}}</th>
                <th scope="col">{{__('messages.start_at')}}</th>
                <th scope="col">{{__('messages.end_at')}}</th>
                <th scope="col">{{__('messages.status')}}</th>
                {{-- <th scope="col">{{__('messages.plan_type')}}</th> --}}
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
{{ html()->form()->close() }}
<script type="text/javascript">

    var loadurl = '{{route('provider_detail_pages')}}?tabpage=all-plan&type=tbl&providerid={{request()->providerid}}';

    var table = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        responsive: true,
        dom: '<"row align-items-center"><"table-responsive my-3" rt><"row align-items-center" <"col-md-6" l><"col-md-6" p><"col-md-6" i>><"clear">',
        ajax: {
            url: loadurl,
            type: 'GET',
            data: function(d) {
                // Add custom search parameter
                d.search = {
                    value: $('.dt-search').val()
                };
            },
        },
        columns: [
            {
                data: 'id',
                name: 'id'

            },
            {
                data: 'provider_name', 
                name: 'provider_name',
            },
            {
                data: 'title',
                name: 'title'

            },
            {
                data: 'type',
                name: 'type'
            },
            {
                data: 'amount',
                name: 'amount'
            },
            {
                data: 'start_at',
                name: 'start_at'
            },
            {
                data: 'end_at',
                name: 'end_at'
            },
            {
                data: 'status',
                name: 'status',
                render: function(data, type, row) {
                    return data.charAt(0).toUpperCase() + data.slice(1);
                }
            },
            // {
            //     data: 'plan_type',
            //     name: 'plan_type'
            // },
        ],
        language: {
            processing: "{{ __('messages.processing') }}" // Set your custom processing text
        }
    });

    // Trigger search when user types in the input field
    $('.dt-search').on('keyup', function () {
        table.draw();
    });
</script>