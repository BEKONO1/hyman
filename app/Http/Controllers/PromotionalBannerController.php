<?php

namespace App\Http\Controllers;

use App\Models\BannerPayment;
use App\Models\Payment;
use App\Models\WalletHistory;
use Dflydev\DotAccessData\Data;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\PromotionalBanner;
use App\Models\Setting;
use App\Models\User;
use App\Models\PaymentGateway;
use App\Models\PaymentHistory;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Carbon\Carbon;
use Razorpay\Api\Api;
use Flutterwave\Flutterwave;
use App\Models\Wallet;
use App\Traits\NotificationTrait;
use Illuminate\Support\Facades\Log;

class PromotionalBannerController extends Controller
{
    use NotificationTrait;
    public function index(Request $request)
    {
        $filter = [
            'status' => $request->status,
        ];
        $auth_user = auth()->user();
        return view('promotionalbanner.index', compact('auth_user', 'filter'));
    }

    public function index_data(DataTables $datatable, Request $request)
    {
        $query = PromotionalBanner::with('provider')
            ->select('promotional_banners.*');

        if (!auth()->user()->hasAnyRole('admin', 'demo_admin')) {
            $query->where('promotional_banners.provider_id', auth()->id());
        }

        $filter = $request->filter;
        if (!empty($filter)) {
            if (!empty($filter['title'])) {
                $query->where('promotional_banners.title', 'like', '%' . $filter['title'] . '%');
            }
            if (!empty($filter['column_status'])) {
                $query->where('promotional_banners.status', $filter['column_status']);
            }
        }

        return $datatable->eloquent($query)
            ->filter(function ($query) use ($request) {
                if (!empty($request->search['value'])) {
                    $search = strtolower($request->search['value']);
                    $query->where(function ($q) use ($search) {
                        $q->whereRaw('LOWER(promotional_banners.id) LIKE ?', ["%{$search}%"])
                            ->orWhereRaw('LOWER(promotional_banners.updated_at) LIKE ?', ["%{$search}%"])
                            ->orWhereHas('provider', function ($q2) use ($search) {
                                $q2->whereRaw('LOWER(display_name) LIKE ?', ["%{$search}%"]);
                            })
                            ->orWhereRaw('LOWER(promotional_banners.status) LIKE ?', ["%{$search}%"]);
                    });
                }
            })
            ->editColumn('id', function ($row) {
                return '#' . $row->id;
            })
            ->editColumn('display_name', function ($row) {
                return view('provider.user', ['query' => $row->provider]); // Pass provider relation
            })
            ->orderColumn('display_name', function ($query, $order) {
                $query->orderBy('users.display_name', $order); // Use the existing join
            })
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="form-check-input select-table-row" id="datatable-row-' . $row->id . '" name="datatable_ids[]" value="' . $row->id . '" onclick="dataTableRowCheck(' . $row->id . ', this)">';
            })
            ->addColumn('banner', function ($row) {
                $bannerImage = getSingleMedia($row, 'banner_attachment');
                return '<img src="' . $bannerImage . '" class="avatar avatar-60 image-fluid" alt="Banner Image">';
            })
            ->addColumn('date_range', function ($row) {
                return date('M d, Y', strtotime($row->start_date)) . ' - ' . date('M d, Y', strtotime($row->end_date));
            })
            ->orderColumn('date_range', function ($query, $order) {
                $query->orderBy('promotional_banners.start_date', $order); // Sort by the `start_date` column
            })
            ->addColumn('price', function ($row) {
                return getPriceFormat($row->total_amount);
            })
            ->orderColumn('price', function ($query, $order) {
                $query->orderBy('promotional_banners.total_amount', $order);
            })
            ->addColumn('reason', function ($row) {
                return $row->reject_reason;
            })
            ->addColumn('payment_status', function ($row) {
                if ($row->payment_status === 'paid') {
                    return '<span class="badge bg-success-subtle text-success">' . __('messages.paid') . '</span>';
                } elseif ($row->payment_status === 'refunded') {
                    return '<span class="badge bg-info-subtle text-info">' . __('messages.refunded') . '</span>';
                } else {
                    return '<select class="form-control payment-status-dropdown" data-id="' . $row->id . '">
                                <option value="pending" ' . ($row->payment_status === 'pending' ? 'selected' : '') . '>' . __('messages.pending') . '</option>
                                <option value="paid" ' . ($row->payment_status === 'paid' ? 'selected' : '') . '>' . __('messages.paid') . '</option>
                            </select>';
                }
            })
            ->addColumn('status', function ($row) {
                switch ($row->status) {
                    case 'accepted':
                        return '<span class="badge badge-active text-success bg-success-subtle">' . __('messages.accepted') . '</span>';
                    case 'rejected':
                        return '<span class="badge badge-danger">' . __('messages.rejected') . '</span>';
                    default:
                        return '<span class="badge badge-active text-warning bg-warning-subtle">' . __('messages.pending') . '</span>';
                        // return '<span class="badge badge-warning">Pending</span>';
                }
            })
            ->orderColumn('status', function ($query, $order) {
                $query->orderBy('promotional_banners.status', $order);
            })
            ->addColumn('provider_id', function ($row) {
                // dd($row->provider_id);
                if (auth()->user()->hasAnyRole(['admin', 'demo_admin'])) {
                    return optional($row->provider_id)->username ?? 'N/A';
                }
                return $row->provider_id;
            })
            ->addColumn('action', function ($banner) {
                return view('promotionalbanner.action', compact('banner'))->render();
            })

            ->rawColumns(['check', 'banner', 'status', 'payment_status', 'action', 'provider_name'])
            ->make(true);
    }

    public function destroy($id)
    {
        $banner = PromotionalBanner::findOrFail($id);
        $banner->delete();

        return response()->json([
            'status' => true,
            'message' => __('messages.delete_success', ['form' => __('messages.promotional-banner')])
        ]);
    }

    public function create()
    {
        $setting = Setting::where('type', 'provider-banner')->first();
        $per_day_charge = $setting ? json_decode($setting->value)->promotion_price : 0;
        $paymentGateways = PaymentGateway::where('status', 1)
            ->whereNotIn('type', ['cash'])
            ->get();

        return view('promotionalbanner.create', compact('per_day_charge', 'paymentGateways'));
    }

    public function store(Request $request)
    {
        $provider_id = auth()->check() && auth()->user()->hasAnyRole(['admin', 'demo_admin'])
            ? $request->provider_id
            : auth()->id();

        if ($request->filled('banner_id')) {
            $banner = PromotionalBanner::find($request->banner_id);
            return $this->handlePaymentMethods($request, $banner);
        }

        $request->validate([
            'title' => 'nullable',
            'date_range' => 'required|string',
            'description' => 'nullable',
            'banner_type' => 'required|in:service,link',
            'banner_redirect_url' => 'required_if:banner_type,link|url|nullable',
            'service_id' => 'required_if:banner_type,service|exists:services,id',
        ]);

        try {
            DB::beginTransaction();

            $setting = Setting::where('type', 'provider-banner')->first();
            $per_day_charge = $setting ? json_decode($setting->value)->promotion_price : 0;

            [$start_date, $end_date] = array_map([Carbon::class, 'parse'], explode(' to ', $request->date_range));
            $duration = $start_date->diffInDays($end_date) + 1;
            $total_amount = $per_day_charge * $duration;

            if ($total_amount <= 0) {
                return response()->json(['status' => false, 'message' => 'The total amount must be greater than zero.'], 400);
            }

            $banner = PromotionalBanner::create([
                'provider_id' => (int) $provider_id,
                'title' => $request->title,
                'description' => $request->short_description,
                'banner_type' => $request->banner_type,
                'banner_redirect_url' => $request->banner_redirect_url,
                'service_id' => $request->service_id,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'duration' => $duration,
                'charges' => $per_day_charge,
                'total_amount' => $total_amount,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'status' => 'pending',
            ]);

            if ($request->hasFile('image')) {
                storeMediaFile($banner, $request->file('image'), 'banner_attachment');
            }

            $this->sendNotification([
                'activity_type' => 'promotional_banner',
                'banner_id' => $banner->id,
                'banner_title' => $banner->title ?? 'unknown',
                'provider_id' => $banner->provider_id,
                'provider_name' => optional($banner->provider)->first_name . ' ' . optional($banner->provider)->last_name,
            ]);

            return $this->handlePaymentMethods($request, $banner);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function handlePaymentMethods(Request $request, $banner)
    {
        $method = $request->payment_method;

        switch ($method) {
            case 'stripe':
                return $this->handleStripe($request, $banner);

            case 'razorPay':
                return $this->handleRazorPay($request, $banner);

            case 'flutterwave':
                return $this->handleFlutterwave($request, $banner);

            default:
                throw new \Exception('Invalid payment method');
        }
    }


    public function handleStripe(Request $request, $banner)
    {
        try {
            $gateway = PaymentGateway::where('type', 'stripe')->where('status', 1)->first();
            if (
                !$gateway ||
                !($gatewayData = json_decode($gateway->value, true)) ||
                empty($gatewayData['stripe_key']) ||
                empty($gatewayData['stripe_publickey'])
            ) {
                return redirect()->route('promotional-banner')->with('error', 'Stripe configuration error.');
            }

            \Stripe\Stripe::setApiKey($gatewayData['stripe_key']);

            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'usd',
                            'product_data' => ['name' => $request->short_description ?? '-'],
                            'unit_amount' => (int) ($banner['total_amount'] * 100),
                        ],
                        'quantity' => 1,
                    ]
                ],
                'mode' => 'payment',
                'success_url' => route('stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('promotional-banner'),
                'metadata' => ['banner_id' => $banner->id]
            ]);

            \DB::commit();

            return response()->json([
                'status' => true,
                'checkout_url' => $session->url
            ]);
        } catch (\Exception $e) {
            return redirect()->route('promotional-banner')->with('error', 'Payment failed. Please try again.');
        }
    }
    public function handleRazorPay(Request $request, $banner)
    {
        $gateway = PaymentGateway::where('type', 'razorpay')->where('status', 1)->first();
        $gatewayData = json_decode($gateway?->value ?? '{}', true);

        if (!$gateway || !isset($gatewayData['razor_key'], $gatewayData['razor_secret'])) {
            return response()->json(['error' => 'Razorpay configuration error.'], 400);
        }

        DB::commit();
        return response()->json([
            'key' => $gatewayData['razor_key'],
            'amount' => $banner->total_amount * 100,
            'currency' => 'INR',
            'name' => config('app.name'),
            'description' => $banner->title,
            'order_id' => null,
            'banner_id' => $banner->id,
            'prefill' => [
                'name' => auth()->user()->first_name ?? '',
                'email' => auth()->user()->email ?? '',
                'contact' => auth()->user()->contact_number ?? ''
            ],
            'success_url' => route('rozar.success'),
            'status' => true,
            'payment_method' => 'razorPay'
        ]);
    }

    public function handleFlutterwave(Request $request, $banner)
    {
        try {
            $gateway = PaymentGateway::where('type', 'flutterwave')->where('status', 1)->first();
            $gatewayData = json_decode($gateway?->value ?? '{}', true);
            $flutterwaveKey = $gatewayData['flutterwave_public'] ?? null;
            if (!$gateway || !$flutterwaveKey) {
                return response()->json(['error' => 'Flutterwave configuration error.'], 400);
            }

            $tx_ref = 'FLW-' . uniqid() . '-' . time();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'public_key' => $flutterwaveKey,
                    'tx_ref' => $tx_ref,
                    'amount' => $banner->total_amount,
                    'currency' => strtoupper('INR'),
                    'country' => 'NG',
                    'payment_options' => 'card',
                    'customer' => [
                        'email' => auth()->user()->email,
                        'name' => auth()->user()->name ?? 'Customer',
                        'phonenumber' => auth()->user()->phone ?? ''
                    ],
                    'meta' => ['banner_id' => $banner->id],
                    'customizations' => [
                        'title' => config('app.name', 'Subscription Payment'),
                        'description' => 'Payment for banner #' . $banner->id,
                        'logo' => asset('logo.png')
                    ],
                    'payment_method' => 'flutterwave',
                    'redirect_url' => route('flutter.success')
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Flutterwave Payment Error:', [
                'error' => $e->getMessage(),
                'user' => auth()->user()->email
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Payment initialization failed: ' . $e->getMessage()
            ], 400);
        }
    }


    public function handleSuccess(Request $request)
    {
        $session_id = $request->query('session_id'); // Get Stripe session ID (if applicable)
        $razorpay_payment_id = $request->query('razorpay_payment_id'); // Get Razorpay payment ID (if applicable)


        try {

            // Handle Stripe Payment
            if (!$session_id) {
                return redirect()->route('promotional-banner')->with('error', 'Invalid Stripe payment session.');
            }

            // Retrieve payment gateway settings for Stripe
            $gateway = PaymentGateway::where('type', 'stripe')->where('status', 1)->first();
            if (!$gateway) {
                return redirect()->route('promotional-banner')->with('error', 'Stripe payment gateway configuration not found.');
            }

            $gatewayData = json_decode($gateway->value, true);
            if (!isset($gatewayData['stripe_key'])) {
                return redirect()->route('promotional-banner')->with('error', 'Stripe API key missing.');
            }

            Stripe::setApiKey($gatewayData['stripe_key']);

            // Retrieve session details from Stripe
            $session = Session::retrieve($session_id);
            $paymentIntentId = $session->payment_intent;
            $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntentId);

            if ($paymentIntent->status !== 'succeeded') {
                return redirect()->route('promotional-banner')->with('error', 'Stripe payment failed or is still pending.');
            }
            // Get Banner ID from Stripe session metadata
            $bannerId = $session->metadata->banner_id ?? null;


            if (!$bannerId) {
                return redirect()->route('promotional-banner')->with('error', 'Banner ID is missing.');
            }

            // Find the banner
            $banner = PromotionalBanner::find($bannerId);
            if (!$banner) {
                return redirect()->route('promotional-banner')->with('error', 'Banner not found.');
            }

            // Update banner payment status
            $banner->update([
                'payment_status' => 'paid',
                'status' => 'pending',
            ]);

            // Save payment details in `banner_payments`
            BannerPayment::create([
                'provider_id' => $banner->provider_id,
                'banner_id' => $banner->id,
                'total_amount' => $banner->total_amount,
                'payment_type' => 'stripe',
                'txn_id' => $paymentIntentId,
                'payment_status' => 'paid',
                'datetime' => Carbon::now(),
            ]);

            return redirect()->route('promotional-banner')->with('success', 'Payment successful.');
        } catch (\Exception $e) {

            return redirect()->route('promotional-banner')->with('error', 'Error processing payment: ' . $e->getMessage());
        }
    }

    public function handlerozarSuccess(Request $request)
    {

        $gateway = PaymentGateway::where('type', 'razorpay')->where('status', 1)->first();
        if (!$gateway) {
            return response()->json(['error' => 'Razorpay payment gateway configuration not found.'], 400);
        }
        $gatewayData = json_decode($gateway->value, true);

        $paymentId = $request->input('razorpay_payment_id');
        $razorpayOrderId = session('razorpay_order_id');
        $plan_id = $request->input('plan_id');

        $razorpayKey = $gatewayData['razor_key'];
        $razorpaySecret = $gatewayData['razor_secret'];

        $api = new \Razorpay\Api\Api($razorpayKey, $razorpaySecret);
        $payment = $api->payment->fetch($paymentId);

        $banner = PromotionalBanner::findOrFail($request->banner_id);
        if ($payment->status === 'authorized') {
            $banner->update([
                'payment_status' => 'paid',
                'status' => 'pending',
            ]);
            BannerPayment::create([
                'provider_id' => $banner->provider_id,
                'banner_id' => $banner->id,
                'total_amount' => $banner->total_amount,
                'payment_type' => 'razorpay',
                'txn_id' => $request->razorpay_payment_id,
                'payment_status' => 'paid',
                'datetime' => Carbon::now(),
            ]);
            return redirect()->route('promotional-banner')->with('success', 'Payment successful.');
        } else if ($payment->status === 'authorized') {
            $banner = PromotionalBanner::findOrFail($request->banner_id);
            $banner->update([
                'payment_status' => 'paid',
                'status' => 'pending', // Adjust based on your business logic
            ]);
            BannerPayment::create([
                'provider_id' => $banner->provider_id,
                'banner_id' => $banner->id,
                'total_amount' => $banner->total_amount,
                'payment_type' => 'razorpay',
                'txn_id' => $request->razorpay_payment_id,
                'payment_status' => 'paid',
                'datetime' => Carbon::now(),
            ]);
            return redirect()->route('promotional-banner')->with('success', 'Payment authorized successfully.');
        } else {
            return redirect()->route('promotional-banner')->with('error', 'Payment verification failed.');
        }
    }


    protected function handleFlutterwaveSuccess(Request $request)
    {
        try {
            $transactionId = $request->input('transaction_id');
            $tx_ref = $request->input('tx_ref');
            $plan_id = $request->input('plan_id');

            $flutterwaveKey = GetpaymentMethod('flutterwave_secretkey');

            // Verify the transaction
            $response = Http::withToken($flutterwaveKey)
                ->get("https://api.flutterwave.com/v3/transactions/{$transactionId}/verify");

            $responseData = $response->json();

            if (
                $response->successful() &&
                isset($responseData['status']) &&
                $responseData['status'] === 'success' &&
                $responseData['data']['tx_ref'] === $tx_ref
            ) {

                return $this->handlePaymentSuccess(
                    $plan_id,
                    $responseData['data']['amount'],
                    'flutterwave',
                    $transactionId
                );
            }

            throw new \Exception('Payment verification failed');
        } catch (\Exception $e) {
            Log::error('Flutterwave Payment Error', [
                'error' => $e->getMessage(),
                'transaction_id' => $transactionId ?? null,
                'tx_ref' => $tx_ref ?? null
            ]);

            return redirect('/')->with('error', 'Payment verification failed: ' . $e->getMessage());
        }
    }

    public function flutterwavehandleCallback(Request $request)
    {
        $transactionID = $request->transaction_id;
        $data = Flutterwave::verifyTransaction($transactionID);

        if ($data['status'] === 'successful') {
            $banner = PromotionalBanner::findOrFail($data['meta']['banner_id']);
            $banner->update([
                'payment_status' => 'paid',
                'status' => 'pending',
            ]);
            BannerPayment::create([
                'provider_id' => $banner->provider_id,
                'banner_id' => $banner->id,
                'total_amount' => $banner->total_amount,
                'payment_type' => 'flutterwave',
                'txn_id' => $transactionID,
                'payment_status' => 'paid',
                'datetime' => Carbon::now(),
            ]);
            return redirect()->route('promotional-banner')->with('success', 'Payment successful');
        }

        return redirect()->route('promotional-banner')->with('error', 'Payment failed');
    }


    public function walletcheckBalance(Request $request)
    {
        $wallet = Wallet::where('user_id', auth()->id())->first();
        $sufficient_balance = $wallet && $wallet->amount >= $request->amount;

        return response()->json([
            'sufficient_balance' => $sufficient_balance,
            'current_balance' => $wallet ? $wallet->amount : 0
        ]);
    }

    public function walletprocessPayment(Request $request)
    {
        $wallet = Wallet::where('user_id', auth()->id())->first();

        if (!$wallet || $wallet->amount < $request->amount) {
            return response()->json([
                'status' => false,
                'message' => 'Insufficient balance'
            ], 400);
        }

        try {
            DB::transaction(function () use ($request, $wallet) {
                // Deduct from wallet
                $wallet->decrement('amount', $request->amount);

                // Update banner payment status
                $banner = PromotionalBanner::findOrFail($request->banner_id);
                $banner->update([
                    'payment_status' => 'paid',
                    'status' => 'pending'
                ]);
            });

            return response()->json([
                'status' => true,
                'message' => 'Payment successful'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Payment failed, please pay by cash'
            ], 500);
        }
    }

    public function updatePaymentStatus(Request $request, $id)
    {
        // dd($request);
        $banner = PromotionalBanner::findOrFail($id);
        $banner->update(['payment_status' => $request->payment_status]);

        return response()->json(['status' => true, 'message' => 'Payment status updated successfully.']);
    }
    public function paymentSuccess($bannerId)
    {
        $banner = PromotionalBanner::findOrFail($bannerId);
        return view('promotionalbanner.payment-success', compact('banner'));
    }

    public function paymentCancel($bannerId)
    {
        $banner = PromotionalBanner::findOrFail($bannerId);
        return view('promotionalbanner.payment-cancel', compact('banner'));
    }

    public function updateStatus(Request $request, $id)
    {
        $banner = PromotionalBanner::findOrFail($id);

        if ($request->status == 'accepted') {
            $banner->update([
                'status' => 'accepted',
                'reject_reason' => $request->reject_reason,
            ]);

            $notificationData = [
                'activity_type' => 'promotional_banner_accepted',
                'banner_id' => $banner->id,
                'banner_title' => $banner->title,
                'provider_id' => $banner->provider_id,
                'provider_name' => optional($banner->provider)->name,
            ];

            $this->sendNotification($notificationData);
        } elseif ($request->status == 'rejected') {
            DB::beginTransaction();
            try {
                $currentPaymentStatus = $banner->payment_status;

                $banner->update([
                    'status' => 'rejected',
                    'reject_reason' => $request->reject_reason,
                ]);

                // Send rejection notification
                $notificationData = [
                    'activity_type' => 'promotional_banner_rejected',
                    'banner_id' => $banner->id,
                    'banner_title' => $banner->title,
                    'provider_id' => $banner->provider_id,
                    'provider_name' => optional($banner->provider)->name,
                    'reject_reason' => $request->reject_reason,
                ];
                $this->sendNotification($notificationData);

                // Only refund if payment_status was 'paid'
                if ($currentPaymentStatus === 'paid') {
                    // Update payment status to 'refunded'
                    $banner->update(['payment_status' => 'refunded']);

                    // Process refund
                    $userId = $banner->provider_id;
                    $wallet = Wallet::firstOrCreate(
                        ['user_id' => $userId],
                        ['amount' => 0, 'status' => 1, 'title' => 'Provider Wallet']
                    );

                    $refundAmount = $banner->total_amount;
                    $wallet->increment('amount', $refundAmount);

                    // Send refund notification
                    $walletNotificationData = [
                        'activity_type' => 'wallet_refund_promotional_banner',
                        'banner_id' => $banner->id,
                        'banner_title' => $banner->title,
                        'refund_amount' => $refundAmount,
                        'provider_id' => $banner->provider_id,
                        'wallet' => $wallet,
                    ];
                    $this->sendNotification($walletNotificationData);
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('promotional-banner')->with('error', 'An error occurred while rejecting the banner.');
            }
        }

        return response()->json(['status' => true, 'message' => __('messages.update_form', ['form' => __('messages.status')])]);
    }


    public function action($id, $type = null)
    {
        $banner = PromotionalBanner::withTrashed()->find($id);

        if ($type === 'restore') {
            $banner->restore();
            $message = __('messages.restore_success', ['form' => __('messages.promotional-banner')]);
        } else if ($type === 'forcedelete') {
            $banner->forceDelete();
            $message = __('messages.forcedelete_success', ['form' => __('messages.promotional-banner')]);
        }

        return comman_custom_response(['message' => $message, 'status' => true]);
    }
    public function show($id)
    {
        $paymentGateways = PaymentGateway::where('status', 1)
            ->whereNotIn('type', ['cash'])
            ->get();
        if (auth()->user()->hasAnyRole(['admin', 'demo_admin'])) {
            $banner = PromotionalBanner::with(['service', 'provider'])->findOrFail($id);
        } else {
            $banner = PromotionalBanner::with(['service', 'provider'])
                ->where('provider_id', auth()->id())
                ->find($id);

            if (!$banner) {
                return redirect()->route('promotional-banner')->withErrors(trans('messages.demo_permission_denied'));
            }
        }


        $banner->banner_image = getSingleMedia($banner, 'banner_attachment');
        return view('promotionalbanner.show', compact('banner', 'paymentGateways'));
    }

    public function processWalletPayment($request, $provider_id, $total_amount, $duration, $per_day_charge, $start_date, $end_date)
    {
        $wallet = Wallet::where('user_id', $provider_id)->first();

        if (!$wallet || $wallet->amount < $total_amount) {
            return response()->json([
                'status' => false,
                'message' => 'Insufficient balance'
            ], 400);
        }

        try {
            DB::transaction(function () use ($request, $provider_id, $total_amount, $duration, $per_day_charge, $start_date, $end_date, $wallet) {
                // Deduct from wallet
                $wallet->decrement('amount', $total_amount);

                // Create the banner entry in the database
                $banner = PromotionalBanner::create([
                    'provider_id' => $provider_id,
                    'title' => $request->title,
                    'description' => $request->description,
                    'banner_type' => $request->banner_type,
                    'banner_redirect_url' => $request->banner_redirect_url,
                    'service_id' => $request->service_id,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'duration' => $duration,
                    'charges' => $per_day_charge,
                    'total_amount' => $total_amount,
                    'payment_method' => 'wallet',
                    'payment_status' => 'paid',
                    'status' => 'pending',
                ]);

                // Store the image file
                if ($request->hasFile('image')) {
                    storeMediaFile($banner, $request->file('image'), 'banner_attachment');
                }

                // Create the BannerPayment entry in the database
                BannerPayment::create([
                    'banner_id' => $banner->id,
                    'provider_id' => $provider_id,
                    'total_amount' => $total_amount,
                    'payment_type' => 'wallet',
                    'payment_status' => 'pending',
                    'txn_id' => null,
                    'datetime' => Carbon::now(),
                ]);
            });

            return response()->json([
                'status' => true,
                'message' => 'Payment successful and banner created.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Payment failed: ' . $e->getMessage()
            ], 500);
        }
    }
    public function bulk_action(Request $request)
    {
        // Validate required fields
        $request->validate([
            'rowIds' => 'required|string',
            'action_type' => 'required|string'
        ]);

        // Explode row IDs
        $ids = explode(',', $request->rowIds);
        if (empty($ids) || !is_array($ids)) {
            return response()->json(['status' => false, 'message' => 'Invalid IDs provided']);
        }

        // Fetch records with Trashed to handle restore/permanent delete
        $banners = PromotionalBanner::withTrashed()->whereIn('id', $ids)->get();

        if ($banners->isEmpty()) {
            return response()->json(['status' => false, 'message' => 'No records found for the provided IDs.']);
        }

        $actionType = $request->action_type;
        $message = 'Bulk action applied successfully';

        switch ($actionType) {
            case 'change-status':
                $banners->each(function ($banner) use ($request) {
                    $banner->update(['status' => $request->status]);
                });
                $message = 'Banner status updated successfully';
                break;

            case 'delete':
                PromotionalBanner::whereIn('id', $ids)->delete();
                $message = 'Banners moved to trash';
                break;

            case 'restore':
                PromotionalBanner::whereIn('id', $ids)->restore();
                $message = 'Banners restored successfully';
                break;

            case 'permanently-delete':
                PromotionalBanner::whereIn('id', $ids)->forceDelete();
                $message = 'Banners permanently deleted';
                break;

            default:
                return response()->json(['status' => false, 'message' => 'Invalid action type']);
        }

        return response()->json(['status' => true, 'message' => $message]);
    }
}
