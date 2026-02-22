<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\{
    Booking,
    Category,
    Service,
    Payment,
    Slider,
    User,
    Setting,
    AppSetting,
    ProviderType,
    ProviderPayout,
    PaymentGateway,
    BookingRating,
    HandymanRating,
    HandymanType,
    HandymanPayout,
    BookingHandymanMapping,
    ProviderServiceAddressMapping,
    Wallet,
    Blog,
    PostJobRequest,
    FrontendSetting,
    Country,
    LiveLocation,
    CommissionEarning,
    Permission,
    PromotionalBanner,
    ServiceZone,
};
use App\Http\Resources\API\{
    BookingResource,
    ServiceResource,
    CategoryResource,
    SliderResource,
    UserResource,
    PaymentGatewayResource,
    BookingRatingResource,
    HandymanRatingResource,
    PostJobRequestResource,
    BlogResource,
    CountryResource,
    TypeResource
};
use App\Http\Resources\PromotionalBannerResource;

use App\Traits\TranslationTrait;

use App\Traits\ZoneTrait;

class DashboardController extends Controller
{
    use TranslationTrait;
    use ZoneTrait;

    /**
     * Calculate distance between two points using Haversine formula
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2, $unit = 'km')
    {
        $earthRadius = $unit === 'km' ? 6371 : 3959; // Radius of the earth in km or miles

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return $distance;
    }

    public function dashboardDetail(Request $request)
    {
    

        $headerValue = $request->header('language-code') ?? session()->get('locale', 'en');
        $per_page = 6;
        $customer_review = null;
        $notification = 0;
        $upcomming_booking = null;
        $is_email_verified = 0;

        $serviceZone = ServiceZone::all();

        // Get sliders
        $slider = SliderResource::collection(Slider::where('status', 1)->paginate($per_page));

        // Get promotional banners
        $banners = PromotionalBanner::where('promotional_banners.status', 'accepted')
            ->whereDate('promotional_banners.start_date', '<=', now()->toDateString())
            ->whereDate('promotional_banners.end_date', '>=', now()->toDateString())
            ->leftJoin('services', 'promotional_banners.service_id', '=', 'services.id')
            ->select('promotional_banners.*', 'services.name as service_name')
            ->orderBy('promotional_banners.created_at', 'desc')
            ->get();
        $promotional_banners = PromotionalBannerResource::collection($banners);

        // Get categories
        $category_section = FrontendSetting::getValueByKey('section_2');
        $category = CategoryResource::collection(
            Category::whereIN('id', $category_section->category_id)
                ->orderBy('name', 'asc')
                ->paginate(8)
        );

        // Initialize service query
        $serviceQuery = Service::where('status', 1)
            ->where('service_type', 'service')
            ->where('service_request_status', "approve");


        // Filter by provider status
        $serviceQuery->whereHas('providers', function ($query) {
            $query->where('status', 1);
        });

        // Apply subscription filter if needed
        if (default_earning_type() === 'subscription') {
            $serviceQuery->whereHas('providers', function ($query) {
                $query->where('status', 1)->where('is_subscribe', 1);
            });
        }

        // Apply location filter if coordinates are provided
        if (
            $request->has('latitude') && !empty($request->latitude) &&
            $request->has('longitude') && !empty($request->longitude)
        ) {

            $serviceZone = ServiceZone::all();

            if (count($serviceZone) > 0) {

                try {

                    $matchingZoneIds = $this->getMatchingZonesByLatLng($request->latitude, $request->longitude);


                    $serviceQuery->whereHas('serviceZoneMapping', function ($query) use ($matchingZoneIds) {
                        $query->whereIn('zone_id', $matchingZoneIds);
                    });
                } catch (\Exception $e) {
                    \Log::error('Location filtering error: ' . $e->getMessage());
                }
            } else {

                $get_distance = getSettingKeyValue('site-setup', 'radious');
                $get_unit = getSettingKeyValue('site-setup', 'distance_type');

                $locations = Service::locationService($request->latitude, $request->longitude, $get_distance, $get_unit);
                $service_in_location = ProviderServiceAddressMapping::whereIn('provider_address_id', $locations)->get()->pluck('service_id');
                $serviceQuery = $serviceQuery->whereIn('id', $service_in_location);
            }
        }

        // Apply city filter if provided
        if ($request->has('city_id') && !empty($request->city_id)) {
            $serviceQuery->whereHas('providers', function ($query) use ($request) {
                $query->where('city_id', $request->city_id);
            });
        }

        // Get final services
        $service = ServiceResource::collection(
            $serviceQuery->orderBy('id', 'desc')->paginate($per_page)
        );

        $service = ServiceResource::collection($service);

        if ($request->has('latitude') && !empty($request->latitude) && $request->has('longitude') && !empty($request->longitude)) {


            $serviceZone = ServiceZone::all();

            if (count($serviceZone) > 0) {
                try {

                    $matchingZoneIds = $this->getMatchingZonesByLatLng($request->latitude, $request->longitude);

                    $service->whereHas('serviceZoneMapping', function ($query) use ($matchingZoneIds) {
                        $query->whereIn('zone_id', $matchingZoneIds);
                    })->where('is_featured', 1)
                        ->get();
                    $service = ServiceResource::collection($service);
                } catch (\Exception $e) {
                    // If there's an error, keep existing featured services
                    \Log::error('Location filtering error: ' . $e->getMessage());
                }
            } else {

                $get_distance = getSettingKeyValue('site-setup', 'radious');
                $get_unit = getSettingKeyValue('site-setup', 'distance_type');

                $locations = Service::locationService($request->latitude, $request->longitude, $get_distance, $get_unit);
                $service_in_location = ProviderServiceAddressMapping::whereIn('provider_address_id', $locations)->get()->pluck('service_id');
                $service = $service->whereIn('id', $service_in_location);
            }
        }

        $provider = User::where('user_type', 'provider')->where('status', 1);

        // Apply subscription filter if needed
        if (default_earning_type() === 'subscription') {
            $provider = $provider->where('is_subscribe', 1);
        }

        // Apply location filter if coordinates are provided
        if (
            $request->has('latitude') && !empty($request->latitude) &&
            $request->has('longitude') && !empty($request->longitude)
        ) {
            try {
                $get_distance = getSettingKeyValue('site-setup', 'radious');
                $get_unit = getSettingKeyValue('site-setup', 'distance_type');

                // Get providers within radius using Haversine formula
                $unit_value = $get_unit == 'km' ? 6371 : 3959;
                $latitude = $request->latitude;
                $longitude = $request->longitude;

                $provider = $provider->whereHas('providerAddress', function ($query) use ($latitude, $longitude, $unit_value, $get_distance) {
                    $query->selectRaw("
                        id, 
                        provider_id,
                        ( {$unit_value} * acos( cos( radians({$latitude}) ) * 
                        cos( radians( latitude ) ) * 
                        cos( radians( longitude ) - radians({$longitude}) ) + 
                        sin( radians({$latitude}) ) * 
                        sin( radians( latitude ) ) ) ) AS distance")
                        ->having('distance', '<=', $get_distance);
                });
            } catch (\Exception $e) {
                \Log::error('Provider location filtering error: ' . $e->getMessage());
            }
        }

        // Apply city filter if provided
        if ($request->has('city_id') && !empty($request->city_id)) {
            $provider = $provider->where('city_id', $request->city_id);
        }

        $provider = UserResource::collection($provider->paginate($per_page));

        $featured_service_section = FrontendSetting::getValueByKey('section_4');

        $featured_service = Service::whereIN('id', $featured_service_section->service_id);

        $featured_service = $featured_service->whereHas('providers', function ($a) use ($request) {
            $a->where('status', 1);
        });

        if (default_earning_type() === 'subscription') {
            $featured_service = $featured_service->whereHas('providers', function ($a) use ($request) {
                $a->where('status', 1)->where('is_subscribe', 1);
            });
        }

        $featured_service = ServiceResource::collection($featured_service->orderBy('id', 'desc')->paginate($per_page));



        if ($request->has('latitude') && !empty($request->latitude) && $request->has('longitude') && !empty($request->longitude)) {

            $serviceZone = ServiceZone::all();

            if (count($serviceZone) > 0) {

                $matchingZoneIds = $this->getMatchingZonesByLatLng($request->latitude, $request->longitude);

                $featured_service = Service::with('zones')
                    ->where('is_featured', 1)
                    ->whereHas('zones', function ($q) use ($matchingZoneIds) {
                        $q->whereIn('zone_id', $matchingZoneIds);
                    });
            } else {

                $get_distance = getSettingKeyValue('site-setup', 'radious');
                $get_unit = getSettingKeyValue('site-setup', 'distance_type');

                $locations = Service::locationService($request->latitude, $request->longitude, $get_distance, $get_unit);
                $service_in_location = ProviderServiceAddressMapping::whereIn('provider_address_id', $locations)->get()->pluck('service_id');
                $featured_service = Service::with('providerServiceAddress')->whereIn('id', $service_in_location)->where('is_featured', 1)->get();
                $featured_service = ServiceResource::collection($featured_service);
            }

                   if(default_earning_type() === 'subscription'){
                        $featured_service = $featured_service->whereHas('providers', function ($a) use ($request) {
                        $a->where('status', 1)->where('is_subscribe', 1);
                    });
                }
       
                $featured_service  = $featured_service->get();
               
                $featured_service = ServiceResource::collection($featured_service);
            }


        if ($request->has('customer_id') && isset($request->customer_id)) {
            $customer_review = BookingRating::with('customer', 'service')->where('customer_id', $request->customer_id)->get();
            if (!empty($customer_review)) {
                $customer_review = BookingRatingResource::collection($customer_review);
            }
            $user = User::where('id', $request->customer_id)->first();

            $notification = 0;

            if ($user) {

                $notification = count($user->unreadNotifications);
                $is_email_verified = $user->is_email_verified ? 1 : 0;
            }

            $upcomming_booking = Booking::where('customer_id', $request->customer_id)
                ->with('customer')->where('status', 'accept')->orderBy('id', 'DESC')->first();

            if (!empty($upcomming_booking)) {
                $upcomming_booking = new BookingResource($upcomming_booking);
            }
        }

        $blogs = BlogResource::collection(Blog::paginate($per_page));


        $response = [
            'status'         => true,
            'slider'         => $slider,
            'promotional_banner' => $promotional_banners,
            'category'       => $category,
            'service'        => $service,
            'featured_service' => $featured_service,
            'provider'       => $provider,
            'customer_review' => $customer_review,
            'notification_unread_count' => $notification,
            'upcomming_confirmed_booking' => $upcomming_booking,
            'blogs' => $blogs,
            'is_email_verified' => $is_email_verified,
        ];

        return comman_custom_response($response);
    }
    public function providerDashboard(Request $request)
    {
        $per_page = config('constant.PER_PAGE_LIMIT');

        $provider = User::find(auth()->user()->id);

        $booking = Booking::myBooking();
        $total_booking = $booking->count();

        $service = Service::myService()->where('status', 1);
        $total_service = $service->count();

        if ($request->has('city_id') && !empty($request->city_id)) {
            $service = $service->whereHas('providers', function ($a) use ($request) {
                $a->where('city_id', $request->city_id);
            });
        }

        $service = ServiceResource::collection($service->orderBy('id', 'desc')->take(4)->get());

        $total_active_handyman = UserResource::collection(User::myUsers()->where('status', 1)->get());

        $handyman = UserResource::collection(User::myUsers()->where('status', 1)->take(4)->get());

        $total_revenue    = ProviderPayout::where('provider_id', $provider->id)->sum('amount') ?? 0;

        $handymanIds = User::with('providerHandyman')
            ->where('provider_id', $provider->id)
            ->pluck('id');
        $handymanIds[] = $provider->id;
        $user = User::with('commission_earning')->where('id', $provider->id)->where('user_type', 'provider')->first();
        $remaining_payout  = 0;
        $data['revenueData']    =    [];
        if ($user) {
            $commissions = $user->commission_earning()
                ->whereHas('getbooking', function ($query) {
                    $query->where('status', 'completed');
                })
                ->where('commission_status', 'unpaid')
                ->pluck('booking_id'); // Get all booking IDs

            $ProviderEarning = 0;

            if ($commissions->isNotEmpty()) {
                // Fetch all unpaid commissions for the relevant bookings in a single query
                $ProviderEarning = CommissionEarning::whereIn('booking_id', $commissions)
                    ->whereIn('user_type', ['provider', 'handyman'])
                    ->where('commission_status', 'unpaid')
                    ->sum('commission_amount'); // Directly sum the commission_amount
            }
            $remaining_payout  = $ProviderEarning;
            //$remaining_payout  = CommissionEarning::where('employee_id',$provider->id)->where('commission_status', 'unpaid')->sum('commission_amount') ?? 0;
            $revenuedata = ProviderPayout::selectRaw('sum(amount) as total , DATE_FORMAT(updated_at , "%m") as month')
                ->where('provider_id', $user->id)
                ->whereYear('updated_at', date('Y'))
                // ->whereIn('commission_status', ['paid'])
                ->groupBy('month');
            $revenuedata = $revenuedata->get();
            $data['revenueData']    =    [];
            for ($i = 1; $i <= 12; $i++) {
                $revenueData = 0.0;
                foreach ($revenuedata as $revenue) {
                    if ($revenue['month'] == $i) {

                        $data['revenueData'][] = [
                            $i => $revenue['total']
                        ];
                        $revenueData++;
                    }
                }
                if ($revenueData == 0) {
                    $data['revenueData'][] = (object) [];
                }
            }
        }
        $commissionData = ProviderType::where('id', $provider->providertype_id)->first();
        $commission = $commissionData ? new TypeResource($commissionData) : null;
        $notification = count($provider->unreadNotifications);

        $active_plan = get_user_active_plan($provider->id);
        if (is_any_plan_active($provider->id) == 0 && is_subscribed_user($provider->id) == 0) {
            $active_plan = user_last_plan($provider->id);
        }
        $provider_wallet = Wallet::where('user_id', $provider->id)->where('status', 1)->first();
        $online_handyman = User::myUsers()->where('is_available', 1)->orderBy('last_online_time', 'desc')->limit(10)->get();
        $profile_array = [];
        if (!empty($online_handyman)) {
            foreach ($online_handyman as $online) {
                $profile_array[] = $online->login_type !== null ? $online->social_image : getSingleMedia($online, 'profile_image', null);
            }
        }
        $post_request = PostJobRequest::where('status', 'requested')->latest()->take(5)->get();
        $post_requests = PostJobRequestResource::collection($post_request);
        $upcomming_booking = Booking::myBooking()->with('customer')->where('date', '>', now())->where('status', 'pending')->orderBy('id', 'DESC')->take(5)->get();
        if (!empty($upcomming_booking)) {
            $upcomming_booking = BookingResource::collection($upcomming_booking);
        }

        $response = [
            'status'         => true,
            'total_booking'  => $total_booking,
            'total_service'  => $total_service,
            'total_active_handyman' => $total_active_handyman->count(),
            'total_cash_in_hand'     => total_cash_in_hand(auth()->user()->id),
            'service'        => $service,
            'handyman'       => $handyman,
            'total_revenue'  => $total_revenue,
            'monthly_revenue' => $data,
            'commission'     => $commission,
            'notification_unread_count' => $notification,
            'subscription'  => $active_plan,
            'is_subscribed' => is_subscribed_user($provider->id),
            'provider_wallet' => $provider_wallet,
            'online_handyman' => $profile_array,
            'post_requests' => $post_requests,
            'upcomming_booking' => $upcomming_booking,
            'is_email_verified' => $provider->is_email_verified,
            'remaining_payout' => $remaining_payout,
        ];

        return comman_custom_response($response);
    }
    public function handymanDashboard(Request $request)
    {
        $per_page = config('constant.PER_PAGE_LIMIT');
        $handyman = User::find(auth()->user()->id);
        if ($handyman) {
            $sitesetup = Setting::getValueByKey('site-setup', 'site-setup');
            $get_current_time = Carbon::now();
            $handyman->last_online_time = $get_current_time->toTimeString();
            $handyman->update();
        }
        $booking =  BookingHandymanMapping::with('bookings')->where('handyman_id', auth()->user()->id)->get();

        $upcomming = BookingHandymanMapping::with('bookings')->whereHas('bookings', function ($bookings) {
            $bookings->where('status', 'accept');
        })->where('handyman_id', auth()->user()->id)->orderBy('id', 'DESC')->get();

        $today_booking =  BookingHandymanMapping::with('bookings')->whereHas('bookings', function ($bookings) {
            $bookings->whereDate('date', Carbon::today());
        })->where('handyman_id', auth()->user()->id)->get();

        $completed_booking = BookingHandymanMapping::with('bookings')->whereHas('bookings', function ($bookings) {
            $bookings->where('status', 'completed');
        })->where('handyman_id', auth()->user()->id)->orderBy('id', 'DESC')->get();

        $handyman_rating = HandymanRating::where('handyman_id', auth()->user()->id)->orderBy('id', 'desc')->paginate(10);
        $handyman_rating = HandymanRatingResource::collection($handyman_rating);

        $commission = HandymanType::where('id', $handyman->handymantype_id)->first();

        $total_revenue    = HandymanPayout::where('handyman_id', auth()->user()->id)->sum('amount') ?? 0;
        $remaining_payout  = CommissionEarning::where('employee_id', $handyman->id)->where('commission_status', 'unpaid')->sum('commission_amount') ?? 0;

        $revenuedata = HandymanPayout::selectRaw('sum(amount) as total , DATE_FORMAT(updated_at , "%m") as month')
            ->where('handyman_id', auth()->user()->id)
            ->whereYear('updated_at', date('Y'))
            // ->whereIn('commission_status', ['unpaid', 'paid'])
            ->groupBy('month');
        $revenuedata = $revenuedata->get();
        $data['revenueData']    =    [];
        for ($i = 1; $i <= 12; $i++) {
            $revenueData = 0.0;
            foreach ($revenuedata as $revenue) {
                if ($revenue['month'] == $i) {

                    $data['revenueData'][] = [
                        $i => $revenue['total']
                    ];
                    $revenueData++;
                }
            }
            if ($revenueData == 0) {
                $data['revenueData'][] = (object) [];
            }
        }

        $notification = count($handyman->unreadNotifications);

        $upcomming_booking = Booking::myBooking()->with('customer')->where('status', 'pending')->orderBy('id', 'DESC')->take(5)->get();
        if (!empty($upcomming_booking)) {
            $upcomming_booking = BookingResource::collection($upcomming_booking);
        }

        $response = [
            'status'                        => true,
            'total_cash_in_hand'            => total_cash_in_hand(auth()->user()->id),
            'total_booking'                 => $booking->count(),
            'upcomming_booking'             => $upcomming->count(),
            'today_booking'                 => $today_booking->count(),
            'commission'                    => $commission,
            'handyman_reviews'              => $handyman_rating,
            'total_revenue'                 => $total_revenue,
            'monthly_revenue'               => $data,
            'notification_unread_count'     => $notification,
            'isHandymanAvailable'           => $handyman->is_available,
            'completed_booking'             => $completed_booking->count(),
            'upcomming_booking'             => $upcomming_booking,
            'is_email_verified'             => $handyman->is_email_verified,
            'remaining_payout'              => $remaining_payout,
        ];
        return comman_custom_response($response);
    }
    public function adminDashboard(Request $request)
    {
        $admin = User::find(auth()->user()->id);

        $notification = count($admin->unreadNotifications);

        $services = Booking::with('categoryService')->myBooking()->showServiceCount()->take(5)->get();

        $post_request = PostJobRequest::latest()->take(5)->get();
        $post_requests = PostJobRequestResource::collection($post_request);

        $totalProviders = User::where('user_type', 'provider')->withTrashed()->count();

        $general_setting = Setting::getValueByKey('general-setting', 'general-setting');
        $total_tax  =    Booking::with('commissionsdata')->whereHas('commissionsdata', function ($query) {
            $query->whereIn('commission_status', ['unpaid', 'paid'])->groupBy('booking_id');
        })->sum('final_total_tax') ?? 0;
        $my_earning  =    CommissionEarning::whereIn('user_type', ['admin', 'demo_admin'])->whereIn('commission_status', ['unpaid', 'paid'])->sum('commission_amount') ?? 0;
        $response = [
            'status'                        => true,
            'total_booking'                 => Booking::myBooking()->count(),
            'total_service'                 => Service::myService()->count(),
            'total_provider'                => $totalProviders,
            'total_tax'                     => $total_tax,
            'my_earning'                    => $my_earning,
            'total_revenue'                 => CommissionEarning::whereIn('commission_status', ['paid', 'unpaid'])->sum('commission_amount') ?? 0,
            'monthly_revenue'               => adminEarning(),
            'provider'                      => UserResource::collection(User::myUsers('get_provider')->orderBy('id', 'DESC')->take(5)->get()),
            'user'                          => UserResource::collection(User::myUsers('get_customer')->orderBy('id', 'DESC')->take(5)->get()),
            'upcomming_booking'             => BookingResource::collection(Booking::myBooking()->where('status', 'pending')->orderBy('id', 'DESC')->take(5)->get()),
            'notification_unread_count'     => $notification,

        ];

        return comman_custom_response($response);
    }
    public function configurations(Request $request)
    {
        $headerValue = $request->header('language-code') ?? session()->get('locale', 'en');
        $sitesetup = Setting::getValueByKey('site-setup', 'site-setup');
        $general_setting = Setting::getValueByKey('general-setting', 'general-setting');
        $service_config = Setting::getValueByKey('service-configurations', 'service-configurations');
        $provider_banner = Setting::getValueByKey('provider-banner', 'provider-banner');
        $social_media = Setting::getValueByKey('social-media', 'social-media');
        $other_setting = Setting::getValueByKey('OTHER_SETTING', 'OTHER_SETTING');
        // $terms_condition = Setting::getValueByKey('terms_condition','terms_condition');
        // $privacy_policy = Setting::getValueByKey('privacy_policy','privacy_policy');
        // $help_support = Setting::getValueByKey('help_support','help_support');
        // $refund_policy = Setting::getValueByKey('refund_cancellation_policy','refund_cancellation_policy');
        // $data_deletion_request = Setting::getValueByKey('data_deletion_request','data_deletion_request');

        $terms_condition = Setting::getValueByKey('terms_condition', 'terms_condition', $headerValue);
        $privacy_policy = Setting::getValueByKey('privacy_policy', 'privacy_policy', $headerValue);
        $help_support = Setting::getValueByKey('help_support', 'help_support', $headerValue);
        $refund_policy = Setting::getValueByKey('refund_cancellation_policy', 'refund_cancellation_policy', $headerValue);
        $data_deletion_request = Setting::getValueByKey('data_deletion_request', 'data_deletion_request', $headerValue);
        $about_us = Setting::getValueByKey('about_us', 'about_us', $headerValue);
        $earning_setting = Setting::getValueByKey('earning-setting', 'earning-setting');
        $country_obj = Country::where('id', $sitesetup->default_currency)->first();
        $user = User::withTrashed()->where('id', (int)$request->input('user_id'))->first();
        $is_user_authorized = false;
        if (!empty($user)) {
            if ($user->status === 0) {
                $is_user_authorized = false;
            } elseif ($user->status === 1) {
                $is_user_authorized = !$user->trashed();
            }
        }
        $permissions = Permission::pluck('name');

        $userPermissions = $user ? $user->getAllPermissions()->pluck('name')->toArray() : [];

        $roleAndPermission = $permissions->mapWithKeys(function ($permission) use ($userPermissions) {
            $formattedPermission = str_replace(' ', '_', $permission); // Replace spaces with underscores
            return [$formattedPermission => in_array($permission, $userPermissions) ? 1 : 0];
        });
        $response = [
            "site_name" => $general_setting->site_name,
            "site_description" => $general_setting->site_description,
            "inquiry_email" => $general_setting->inquriy_email,
            "helpline_number" => $general_setting->helpline_number,
            "website" => $general_setting->website,
            "zipcode" => $general_setting->zipcode,
            "site_copyright" => $sitesetup->site_copyright,
            "date_format" => $sitesetup->date_format,
            "time_format" => $sitesetup->time_format,
            "time_zone" => $sitesetup->time_zone,
            "distance_type" => $sitesetup->distance_type,
            "radius" => $sitesetup->radious,
            'is_user_authorized' => $is_user_authorized,

            "playstore_url" => $sitesetup->playstore_url,
            "appstore_url" => $sitesetup->appstore_url,
            "provider_appstore_url" => $sitesetup->provider_appstore_url,
            "provider_playstore_url" => $sitesetup->provider_playstore_url,

            "currency_country_code" => $sitesetup->default_currency,
            "currency_position" => $sitesetup->currency_position,
            "currency_symbol" => $country_obj->symbol,
            "currency_code" => $country_obj->currency_code,
            "decimal_point" => $sitesetup->digitafter_decimal_point,
            "advance_payment_status" => $service_config->advance_payment,
            "cancellation_charge" => isset($service_config->cancellation_charge) ? $service_config->cancellation_charge : 0,
            "cancellation_charge_amount" => isset($service_config->cancellation_charge_amount) ? (float)$service_config->cancellation_charge_amount : 0,
            "cancellation_charge_hours" => isset($service_config->cancellation_charge_hours) ? (float)$service_config->cancellation_charge_hours : 0,
            "slot_service_status" => $service_config->slot_service,
            "digital_service_status" => $service_config->digital_services,
            "service_package_status" => $service_config->service_packages,
            "service_addon_status" => $service_config->service_addons,
            "job_request_service_status" => $service_config->post_services,
            "social_login_status" => $other_setting->social_login,
            "google_login_status" => $other_setting->google_login,
            "apple_login_status" => $other_setting->apple_login,
            "otp_login_status" => $other_setting->otp_login,
            "online_payment_status" => $other_setting->online_payment,
            "blog_status" => $other_setting->blog,
            "maintenance_mode" => $other_setting->maintenance_mode,
            "wallet_status" => $other_setting->wallet,
            "chat_gpt_status" => $other_setting->enable_chat_gpt,
            "test_chat_gpt_without_key" => $other_setting->test_without_key,
            "dashboard_type" => $other_setting->dashboard_type,
            "force_update_user_app" => $other_setting->force_update_user_app,
            "user_app_minimum_version" => (int)$other_setting->user_app_minimum_version,
            "user_app_latest_version" =>  (int)$other_setting->user_app_latest_version,
            "force_update_provider_app" => $other_setting->force_update_provider_app,
            "provider_app_minimum_version" => (int)$other_setting->provider_app_minimum_version,
            "provider_app_latest_version" =>  (int)$other_setting->provider_app_latest_version,
            "force_update_admin_app" => $other_setting->force_update_admin_app,
            "admin_app_minimum_version" => (int)$other_setting->admin_app_minimum_version,
            "admin_app_latest_version" =>  (int)$other_setting->admin_app_latest_version,
            "is_in_app_purchase_enable" => isset($other_setting->is_in_app_purchase_enable) ? (int)$other_setting->is_in_app_purchase_enable : 0,
            "entitlement_id" => isset($other_setting->entitlement_id) ? $other_setting->entitlement_id : null,
            "google_public_api_key" => isset($other_setting->google_public_api_key) ? $other_setting->google_public_api_key : null,
            "apple_public_api_key" => isset($other_setting->apple_public_api_key) ? $other_setting->apple_public_api_key : null,
            "firebase_notification_status" => $other_setting->firebase_notification,

            "facebook_url" => $social_media->facebook_url,
            "linkedin_url" => $social_media->linkedin_url,
            "instagram_url" => $social_media->instagram_url,
            "youtube_url" => $social_media->youtube_url,
            "twitter_url" => $social_media->twitter_url,

            "terms_conditions" => $terms_condition,
            "privacy_policy" => $privacy_policy,
            "help_support" => $help_support,
            "refund_policy" => $refund_policy,
            "data_deletion_request" => $data_deletion_request,
            "about_us" => $about_us,
            "earning_type" => $earning_setting,
            "auto_assign_status" => !empty($other_setting->auto_assign_provider) ? $other_setting->auto_assign_provider : 0,
            "role_and_permission" => $roleAndPermission,
            "provider_banner_amount" => isset($provider_banner->promotion_price) ? (float)$provider_banner->promotion_price : 0,
            "promotional_banner" => isset($provider_banner->promotion_enable) ? (bool)$provider_banner->promotion_enable : false,
            "enable_chat" => isset($other_setting->enable_chat) ? $other_setting->enable_chat : 0,

        ];
        if (!empty($request->is_authenticated) && $request->is_authenticated == 1) {
            $response["google_map_key"] = $sitesetup->google_map_keys;
            $response["chat_gpt_key"] = $other_setting->chat_gpt_key;
            $response["project_id"] = $other_setting->project_id;
        }

        return comman_custom_response($response);
    }

    function firebaseDetails(Request $request)
    {
        $setting = Setting::getValueByKey('OTHER_SETTING', 'OTHER_SETTING');

        $firebase_token = getAccessToken();

        $data = [
            'project_id' => $setting ? $setting->project_id : null,
            'firebase_token' => $firebase_token,
        ];

        $message = trans('messages.firebase_data');

        return response()->json(['status' => true, 'data' => $data, 'message' => $message]);
    }
}
