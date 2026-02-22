{{ html()->hidden('id', null)->class('form-control')->placeholder('id') }}
{{ html()->hidden('type', 'hjg')->class('form-control')->placeholder('id') }}
<h5 class="mb-2">{{__('messages.plan')}}</h5>
<div class="table-responsive">
    <table class="table data-table mb-0">
        <thead class="table-color-heading">
            <tr class="text-secondary">
                <th scope="col">{{__('messages.planname')}}</th>
                <th scope="col">{{__('messages.planType')}}</th>
                <th scope="col">{{__('messages.plan_type')}}</th>
                
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
{{ html()->form()->close() }}
<script type="text/javascript">

var loadurl = '{{route('provider_detail_pages')}}?tabpage=subscribe-plan&type=tbl&providerid={{ request()->providerid }}';

    var table = $('.data-table').DataTable({
        processing: true,
          serverSide: true,

        ajax: {
            url: loadurl,
            type: 'GET'
        },
        columns: [
            {
                data: 'title',
                name: 'title'

            },
            {
                data: 'type',
                name: 'type'
            },
            
            {
                data: 'plan_type',
                name: 'plan_type'
            },
        ],
        language: {
          processing: "{{ __('messages.processing') }}" // Set your custom processing text
        }
    });
</script>