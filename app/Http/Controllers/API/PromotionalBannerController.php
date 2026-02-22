<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\PromotionalBannerResource;
use App\Models\BannerPayment;
use App\Models\Wallet;
use App\Models\WalletHistory;
use App\Traits\NotificationTrait;
use Illuminate\Http\Request;
use App\Models\PromotionalBanner;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Setting;

class PromotionalBannerController extends Controller
{
    use NotificationTrait;

    public function PromotionalBannerList(Request $request)
    {
        $provider_id = auth()->user()->id ?? $request->provider_id;
        if ($provider_id == null) {
            return response()->json([
                'status' => false,
                'message' => 'Provider ID is required.',
                'data' => [],
            ], 400);
        }
    
        // Fetch banners with related service name
        $banners = PromotionalBanner::where('promotional_banners.provider_id', $provider_id)
            ->leftJoin('services', 'promotional_banners.service_id', '=', 'services.id')
            ->select('promotional_banners.*', 'services.name as service_name')
            ->orderBy('promotional_banners.created_at', 'desc');
    
        // Apply filters
        if ($request->has('status') && $request->status !== 'all') {
            $banners->where('promotional_banners.status', $request->status);
        }
    
        if ($request->has('start_date')) {
            $banners->where('promotional_banners.start_date', '>=', $request->start_date);
        }
    
        if ($request->has('end_date')) {
            $banners->where('promotional_banners.end_date', '<=', $request->end_date);
        }
    
        
    
        // Paginate banners
        $perPage = config('constant.PER_PAGE_LIMIT', 10); // Default to 10 items per page
        if ($request->has('per_page') && $request->per_page === 'all') {
            $banners = $banners->get();
        } else {
            $banners = $banners->paginate($perPage);
        }
    
        $pagination = $banners instanceof \Illuminate\Pagination\LengthAwarePaginator ? [
            'total_items' => $banners->total(),
            'per_page' => $banners->perPage(),
            'currentPage' => $banners->currentPage(),
            'totalPages' => $banners->lastPage(),
            'next_page' => $banners->currentPage() < $banners->lastPage() ? $banners->currentPage() + 1 : null,
            'previous_page' => $banners->currentPage() > 1 ? $banners->currentPage() - 1 : null,
        ] : null;

        // Check if banners exist
        if ($banners->count() == 0) {
            return response()->json([
                'pagination' => $pagination,
                'data' => [],
            ], 200);
        }
        return response()->json([
            'status' => true,
            'message' => 'Banners retrieved successfully',
            'data' => PromotionalBannerResource::collection($banners),
            'pagination' => $pagination,
        ], 200);
    }


    public function apiSavePayment(Request $request)
    {
        $validated = $request->validate([
            'banner_id' => 'required|exists:promotional_banners,id',
            'payment_type' => 'required|string',
            'txn_id' => 'required|string',
            'payment_status' => 'required|string',
        ]);

        $banner = PromotionalBanner::findOrFail($request->banner_id);

        if($banner)
        { 
            // Update banner payment status
            $banner->update([
                'payment_status' => 'paid',
            ]);
        
            // Save payment details in `banner_payments`
            BannerPayment::create([
                'provider_id' => $banner->provider_id,
                'banner_id' => $banner->id,
                'total_amount' => $banner->total_amount,
                'payment_type' => $validated['payment_type'],
                'txn_id' => $validated['txn_id'],
                'payment_status' => $validated['payment_status'],
                'datetime' => Carbon::now(),
            ]);
        }
        return response()->json([
            'status' => true,
            'message' => __('messages.payment_successful')
        ]);
    }

    public function savebanner(Request $request)
    {
        $request->validate([
            'title' => 'nullable',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'description' => 'nullable|string',
            'banner_type' => 'required|in:service,link',
            'banner_redirect_url' => 'required_if:banner_type,link|url|nullable',
            'service_id' => 'required_if:banner_type,service|exists:services,id',
        ]);

        DB::beginTransaction();
        // Calculate charges
        $setting = Setting::where('type', 'provider-banner')->first();
        $per_day_charge = $setting ? json_decode($setting->value)->promotion_price : 0;
        $start_date = Carbon::parse($request->start_date);
        $end_date = Carbon::parse($request->end_date);
        $duration = $start_date->diffInDays($end_date) + 1;
        $total_amount = $per_day_charge * $duration;

        $provider_id = auth()->user()->id;
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
            'payment_status' => 'pending',
            'status' => 'pending'
        ]);
        $attachments = storeAttachments($request, 'banner_attachment', $banner);
        DB::commit();

        $notificationData = [
            'activity_type' => 'promotional_banner',
            'banner_id' => $banner->id,
            'banner_title' => $banner->title,
            'provider_id' => $banner->provider_id,
            'provider_name' => optional($banner->provider)->name,
        ];

        $this->sendNotification($notificationData);
        return response()->json([
            'status' => true,
            'message' => __('messages.banner_saved_successfully'),
            'banner' => $banner
        ]);
    }

    public function updateStatus(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer|exists:promotional_banners,id',
            'status' => 'required|in:accepted,rejected',
            'reject_reason' => 'required_if:status,rejected|string|max:255',
        ]);
        if (!in_array(auth()->user()->user_type, ['admin', 'demo_admin'])) {
            return response()->json([
                'status' => false,
                'message' => __('messages.unauthorized_access'),
            ], 403);
        }
        $banner = PromotionalBanner::find($validated['id']);
        if (!$banner) {
            return response()->json([
                'status' => false,
                'message' => __('messages.promotional_banner_not_found'),
            ], 404);
        }
        $updateData = [
            'status' => $validated['status'],
            'reject_reason' => $validated['status'] === 'rejected' ? $validated['reject_reason'] : null,
        ];
        if ($banner->status === $validated['status']) {
            return response()->json([
                'status' => false,
                'message' => __('messages.status_already_set', ['status' => $validated['status']]),
            ], 400);
        }
        DB::beginTransaction();

        try {
            if ($validated['status'] === 'accepted') {
                $banner->update([
                    'status' => 'accepted',
                    'reject_reason' => null,
                ]);

                $notificationData = [
                    'activity_type' => 'promotional_banner_accepted',
                    'banner_id' => $banner->id,
                    'banner_title' => $banner->title,
                    'provider_id' => $banner->provider_id,
                    'provider_name' => optional($banner->provider)->name,
                ];

                $this->sendNotification($notificationData);
            }

            if ($validated['status'] === 'rejected') {
                $banner->update([
                    'status' => 'rejected',
                    'reject_reason' => $validated['reject_reason'],
                ]);

                $notificationData = [
                    'activity_type' => 'promotional_banner_rejected',
                    'banner_id' => $banner->id,
                    'banner_title' => $banner->title,
                    'provider_id' => $banner->provider_id,
                    'provider_name' => optional($banner->provider)->name,
                    'reject_reason' => $validated['reject_reason'],
                ];
    
                $this->sendNotification($notificationData);

                $userId = $banner->provider_id;
                if (!$userId) {
                    return response()->json([
                        'status' => false,
                        'message' => __('messages.no_provider_assigned'),
                    ], 400);
                }

                $wallet = Wallet::firstOrCreate(
                    ['user_id' => $userId],
                    ['amount' => 0, 'status' => 1, 'title' => 'Provider Wallet']
                );

                $refundAmount = $banner->total_amount;

                $wallet->amount += $refundAmount;
                $wallet->save();

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

            // Return success response
            return response()->json([
                'status' => true,
                'message' => __('messages.update_form'),
                'data' => $banner,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            // Catch any other general exceptions and return a generic error message
            return response()->json([
                'status' => false,
                'message' => __('messages.error_occurred'),
            ], 500);
        }
    }

    public function destroyBanner(Request $request, $id)
    {
        $banner = PromotionalBanner::find($id);

        if (!$banner) {
            return response()->json([
                'status' => false,
                'message' => __('messages.not_found_banner'),
            ], 404);
        }

        if ($banner->provider_id !== auth()->user()->id) {
            return response()->json([
                'status' => false,
                'message' => __('messages.unauthorized_action'),
            ], 403);
        }

        $banner->delete();

        return response()->json([
            'status' => true,
            'message' => __('messages.banner_deleted_successfully'),
        ]);
    }

    public function promtionalBannerList()
{
    try {
        $banners = PromotionalBanner::with(['provider', 'service'])
            ->where('status', 'accepted')
            ->where('payment_status', 'paid')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now()->startOfDay()) // Changed to include full day
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => PromotionalBannerResource::collection($banners)
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
}
