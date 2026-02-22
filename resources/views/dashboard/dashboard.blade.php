
<x-master-layout>
    <div class="container-fluid">
        <div class="row">
            <!-- test-->
            @if($rezorpayX_details ==null)
            <div class="col-md-12">
                <div class="alert alert-warning border border-warning py-3">
                    <p class="h5 text-warning">
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <i class="fas fa-info-circle"></i>
                            {{__('messages.info_message')}}
                            <a href="{{ route('setting.index') }}" target="_blank" class="text-primary"> {{__('messages.here_is_the_link')}}<i class="fas fa-external-link-alt mx-2"></i></a>
                        </div>
                    </p>
                </div>
            </div>
            @endif

            <div class="col-md-12">
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <a href="{{ route('service.index') }}">
                            <div class="card total-booking-card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <div class="d-flex flex-wrap justify-content-start align-items-center">
                                                <h4 class="mb-2 booking-text  fw-bold">{{ !empty($data['dashboard']['count_total_service']) ? $data['dashboard']['count_total_service']: 0 }} </h4>
                                                <!-- <h4 class="mb-2 booking-text  font-weight-bold text-break"> 000000000000 </h4> -->
                                            </div>
                                            <p class="mb-0 booking-text">{{ __('messages.total_name', ['name' => __('messages.service')]) }}</p>
                                        </div>
                                        <div class="col-auto d-flex align-items-center flex-column">
                                            <div class="iq-card-icon iq-card-icon-booking icon-shape  rounded-circle shadow">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M8.52083 14.7917L6.5625 12.8542C6.4375 12.7292 6.375 12.5799 6.375 12.4062C6.375 12.2326 6.4375 12.0764 6.5625 11.9375C6.67361 11.8264 6.82292 11.7708 7.01042 11.7708C7.19792 11.7708 7.35417 11.8264 7.47917 11.9375L9.02083 13.4583L12.3958 10.0625C12.5347 9.93749 12.691 9.87846 12.8646 9.88541C13.0382 9.89235 13.1875 9.95832 13.3125 10.0833C13.4236 10.2222 13.4826 10.3785 13.4896 10.5521C13.4965 10.7257 13.4375 10.875 13.3125 11L9.52083 14.7917C9.38195 14.9305 9.21528 15 9.02083 15C8.82639 15 8.65972 14.9305 8.52083 14.7917ZM3.79167 18.4583C3.41667 18.4583 3.08681 18.316 2.80208 18.0312C2.51736 17.7465 2.375 17.4167 2.375 17.0417V4.20832C2.375 3.81943 2.51736 3.4861 2.80208 3.20832C3.08681 2.93055 3.41667 2.79166 3.79167 2.79166H5.10417V2.24999C5.10417 2.05555 5.17361 1.88888 5.3125 1.74999C5.45139 1.6111 5.61806 1.54166 5.8125 1.54166C6.02083 1.54166 6.19444 1.6111 6.33333 1.74999C6.47222 1.88888 6.54167 2.05555 6.54167 2.24999V2.79166H13.4583V2.24999C13.4583 2.05555 13.5278 1.88888 13.6667 1.74999C13.8056 1.6111 13.9722 1.54166 14.1667 1.54166C14.375 1.54166 14.5486 1.6111 14.6875 1.74999C14.8264 1.88888 14.8958 2.05555 14.8958 2.24999V2.79166H16.2083C16.5972 2.79166 16.9306 2.93055 17.2083 3.20832C17.4861 3.4861 17.625 3.81943 17.625 4.20832V17.0417C17.625 17.4167 17.4861 17.7465 17.2083 18.0312C16.9306 18.316 16.5972 18.4583 16.2083 18.4583H3.79167ZM3.79167 17.0417H16.2083V8.12499H3.79167V17.0417ZM3.79167 6.87499H16.2083V4.20832H3.79167V6.87499ZM3.79167 6.87499V4.20832V6.87499Z" fill="white"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6">

                            <div class="card total-service-card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <div class="d-flex flex-wrap justify-content-start align-items-center">
                                                <h4 class="mb-2 booking-text fw-bold">{{ !empty($data['total_tax']) ? getPriceFormat($data['total_tax']) : getPriceFormat(0) }}</h4>
                                            </div>
                                            <p class="mb-0 booking-text">{{ __('messages.total_name', ['name' => __('messages.Tax')]) }}</p>
                                        </div>
                                        <div class="col-auto d-flex flex-column">
                                            <div class="iq-card-icon iq-card-icon-revenue icon-shape text-white rounded-circle shadow">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M9.37508 15.9792H10.5626V14.9167C11.4098 14.8194 12.0695 14.559 12.5417 14.1354C13.014 13.7118 13.2501 13.1389 13.2501 12.4167C13.2501 11.7083 13.0487 11.1285 12.6459 10.6771C12.2431 10.2257 11.5556 9.7986 10.5834 9.39582C9.79175 9.06249 9.21536 8.76388 8.85425 8.49999C8.49314 8.2361 8.31258 7.88193 8.31258 7.43749C8.31258 7.02082 8.46536 6.69096 8.77091 6.44791C9.07647 6.20485 9.49314 6.08332 10.0209 6.08332C10.4376 6.08332 10.7987 6.18055 11.1042 6.37499C11.4098 6.56943 11.6667 6.86805 11.8751 7.27082L12.9376 6.77082C12.6876 6.25693 12.3716 5.8611 11.9897 5.58332C11.6077 5.30555 11.1459 5.13888 10.6042 5.08332V4.04166H9.41675V5.08332C8.69453 5.18055 8.13203 5.44443 7.72925 5.87499C7.32647 6.30555 7.12508 6.83332 7.12508 7.45832C7.12508 8.15277 7.33689 8.70485 7.7605 9.11457C8.18411 9.52429 8.82647 9.90277 9.68758 10.25C10.5904 10.625 11.2119 10.9687 11.5522 11.2812C11.8924 11.5937 12.0626 11.9722 12.0626 12.4167C12.0626 12.8611 11.882 13.2153 11.5209 13.4792C11.1598 13.743 10.7015 13.875 10.1459 13.875C9.60425 13.875 9.12508 13.7222 8.70841 13.4167C8.29175 13.1111 8.00008 12.6875 7.83341 12.1458L6.70842 12.5208C7.00008 13.1736 7.36467 13.6875 7.80217 14.0625C8.23966 14.4375 8.76397 14.7083 9.37508 14.875V15.9792ZM10.0001 18.4583C8.8473 18.4583 7.75355 18.2361 6.71883 17.7917C5.68411 17.3472 4.7848 16.743 4.02091 15.9792C3.25703 15.2153 2.65286 14.3194 2.20841 13.2917C1.76397 12.2639 1.54175 11.1667 1.54175 9.99999C1.54175 8.83332 1.76397 7.73263 2.20841 6.69791C2.65286 5.66318 3.25703 4.76735 4.02091 4.01041C4.7848 3.25346 5.68064 2.65277 6.70842 2.20832C7.73619 1.76388 8.83341 1.54166 10.0001 1.54166C11.1667 1.54166 12.2674 1.76388 13.3022 2.20832C14.3369 2.65277 15.2327 3.25346 15.9897 4.01041C16.7466 4.76735 17.3473 5.65971 17.7917 6.68749C18.2362 7.71527 18.4584 8.81943 18.4584 9.99999C18.4584 11.1667 18.2362 12.2639 17.7917 13.2917C17.3473 14.3194 16.7466 15.2153 15.9897 15.9792C15.2327 16.743 14.3404 17.3472 13.3126 17.7917C12.2848 18.2361 11.1806 18.4583 10.0001 18.4583ZM10.0001 17.0417C11.9584 17.0417 13.6216 16.3542 14.9897 14.9792C16.3577 13.6042 17.0417 11.9444 17.0417 9.99999C17.0417 8.04166 16.3577 6.37846 14.9897 5.01041C13.6216 3.64235 11.9584 2.95832 10.0001 2.95832C8.05564 2.95832 6.39591 3.64235 5.02091 5.01041C3.64591 6.37846 2.95841 8.04166 2.95841 9.99999C2.95841 11.9444 3.64591 13.6042 5.02091 14.9792C6.39591 16.3542 8.05564 17.0417 10.0001 17.0417Z" fill="white"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                    </div>

                    <div class="col-lg-3 col-md-6">
                        <a  href="{{ route('earning') }}">
                            <div class="card total-revenue">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <div class="d-flex flex-wrap justify-content-start align-items-center">
                                                <h4 class="mb-2 booking-text fw-bold">{{ getPriceFormat($data['total_earning']) }}</h4>
                                                <p class="mb-0 ml-3 text-danger fw-bold"></p>
                                            </div>
                                            <p class="mb-0 booking-text">{{ __('messages.my_earning') }}</p>
                                        </div>
                                        <div class="col-auto d-flex flex-column">
                                            <div class="iq-card-icon iq-card-icon-revenue icon-shape text-white rounded-circle shadow">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M9.37508 15.9792H10.5626V14.9167C11.4098 14.8194 12.0695 14.559 12.5417 14.1354C13.014 13.7118 13.2501 13.1389 13.2501 12.4167C13.2501 11.7083 13.0487 11.1285 12.6459 10.6771C12.2431 10.2257 11.5556 9.7986 10.5834 9.39582C9.79175 9.06249 9.21536 8.76388 8.85425 8.49999C8.49314 8.2361 8.31258 7.88193 8.31258 7.43749C8.31258 7.02082 8.46536 6.69096 8.77091 6.44791C9.07647 6.20485 9.49314 6.08332 10.0209 6.08332C10.4376 6.08332 10.7987 6.18055 11.1042 6.37499C11.4098 6.56943 11.6667 6.86805 11.8751 7.27082L12.9376 6.77082C12.6876 6.25693 12.3716 5.8611 11.9897 5.58332C11.6077 5.30555 11.1459 5.13888 10.6042 5.08332V4.04166H9.41675V5.08332C8.69453 5.18055 8.13203 5.44443 7.72925 5.87499C7.32647 6.30555 7.12508 6.83332 7.12508 7.45832C7.12508 8.15277 7.33689 8.70485 7.7605 9.11457C8.18411 9.52429 8.82647 9.90277 9.68758 10.25C10.5904 10.625 11.2119 10.9687 11.5522 11.2812C11.8924 11.5937 12.0626 11.9722 12.0626 12.4167C12.0626 12.8611 11.882 13.2153 11.5209 13.4792C11.1598 13.743 10.7015 13.875 10.1459 13.875C9.60425 13.875 9.12508 13.7222 8.70841 13.4167C8.29175 13.1111 8.00008 12.6875 7.83341 12.1458L6.70842 12.5208C7.00008 13.1736 7.36467 13.6875 7.80217 14.0625C8.23966 14.4375 8.76397 14.7083 9.37508 14.875V15.9792ZM10.0001 18.4583C8.8473 18.4583 7.75355 18.2361 6.71883 17.7917C5.68411 17.3472 4.7848 16.743 4.02091 15.9792C3.25703 15.2153 2.65286 14.3194 2.20841 13.2917C1.76397 12.2639 1.54175 11.1667 1.54175 9.99999C1.54175 8.83332 1.76397 7.73263 2.20841 6.69791C2.65286 5.66318 3.25703 4.76735 4.02091 4.01041C4.7848 3.25346 5.68064 2.65277 6.70842 2.20832C7.73619 1.76388 8.83341 1.54166 10.0001 1.54166C11.1667 1.54166 12.2674 1.76388 13.3022 2.20832C14.3369 2.65277 15.2327 3.25346 15.9897 4.01041C16.7466 4.76735 17.3473 5.65971 17.7917 6.68749C18.2362 7.71527 18.4584 8.81943 18.4584 9.99999C18.4584 11.1667 18.2362 12.2639 17.7917 13.2917C17.3473 14.3194 16.7466 15.2153 15.9897 15.9792C15.2327 16.743 14.3404 17.3472 13.3126 17.7917C12.2848 18.2361 11.1806 18.4583 10.0001 18.4583ZM10.0001 17.0417C11.9584 17.0417 13.6216 16.3542 14.9897 14.9792C16.3577 13.6042 17.0417 11.9444 17.0417 9.99999C17.0417 8.04166 16.3577 6.37846 14.9897 5.01041C13.6216 3.64235 11.9584 2.95832 10.0001 2.95832C8.05564 2.95832 6.39591 3.64235 5.02091 5.01041C3.64591 6.37846 2.95841 8.04166 2.95841 9.99999C2.95841 11.9444 3.64591 13.6042 5.02091 14.9792C6.39591 16.3542 8.05564 17.0417 10.0001 17.0417Z" fill="white"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <a  href="{{ route('earning') }}">
                            <div class="card total-revenue">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <div class="d-flex flex-wrap justify-content-start align-items-center">
                                                <h4 class="mb-2 booking-text fw-bold">{{ getPriceFormat($data['total_revenue']) }}</h4>
                                                <p class="mb-0 ml-3 text-danger fw-bold"></p>
                                            </div>
                                            <p class="mb-0 booking-text">{{ __('messages.total_name', ['name' => __('messages.revenue')]) }}</p>
                                        </div>
                                        <div class="col-auto d-flex flex-column">
                                            <div class="iq-card-icon iq-card-icon-revenue icon-shape text-white rounded-circle shadow">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M9.37508 15.9792H10.5626V14.9167C11.4098 14.8194 12.0695 14.559 12.5417 14.1354C13.014 13.7118 13.2501 13.1389 13.2501 12.4167C13.2501 11.7083 13.0487 11.1285 12.6459 10.6771C12.2431 10.2257 11.5556 9.7986 10.5834 9.39582C9.79175 9.06249 9.21536 8.76388 8.85425 8.49999C8.49314 8.2361 8.31258 7.88193 8.31258 7.43749C8.31258 7.02082 8.46536 6.69096 8.77091 6.44791C9.07647 6.20485 9.49314 6.08332 10.0209 6.08332C10.4376 6.08332 10.7987 6.18055 11.1042 6.37499C11.4098 6.56943 11.6667 6.86805 11.8751 7.27082L12.9376 6.77082C12.6876 6.25693 12.3716 5.8611 11.9897 5.58332C11.6077 5.30555 11.1459 5.13888 10.6042 5.08332V4.04166H9.41675V5.08332C8.69453 5.18055 8.13203 5.44443 7.72925 5.87499C7.32647 6.30555 7.12508 6.83332 7.12508 7.45832C7.12508 8.15277 7.33689 8.70485 7.7605 9.11457C8.18411 9.52429 8.82647 9.90277 9.68758 10.25C10.5904 10.625 11.2119 10.9687 11.5522 11.2812C11.8924 11.5937 12.0626 11.9722 12.0626 12.4167C12.0626 12.8611 11.882 13.2153 11.5209 13.4792C11.1598 13.743 10.7015 13.875 10.1459 13.875C9.60425 13.875 9.12508 13.7222 8.70841 13.4167C8.29175 13.1111 8.00008 12.6875 7.83341 12.1458L6.70842 12.5208C7.00008 13.1736 7.36467 13.6875 7.80217 14.0625C8.23966 14.4375 8.76397 14.7083 9.37508 14.875V15.9792ZM10.0001 18.4583C8.8473 18.4583 7.75355 18.2361 6.71883 17.7917C5.68411 17.3472 4.7848 16.743 4.02091 15.9792C3.25703 15.2153 2.65286 14.3194 2.20841 13.2917C1.76397 12.2639 1.54175 11.1667 1.54175 9.99999C1.54175 8.83332 1.76397 7.73263 2.20841 6.69791C2.65286 5.66318 3.25703 4.76735 4.02091 4.01041C4.7848 3.25346 5.68064 2.65277 6.70842 2.20832C7.73619 1.76388 8.83341 1.54166 10.0001 1.54166C11.1667 1.54166 12.2674 1.76388 13.3022 2.20832C14.3369 2.65277 15.2327 3.25346 15.9897 4.01041C16.7466 4.76735 17.3473 5.65971 17.7917 6.68749C18.2362 7.71527 18.4584 8.81943 18.4584 9.99999C18.4584 11.1667 18.2362 12.2639 17.7917 13.2917C17.3473 14.3194 16.7466 15.2153 15.9897 15.9792C15.2327 16.743 14.3404 17.3472 13.3126 17.7917C12.2848 18.2361 11.1806 18.4583 10.0001 18.4583ZM10.0001 17.0417C11.9584 17.0417 13.6216 16.3542 14.9897 14.9792C16.3577 13.6042 17.0417 11.9444 17.0417 9.99999C17.0417 8.04166 16.3577 6.37846 14.9897 5.01041C13.6216 3.64235 11.9584 2.95832 10.0001 2.95832C8.05564 2.95832 6.39591 3.64235 5.02091 5.01041C3.64591 6.37846 2.95841 8.04166 2.95841 9.99999C2.95841 11.9444 3.64591 13.6042 5.02091 14.9792C6.39591 16.3542 8.05564 17.0417 10.0001 17.0417Z" fill="white"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                </div>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <h4 class="">{{__('messages.monthly_revenue')}}</h4>
                        </div>
                        <div id="monthly-revenue" class="custom-chart"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-sm-6">
                <div class="card top-providers">
                    <div class="card-header d-flex justify-content-between gap-10">
                        <h4 class="fw-bold">{{ __('messages.recent_provider') }} ({{$data['dashboard']['count_total_provider']}})</h4>
                            <a href="{{ route('provider.index') }}" class="btn-link btn-link-hover"><u>{{__('messages.view_all')}} </u></a>
                    </div>
                    <div class="card-body p-0">
                        <ul class="common-list list-unstyled">
                            @foreach($data['dashboard']['new_provider'] as $provider)
                            <li style="pointer-events:none;">
                                <div class="media gap-3">
                                    <div class="h-avatar is-medium h-5">
                                        <img class="avatar-50 rounded-circle bg-light" alt="user-icon" src="{{ getSingleMedia($provider,'profile_image', null) }}">
                                    </div>

                                    <div class="media-body ">
                                        <h5 class="mb-1"><span class="fw-bold">{{!empty($provider->display_name) ? $provider->display_name : '-'}}</span> </h5>
                                            <span class="common-list_rating d-flex gap-1">
                                                <i class="ri-star-s-fill"></i>
                                                {{round($provider->getServiceRating->avg('rating'), 1)}}
                                            </span>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-sm-6">
                <div class="card top-providers">
                    <div class="card-header d-flex justify-content-between gap-10">
                        <h4 class="fw-bold">{{ __('messages.recent_customer') }}</h4>
                        <a href="{{ route('user.index') }}" class="btn-link btn-link-hover"><u>{{__('messages.view_all')}}</u></a>
                    </div>
                    <div class="card-body p-0">
                        <ul class="common-list list-unstyled">
                            @foreach($data['dashboard']['new_customer'] as $customer)
                            <li style="pointer-events:none;">
                                <div class="media gap-3">
                                    <div class="h-avatar is-medium h-5">
                                        <img class="avatar-50 rounded-circle bg-light" alt="user-icon" src="{{ getSingleMedia($customer,'profile_image', null) }}">
                                    </div>
                                    <div class="media-body ">
                                        <h5 class="mb-1"><span class="fw-bold">{{!empty($customer->display_name) ? $customer->display_name : '-'}}</span>  </h5>
                                        <span>
                                            {{
                                                optional($data['datetime'])->date_format && optional($data['datetime'])->time_format
                                                ? \Carbon\Carbon::parse($customer->created_at)
                                                    ->format(optional($data['datetime'])->date_format) .'  '. \Carbon\Carbon::parse($customer->created_at)
                                                    ->format(optional($data['datetime'])->time_format)
                                                : ''
                                            }}
                                        </span>


                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-sm-12">
                <div class="card recent-activities">
                    <div class="card-header d-flex justify-content-between gap-10">
                        <h4>{{__('messages.recent_booking')}} ({{$data['dashboard']['count_total_booking']}})</h4>
                        <a href="{{ route('booking.index') }}" class="btn-link btn-link-hover"><u>{{__('messages.view_all')}}</u></a>
                    </div>
                        <div class="card-body">
                            <ul class="common-list p-0">

                                @foreach($data['dashboard']['upcomming_booking'] as $booking)
                                    <li class="d-flex gap-3 align-items-start align-items-lg-center justify-content-between flex-column flex-sm-row "  style="pointer-events:none;">
                                        <div class="media align-items-center gap-3">
                                                <div class="h-avatar is-medium h-5">
                                                    <img class="avatar-50 rounded-circle bg-light" alt="user-icon" src="{{ getSingleMedia($booking->customer,'profile_image', null) }}">
                                                </div>
                                                <div class="media-body ">
                                                    <h5 class="mb-1">#{{$booking->id}}</h5>
                                                    <span>{{
        optional($data['datetime'])->date_format && optional($data['datetime'])->time_format
        ? date(optional($data['datetime'])->date_format, strtotime($booking->date)) .'  '. date(optional($data['datetime'])->time_format, strtotime($booking->date))
        : ''
    }}</span>
                                                    {{-- <span>{{(date("$data['datetime']->date_format $data['datetime']->time_format", strtotime($booking->date)))}}</span> --}}
                                                </div>
                                        </div>
                                        <span class="badge rounded-pill py-2 px-3 bg-primary-subtle text-capitalize">{{ucwords(str_replace('_', ' ', $booking->status))}}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                </div>
            </div>
        </div>
    </div>
</x-master-layout>
<script>
    var chartData = '<?php echo $data['category_chart']['chartdata']; ?>';
    var currency_data = '<?php echo json_encode(currency_data()); ?>';
    var currency_object = JSON.parse(currency_data);
    var chartArray = JSON.parse(chartData);
    var chartlabel = '<?php echo $data['category_chart']['chartlabel']; ?>';
    var labelsArray = JSON.parse(chartlabel);
    if(jQuery('#monthly-revenue').length){
        var options = {
        series: [{
            name: 'revenue',
            data: [ {{ implode ( ',' ,$data['revenueData'] ) }} ]
            // data: [30, 39, 20, 28, 36, 33,20]
        }],
        chart: {
            height: 265,
            type: 'line',
            toolbar:{
                show: true,
            },
            events: {
                click: function(chart, w, e) {
                }
            }
        },
        colors: ['var(--bs-primary)'],
        plotOptions: {
            bar: {
                horizontal: false,
                s̶t̶a̶r̶t̶i̶n̶g̶S̶h̶a̶p̶e̶: 'flat',
                e̶n̶d̶i̶n̶g̶S̶h̶a̶p̶e̶: 'flat',
                borderRadius: 0,
                columnWidth: '70%',
                barHeight: '70%',
                distributed: false,
                rangeBarOverlap: true,
                rangeBarGroupRows: false,
                colors: {
                    ranges: [{
                        from: 0,
                        to: 0,
                        color: undefined
                    }],
                    backgroundBarColors: [],
                    backgroundBarOpacity: 1,
                    backgroundBarRadius: 0,
                },
                dataLabels: {
                    position: 'top',
                    maxItems: 100,
                    hideOverflowingLabels: true,
                }
            }
        },
        dataLabels: {
          enabled: false
        },
        grid: {
            borderColor: 'var(--bs-border-color)',
            xaxis: {
                lines: {
                    show: false
                }
            },
            yaxis: {
                lines: {
                    show: true,
                }
            }
        },
        legend: {
          show: false
        },
        yaxis: {
            labels: {
                offsetY:0,
                minWidth: 60,
                maxWidth: 60,
                style: {
                    colors: 'var(--bs-body-color)',
                },
                formatter: function(value) {
                    return currency_object.currency_symbol + value;
                }
            },
        },
        xaxis: {
            categories: [
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'June',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec'
            ],
            labels: {
                minHeight: 22,
                maxHeight: 22,
                style: {
                    colors: 'var(--bs-body-color)',
                    fontSize: '12px'
                }
            }
        }
        };

        var chart = new ApexCharts(document.querySelector("#monthly-revenue"), options);
        chart.render();
    }

</script>
