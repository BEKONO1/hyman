<?php

namespace App\Http\Controllers;

use App\Exports\BookingExport;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Coupon;
use App\Models\Service;
use App\Models\Payment;
use App\Models\User;
use App\Models\BookingStatus;
use App\Models\PostJobRequest;
use App\Models\ProviderAddressMapping;
use App\Http\Requests\BookingUpdateRequest;
use App\Models\Notification;
use Yajra\DataTables\DataTables;
use PDF;
use App\Models\AppSetting;
use Carbon\Carbon;
use App\Traits\NotificationTrait;
use App\Traits\EarningTrait;

use App\Models\ServiceAddon;
use App\Models\BookingRating;
use App\Models\Setting;
use App\Models\Country;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\PaymentGateway;
use App\Models\ServiceZone;
use App\Traits\TranslationTrait;

class BookingController extends Controller
{
    use NotificationTrait;
    use EarningTrait;
    use TranslationTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filter = [
            'status' => $request->status,
        ];
        $pageTitle = __('messages.bookings');
        $auth_user = authSession();
        $authRole = $auth_user->roles->pluck('name')->first();
        $assets = ['datatable'];

        $provider_id = '';
        $handyman_id = '';
        $customer_id = '';

        // Calculate total earnings based on role
        $advanceFilter = ['paymentStatus' => ['paid', 'pending', 'advance_paid', 'advance_refund']];
        $paymentStatus = ['paid', 'pending', 'advanced_paid', 'Advanced Refund'];
        $paymentType = PaymentGateway::where('status', 1)->get()->pluck('title', 'type')->put('wallet', 'Wallet');
        $bookingStatus = BookingStatus::where('status', 1)->orderBy('sequence', 'ASC')->get()->pluck('label', 'value');
        switch ($authRole) {
            case 'admin':
                $totalEarning = Booking::where('status', '!=', 'cancelled')
                    ->whereHas('handymanAdded', function ($query) {
                        $query->whereNotNull('provider_id'); // Ensure handyman is not null
                    })
                    ->sum('total_amount');
                break;
            case 'demo_admin':
                $totalEarning = Booking::where('status', '!=', 'cancelled')
                    ->whereHas('handymanAdded', function ($query) {
                        $query->whereNotNull('provider_id'); // Ensure handyman is not null
                    })
                    ->sum('total_amount');
                break;


            case 'provider':
                $totalEarning = Booking::where('status', '!=', 'cancelled')->whereHas('handymanAdded', function ($query) use ($auth_user) {
                    $query->where('provider_id', $auth_user->id);
                })->sum('total_amount');
                break;


            case 'handyman':
                $totalEarning = Booking::where('status', '!=', 'cancelled')->whereHas('handymanAdded', function ($query) use ($auth_user) {
                    $query->where('handyman_id', $auth_user->id);
                })->sum('total_amount');
                break;

            default:
                $totalEarning = 0;
                break;
        }

        $advanceFilter = [
            'paymentStatus' => ['paid', 'advance paid', 'advance refund'],
            'paymentType' => PaymentGateway::where('status', 1)->get()->pluck('title', 'type')->put('wallet', 'Wallet'),
            'bookingStatus' => BookingStatus::where('status', 1)->orderBy('sequence', 'ASC')->get()->pluck('label', 'value')
        ];

        return view('booking.index', compact('pageTitle', 'auth_user', 'assets', 'filter', 'customer_id', 'provider_id', 'handyman_id', 'advanceFilter', 'totalEarning'));
    }


    public function index_data(DataTables $datatable, Request $request)
    {
        $auth_user = authSession();
        $query = Booking::query()->myBooking()->with('payment', 'commissionsdata', 'handymanAdded');
        if ($request->provider_id) {
            $query->where('provider_id', $request->provider_id);
            $query->where('status', 'completed')
                ->whereHas('commissionsdata', function ($q) {
                    $q->whereIn('commission_status', ['paid', 'unpaid']);
                });
        }
        // Apply role-based filters
        if ($auth_user->hasRole('handyman')) {
            $query->whereHas('handymanAdded', function ($q) use ($auth_user) {
                $q->where('handyman_id', $auth_user->id);
            });
        }

        $filter = $request->filter;
        $primary_locale = app()->getLocale() ?? 'en';
        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }
            if (isset($filter['status'])) {
                $query->where('status', $filter['status']);
            }
        }

        // Handle advanceFilter
        $advanceFilter = $request->advanceFilter;

        if (isset($advanceFilter)) {
            // Date range filter - Updated logic
            if (!empty($advanceFilter['date_range'])) {
                $dates = explode(' to ', $advanceFilter['date_range']);
                if (count($dates) === 2) {
                    $startDate = date('Y-m-d', strtotime($dates[0]));
                    $endDate = date('Y-m-d', strtotime($dates[1]));
                    $query->whereDate('date', '>=', $startDate)
                        ->whereDate('date', '<=', $endDate);
                } elseif (count($dates) === 1) {
                    $date = date('Y-m-d', strtotime($dates[0]));
                    $query->whereDate('date', $date);
                }
            }

            // Other filters...
            $filters = [
                'customer_id' => 'customer_id',
                'provider_id' => 'provider_id',
                'service_id' => 'service_id',
                'handyman_id' => ['handymanAdded', 'handyman_id'],
                'booking_status' => 'status',
                'payment_status' => ['payment', 'payment_status'],
                'payment_type' => ['payment', 'payment_type'],
            ];

            foreach ($filters as $key => $filter) {
                if (!empty($advanceFilter[$key])) {
                    if (is_array($advanceFilter[$key])) {
                        if (is_array($filter)) {
                            $query->whereHas($filter[0], function ($subQuery) use ($filter, $advanceFilter, $key) {
                                $subQuery->whereIn($filter[1], $advanceFilter[$key]);
                            });
                        } else {
                            $query->whereIn($filter, $advanceFilter[$key]);
                        }
                    } else {
                        $query->where($filter, $advanceFilter[$key]);
                    }
                }
            }
        }

        if (auth()->user()->hasAnyRole(['admin'])) {
            $query->withTrashed();
        }

        return $datatable->eloquent($query)
            // ->addColumn('check', function ($row) {
            //     return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-' . $row->id . '"  name="datatable_ids[]" value="' . $row->id . '" data-type="booking" onclick="dataTableRowCheck(' . $row->id . ',this)">';
            // })
            ->editColumn('id', function ($query) {
                return "<a class='btn-link btn-link-hover' href=" . route('booking.show', $query->id) . ">#" . $query->id . "</a>";
            })
            ->editColumn('customer_id', function ($query) {
                return view('booking.customer', compact('query'));
            })
            ->filterColumn('customer_id', function ($query, $keyword) {
                $query->whereHas('customer', function ($q) use ($keyword) {
                    $q->where('display_name', 'like', '%' . $keyword . '%');
                });
            })
            ->orderColumn('customer_id', function ($query, $order) {
                $query->select('bookings.*')
                    ->join('users as customers', 'customers.id', '=', 'bookings.customer_id')
                    ->orderBy('customers.display_name', $order);
            })
            ->editColumn('service_id', function ($query) use ($primary_locale) {
                if (!empty($query->bookingPackage)) {
                    // $name = optional($query->bookingPackage)->name;
                    $name = $this->getTranslation($query->bookingPackage->package->translations, $primary_locale, 'name', optional(optional($query->bookingPackage)->package)->name) ?? optional($query->bookingPackage)->name;
                } else {
                    // $name = optional($query->service)->name;
                    $name = $this->getTranslation($query->service->translations, $primary_locale, 'name', optional($query->service)->name) ?? optional($query->service)->name;
                }
                $service_name = ($query->service_id != null && isset($query->service)) ? $name : "";
                return "<a class='btn-link btn-link-hover' href=" . route('booking.show', $query->id) . ">" . $service_name . "</a>";
            })
            ->filterColumn('service_id', function ($query, $keyword) use ($primary_locale) {
                // $query->whereHas('service', function ($q) use ($keyword) {
                //     $q->where('name', 'like', '%' . $keyword . '%');
                // });
                $query->whereHas('service', function ($q) use ($keyword, $primary_locale) {
                    if ($primary_locale !== 'en') {
                        $q->where(function ($q) use ($keyword, $primary_locale) {
                            // Search in the translations table for the given locale
                            $q->whereHas('translations', function ($q) use ($keyword, $primary_locale) {
                                $q->where('locale', $primary_locale)
                                    ->where('value', 'LIKE', '%' . $keyword . '%');
                            })
                                // Fallback to checking 'name' field if no translation is found
                                ->orWhere('name', 'LIKE', '%' . $keyword . '%');
                        });
                    } else {
                        // If locale is 'en', search directly in the 'name' field
                        $q->where('name', 'LIKE', '%' . $keyword . '%');
                    }
                });
            })
            ->orderColumn('service_id', function ($query, $order) {
                $query->join('services', 'services.id', '=', 'bookings.service_id')
                    ->orderBy('services.name', $order);
            })
            ->editColumn('date', function ($query) {
                $sitesetup = Setting::where('type', 'site-setup')->where('key', 'site-setup')->first();
                $datetime = $sitesetup ? json_decode($sitesetup->value) : null;

                $date = optional($datetime)->date_format && optional($datetime)->time_format
                    ? date(optional($datetime)->date_format, strtotime($query->date)) . '  ' . date(optional($datetime)->time_format, strtotime($query->date))
                    : $query->date;

                return $date;
            })
            ->editColumn('provider_id', function ($query) {
                return view('booking.provider', compact('query'));
            })
            ->filterColumn('provider_id', function ($query, $keyword) {
                $query->whereHas('provider', function ($q) use ($keyword) {
                    $q->where('display_name', 'like', '%' . $keyword . '%');
                });
            })
            ->orderColumn('provider_id', function ($query, $order) {
                $query->select('bookings.*')
                    ->join('users as providers', 'providers.id', '=', 'bookings.provider_id')
                    ->orderBy('providers.display_name', $order);
            })
            ->editColumn('status', function ($query) {
                return bookingstatus(BookingStatus::bookingStatus($query->status));
            })
            ->editColumn('payment_id', function ($query) {
                $payment = $query->payment()->orderBy('id', 'desc')->first();
                $payment_status = $payment ? $payment->payment_status : null;
                if ($payment_status !== null) {
                    $status = '<span class="text-center text-white badge bg-primary">' . str_replace('_', " ", ucfirst($payment_status)) . '</span>';
                } else {
                    $status = '<span class="badge text-primary bg-primary-subtle">' . __('messages.pending') . '</span>';
                }
                return $status;
            })
            ->filterColumn('payment_id', function ($query, $keyword) {
                $query->whereHas('payment', function ($q) use ($keyword) {
                    $q->where('payment_status', 'like', $keyword . '%');
                });
            })
            ->editColumn('total_amount', function ($query) {
                return $query->total_amount ? getPriceFormat($query->total_amount) : '-';
            })

            ->addColumn('action', function ($booking) {
                return view('booking.action', compact('booking'))->render();
            })

            ->editColumn('updated_at', function ($query) {
                $diff = Carbon::now()->diffInHours($query->updated_at);
                if ($diff < 25) {
                    return $query->updated_at->diffForHumans();
                } else {
                    return $query->updated_at->isoFormat('llll');
                }
            })
            ->addIndexColumn()
            ->rawColumns(['action', 'status', 'payment_id', 'service_id', 'id'])
            ->toJson();
    }


    public function export(Request $request)
    {
        // Get the selected format (default to 'xlsx')
        $format = $request->input('format', 'xlsx');

        // Validate format
        $validFormats = ['xlsx', 'xls', 'ods', 'csv', 'pdf', 'html'];
        if (!in_array($format, $validFormats)) {
            return response()->json(['error' => 'Invalid format selected.'], 400);
        }

        // Get the selected columns (passed as JSON array)
        $columns = json_decode($request->input('columns'), true);

        // Create a new instance of BookingExport with role-based filtering
        $auth_user = authSession();

        // Modify the query based on user role
        $query = Booking::query()->with(['service', 'customer', 'provider', 'payment', 'handymanAdded']);

        // Apply role-based filters
        switch ($auth_user->roles->pluck('name')->first()) {
            case 'handyman':
                $query->whereHas('handymanAdded', function ($q) use ($auth_user) {
                    $q->where('handyman_id', $auth_user->id);
                });
                break;

            case 'provider':
                $query->where('provider_id', $auth_user->id);
                break;

            case 'user':
                $query->where('customer_id', $auth_user->id);
                break;
        }

        // Apply any additional filters from the request
        if ($request->has('advanceFilter')) {
            $advanceFilter = $request->advanceFilter;

            // Apply filters similar to index_data method
            if (!empty($advanceFilter['customer_id'])) {
                $query->whereIn('customer_id', $advanceFilter['customer_id']);
            }
            if (!empty($advanceFilter['service_id'])) {
                $query->whereIn('service_id', $advanceFilter['service_id']);
            }
            if (!empty($advanceFilter['booking_status'])) {
                $query->whereIn('status', $advanceFilter['booking_status']);
            }
            if (!empty($advanceFilter['payment_status'])) {
                $query->whereHas('payment', function ($q) use ($advanceFilter) {
                    $q->whereIn('payment_status', $advanceFilter['payment_status']);
                });
            }
            if (!empty($advanceFilter['payment_type'])) {
                $query->whereHas('payment', function ($q) use ($advanceFilter) {
                    $q->whereIn('payment_type', $advanceFilter['payment_type']);
                });
            }
            if (!empty($advanceFilter['date_range'])) {
                $dates = explode(' to ', $advanceFilter['date_range']);
                if (count($dates) === 2) {
                    $query->whereDate('date', '>=', $dates[0])
                        ->whereDate('date', '<=', $dates[1]);
                } elseif (count($dates) === 1) {
                    $query->whereDate('date', $dates[0]);
                }
            }
        }

        // Check if there's any data to export
        $bookingData = $query->get();
        if ($bookingData->isEmpty()) {
            return response()->json(['error' => 'No data found for export.'], 404);
        }

        // Create BookingExport instance with the filtered query
        $bookingExport = new BookingExport($columns, $query);

        // Set the filename based on the selected format
        $filename = 'bookings.' . $format;

        // Handle PDF format specifically
        if ($format === 'pdf') {
            return Excel::download($bookingExport, $filename, \Maatwebsite\Excel\Excel::DOMPDF);
        }

        // Handle other formats (xlsx, xls, ods, csv, html)
        return Excel::download($bookingExport, $filename, constant('\Maatwebsite\Excel\Excel::' . strtoupper($format)));
    }

    /* bulck action method */
    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);

        $actionType = $request->action_type;

        $message = 'Bulk Action Updated';
        switch ($actionType) {
            case 'change-status':
                $branches = Booking::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = 'Bulk Booking Status Updated';
                break;

            case 'delete':
                $bookings = Booking::whereIn('id', $ids)->get();
                foreach ($bookings as $booking) {
                    $booking->delete();
                }

                $message = 'Bulk Booking Deleted';
                break;

            case 'restore':
                $bookings = Booking::withTrashed()->whereIn('id', $ids)->get();
                foreach ($bookings as $booking) {
                    $booking->restore();
                }
                $message = 'Bulk Booking Restored';
                break;

            case 'permanently-delete':
                $bookings = Booking::withTrashed()->whereIn('id', $ids)->get();
                foreach ($bookings as $booking) {
                    $booking->forceDelete();
                }
                $message = 'Bulk Booking Permanently Deleted';
                break;

            default:
                return response()->json(['status' => false, 'message' => 'Action Invalid']);
                break;
        }

        return response()->json(['status' => true, 'message' => $message]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $id = $request->id;
        $auth_user = authSession();

        $bookingdata = Booking::find($id);
        $pageTitle = __('messages.update_form_title', ['form' => __('messages.booking')]);

        if ($bookingdata == null) {
            $pageTitle = __('messages.add_button_form', ['form' => __('messages.booking')]);
            $bookingdata = new Booking;
        }

        return view('booking.create', compact('pageTitle', 'bookingdata', 'auth_user'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $sitesetup = Setting::where('type', 'site-setup')->where('key', 'site-setup')->first();
        $admin = json_decode($sitesetup->value);
        date_default_timezone_set($admin->time_zone ?? 'UTC');
        $data = $request->all();
        $serviceZone = ServiceZone::all();

        if (count($serviceZone) > 0) {

            $data['zone_id'] = $data['booking_address_id'] ?? null;
        }

        $data['tax'] = null;

        if ($request->id == null) {
            $data['status'] = !empty($data['status']) ? $data['status'] : 'pending';
        }

        if (isset($data['booking_slot'])) {
            $date = isset($request->date) ? date('Y-m-d', strtotime($request->date)) : date('Y-m-d');
            $time = date('H:i:s', strtotime($data['booking_slot']));
            $data['date'] = $date . ' ' . $time;
        } else {
            $data['date'] = isset($request->date) ? date('Y-m-d H:i:s', strtotime($request->date)) : date('Y-m-d H:i:s');
        }
        $service_data = Service::find($data['service_id']);

        $data['provider_id'] = !empty($data['provider_id']) ? $data['provider_id'] : $service_data->provider_id;

        if ($request->has('tax') && $request->tax != null) {
            $data['tax'] = json_encode($request->tax);
        }

        if ($request->coupon_id != null) {
            $coupons = Coupon::with('serviceAdded')->where('code', $request->coupon_id)
                ->where('expire_date', '>', date('Y-m-d H:i'))->where('status', 1)
                ->whereHas('serviceAdded', function ($coupon) use ($service_data) {
                    $coupon->where('service_id', $service_data->id);
                })->first();

            if ($coupons == null) {
                return comman_message_response(__('messages.invalid_coupon_code'), 406);
            }

            // ✅ Check if the coupon discount exceeds the service price
            $discountAmount = 0;
            if ($coupons->discount_type === 'fixed') {
                $discountAmount = $coupons->discount;
            } elseif ($coupons->discount_type === 'percent') {
                $discountAmount = ($service_data->price * $coupons->discount) / 100;
            }

            if ($discountAmount >= $service_data->price) {
                return comman_message_response(__('messages.invalid_coupon_code'), 406);
            }

            $data['coupon_id'] = $coupons->id;
        }


        $user = User::where('id', $data['provider_id'])->with('providertype')->first();

        $result = Booking::updateOrCreate(['id' => $request->id], $data);

        $activity_data = [
            'activity_type' => 'add_booking',
            'booking_id' => $result->id,
            'booking' => $result,
        ];
        $this->sendNotification($activity_data);


        if ($data['coupon_id'] != null) {
            $coupons = Coupon::find($data['coupon_id']);

            $coupon_data = [
                'booking_id' => $result->id,
                'code' => $coupons->code,
                'discount' => $coupons->discount,
                'discount_type' => $coupons->discount_type,
            ];

            $result->couponAdded()->create($coupon_data);
        }

        if (count($serviceZone) == 0) {

            if ($request->has('booking_address_id') && $request->booking_address_id != null) {
                $booking_address_mapping = ProviderAddressMapping::find($data['booking_address_id']);

                $booking_address_data = [
                    'booking_id' => $result->id,
                    'address' => $booking_address_mapping->address,
                    'latitude' => $booking_address_mapping->latitude,
                    'longitude' => $booking_address_mapping->longitude,
                ];
                $result->addressAdded()->create($booking_address_data);
            }
        }


        if ($request->has('service_addon_id') && is_array($request->service_addon_id) != null) {
            foreach ($request->service_addon_id as $serviceaddon) {
                $booking_serviceaddon_mapping = ServiceAddon::find($serviceaddon);
                if ($booking_serviceaddon_mapping) {
                    $booking_serviceaddon_data = [
                        'booking_id' => $result->id,
                        'service_addon_id' => $booking_serviceaddon_mapping->id,
                        'name' => $booking_serviceaddon_mapping->name,
                        'price' => $booking_serviceaddon_mapping->price,
                    ];

                    $result->bookingAddonService()->create($booking_serviceaddon_data);
                }
            }
        }


        if ($request->has('booking_package') && $request->booking_package != null) {
            $booking_package = [
                'booking_id' => $result->id,
                'service_package_id' => $data['booking_package']['id'],
                'provider_id' => $data['provider_id'],
                'name' => $data['booking_package']['name'],
                'is_featured' => $data['booking_package']['is_featured'],
                'package_type' => $data['booking_package']['package_type'],
                'price' => $data['booking_package']['price'],
            ];
            if (!empty($data['booking_package']['start_at'])) {
                $booking_package['start_at'] = $data['booking_package']['start_at'];
            }
            if (!empty($data['booking_package']['end_at'])) {
                $booking_package['end_at'] = $data['booking_package']['end_at'];
            }
            if (!empty($data['booking_package']['subcategory_id'])) {
                $booking_package['subcategory_id'] = $data['booking_package']['subcategory_id'];
            }
            if (!empty($data['booking_package']['category_id'])) {
                $booking_package['category_id'] = $data['booking_package']['category_id'];
            }
            if (!empty($data['booking_package']['service_id'])) {

                $serviceIds = explode(',', $data['booking_package']['service_id']);

                $services = [];

                foreach ($serviceIds as $serviceId) {
                    $service = Service::find($serviceId);

                    if ($service) {
                        $services[] = [
                            'service_id' => $service->id,
                            'price' => $service->price,
                        ];
                    }
                }

                $booking_package['services'] = json_encode($services);
            }
            $result->bookingPackage()->create($booking_package);
        }
        if (!empty($data['type']) && $data['type'] === 'user_post_job') {
            $post_request = PostJobRequest::where('id', $data['post_request_id'])->first();
            $post_request->date = isset($request->date) ? date('Y-m-d H:i:s', strtotime($request->date)) : date('Y-m-d H:i:s');
            $post_request->update();
        }
        if ($result->wasRecentlyCreated) {
            $message = __('messages.save_form', ['form' => __('messages.booking')]);
        }

        if ($request->is('api/*')) {
            $response = [
                'message' => $message,
                'booking_id' => $result->id
            ];
            return comman_custom_response($response);
        }
        return redirect(route('booking.index'))->withSuccess($message);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $auth_user = authSession();

        $user = auth()->user();
        $user->last_notification_seen = now();
        $user->save();

        if (count($user->unreadNotifications) > 0) {

            foreach ($user->unreadNotifications as $notifications) {

                if ($notifications['data']['id'] == $id) {

                    $notification = $user->unreadNotifications->where('id', $notifications['id'])->first();
                    if ($notification) {
                        $notification->markAsRead();
                    }
                }
            }
        }


        $bookingdata = Booking::with('bookingExtraCharge', 'payment', 'service')->myBooking()->find($id);


        $tabpage = 'info';
        if (empty($bookingdata)) {
            $msg = __('messages.not_found_entry', ['name' => __('messages.booking')]);
            return redirect(route('booking.index'))->withError($msg);
        }
        if (count($auth_user->unreadNotifications) > 0) {
            $auth_user->unreadNotifications->where('data.id', $id)->markAsRead();
        }

        $pageTitle = __('messages.view_form_title', ['form' => __('messages.booking')]);
        return view('booking.view', compact('pageTitle', 'bookingdata', 'auth_user', 'tabpage'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $auth_user = authSession();

        $bookingdata = Booking::myBooking()->find($id);

        $pageTitle = __('messages.update_form_title', ['form' => __('messages.booking')]);
        $relation = [
            'status' => BookingStatus::where('status', 1)->orderBy('sequence', 'ASC')->get()->pluck('label', 'value'),
        ];
        return view('booking.edit', compact('pageTitle', 'bookingdata', 'auth_user') + $relation);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BookingUpdateRequest $request, $id)
    {
        if (demoUserPermission()) {
            return redirect()->back()->withErrors(trans('messages.demo_permission_denied'));
        }
        $data = $request->all();



        $data['date'] = isset($request->date) ? date('Y-m-d H:i:s', strtotime($request->date)) : date('Y-m-d H:i:s');
        $data['start_at'] = isset($request->start_at) ? date('Y-m-d H:i:s', strtotime($request->start_at)) : null;
        $data['end_at'] = isset($request->end_at) ? date('Y-m-d H:i:s', strtotime($request->end_at)) : null;


        $bookingdata = Booking::find($id);
        $paymentdata = Payment::where('booking_id', $id)->first();
        if ($data['status'] === 'hold') {
            if ($bookingdata->start_at == null && $bookingdata->end_at == null) {
                $duration_diff = duration($data['start_at'], $data['end_at'], 'in_minute');
                $data['duration_diff'] = $duration_diff;
            } else {
                if ($bookingdata->status == $data['status']) {
                    $booking_start_date = $bookingdata->start_at;
                    $request_start_date = $data['start_at'];
                    if ($request_start_date > $booking_start_date) {
                        $msg = __('messages.already_in_status', ['status' => $data['status']]);
                        return redirect()->back()->withSuccess($msg);
                    }
                } else {
                    $duration_diff = $bookingdata->duration_diff;
                    $new_diff = duration($bookingdata->start_at, $bookingdata->end_at, 'in_minute');
                    $data['duration_diff'] = $duration_diff + $new_diff;
                }
            }
        }
        if ($bookingdata->status != $data['status']) {
            $activity_type = 'update_booking_status';
        }
        if ($data['status'] == 'cancelled') {
            $activity_type = 'cancel_booking';
        }
        $data['reason'] = isset($data['reason']) ? $data['reason'] : null;
        $old_status = $bookingdata->status;

        $user = AUth::user();

        $data['user'] = $user;

        $bookingdata->update($data);
        if ($old_status != $data['status']) {
            $bookingdata->old_status = $old_status;

            $activity_data = [
                'activity_type' => $activity_type,
                'booking_id' => $id,
                'booking' => $bookingdata,
            ];
            $this->sendNotification($activity_data);
        }
        if ($bookingdata->payment_id != null) {
            $data['payment_status'] = isset($data['payment_status']) ? $data['payment_status'] : 'pending';
            $paymentdata->update($data);

            if ($bookingdata->payment_id != null) {
                $data['payment_status'] = isset($data['payment_status']) ? $data['payment_status'] : 'pending';
                $paymentdata->update($data);

                $activity_data = [
                    'activity_type' => 'payment_message_status',
                    'payment_status' => $data['payment_status'],
                    'booking_id' => $id,
                    'booking' => $bookingdata,
                ];
                $this->sendNotification($activity_data);
            }
        }
        $message = __('messages.update_form', ['form' => __('messages.booking')]);

        if ($request->is('api/*')) {

            return comman_message_response($message);
        }

        return redirect(route('booking.index'))->withSuccess($message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (demoUserPermission()) {
            return redirect()->back()->withErrors(trans('messages.demo_permission_denied'));
        }
        $booking = Booking::find($id);

        $msg = __('messages.msg_fail_to_delete', ['item' => __('messages.booking')]);

        if ($booking != '') {
            Notification::whereJsonContains('data->id', $booking->id)->delete();
            $booking->delete();
            $msg = __('messages.msg_deleted', ['name' => __('messages.booking')]);
        }
        return comman_custom_response(['message' => $msg, 'status' => true]);
    }

    public function bookingAssignForm(Request $request)
    {

        $bookingdata = Booking::find($request->id);
        $pageTitle = __('messages.assign_form_title', ['form' => __('messages.booking')]);
        return view('booking.assigned_form', compact('bookingdata', 'pageTitle'));
    }

    public function bookingAssigned(Request $request)
    {
        $bookingdata = Booking::with('payment', 'handymanAdded')->find($request->id);

        $assigned_handyman_ids = [];
        $assigned_handyman = [];
        if ($bookingdata->handymanAdded()->count() > 0) {
            $assigned_handyman_ids = $bookingdata->handymanAdded()->pluck('handyman_id')->toArray();
            $bookingdata->handymanAdded()->delete();
            $assigned_handyman = $bookingdata->handymanAdded()->orderBy('id', 'desc')->withTrashed()->first();
            $message = __('messages.transfer_to_handyman');
            $activity_type = 'transfer_booking';
            $activity_data = [
                'activity_type' => $activity_type,
                'booking_id' => $bookingdata->id,
                'old_handyman_id' => $assigned_handyman ? $assigned_handyman : null,
                'booking' => $bookingdata,
            ];
            $this->sendNotification($activity_data);
        }

        if ($request->handyman_id != null) {
            $message = __('messages.assigned_to_handyman');
            $activity_type = 'assigned_booking';
        }
        $remove_notification_id = [];
        if ($request->handyman_id != null) {

            foreach ($request->handyman_id as $handyman) {

                $user = User::where('id', $handyman)->with('handymantype')->first();

                $assign_to_handyman = [
                    'booking_id' => $bookingdata->id,
                    'handyman_id' => $handyman,
                ];

                $remove_notification_id = removeArrayValue($assigned_handyman_ids, $handyman);
                $bookingdata->handymanAdded()->insert($assign_to_handyman);
            }
        }


        $bookingdata->status = 'accept';
        $bookingdata->save();
        $bookingdata = $bookingdata->with('handymanAdded', 'payment')->find($bookingdata->id);
        if ($bookingdata) {
            $this->addBookingCommission($bookingdata);
        }
        if ($activity_type == "assigned_booking") {
            $activity_data = [
                'activity_type' => $activity_type,
                'booking_id' => $bookingdata->id,
                'booking' => $bookingdata,
            ];
            $this->sendNotification($activity_data);
        }

        $message = __('messages.save_form', ['form' => __('messages.booking')]);
        if ($request->is('api/*')) {
            return comman_message_response($message);
        }

        return response()->json(['status' => true, 'event' => 'callback', 'message' => $message]);
    }

    public function action(Request $request)
    {
        $id = $request->id;
        $type = $request->type;
        $booking_data = Booking::withTrashed()->where('id', $id)->first();
        $msg = __('messages.not_found_entry', ['name' => __('messages.booking')]);
        if ($request->type === 'restore') {
            if ($booking_data != '') {
                $booking_data->restore();
                $msg = __('messages.msg_restored', ['name' => __('messages.booking')]);
            }
        }
        if ($request->type === 'forcedelete') {
            $booking_data->forceDelete();
            $msg = __('messages.msg_forcedelete', ['name' => __('messages.booking')]);
        }

        return comman_custom_response(['message' => $msg, 'status' => true]);
    }
    public function bookingDetails(Request $request, $id)
    {
        $auth_user = authSession();
        if ($id != auth()->user()->id && !auth()->user()->hasRole(['admin', 'demo_admin'])) {
            return redirect(route('home'))->withErrors(trans('messages.demo_permission_denied'));
        }
        $providerdata = User::with([
            'providerBooking' => function ($query) {
                $query->orderBy('updated_at', 'desc');
            }
        ])->where('user_type', 'provider')->where('id', $id)->first();

        $earningData = array();
        foreach ($providerdata->providerBooking as $booking) {

            $booking_id = $booking->id;
            $provider_name = optional($booking->provider)->display_name ?? '-';
            $provider_image = getSingleMedia(optional($booking->provider), 'profile_image', null);
            $provider_contact = optional($booking->provider)->contact_number ?? '-';
            $provider_email = optional($booking->provider)->email ?? '-';
            $amount = $booking->total_amount;
            $payment_status = optional($booking->payment)->payment_status ?? null;
            $start_at = $booking->start_at;
            $end_at = $booking->end_at;
            $earningData[] = [
                'provider_id' => $providerdata->id,
                'booking_id' => $booking->id,
                'provider_name' => $provider_name,
                'provider_image' => $provider_image,
                'provider_email' => $provider_email,
                'provider_contact' => $provider_contact,
                'amount' => $amount,
                'payment_status' => $payment_status,
                'start_at' => $start_at,
                'end_at' => $end_at,
            ];
        }

        if ($request->ajax()) {
            return Datatables::of($earningData)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '-';
                    $booking_id = $row['booking_id'];
                    $btn = "<a href=" . route('booking.show', $booking_id) . "><i class='fas fa-eye'></i></a>";
                    return $btn;
                })
                ->editColumn('provider_name', function ($row) {
                    return view('booking.provider', compact('row'));
                })
                ->editColumn('payment_status', function ($row) {
                    $payment_status = $row['payment_status'];

                    if ($payment_status !== null) {
                        $status = '<span class="text-center text-white badge bg-primary">' . str_replace('_', " ", ucfirst($payment_status)) . '</span>';
                    } else {
                        $status = '<span class="badge text-primary bg-primary-subtle">' . __('messages.pending') . '</span>';
                    }
                    return $status;
                })
                ->editColumn('start_at', function ($row) {
                    if (is_array($row)) {
                        $row = (object) $row;
                    }
                    $startAt = isset($row->start_at) ? $row->start_at : null;
                    if ($startAt !== null) {
                        $sitesetup = Setting::where('type', 'site-setup')->where('key', 'site-setup')->first();
                        $datetime = $sitesetup ? json_decode($sitesetup->value) : null;
                        $date = optional($datetime)->date_format && optional($datetime)->time_format
                            ? date(optional($datetime)->date_format, strtotime($startAt)) . '  ' . date(optional($datetime)->time_format, strtotime($startAt))
                            : $startAt;
                        return $date;
                    }
                    return null;
                })
                ->editColumn('end_at', function ($row) {
                    if (is_array($row)) {
                        $row = (object) $row;
                    }
                    $endAt = isset($row->end_at) ? $row->end_at : null;
                    if ($endAt !== null) {
                        $sitesetup = Setting::where('type', 'site-setup')->where('key', 'site-setup')->first();
                        $datetime = $sitesetup ? json_decode($sitesetup->value) : null;
                        $date = optional($datetime)->date_format && optional($datetime)->time_format
                            ? date(optional($datetime)->date_format, strtotime($endAt)) . '  ' . date(optional($datetime)->time_format, strtotime($endAt))
                            : $endAt;
                        return $date;
                    }
                    return null;
                })
                ->editColumn('amount', function ($row) {
                    return $row['amount'] ? getPriceFormat($row['amount']) : '-';
                })
                ->rawColumns(['action', 'payment_status', 'amount', 'check'])
                ->make(true);
        }
        if (empty($providerdata)) {
            $msg = __('messages.not_found_entry', ['name' => __('messages.provider')]);
            return redirect(route('provider.index'))->withError($msg);
        }
        $pageTitle = __('messages.bookings');
        return view('booking.details', compact('pageTitle', 'earningData', 'auth_user', 'providerdata'));
    }
    public function bookingstatus(Request $request, $id)
    {
        $tabpage = $request->tabpage;
        $auth_user = authSession();
        $user_id = $auth_user->id;
        $user_data = User::find($user_id);

        $bookingdata = Booking::with('handymanAdded', 'payment', 'bookingExtraCharge', 'bookingAddonService')
            ->myBooking()
            ->find($id);

        $payment = Payment::where('booking_id', $id)->orderBy('id', 'desc')->first() ?? null;

        $serviceconfig = Setting::getValueByKey('service-configurations', 'service-configurations');
        $advancePaymentPercentage = isset($serviceconfig->advance_paynment_percantage) ? $serviceconfig->advance_paynment_percantage : 0;
        $global_advance_payment = isset($serviceconfig->global_advance_payment) ? $serviceconfig->global_advance_payment : 0;

        $bookingdata->service->is_enable_advance_payment = $bookingdata->service->type == 'fixed'
            ? ($bookingdata->service->is_enable_advance_payment == 1
                ? $bookingdata->service->is_enable_advance_payment
                : $global_advance_payment)
            : 0;

        $bookingdata->service->advance_payment_amount = $bookingdata->service->type == 'fixed'
            ? ($bookingdata->service->advance_payment_amount > 0
                ? $bookingdata->service->advance_payment_amount
                : $advancePaymentPercentage)
            : 0;

        // ✅ Advance Payment Refund Message Logic
        $advancePaymentMade = false;
        $advancePaymentMessage = null;

        if ($bookingdata->status == 'cancelled') {
            $advanceAmount = $bookingdata->service->advance_payment_amount;
            $paymentAmount = $payment ? $payment->amount : 0;

            if ($advanceAmount > 0 && $paymentAmount >= $advanceAmount) {
                $advancePaymentMade = true;
                $advancePaymentMessage = __('messages.advance_payment_refund_message', ['days' => 7]);
            }
        }

        switch ($tabpage) {
            case 'info':
                $data = view('booking.' . $tabpage, compact(
                    'user_data',
                    'tabpage',
                    'auth_user',
                    'bookingdata',
                    'payment'
                ))->render();
                break;

            case 'status':
                $data = view('booking.' . $tabpage, compact(
                    'user_data',
                    'tabpage',
                    'auth_user',
                    'bookingdata',
                    'payment',
                    'advancePaymentMade',
                    'advancePaymentMessage'
                ))->render();
                break;

            default:
                $data = view('booking.' . $tabpage, compact('tabpage', 'auth_user', 'bookingdata'))->render();
                break;
        }

        return response()->json($data);
    }

    public function createPDF($id)
    {
        $data = AppSetting::take(1)->first();
        $bookingdata = Booking::with('handymanAdded', 'payment', 'bookingExtraCharge', 'bookingPackage', 'bookingAddonService')->myBooking()->find($id);
        $payment = Payment::where('booking_id', $id)->orderBy('id', 'desc')->first() ?? null;
        $serviceconfig = Setting::getValueByKey('service-configurations', 'service-configurations');
        $advancePaymentPercentage = isset($serviceconfig->advance_paynment_percantage) ? $serviceconfig->advance_paynment_percantage : 0;
        $global_advance_payment = isset($serviceconfig->global_advance_payment) ? $serviceconfig->global_advance_payment : 0;
        $bookingdata->service->is_enable_advance_payment = $bookingdata->service->type == 'fixed' ? ($bookingdata->service->is_enable_advance_payment == 1 ? $bookingdata->service->is_enable_advance_payment : $global_advance_payment) : 0;
        $bookingdata->service->advance_payment_amount = $bookingdata->service->type == 'fixed' ? ($bookingdata->service->advance_payment_amount > 0 ? $bookingdata->service->advance_payment_amount : $advancePaymentPercentage) : 0;
        $pdf = Pdf::loadView('booking.invoice_pdf', ['bookingdata' => $bookingdata, 'data' => $data, 'payment' => $payment]);
        return $pdf->download('invoice_' . $bookingdata->id . '.pdf');
    }

    public function updateStatus(Request $request)
    {

        switch ($request->type) {
            case 'payment':
                $data = Payment::where('booking_id', $request->bookingId)->update(['payment_status' => $request->status]);
                break;
            default:

                $data = Booking::find($request->bookingId)->update(['status' => $request->status]);
                break;
        }

        return comman_custom_response(['message' => 'Status Updated', 'status' => true]);
    }

    public function saveBookingRating(Request $request)
    {
        $rating_data = $request->all();
        $result = BookingRating::updateOrCreate(['id' => $request->id], $rating_data);

        $message = __('messages.update_form', ['form' => __('messages.rating')]);
        if ($result->wasRecentlyCreated) {
            $message = __('messages.save_form', ['form' => __('messages.rating')]);
        }

        return redirect()->back()->withSuccess($message);
    }
    public function getPaymentMethod(Request $request)
    {
        $data = $request->all();
        $data['datetime'] = now();

        $data['payment_status'] = 'failed';

        $payment_data = Payment::where('booking_id', $data['booking_id'])->first();

        if (!empty($payment_data)) {
            $payment_data->update($data);
        } else {
            $payment_data = Payment::create($data);
        }
        $sitesetup = Setting::where('type', 'site-setup')->where('key', 'site-setup')->first();
        $sitesetupdata = $sitesetup ? json_decode($sitesetup->value, true) : null;

        $country_id = $sitesetupdata['default_currency'] ?? null;
        $country = Country::find($country_id);

        $data['currency_code'] = $country ? $country->currency_code : "USD";

        switch ($data['payment_type']) {
            case 'stripe':
                $data['payment_geteway_data'] = getPaymentMethodkey($data['payment_type']);
                break;

            default:

                break;
        }

        return comman_custom_response($data);
    }


    public function createStripePayment(Request $request)
    {

        $data = $request->all();

        $checkout_session = getstripepayments($data);

        if (isset($checkout_session['message'])) {

            return comman_custom_response($checkout_session);
        } else {
            Payment::where('booking_id', $data['booking_id'])->update(['other_transaction_detail' => $checkout_session['id']]);

            return comman_custom_response($checkout_session);
        }
    }

    public function saveStripePayment(Request $request, $id)
    {

        $type = $request->type;

        $result = Payment::where('booking_id', $id)->first();

        $stripe_session_id = $result->other_transaction_detail;
        $payment_type = $result->payment_type;

        $session_object = getstripePaymnetId($stripe_session_id, $payment_type);

        if ($session_object['payment_intent'] !== '' && $session_object['payment_status'] == 'paid') {

            $result->txn_id = $session_object['payment_intent'];

            if ($type == 'advance_payment') {

                $result->payment_status = 'advanced_paid';
            } else {

                $result->payment_status = 'paid';
            }
        };

        $result->update();

        $booking = Booking::find($id);
        if (!empty($result) && $result->payment_status == 'advanced_paid') {
            $booking->advance_paid_amount = $result->total_amount;
            $booking->status = 'pending';
        }
        $booking->payment_id = $result->id;
        $booking->update();

        $activity_data = [
            'activity_type' => 'payment_message_status',
            'payment_status' => str_replace("_", " ", ucfirst($result->payment_status)),
            'booking_id' => $booking->id,
            'booking' => $booking,
        ];
        $this->sendNotification($activity_data);

        return redirect('/booking-list');
    }

    public function getEarningsBreakdown(Request $request)
    {
        $authRole = auth()->user()->roles->pluck('name')->first();
        $bookings = Booking::query()->where('status', '!=', 'cancelled')->with('commissionsdata', 'payment', 'handymanAdded');

        // Apply filters from the request
        if ($request->has('advanceFilter')) {
            $advanceFilter = $request->advanceFilter;

            // Regular filters
            $filters = [
                'customer_id' => 'customer_id',
                'service_id' => 'service_id',
                'provider_id' => 'provider_id',
                'handyman_id' => ['handymanAdded', 'handyman_id'],
                'booking_status' => 'status',
                'payment_status' => ['payment', 'payment_status'],
                'payment_type' => ['payment', 'payment_type'],
                'date_range' => null, // Special handling for date range
            ];

            foreach ($filters as $key => $filter) {
                if (!empty($advanceFilter[$key])) {
                    if ($key === 'date_range') {
                        $dates = explode(' to ', $advanceFilter['date_range']);
                        if (count($dates) === 2) {
                            $bookings->whereDate('date', '>=', $dates[0])
                                ->whereDate('date', '<=', $dates[1]);
                        } elseif (count($dates) === 1) {
                            $bookings->whereDate('date', $dates[0]);
                        }
                    } else {
                        if (is_array($advanceFilter[$key])) {
                            if (is_array($filter)) {
                                $bookings->whereHas($filter[0], function ($query) use ($filter, $advanceFilter, $key) {
                                    $query->whereIn($filter[1], $advanceFilter[$key]);
                                });
                            } else {
                                $bookings->whereIn($filter, $advanceFilter[$key]);
                            }
                        } else {
                            $bookings->where($filter, $advanceFilter[$key]);
                        }
                    }
                }
            }
        }

        // Apply role-based filtering
        switch ($authRole) {
            case 'admin':
            case 'demo_admin':
                $bookings = $bookings->get();
                break;

            case 'provider':
                $bookings = $bookings->where('provider_id', auth()->user()->id)->get();
                break;

            case 'handyman':
                $bookings = $bookings->whereHas('handymanAdded', function ($query) {
                    $query->where('handyman_id', auth()->user()->id);
                })->get();
                break;

            default:
                $bookings = collect();
                break;
        }

        // Initialize earnings array
        $earnings = [
            'admin' => 0,
            'provider' => 0,
            'handyman' => 0,
            'tax' => 0,
            'discount' => 0,
            'total' => 0,
            'totalAmountWithDiscount' => 0,
            'totalAmountWithoutDiscount' => 0,
        ];

        foreach ($bookings as $booking) {
            // Get the raw total amount before any adjustments
            $rawTotal = $booking->total_amount + ($booking->final_discount_amount ?? 0);

            // // Calculate actual total after discount
            $actualTotal = $booking->total_amount;

            // Handle commission distribution
            if ($booking->handymanAdded->count() > 0) {
                foreach ($booking->commissionsdata as $commission) {
                    switch ($commission->user_type) {
                        case 'admin':
                            $earnings['admin'] += $commission->commission_amount;
                            break;
                        case 'provider':
                            $earnings['provider'] += $commission->commission_amount;
                            break;
                        case 'handyman':
                            $earnings['handyman'] += $commission->commission_amount;
                            break;
                    }
                }
                // Track components
                $earnings['tax'] += $booking->final_total_tax ?? 0;
                $earnings['discount'] += $booking->final_discount_amount ?? 0;

                // Update totals - switched rawTotal and actualTotal
                $earnings['totalAmountWithDiscount'] += $rawTotal;
                $earnings['totalAmountWithoutDiscount'] += $actualTotal;
                $earnings['total'] += $rawTotal - ($booking->final_discount_amount ?? 0); // Changed to rawTotal to show amount before discount

            }
            // else {
            //     // If no handyman, provider gets the full amount after discount and tax
            //     $earnings['provider'] += ($actualTotal - ($booking->final_total_tax ?? 0));
            // }


        }

        // Round all values to 2 decimal places for consistency
        foreach ($earnings as $key => $value) {
            $earnings[$key] = getPriceFormat($value);
        }
        // dd($earnings['total']);

        return response()->json([
            'totalEarning' => $earnings['total'],
            'earnings' => $earnings,
            'userRole' => $authRole,
        ]);
    }
}
