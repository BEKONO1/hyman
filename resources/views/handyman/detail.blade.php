<x-master-layout>
    {{ html()->form('DELETE', route('provider.destroy', $handymandata->id))->attribute('data--submit', 'provider' . $handymandata->id)->open() }}
    <main class="main-area">
        <div class="main-content">
            <div class="container-fluid">
                @include('partials._handyman')
                <div class="card">
                <div class="card-body p-30">
                        <div class="provider-details-overview ">                            
                            <div class="provider-details-overview__statistics">
                                <div class="statistics-card statistics-card__style2 statistics-card__pending-withdraw">
                                    <h2>{{ getPriceFormat($handymanData['pendWithdrwan']) ?? 0}}</h2>
                                    <h3>{{__('messages.pending_withdraw')}}</h3>
                                </div>

                            <div class="statistics-card statistics-card__style2 statistics-card__already-withdraw">
                                <h2>{{getPriceFormat($handymanData['handymanAlreadyWithdrawAmt']) ?? 0}}</h2>
                                <h3>{{__('messages.already_withdraw')}}</h3>
                            </div>

                            <div class="statistics-card statistics-card__style2 statistics-card__total-earning">
                                <h2>{{$handymanData['totalbooking'] ?? 0}}</h2>
                                <h3>{{__('messages.total_name', ['name' => __('messages.booking')])}}</h3>
                            </div>

                            <div
                                class="statistics-card statistics-card__style2 statistics-card__withdrawable-amount">
                                <h2>{{getPriceFormat($handymanData['wallet']) ?? 0}}</h2>
                                <h3>{{__('messages.wallet_balance')}}</h3>
                            </div>
                        </div>
                        <div class="provider-details-overview__order-overview">
                            <div class="statistics-card statistics-card__order-overview h-100 pb-2">
                                <h3 class="mb-0 text-center">{{__('messages.booking_overview')}}</h3>
                                @if($data['PendingStatusCount']+$data['InProgressstatuscount']+$data['Completedstatuscount']+$data['Ongoingstatuscount'] > 0)
                                <div id="chart" class="d-flex justify-content-center">

                                </div>
                                @else
                                <p style = "color:#009900; font-size:20px;
                                     font-style:italic; text-align:center; margin-top: 20%;">
                                      {{__('messages.nodata')}}

                                  </p>
                                  @endif
                                <div class="resize-triggers">
                                    <div class="expand-trigger">
                                        <div style="width: 310px; height: 234px"></div>
                                    </div>
                                    <div class="contract-trigger"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="information-details-box d-flex flex-md-row flex-column gap-4">
                                <img class="avatar-img rounded-2 object-fit-cover" src="{{ getSingleMedia($handymandata,'profile_image', null)}}" alt="" />
                                <div class="media-body">
                                    <h2 class="information-details-box__title mb-4">
                                        {{ $handymandata->display_name }}
                                    </h2>
                                    <ul class="contact-list list-inline d-flex gap-3 flex-column">
                                        <li>
                                            <i class="ri-smartphone-line"></i>
                                            <a
                                                href="{{ $handymandata->contact_number }}" class="contact-info-text title-text p-0">{{ !empty($handymandata->contact_number) ? $handymandata->contact_number: '-' }}</a>
                                        </li>
                                        <li>
                                            <i class="ri-mail-line"></i>
                                            <a href="{{ $handymandata->email }}" class="contact-info-text heading-colo p-0">{{ $handymandata->email }}</a>
                                        </li>
                                        <li>
                                            <i class="ri-map-2-line"></i>
                                            <span class="contact-info-text">{{ !empty($handymandata->address) ?$handymandata->address : '-' }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
{{ html()->form()->close() }}
@section('bottom_script')


    <script type="text/javascript">
        var pendingCount = parseInt("{{ $data['PendingStatusCount'] }}");
        var inProgressCount = parseInt("{{ $data['InProgressstatuscount'] }}");
        var Completedcount = parseInt("{{ $data['Completedstatuscount'] }}");
        var onGoingCount = parseInt("{{ $data['Ongoingstatuscount'] }}");

        var options = {
            series: [Completedcount, onGoingCount, pendingCount, inProgressCount],
            chart: {
                width: 380,
                type: 'pie',
            },
            labels: ['Completed', 'On Going','Pending', 'In Progress'],
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };

        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();
    </script>
@endsection
</x-master-layout>