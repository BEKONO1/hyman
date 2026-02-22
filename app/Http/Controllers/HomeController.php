<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\User;
use App\Models\Payment;
use App\Models\Service;
use App\Models\Slider;
use App\Models\Category;
use App\Models\ProviderDocument;
use App\Models\AppSetting;
use App\Models\Setting;
use App\Models\ProviderPayout;
use App\Models\HandymanPayout;
use App\Models\ServiceAddon;
use App\Models\AppDownload;
use App\Models\FrontendSetting;
use App\Models\PaymentGateway;
use App\Models\SubCategory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Models\BookingRating;
use App\Models\CommissionEarning;
use App\Models\HelpDesk;
use App\Models\PromotionalBanner;
use App\Models\SubscriptionTransaction;
use DB;
use Illuminate\Support\Facades\Artisan;
use App\Models\Documents;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        if (request()->ajax()) {
            $start = (!empty($_GET["start"])) ? date('Y-m-d', strtotime($_GET["start"])) : ('');
            $end = (!empty($_GET["end"])) ? date('Y-m-d', strtotime($_GET["end"])) : ('');
            $data =  Booking::myBooking()->whereIn('status', ['pending', 'accept', 'on_going', 'in_progress'])->whereDate('date', '>=', $start)->whereDate('date',   '<=', $end)->with('service')->get();
            return response()->json($data);
        }

        $data['dashboard'] = [
            'count_total_booking'               => Booking::myBooking()->count(),
            'count_total_service'               => Service::myService()->count(),
            'count_total_provider'              => User::where('user_type', 'provider')->count(),
            'new_customer'                      => User::myUsers('get_customer')->orderBy('id', 'DESC')->take(5)->get(),
            'new_provider'                      => User::myUsers('get_provider')->with('getServiceRating')->orderBy('id', 'DESC')->take(5)->get(),
            // 'upcomming_booking'                 => Booking::myBooking()->with('customer')->where('status', 'pending')->orderBy('id', 'DESC')->take(5)->get(),
            'upcomming_booking'                 => Booking::myBooking()->with('customer')->orderBy('id', 'DESC')->take(5)->get(),
            'top_services_list'                 => Booking::myBooking()->showServiceCount()->take(5)->get(),
            'count_handyman_pending_booking'    => Booking::myBooking()->where('status', 'pending')->count(),
            'count_handyman_complete_booking'   => Booking::myBooking()->where('status', 'completed')->count(),
            'count_handyman_cancelled_booking'  => Booking::myBooking()->where('status', 'cancelled')->count(),
            'top_handyman'                      => User::myUsers()->orderBy('id', 'DESC')->take(5)->get(),
        ];

        $data['category_chart'] = [
            'chartdata'     => Booking::myBooking()->showServiceCount()->take(4)->get()->pluck('count_pid'),
            'chartlabel'    => Booking::myBooking()->showServiceCount()->take(4)->get()->pluck('service.category.name')
        ];

        //$data['total_revenue'] = Payment::whereIn('payment_status', ['paid', 'advanced_paid'])->sum('total_amount');
        $data['CommissionEarning'] = CommissionEarning::whereIn('commission_status', ['paid', 'unpaid'])->sum('commission_amount');
        $data['cancellationcharge'] = Booking::where('status', 'cancelled')->sum('cancellation_charge_amount');
        $data['total_subscription_amout'] = SubscriptionTransaction::where('payment_status', 'paid')->sum('amount');
        $promotionalBannerAmount = PromotionalBanner::where('payment_status', 'paid')->sum('total_amount');
        $data['total_revenue'] = $data['CommissionEarning'] + $data['cancellationcharge'] + $data['total_subscription_amout'] + $promotionalBannerAmount;
        if ($user->hasAnyRole(['admin', 'demo_admin'])) {
            $data['revenueData']    =  adminEarning();
        }
        $setting = Setting::getValueByKey('site-setup', 'site-setup');
        $digitafter_decimal_point = $setting ? $setting->digitafter_decimal_point : "2";
        if ($user->hasRole('provider')) {
            $revenuedata = ProviderPayout::selectRaw('sum(amount) as total , DATE_FORMAT(updated_at , "%m") as month')
                ->where('provider_id', $user->id)
                ->whereYear('updated_at', date('Y'))
                // ->whereIn('commission_status', ['paid'])
                ->groupBy('month');
            $revenuedata = $revenuedata->get()->toArray();
            $data['revenueData']    =    [];
            $data['revenuelableData']    =    [];
            for ($i = 1; $i <= 12; $i++) {
                $revenueData = 0.0;

                foreach ($revenuedata as $revenue) {
                    if ($revenue['month'] == $i) {
                        $data['revenueData'][] = round($revenue['total'], $digitafter_decimal_point);
                        $revenueData++;
                    }
                }
                if ($revenueData == 0) {
                    $data['revenueData'][] = 0;
                }
            }
            // dd($data['revenueData']);
            $data['currency_data'] = currency_data();
        }

        $data['total_tax']  =    Booking::with('commissionsdata')->whereHas('commissionsdata', function ($query) {
            $query->whereIn('commission_status', ['unpaid', 'paid'])->groupBy('booking_id');
        })->sum('final_total_tax') ?? 0;
        // $data['total_earning']  = CommissionEarning::whereIn('user_type',['admin', 'demo_admin'])->whereIn('commission_status', ['unpaid','paid'])->sum('commission_amount') ?? 0;
        $promotionalBannerAmount = PromotionalBanner::where('payment_status', 'paid')->sum('total_amount');
        $data['total_earning']  = CommissionEarning::whereIn('user_type', ['admin', 'demo_admin'])
            ->whereIn('commission_status', ['unpaid', 'paid'])
            ->sum('commission_amount') ?? 0;
        $data['total_earning'] += $promotionalBannerAmount + $data['cancellationcharge'] + $data['total_subscription_amout'];


        $data['total_earning'] += $data['cancellationcharge'] + $data['total_subscription_amout'];
        // dd($data);

        //     $data['total_revenue'] = getPriceFormat($total_revenue);
        // }

        //     $data['total_revenue'] = getPriceFormat($total_revenue);
        // }
        // if ($user->hasRole('handyman') || $user->hasRole('provider')) {
        //     // $data['total_revenue']  = HandymanPayout::where('handyman_id', $user->id)->sum('amount') ?? 0;
        //     $data['total_earning']  = CommissionEarning::where('employee_id', $user->id)->whereIn('commission_status', ['unpaid', 'paid'])->sum('commission_amount') ?? 0;
        // }
        if ($user->hasRole('provider')) {
            $user = User::with('commission_earning')->where('id', $user->id)->where('user_type', 'provider')->first();
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


            $data['remaining_payout']  = $ProviderEarning;
            $data['total_earning']  = ProviderPayout::where('provider_id', $user->id)->sum('amount') ?? 0;
        } elseif ($user->hasRole('handyman')) {
            $data['remaining_payout']  = CommissionEarning::where('employee_id', $user->id)->where('commission_status', 'unpaid')->sum('commission_amount') ?? 0;
            $data['total_earning']  = HandymanPayout::where('handyman_id', $user->id)->sum('amount') ?? 0;
        }

        $sitesetup = Setting::where('type', 'site-setup')->where('key', 'site-setup')->first();
        $data['datetime'] = $sitesetup ? json_decode($sitesetup->value) : null;

        if (auth()->user()->hasAnyRole(['admin', 'demo_admin'])) {
            return $this->adminDashboard($data);
        } else if (auth()->user()->hasAnyRole('provider')) {
            return $this->providerDashboard($data);
        } else if (auth()->user()->hasAnyRole('handyman')) {
            return $this->handymanDashboard($data);
        } else {
            return $this->userDashboard($data);
        }
    }

    /**
     * Admin Dashboard
     *
     * @param $data
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function adminDashboard($data)
    {

        $rezorpayX_details = PaymentGateway::where('type', 'razorPayX')->where('status', 1)->first();
        return view('dashboard.dashboard', compact('data', 'rezorpayX_details'));
    }
    public function providerDashboard($data)
    {

        return view('dashboard.provider-dashboard', compact('data'));
    }
    public function handymanDashboard($data)
    {

        return view('dashboard.handyman-dashboard', compact('data'));
    }
    public function userDashboard($data)
    {
        return view('dashboard.user-dashboard', compact('data'));
    }
    public function changeStatus(Request $request)
    {
        if (demoUserPermission()) {
            $message = __('messages.demo_permission_denied');
            $response = [
                'status'    => false,
                'message'   => $message
            ];

            return comman_custom_response($response);
        }
        $type = $request->type;
        $message_form = __('messages.item');
        $message = trans('messages.update_form', ['form' => trans('messages.status')]);
        switch ($type) {
            case 'role':
                $role = \App\Models\Role::find($request->id);
                $role->status = $request->status;
                $role->save();
                break;
            case 'category_status':
                $category = \App\Models\Category::find($request->id);
                $category->status = $request->status;
                $category->save();
                break;
            case 'category_featured':
                $message_form = __('messages.category');
                $category = \App\Models\Category::find($request->id);
                $category->is_featured = $request->status;
                $category->save();
                break;
            case 'service_status':
                $service = \App\Models\Service::find($request->id);
                $service->status = $request->status;
                $service->save();
                break;
            case 'coupon_status':
                $coupon = \App\Models\Coupon::find($request->id);
                $coupon->status = $request->status;
                $coupon->save();
                break;
            case 'document_status':
                $document = \App\Models\Documents::find($request->id);
                $document->status = $request->status;
                $document->save();
                break;
            case 'document_required':
                $message_form = __('messages.document');
                $document = \App\Models\Documents::find($request->id);
                $document->is_required = $request->status;
                $document->save();
                break;
            case 'provider_is_verified':
                $message_form = __('messages.providerdocument');
                $document = \App\Models\ProviderDocument::find($request->id);
                $document->is_verified = $request->status;
                $document->save();
                break;
            case 'tax_status':
                $tax = \App\Models\Tax::find($request->id);
                $tax->status = $request->status;
                $tax->save();
                break;
            case 'provideraddress_status':
                $provideraddress = \App\Models\ProviderAddressMapping::find($request->id);
                $provideraddress->status = $request->status;
                $provideraddress->save();
                break;
            case 'slider_status':
                $slider = \App\Models\Slider::find($request->id);
                $slider->status = $request->status;
                $slider->save();
                break;
            case 'servicefaq_status':
                $servicefaq = \App\Models\ServiceFaq::find($request->id);
                $servicefaq->status = $request->status;
                $servicefaq->save();
                break;
            case 'wallet_status':
                $wallet = \App\Models\Wallet::find($request->id);
                $wallet->status = $request->status;
                $wallet->save();
                break;
            case 'subcategory_status':
                $subcategory = \App\Models\SubCategory::find($request->id);
                $subcategory->status = $request->status;
                $subcategory->save();
                break;
            case 'subcategory_featured':
                $message_form = __('messages.subcategory');
                $subcategory = \App\Models\SubCategory::find($request->id);
                $subcategory->is_featured = $request->status;
                $subcategory->save();
                break;
            case 'plan_status':
                $plans = \App\Models\Plans::find($request->id);
                $plans->status = $request->status;
                $plans->save();
                break;
            case 'bank_status':
                $banks = \App\Models\Bank::find($request->id);
                $banks->status = $request->status;
                $banks->save();
                break;
            case 'blog_status':
                $blog = \App\Models\Blog::find($request->id);
                $blog->status = $request->status;
                $blog->save();
                break;
            case 'servicepackage_status':
                $servicepackage = \App\Models\ServicePackage::find($request->id);
                $servicepackage->status = $request->status;
                $servicepackage->save();
                break;
            case 'notificationtemplate_status':
                $notificationtemplate = \App\Models\NotificationTemplate::find($request->id);
                $notificationtemplate->status = $request->status;
                $notificationtemplate->save();
            case 'serviceaddon_status':
                $serviceaddon = \App\Models\ServiceAddon::find($request->id);
                $serviceaddon->status = $request->status;
                $serviceaddon->save();
                break;
            case 'user_verify_email':
                $user = \App\Models\User::find($request->id);
                $user->is_email_verified = $request->status;
                $user->save();
                break;
            case 'user_service_status':
                $userService = \App\Models\Service::find($request->id);
                $userService->status = $request->status;
                $userService->save();
                break;
            case 'handyman_type_status':
                $handyman_type_status = \App\Models\HandymanType::find($request->id);
                $handyman_type_status->status = $request->status;
                $handyman_type_status->save();
                break;
            case 'providertype_status':
                $providertype_status = \App\Models\ProviderType::find($request->id);
                $providertype_status->status = $request->status;
                $providertype_status->save();
                break;
            case 'servicezone_status':
                $servicezone_status = \App\Models\ServiceZone::find($request->id);
                $servicezone_status->status = $request->status;
                $servicezone_status->save();
                break;
            default:
                $message = 'error';
                break;
        }
        if ($request->has('is_email_verified') && $request->is_email_verified == 'is_email_verified') {
            $message =  __('messages.user_verified', ['form' => $message_form]);
            if ($request->status == 0) {
                $message = __('messages.remove_form', ['form' => $message_form]);
            }
        }
        if ($request->has('is_featured') && $request->is_featured == 'is_featured') {
            $message =  __('messages.added_form', ['form' => $message_form]);
            if ($request->status == 0) {
                $message = __('messages.remove_form', ['form' => $message_form]);
            }
        }
        if ($request->has('is_required') && $request->is_required == 'is_required') {
            $message =  __('messages.added_form', ['form' => $message_form]);
            if ($request->status == 0) {
                $message = __('messages.remove_form', ['form' => $message_form]);
            }
        }
        if ($request->has('provider_is_verified') && $request->provider_is_verified == 'provider_is_verified') {
            $message =  __('messages.is_verify', ['form' => $message_form]);
            if ($request->status == 0) {
                $message = __('messages.remove_form_verify', ['form' => $message_form]);
            }
        }
        return comman_custom_response(['message' => $message, 'status' => true]);
    }

    public function getAjaxList(Request $request)
    {
        $items = array();
        $value = $request->q;

        $auth_user = authSession();
        switch ($request->type) {
            case 'permission':
                $items = \App\Models\Permission::select('id', 'name as text')->whereNull('parent_id');
                if ($value != '') {
                    $items->where('name', 'LIKE', $value . '%');
                }
                $items = $items->get();
                break;
            case 'category_footer':
                $items = \App\Models\Category::select('id', 'name as text')->where('status', 1);
                if (isset($request->is_featured)) {
                    $items->where('is_featured', $request->is_featured);
                }
                if ($value != '') {
                    $items->where('name', 'LIKE', '%' . $value . '%');
                }
                if (isset($request->provider_id) && $request->provider_id !== null) {
                    $items->whereHas('services', function ($query) use ($request) {
                        $query->where('provider_id', $request->provider_id);
                    });
                }
                if ($request->has('language_id')) {
                    $languageId = $request->input('language_id');
                    $items = $items->get()->map(function ($category) use ($languageId) {
                        $translatedName = $category->translate('name', $languageId);
                        $category->text = $translatedName ?: $category->text;
                        return $category;
                    });
                } else {
                    $items = $items->get();
                }
                // $items = $items->get();
                // if (isset($request->language_id) && $request->language_id !== null) {
                //     $languageId = $request->language_id;

                //     // Map through the items and apply translations for the 'name' attribute
                //     $items = $items->map(function ($item) use ($languageId) {
                //         // Translate the 'name' field using the translate method
                //         $translatedName = $item->translate('name', $languageId);

                //         // If a translation is found, use it; otherwise, fallback to the original name
                //         $item->text = $translatedName ?: $item->text;

                //         return $item;
                //     });
                // }

                // Return the items (either translated or original) as JSON response
                // return response()->json($items);
                return response()->json(['status' => 'true', 'results' => $items]);

                break;
            case 'category':
                $items = \App\Models\Category::select('id', 'name as text')->where('status', 1);
                if (isset($request->is_featured)) {
                    $items->where('is_featured', $request->is_featured);
                }
                if ($value != '') {
                    $items->where('name', 'LIKE', '%' . $value . '%');
                }
                if (isset($request->provider_id) && $request->provider_id !== null) {
                    $items->whereHas('services', function ($query) use ($request) {
                        $query->where('provider_id', $request->provider_id);
                    });
                }
                if ($request->has('language_id')) {
                    $languageId = $request->input('language_id');
                    $items = $items->get()->map(function ($category) use ($languageId) {
                        $translatedName = $category->translate('name', $languageId);
                        $category->text = $translatedName ?: $category->text;
                        return $category;
                    });
                    return response()->json($items);
                } else {
                    $items = $items->get();
                }

                break;
            case 'subcategory':
                $items = \App\Models\SubCategory::select('id', 'name as text')->where('status', 1);

                if ($value != '') {
                    $items->where('name', 'LIKE', '%' . $value . '%');
                }
                if ($request->has('category_id')) {
                    $items->where('category_id', $request->category_id);
                }
                if ($request->has('language_id')) {
                    $languageId = $request->input('language_id');
                    $items = $items->get()->map(function ($subcategory) use ($languageId) {
                        $translatedName = $subcategory->translate('name', $languageId);
                        $subcategory->text = $translatedName ?: $subcategory->text;
                        return $subcategory;
                    });
                    return response()->json($items);
                } else {
                    $items = $items->get();
                }
                //$items = $items->get();

                break;
            case 'provider':
                $items = \App\Models\User::select('id', 'display_name as text')
                    ->where('user_type', 'provider')
                    ->where('status', 1);

                if ($value != '') {
                    $items->where('display_name', 'LIKE', $value . '%');
                }

                if ($request->has('language_id')) {
                    $languageId = $request->input('language_id');
                    $items = $items->get()->map(function ($provider) {

                        return [
                            'id' => $provider->id,    // ID to be used by Select2
                            'text' => $provider->text // Display name of the provider
                        ];
                    });
                    return response()->json($items);
                } else {
                    $items = $items->get();
                }
                //$items = $items->get();

                break;

            case 'user':
                $items = \App\Models\User::select('id', 'display_name as text')
                    ->where('user_type', 'user')
                    ->where('status', 1);

                if ($value != '') {
                    $items->where('display_name', 'LIKE', $value . '%');
                }

                $items = $items->get();
                break;

            case 'provider-user':
                $items = \App\Models\User::select('id', 'display_name as text')
                    ->where('user_type', 'provider')->orWhere('user_type', 'user')
                    ->where('status', 1);

                if ($value != '') {
                    $items->where('display_name', 'LIKE', $value . '%');
                }

                $items = $items->get();
                break;

            case 'provider-user-handyman':
                $items = \App\Models\User::select('id', 'display_name as text')
                    ->whereIn('user_type', ['provider', 'user', 'handyman'])
                    ->where('status', 1);

                if ($value != '') {
                    $items->where('display_name', 'LIKE', $value . '%');
                }

                $items = $items->get();
                break;

            case 'handyman':
                $items = \App\Models\User::select('id', 'display_name as text')
                    ->where('user_type', 'handyman')
                    ->where('status', 1);

                if (isset($request->provider_id)) {
                    $items->where('provider_id', $request->provider_id);
                }

                // if (isset($request->booking_id)) {
                //     $booking_data = Booking::find($request->booking_id);

                //     $service_address = $booking_data->handymanByAddress;
                //     if ($service_address != null) {
                //         $items->where('service_address_id', $service_address->id);
                //     }
                // }

                if ($value != '') {
                    $items->where('display_name', 'LIKE', $value . '%');
                }

                $items = $items->get();
                break;
            case 'service':
                $items = \App\Models\Service::select('id', 'name as text')->where('status', 1)->where('service_request_status', 'approve');

                if ($value != '') {
                    $items->where('name', 'LIKE', '%' . $value . '%');
                }
                if (isset($request->provider_id)) {
                    $items->where('provider_id', $request->provider_id);
                }

                if (isset($request->top_rated)) {
                    $minRating = $request->top_rated['min'] ?? 0;
                    $maxRating = $request->top_rated['max'] ?? 5;

                    $topRatedServiceIds = BookingRating::select('service_id', \DB::raw('COALESCE(AVG(rating), 0) as avg_rating'))
                        ->groupBy('service_id')
                        ->havingRaw('avg_rating >= ?', [$minRating])
                        ->havingRaw('avg_rating <= ?', [$maxRating])
                        ->orderByDesc('avg_rating')
                        ->pluck('service_id')
                        ->toArray();

                    if (!empty($topRatedServiceIds)) {
                        $items->whereIn('id', $topRatedServiceIds)
                            ->orderByRaw("FIELD(id, " . implode(',', $topRatedServiceIds) . ")");
                    } else {
                        // Optional: Handle case where no services match the criteria
                        $items->whereRaw('0 = 1'); // Ensures no results are returned
                    }
                }

                if (isset($request->is_featured)) {
                    $items->where('is_featured', 1);
                }

                $items = $items->get();


                break;
            case 'service-list':
                $items = \App\Models\Service::select('id', 'name as text', 'price')
                    ->where('status', 1)
                    ->where('service_request_status', 'approve')
                    ->where('service_type', 'service');
                // Apply search filter if $value is provided
                if (!empty($value)) {
                    $items->where('name', 'LIKE', '%' . $value . '%');
                }

                // // Filter by provider_id if it's provided in the request
                // if ($request->filled('provider_id')) {
                //     $items->where('provider_id', $request->provider_id);
                // }

                // Filter by handyman_id if it's provided in the request
                if ($request->filled('handyman_id')) {
                    $providerId = \App\Models\User::where('id', $request->handyman_id)
                        ->where('user_type', 'handyman')
                        ->value('provider_id'); // Single value fetched
                    if ($providerId) {
                        $items->where('provider_id', $providerId);
                    }
                }

                // $items = $items->get();
                if (auth()->user()->hasRole('provider')) {
                    $provider_id = !empty($request->provider_id) ? $request->provider_id : auth()->user()->id;
                    $items->where('provider_id', $provider_id);
                }
                if (isset($request->provider_id)) {
                    $items->where('provider_id', $request->provider_id);
                }
                if (isset($request->category_id)) {
                    $items->where('category_id', $request->category_id);
                }
                if (isset($request->subcategory_id)) {
                    $items->where('subcategory_id', $request->subcategory_id);
                }
                // $items = $items->get();
                if ($request->has('language_id')) {
                    $languageId = $request->input('language_id');
                    $items = $items->get()->map(function ($service) use ($languageId) {
                        $translatedName = $service->translate('name', $languageId);
                        $service->text = $translatedName ?: $service->text;
                        return $service;
                    });
                    return response()->json($items);
                } else {
                    $items = $items->get();
                }

                break;
            case 'providertype':
                $items = \App\Models\ProviderType::where('status', 1);

                if ($value != '') {
                    $items->where('name', 'LIKE', $value . '%');
                }

                $items = $items->get()->map(function ($item) {
                    $text = $item->name . ' (';

                    if ($item->type === 'percent') {
                        $text .= $item->commission . '%';
                    } elseif ($item->type === 'fixed') {
                        $text .= getPriceFormat($item->commission);
                    } else {
                        $text .= $item->commission;
                    }

                    $text .= ')';

                    return [
                        'id' => $item->id,
                        'text' => $text,
                    ];
                });

                break;

            case 'coupon':
                $items = \App\Models\Coupon::select('id', 'code as text')->where('status', 1);

                if ($value != '') {
                    $items->where('code', 'LIKE', '%' . $value . '%');
                }

                $items = $items->where('status', 1)->get();
                break;

            case 'bank':
                $items = \App\Models\Bank::select('id', 'bank_name as text')->where('provider_id', $request->provider_id)->where('status', 1);

                if ($value != '') {
                    $items->where('name', 'LIKE', $value . '%');
                }
                $items = $items->get();
                break;

            case 'country':
                $items = \App\Models\Country::select('id', 'name as text');

                if ($value != '') {
                    $items->where('name', 'LIKE', $value . '%');
                }
                $items = $items->get();
                break;
            case 'state':
                $items = \App\Models\State::select('id', 'name as text');
                if (isset($request->country_id)) {
                    $items->where('country_id', $request->country_id);
                }
                if ($value != '') {
                    $items->where('name', 'LIKE', $value . '%');
                }
                $items = $items->get();
                break;
            case 'city':
                $items = \App\Models\City::select('id', 'name as text');
                if (isset($request->state_id)) {
                    $items->where('state_id', $request->state_id);
                }
                if ($value != '') {
                    $items->where('name', 'LIKE', $value . '%');
                }
                $items = $items->get();
                break;
            case 'booking_status':
                $items = \App\Models\BookingStatus::select('id', 'label as text');

                if ($value != '') {
                    $items->where('label', 'LIKE', $value . '%');
                }
                $items = $items->get();
                break;
            case 'currency':
                $items = \DB::table('countries')->select(\DB::raw('id id,CONCAT(name , " ( " , symbol ," ) ") text'));

                $items->whereNotNull('symbol')->where('symbol', '!=', '');
                if ($value != '') {
                    $items->where('name', 'LIKE', $value . '%')->orWhere('currency_code', 'LIKE', $value . '%');
                }
                $items = $items->get();
                break;
            case 'country_code':
                $items = \DB::table('countries')->select(\DB::raw('code id,name text'));
                if ($value != '') {
                    $items->where('name', 'LIKE', $value . '%')->orWhere('code', 'LIKE', $value . '%');
                }
                $items = $items->get();
                break;

            case 'time_zone':
                $items = timeZoneList();

                foreach ($items as $k => $v) {

                    if ($value != '') {
                        if (strpos($v, $value) !== false) {
                        } else {
                            unset($items[$k]);
                        }
                    }
                }

                $data = [];
                $i = 0;
                foreach ($items as $key => $row) {
                    $data[$i] = [
                        'id'    => $key,
                        'text'  => $row,
                    ];
                    $i++;
                }
                $items = $data;
                break;
            case 'provider_address':
                $provider_id = !empty($request->provider_id) ? $request->provider_id : $auth_user->id;
                $items = \App\Models\ProviderAddressMapping::select('id', 'address as text', 'latitude', 'longitude', 'status')->where('provider_id', $provider_id)->where('status', 1);
                $items = $items->get();
                break;

            case 'provider_tax':
                $provider_id = !empty($request->provider_id) ? $request->provider_id : $auth_user->id;
                $items = \App\Models\Tax::select('id', 'title as text')->where('status', 1);
                $items = $items->get();
                break;

            case 'zone':
                $items = \App\Models\ServiceZone::select('id', 'name as text')->where('status', 1);

                if ($value != '') {
                    $items->where('name', 'LIKE', '%' . $value . '%');
                }

                if (isset($request->provider_id)) {
                    $items->whereHas('providers', function ($query) use ($request) {
                        $query->where('provider_zone_mappings.provider_id', $request->provider_id);
                    });
                }

                $items = $items->get();
                break;

            case 'documents':
                $items = \App\Models\Documents::select('id', 'name', 'status', 'is_required', \DB::raw('name as text'))->where('status', 1);
                if ($value != '') {
                    $items->where('name', 'LIKE', $value . '%');
                }
                $items = $items->get();
                break;
            case 'handymantype':
                $items = \App\Models\HandymanType::where('status', 1);

                if ($value != '') {
                    $items->where('name', 'LIKE', $value . '%');
                }

                $provider_id = $request->provider_id ?? $auth_user->id;

                // Get all admin IDs
                $adminIds = \App\Models\User::where('user_type', 'admin')->pluck('id')->toArray();

                // Filter by created_by, which could include the provider ID and admin IDs
                $items->whereIn('created_by', array_merge([$provider_id], $adminIds));

                // Retrieve the results and map them
                $items = $items->get()->map(function ($item) {
                    $text = $item->name . ' (';

                    if ($item->type === 'percent') {
                        $text .= $item->commission . '%';
                    } elseif ($item->type === 'fixed') {
                        $text .= getPriceFormat($item->commission); // No country symbol included
                    } else {
                        $text .= $item->commission;
                    }

                    $text .= ')';

                    return [
                        'id' => $item->id,
                        'text' => $text,
                    ];
                });

                break;
            case 'subcategory_list':
                $category_id = !empty($request->category_id) ? $request->category_id : '';
                $items = \App\Models\SubCategory::select('id', 'name as text')->where('category_id', $category_id)->where('status', 1);
                $items = $items->get();
                break;
            case 'service_package':
                $service_id = !empty($request->service_id) ? $request->service_id : $auth_user->id;
                $items = \App\Models\ServicePackage::select('id', 'description as text', 'status')->where('provider_id', $service_id)->where('status', 1);
                $items = $items->get();
                break;
            case 'all_user':
                $items = \App\Models\User::select('id', 'display_name as text')
                    ->where('status', 1);

                if ($value != '') {
                    $items->where('display_name', 'LIKE', $value . '%');
                }

                $items = $items->get();
                break;
            default:
                break;
        }
        return response()->json(['status' => 'true', 'results' => $items]);
    }

    public function removeFile(Request $request)
    {
        if (demoUserPermission()) {
            $message = __('messages.demo_permission_denied');
            $response = [
                'status'    => false,
                'message'   => $message
            ];

            return comman_custom_response($response);
        }

        $type = $request->type;
        $data = null;

        switch ($type) {
            case 'slider_image':
                $data = Slider::find($request->id);
                $message = __('messages.msg_removed', ['name' => __('messages.slider')]);
                break;
            case 'profile_image':
                $data = User::find($request->id);
                $message = __('messages.msg_removed', ['name' => __('messages.profile_image')]);
                break;
            case 'service_attachment':
                $media = Media::find($request->id);
                $media->delete();
                $message = __('messages.msg_removed', ['name' => __('messages.attachments')]);
                break;
            case 'helpdesk_attachment':
                $media = Media::find($request->id);
                $media->delete();
                $message = __('messages.msg_removed', ['name' => __('messages.attachments')]);
                break;
            case 'helpdesk_activity_attachment':
                $media = Media::find($request->id);
                $media->delete();
                $message = __('messages.msg_removed', ['name' => __('messages.attachments')]);
                break;
            case 'category_image':
                $data = Category::find($request->id);
                $message = __('messages.msg_removed', ['name' => __('messages.attachments')]);
                break;
            case 'provider_document':
                $data = ProviderDocument::find($request->id);
                $message = __('messages.msg_removed', ['name' => __('messages.providerdocument')]);
                break;
            case 'booking_attachment':
                $media = Media::find($request->id);
                $media->delete();
                $message = __('messages.msg_removed', ['name' => __('messages.attachments')]);
                break;
            case 'bank_attachment':
                $media = Media::find($request->id);
                $media->delete();
                $message = __('messages.msg_removed', ['name' => __('messages.attachments')]);
                break;
            case 'app_image':
                $data = AppDownload::find($request->id);
                $message = __('messages.msg_removed', ['name' => __('messages.attachments')]);
                break;
            case 'app_image_full':
                $data = AppDownload::find($request->id);
                $message = __('messages.msg_removed', ['name' => __('messages.attachments')]);
                break;
            case 'package_attachment':
                $media = Media::find($request->id);
                $media->delete();
                $message = __('messages.msg_removed', ['name' => __('messages.attachments')]);
                break;
            case 'blog_attachment':
                $media = Media::find($request->id);
                $media->delete();
                $message = __('messages.msg_removed', ['name' => __('messages.attachments')]);
                break;
            case 'serviceaddon_image':
                $data = ServiceAddon::find($request->id);
                $message = __('messages.msg_removed', ['name' => __('messages.service_addon')]);
                break;
            case 'section5_attachment':
                $media = Media::find($request->id);
                $media->delete();
                $message = __('messages.msg_removed', ['name' => __('messages.attachments')]);
                break;
            case 'main_image':
                $data = FrontendSetting::find($request->id);
                $message = __('messages.msg_removed', ['name' => __('messages.main_image')]);
                break;
            case 'google_play':
                $data = FrontendSetting::find($request->id);
                $message = __('messages.msg_removed', ['name' => __('messages.google_image')]);
                break;
            case 'app_store':
                $data = FrontendSetting::find($request->id);
                $message = __('messages.msg_removed', ['name' => __('messages.app_store')]);
                break;
            case 'vimage':
                $data = FrontendSetting::find($request->id);
                $message = __('messages.msg_removed', ['name' => __('messages.app_store')]);
                break;
            case 'login_register_image':
                $data = FrontendSetting::find($request->id);
                $message = __('messages.msg_removed', ['name' => __('messages.app_store')]);
                break;
            case 'logo':
                $data = Setting::find($request->id);
                $message = __('messages.msg_removed', ['name' => __('messages.app_store')]);
                break;
            case 'favicon':
                $data = Setting::find($request->id);
                $message = __('messages.msg_removed', ['name' => __('messages.app_store')]);
                break;
            case 'footer_logo':
                $data = Setting::find($request->id);
                $message = __('messages.msg_removed', ['name' => __('messages.app_store')]);
                break;
            case 'loader':
                $data = Setting::find($request->id);
                $message = __('messages.msg_removed', ['name' => __('messages.app_store')]);
                break;

            case 'subcategory_image':
                $data = SubCategory::find($request->id);
                $message = __('messages.msg_removed', ['name' => __('messages.subcategory')]);
                break;


            default:
                $data = AppSetting::find($request->id);
                $message = __('messages.msg_removed', ['name' => __('messages.image')]);
                break;
        }

        if ($data != null) {
            $data->clearMediaCollection($type);
        }

        $response = [
            'status'    => true,
            'image'     => getSingleMedia($data, $type),
            'id'        => $request->id,
            'preview'   => $type . "_preview",
            'message'   => $message
        ];

        return comman_custom_response($response);
    }

    public function lang($locale)
    {
        \App::setLocale($locale);
        session()->put('locale', $locale);
        \Artisan::call('cache:clear');
        $dir = 'ltr';
        if (in_array($locale, ['ar', 'dv', 'ff', 'ur', 'he', 'ku', 'fa'])) {
            $dir = 'rtl';
        }

        session()->put('dir',  $dir);
        if (auth()->check()) {
            $user = auth()->user();
            $user->language_option = $locale;
            $user->save();
        }
        return redirect()->back();
    }

    function authLogin()
    {
        return view('auth.login');
    }
    function authRegister()
    {
        $requiredDocuments = Documents::where('status', 1)->get();
        return view('auth.register', compact('requiredDocuments'));
    }

    function authRecoverPassword()
    {
        return view('auth.forgot-password');
    }

    function authConfirmEmail()
    {
        return view('auth.verify-email');
    }
    function getAjaxServiceList(Request $request)
    {
        // dd('hello');
        $items = \App\Models\Service::select('id', 'name as text', 'price')->where('status', 1)->where('type', 'fixed')->where('service_request_status', 'approve');

        $provider_id = !empty($request->provider_id) ? $request->provider_id : auth()->user()->id;
        $items->where('provider_id', $provider_id);
        if (isset($request->category_id)) {
            $items->where('category_id', $request->category_id);
        }
        if (isset($request->subcategory_id)) {
            $items->where('subcategory_id', $request->subcategory_id);
        }
        $items = $items->get();
        return response()->json(['status' => 'true', 'results' => $items]);
    }

    public function checkImage(Request $request, $id)
    {

        $type = $request->query('type');
        switch ($type) {
            case 'service':
                $serviceData = Service::find($id);
                if ($serviceData) {
                    $attachments = $serviceData->getMedia('service_attachment');
                } else {
                    $attachments = null; // Handle the case where the service is not found
                }
                break;

            case 'helpdesk':
                $helpdesk = HelpDesk::find($id);
                $attachments = $helpdesk->getMedia('helpdesk_attachment');
                break;

            case 'helpdesk_activity':
                $helpdesk = HelpDesk::find($id);
                $attachments = $helpdesk->getMedia('helpdesk_activity_attachment');
                break;

            case 'category':
                $CategoryData = Category::find($id);
                $attachments = $CategoryData->getMedia('category_image');
                break;

            case 'slider':
                $SliderData = Slider::find($id);
                $attachments = $SliderData->getMedia('slider_image');
                break;

            case 'subcategory':
                $SubCategoryData = SubCategory::find($id);
                $attachments = $SubCategoryData->getMedia('subcategory_image');
                break;

            case 'serviceaddon':
                $SubCategoryData = ServiceAddon::find($id);
                $attachments = $SubCategoryData->getMedia('serviceaddon_image');
                break;

            default:
                $attachments = null;
                break;
        }
        return response()->json(['results' => $attachments]);
    }

    public function migration()
    {
        set_time_limit(0);
        Artisan::call('migrate:fresh', ['--force' => true, '--seed' => true]);
        return redirect()->route('login');
    }
}
