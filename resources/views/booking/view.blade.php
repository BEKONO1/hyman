<x-master-layout>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0 p-4">{{__('messages.booking_info')}}</h3>
                <ul class="nav nav-tabs pay-tabs tabslink payment-view-tabs mb-0" id="tab-text" role="tablist">
                    <li class="nav-item">
                        <a href="{{ route('booking.index') }}" 
                           class="nav-link active btn btn-sm btn-primary" 
                           style="min-width: 150px; text-align: center;">
                            <i class="fa fa-angle-double-left"></i> {{ __('messages.back') }}
                        </a>
                    </li>
                    <li class="nav-item payment-link">
                        <a href="javascript:void(0)" 
                           data-href="{{ route('booking_layout_page', $bookingdata->id) }}?tabpage=status" 
                           data-bs-toggle="modal" 
                           data-target="#breakdownModal"
                           class="nav-link active btn btn-sm btn-primary" 
                           style="min-width: 150px; text-align: center;">
                            {{ __('messages.view_status') }}
                        </a>
                    </li>
                    
                </ul>
            </div>
        </div>
            <!-- <div class="card">
                <div class="card-body"> -->
                    <div class="tab-content" id="pills-tabContent-1">
                        <div class="tab-pane active">
                            <div class="payment_paste_here"></div>
                        </div>
                    </div>
                <!-- </div>
            </div> -->
    </div>

    <div class="modal fade modal-lg" id="breakdownModal" tabindex="-1" aria-labelledby="breakdownModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    {{-- <div class="modal-header">
                        <h2 class="modal-title" id="breakdownModalLabel">{{__('messages.booking_status')}}</h2>
                        <!-- <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button> -->
                    </div> --}}
                    <div class="modal-body">
                        <div class="status-content">
                            <!-- Status data will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
    </div>
</div>
    @section('bottom_script')
    <!-- <script src="{{ asset('js/bootstrap.bundle.js') }}"></script> -->
    <script>
        $(document).ready(function(event) {
            var $this = $('.payment-link').find('a.active');
            loadurl = "{{route('booking_layout_page',$bookingdata->id)}}?tabpage={{$tabpage}}";
            targ = '.payment_paste_here';
            
            id = this.id || '';

            $.post(loadurl, {
                '_token': $('meta[name=csrf-token]').attr('content')
            }, function(data) {
                $(targ).html(data);
            });

            $this.tab('show');
        });
         $('.payment_paste_here').on('change','.booking-Status',function(){
            $.post("{{ route('bookingStatus.update') }}", {
                '_token': $('meta[name=csrf-token]').attr('content'), 
                bookingId:"{{ request()->booking }}",
                status: $(this).val(),
                type: $(this).attr("type"),
            }, function(data) {
             window.location.reload();
            });
        });
        $(document).ready(function() {
        // Load status data when modal is opened
        $('body').on('click', '.payment-link a', function (e) {
            e.preventDefault();
            var loadurl = $(this).data('href');

            $.post(loadurl, {
                '_token': $('meta[name=csrf-token]').attr('content')
            }, function(data) {
                $('.status-content').html(data);
                $('#breakdownModal').modal('show'); // Manually trigger modal
            });
        });

        // Handle booking status changes
        $('.status-content').on('change', '.booking-Status', function(){
            $.post("{{ route('bookingStatus.update') }}", {
                '_token': $('meta[name=csrf-token]').attr('content'), 
                bookingId: "{{ request()->booking }}",
                status: $(this).val(),
                type: $(this).attr("type"),
            }, function(data) {
                window.location.reload();
            });
        });
    });

    </script>
    @endsection
</x-master-layout>