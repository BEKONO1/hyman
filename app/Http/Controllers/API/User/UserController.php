<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Service;
use App\Http\Requests\UserRequest;
use Hash;
use App\Http\Resources\API\UserResource;
use App\Http\Resources\API\ServiceResource;
use Illuminate\Support\Facades\Password;
use App\Models\Booking;
use App\Models\Wallet;
use App\Models\HandymanRating;
use App\Http\Resources\API\HandymanRatingResource;
use App\Traits\NotificationTrait;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationEmail;
use App\Models\ProviderDocument;
use App\Http\Resources\API\DocumentResource;
use App\Models\Setting;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use App\Models\Documents;
use Illuminate\Support\Facades\DB;
use App\Models\ProviderZoneMapping;
use App\Traits\ZoneTrait;
use App\Models\ServiceZoneMapping;
use App\Models\ServiceZone;

class UserController extends Controller
{
    use NotificationTrait;
    use ZoneTrait;

    public function migrateFreshSeed()
    {

        Artisan::call('migrate:fresh', [
            '--force' => true,
            '--no-interaction' => true,
        ]);


        Artisan::call('db:seed', [
            '--force' => true,
            '--no-interaction' => true,
        ]);


        return response()->json(['data' => 'Database migrated and seeded successfully'], 200);
    }


    public function register(UserRequest $request)
    {

        $sitesetup = Setting::where('type', 'site-setup')->where('key', 'site-setup')->first();
        $admin = json_decode($sitesetup->value);
        date_default_timezone_set($admin->time_zone ?? 'UTC');
        $input = $request->all();

        $email = $input['email'];
        $username = $input['username'];
        $password = $input['password'];
        $input['display_name'] = $input['first_name'] . " " . $input['last_name'];
        $input['user_type'] = isset($input['user_type']) ? $input['user_type'] : 'user';
        $input['password'] = Hash::make($password);
        $input['contact_number'] = $input['contact_number'] ?? null;

        // Handle zone selection
        $service_zones = $request->input('service_zones', []);
        // $zone_id = $request->input('zone_id');

        if ($request->provider_id !== null && $request->id == null && default_earning_type() === 'subscription') {
            if (!empty($input['provider_id'] && $input['user_type'] === 'handyman')) {
                $exceed =  get_provider_plan_limit($input['provider_id'], 'handyman');
                if (!empty($exceed)) {
                    if ($exceed == 1) {
                        $message = __('messages.limit_exceed', ['name' => __('messages.handyman')]);
                    } else {
                        $message = __('messages.not_in_plan', ['name' => __('messages.handyman')]);
                    }
                    if ($request->is('api/*')) {
                        return comman_message_response($message);
                    } else {
                        return  redirect()->back()->withErrors($message);
                    }
                }
            }
        }
        if (in_array($input['user_type'], ['handyman', 'provider'])) {
            $input['status'] = isset($input['status']) ? $input['status'] : 0;
        }
        $user = User::withTrashed()
            ->where(function ($query) use ($email, $username) {
                $query->where('email', $email)->orWhere('username', $username);
            })
            ->first();

        if ($user) {
            if ($user->deleted_at == null) {

                $message = trans('messages.login_form');
                $response = [
                    'message' => $message,
                ];
                return comman_custom_response($response);
            }
            $message = trans('messages.deactivate');
            $response = [
                'message' => $message,
                'Isdeactivate' => 1,
            ];
            return comman_custom_response($response);
        } else {
            $user = User::create($input);


            if (
                $user->user_type === 'provider' &&
                $request->has('document_id') &&
                is_array($request->document_id)
            ) {
                $filesToAttach = [];

                foreach ($request->document_id as $index => $docId) {
                    $attachmentKey = 'provider_document_' . $index;
                    $file = $request->file($attachmentKey);

                    if (!empty($docId) && $file) {
                        $providerDoc = ProviderDocument::create([
                            'provider_id' => $user->id,
                            'document_id' => $docId,
                        ]);

                        // Store file
                        storeMediaFile($providerDoc, [$file], 'provider_document');
                    }
                }
            }
            if ($user->user_type == 'user' || $user->user_type == 'provider' || $user->user_type == 'handyman') {
                $id = $user->id;
                $user->assignRole($input['user_type']);

                // Attach zones if provided
                // if (!empty($service_zones)) {
                //     $user->serviceZones()->attach($service_zones);
                // }

                // Handle zone_id for provider
                if ($user->user_type === 'provider' && !empty($service_zones)) {

                    if (is_string($service_zones)) {
                        $zone_ids = array_filter(array_map('intval', array_map('trim', explode(',', $service_zones))));
                    } elseif (is_array($service_zones)) {
                        $zone_ids = array_filter(array_map('intval', $service_zones));
                    } else {
                        $zone_ids = [intval($service_zones)];
                    }



                    foreach ($zone_ids as $zid) {
                        if (!empty($zid)) {
                            ProviderZoneMapping::firstOrCreate([
                                'provider_id' => $user->id,
                                'zone_id' => $zid,
                            ]);
                        }
                    }
                }


                $verificationLink = route('verify', ['id' => $id]);
                Mail::to($user->email)->send(new VerificationEmail($verificationLink));
                $message = 'Email Verification link has been sent to your email. Please Check your inbox';
                $response = [
                    'message' => $message,
                    'data' => $user
                ];
                $activity_data = [
                    'activity_type' => 'register',
                    'user_id' => $user->id,
                    'user_type' => $user->user_type,
                    'user_email' => $user->email,
                    'user_name' => $user->display_name,
                ];
                $this->sendNotification($activity_data);
                return comman_custom_response($response);
            }
            $user->assignRole($input['user_type']);
        }

        if ($user->user_type == 'provider' || $user->user_type == 'handyman' || $user->user_type == 'user') {
            $wallet = array(
                'title' => $user->display_name,
                'user_id' => $user->id,
                'amount' => 0
            );
            $result = Wallet::create($wallet);
        }

        if (!empty($input['loginfrom']) && $input['loginfrom'] === 'vue-app') {
            if ($user->user_type != 'user') {
                $message = trans('messages.save_form', ['form' => $input['user_type']]);
                $response = [
                    'message' => $message,
                    'data' => $user
                ];


                return comman_custom_response($response);
            }
        }
        $input['api_token'] = $user->createToken('auth_token')->plainTextToken;

        unset($input['password']);
        $message = trans('messages.save_form', ['form' => $input['user_type']]);

        $user->api_token = $user->createToken('auth_token')->plainTextToken;
        $response = [
            'message' => $message,
            'data' => $user
        ];

        $activity_data = [
            'activity_type' => 'register',
            'user_id' => $user->id,
            'user_type' => $user->user_type,
            'user_email' => $user->email,
            'user_name' => $user->display_name,
        ];
        $this->sendNotification($activity_data);

        return comman_custom_response($response);
    }


    public function login(Request $request)
    {
        // dd($request->all());
        $Isactivate = request('Isactivate');
        if ($Isactivate == 1) {
            $user = User::withTrashed()
                ->where('email', request('email'))
                ->first();
            if ($user) {
                $user->restore();
            } else {
                $message = trans('auth.failed');
                return comman_message_response($message, 406);
            }
        }
        // dd(Auth::attempt(['email' => request('email'), 'password' => request('password')]));
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {

            $user = Auth::user();
            if ($user->status == 0) {
                Auth::logout();
            }
            if (request('loginfrom') === 'vue-app') {
                if ($user->user_type != 'user') {
                    $message = trans('auth.not_able_login');
                    return comman_message_response($message, 400);
                }
            }
            $user->save();

            $success = $user;
            $success['user_role'] = $user->getRoleNames();
            $success['api_token'] = $user->createToken('auth_token')->plainTextToken;
            $success['profile_image'] = getSingleMedia($user, 'profile_image', null);
            $is_verify_provider = false;

            if ($user->user_type == 'provider') {
                $is_verify_provider = verify_provider_document($user->id);
                $success['subscription'] = get_user_active_plan($user->id);

                if (is_any_plan_active($user->id) == 0 && $success['is_subscribe'] == 0) {
                    $success['subscription'] = user_last_plan($user->id);
                }
                $success['is_subscribe'] = is_subscribed_user($user->id);
                $success['provider_id'] = admin_id();
            }
            if ($user->user_type == 'provider' || $user->user_type == 'user') {
                $wallet = Wallet::where('user_id', $user->id)->first();
                if ($wallet == null) {
                    $wallet = array(
                        'title' => $user->display_name,
                        'user_id' => $user->id,
                        'amount' => 0
                    );
                    Wallet::create($wallet);
                }
            }
            $success['is_verify_provider'] = (int) $is_verify_provider;
            unset($success['media']);
            unset($user['roles']);

            if ($success->user_type == 'handyman' && $success->provider_id == null) {
                $message = trans('auth.assign_provider_msg');
                return comman_message_response($message, 406);
            }

            return response()->json(['data' => $success], 200);
        } else {
            $message = trans('auth.failed');
            return comman_message_response($message, 406);
        }
    }

    public function userList(Request $request)
    {
        $user_type = isset($request['user_type']) ? $request['user_type'] : 'handyman';
        $type = isset($request['type']) ? $request['type'] : '';
        $status = isset($request['status']) ? $request['status'] : 1;
        $all = isset($request['is_user_list_all']) ? $request['is_user_list_all'] : null;

        $user_list = User::orderBy('is_available', 'desc')
            ->orderBy('id', 'desc')
            ->where('user_type', $user_type);

        if (!empty($status)) {
            $user_list = $user_list->where('status', $status);
        }

        //Check if provider is subscribed when earning system is subscription
        if ($type == "filter_provider" || (auth()->user() !== null && !auth()->user()->hasRole(['admin', 'demo_admin']))) {
            if (default_earning_type() == "subscription" && $user_type == 'provider') {
                $user_list = $user_list->where('is_subscribe', 1);
            }
        }
        if (auth()->user() !== null && auth()->user()->hasRole(['admin', 'provider'])) {
            $user_list = $user_list->withTrashed();
            if ($request->has('keyword') && isset($request->keyword)) {
                $user_list = $user_list->where('display_name', 'like', '%' . $request->keyword . '%');
            }
            if ($user_type == 'handyman' && $status == 0) {
                $user_list = $user_list->orWhere('provider_id', NULL)->where('user_type', 'handyman');
            }
            if ($user_type == 'handyman' && $status == 1) {
                $user_list = $user_list->whereNotNull('provider_id')->where('user_type', 'handyman');
            }
        }
        if ($request->has('provider_id')) {
            $user_list = $user_list->where('provider_id', $request->provider_id)->withTrashed();
        }
        if ($request->has('city_id') && !empty($request->city_id)) {
            $user_list = $user_list->where('city_id', $request->city_id);
        }
        if (!empty($all) && $all == "all") {
            $user_list = User::orderBy('is_available', 'desc')
                ->orderBy('id', 'desc')
                ->whereIn('user_type', ['provider', 'handyman', 'user'])->where('status', 1);
        }
        if ($request->has('keyword') && isset($request->keyword)) {
            $user_list = $user_list->where('display_name', 'like', '%' . $request->keyword . '%');
        }
        if ($request->has('booking_id')) {
            $booking_data = Booking::find($request->booking_id);

            $service_address = $booking_data->handymanByAddress;
            if ($service_address != null) {
                $user_list = $user_list->where('service_address_id', $service_address->id);
            }
        }

        $per_page = config('constant.PER_PAGE_LIMIT');
        if ($request->has('per_page') && !empty($request->per_page)) {
            if (is_numeric($request->per_page)) {
                $per_page = $request->per_page;
            }
            if ($request->per_page === 'all') {
                $per_page = $user_list->count();
            }
        }

        $user_list = $user_list->with('providerService')->paginate($per_page);
        $allServices = $user_list->getCollection()->flatMap(function ($user) {
            return $user->providerService;
        });

        $min_price = $allServices->min('price');
        $max_price = $allServices->max('price');
        $items = UserResource::collection($user_list);

        $response = [
            'pagination' => [
                'total_items' => $items->total(),
                'per_page' => $items->perPage(),
                'currentPage' => $items->currentPage(),
                'totalPages' => $items->lastPage(),
                'from' => $items->firstItem(),
                'to' => $items->lastItem(),
                'next_page' => $items->nextPageUrl(),
                'previous_page' => $items->previousPageUrl(),
            ],
            'data' => $items,
            'min_price' => $min_price,
            'max_price' => $max_price,
        ];

        return comman_custom_response($response);
    }

    public function userDetail(Request $request)
    {
        $id = $request->id;

        $user = User::find($id);
        $message = __('messages.detail');
        if (empty($user)) {
            $message = __('messages.user_not_found');
            return comman_message_response($message, 400);
        }

        $service = [];
        $handyman_rating = [];
        $handyman = [];
        $profile_array = [];

        if ($user->user_type == 'provider') {
            $service = Service::where('provider_id', $id)
                ->where(function ($query) {
                    $query->where('service_request_status', 'approve')
                        ->orWhereNull('service_request_status');
                })
                ->where('status', 1)
                ->orderBy('id', 'desc');

            $lat = $request->latitude ?? session()->get('user_lat', null);
            $lng = $request->longitude ?? session()->get('user_lng', null);


            if ($lat && !empty($lat) && $lng && !empty($lng)) {


                $serviceZone = ServiceZone::all();

                if (count($serviceZone) > 0) {


                    try {

                        $allServices = $service->get(); // clone before pagination

                        $matchingServiceIds = [];

                        foreach ($allServices as $serv) {
                            $matchingZoneIds = $this->getNearbyZoneserviceIds($serv->id, $lat, $lng);
                            if (!empty($matchingZoneIds)) {
                                $matchingServiceIds[] = $serv->id;
                            }
                        }

                        if (!empty($matchingServiceIds)) {
                            $service = $service->whereIn('id', $matchingServiceIds);
                        } else {
                            $service = $service->whereRaw('1 = 0'); // force empty result
                        }
                    } catch (\Exception $e) {
                        $service = $service->whereRaw('1 = 0');
                    }
                }
            }

            $service = $service->paginate(10);

            $service = ServiceResource::collection($service);
            $handyman_rating = HandymanRating::where('handyman_id', $id)->orderBy('id', 'desc')->paginate(10);
            $handyman_rating = HandymanRatingResource::collection($handyman_rating);
            $handyman_staff = User::where('user_type', 'handyman')->where('provider_id', $id)->where('is_available', 1)->get();
            $handyman = UserResource::collection($handyman_staff);

            if (!empty($handyman_staff)) {
                foreach ($handyman_staff as $image) {
                    $profile_array[] = $image->login_type !== null ? $image->social_image : getSingleMedia($image, 'profile_image', null);
                }
            }
        }
        $user_detail = new UserResource($user);
        $document = ProviderDocument::where('provider_id', $id)->get();
        if ($user->user_type == 'handyman') {
            $handyman_rating = HandymanRating::where('handyman_id', $id)->orderBy('id', 'desc')->paginate(10);
            $handyman_rating = HandymanRatingResource::collection($handyman_rating);
        }

        $response = [
            'data' => $user_detail,
            'service' => $service,
            'handyman_rating_review' => $handyman_rating,
            'handyman_staff' => $handyman,
            'handyman_image' => $profile_array,
            'document_detail' => $document,
        ];

        return comman_custom_response($response);
    }

    public function changePassword(Request $request)
    {
        $user = User::where('id', \Auth::user()->id)->first();

        if ($user == "") {
            $message = __('messages.user_not_found');
            return comman_message_response($message, 406);
        }

        $hashedPassword = $user->password;

        $match = Hash::check($request->old_password, $hashedPassword);

        $same_exits = Hash::check($request->new_password, $hashedPassword);
        if ($match) {
            if ($same_exits) {
                $message = __('messages.old_new_pass_same');
                return comman_message_response($message, 406);
            }

            $user->fill([
                'password' => Hash::make($request->new_password)
            ])->save();

            $message = __('messages.password_change');
            return comman_message_response($message, 200);
        } else {
            $message = __('messages.valid_password');
            return comman_message_response($message);
        }
    }

    public function updateProfile(Request $request)
    {
        $user = \Auth::user();
        if ($request->has('id') && !empty($request->id)) {
            $user = User::where('id', $request->id)->first();
        }
        if ($user == null) {
            return comman_message_response(__('messages.no_record_found'), 400);
        }

        $data = $request->all();

        if (isset($data['first_name']) || isset($data['last_name'])) {
            $firstName = $data['first_name'] ?? $user->first_name;
            $lastName = $data['last_name'] ?? $user->last_name;
            $data['display_name'] = trim($firstName . ' ' . $lastName);
        }

        $why_choose_me = [
            'title' => $data['title'] ?? null,
            'about_description' => $data['description'] ?? null,
            'reason' => isset($data['reason']) && is_string($data['reason'])
                ? array_filter(json_decode($data['reason'], true), function ($value) {
                    return $value !== null;
                })
                : [],
        ];

        $data['why_choose_me'] = ($why_choose_me);

        $user->fill($data)->update();

        $provider_zone = ProviderZoneMapping::where('provider_id', $request->id)->pluck('zone_id')->toArray();

        // Ensure $request->service_zones is always an array
        $service_zones = $request->service_zones;
        if (is_string($service_zones)) {
            $decoded = json_decode($service_zones, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $service_zones = $decoded;
            } elseif (strpos($service_zones, ',') !== false) {
                $service_zones = explode(',', $service_zones);
            } else {
                $service_zones = [$service_zones];
            }
        }
        $service_zones = array_filter(array_map('intval', (array) $service_zones));

        $removeZone = array_diff($provider_zone, $service_zones);

        $services = Service::where('provider_id', $request->id)->pluck('id')->toArray();

        ServiceZoneMapping::whereIn('service_id', $services)->whereIn('zone_id', $removeZone)->delete();


        if ($user->user_type === 'provider' && isset($data['service_zones'])) {
            try {
                // Step 1: Normalize zone_ids input
                $zone_ids = $data['service_zones'];

                if (is_string($zone_ids)) {
                    $decoded = json_decode($zone_ids, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $zone_ids = $decoded;
                    } elseif (strpos($zone_ids, ',') !== false) {
                        $zone_ids = explode(',', $zone_ids);
                    } else {
                        $zone_ids = [$zone_ids];
                    }
                }

                $zone_ids = array_filter(array_map('intval', (array) $zone_ids));

                foreach ($zone_ids as $zone_id) {
                    ProviderZoneMapping::updateOrCreate(
                        ['provider_id' => $user->id, 'zone_id' => $zone_id],
                        ['updated_at' => now(), 'created_at' => now()]
                    );
                }

                ProviderZoneMapping::where('provider_id', $user->id)
                    ->whereNotIn('zone_id', $zone_ids)
                    ->delete();
            } catch (\Exception $e) {
                \Log::error('Zone sync failed for provider ' . $user->id . ': ' . $e->getMessage());
            }
        }

        if (isset($request->profile_image) && $request->profile_image != null) {
            $user->clearMediaCollection('profile_image');
            $user->addMediaFromRequest('profile_image')->toMediaCollection('profile_image');
        }

        $user_data = User::with(['zones' => function ($query) {
            $query->select('service_zones.id', 'service_zones.name')
                ->orderBy('service_zones.id', 'desc');
        }])->find($user->id);

        $message = __('messages.updated');

        if ($user->login_type !== null && $user->login_type !== 'mobile') {
            $user_data['profile_image'] = $user->social_image ? $user->social_image : getSingleMedia($user_data, 'profile_image', null);
        } else {
            $user_data['profile_image'] = $user->profile_image ? $user->profile_image : getSingleMedia($user_data, 'profile_image', null);
        }

        $user_data['user_role'] = $user->getRoleNames();
        $user_data['zones'] = $user_data->zones->map(function ($zone) {
            return [
                'id' => (int)$zone->id,
                'name' => $zone->name
            ];
        })->values()->toArray();
        unset($user_data['available_zones']);
        unset($user_data['roles']);
        unset($user_data['media']);

        $response = [
            'data' => $user_data,
            'message' => $message
        ];
        return comman_custom_response($response);
    }

    public function logout(Request $request)
    {
        $auth = Auth::user();

        if ($request->is('api*')) {

            if (!Auth::guard('sanctum')->check()) {
                return response()->json(['status' => false, 'message' => __('messages.user_not_logged_in')]);
            }

            $user = Auth::guard('sanctum')->user();

            $user->tokens()->delete();

            return comman_message_response('Logout successfully');
        }
        Auth::logout();

        return comman_message_response('Logout successfully');
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $response = Password::sendResetLink(
            $request->only('email')
        );

        return $response == Password::RESET_LINK_SENT
            ? response()->json(['message' => __($response), 'status' => true], 200)
            : response()->json(['message' => __($response), 'status' => false], 406);
    }

    public function socialLogin(Request $request)
    {
        $input = $request->all();

        if ($input['login_type'] === 'mobile') {
            $user_data = User::where('username', $input['username'])->where('login_type', 'mobile')->first();
        } else {
            $user_data = User::where('email', $input['email'])->first();
        }

        if ($user_data != null) {
            if (!isset($user_data->login_type) || $user_data->login_type  == '') {
                if ($request->login_type === 'google') {
                    $message = __('validation.unique', ['attribute' => 'email']);
                } else {
                    $message = __('validation.unique', ['attribute' => 'username']);
                }
                return comman_message_response($message, 400);
            }

            $user_data->update($input);

            $message = __('messages.login_success');
        } else {

            if ($request->login_type === 'google') {
                $key = 'email';
                $value = $request->email;
            } else {
                $key = 'username';
                $value = $request->username;
            }

            $trashed_user_data = User::where($key, $value)->whereNotNull('login_type')->withTrashed()->first();

            if ($trashed_user_data != null && $trashed_user_data->trashed()) {
                if ($request->login_type === 'google') {
                    $message = __('validation.unique', ['attribute' => 'email']);
                } else {
                    $message = __('validation.unique', ['attribute' => 'username']);
                }
                return comman_message_response($message, 400);
            }

            if ($request->login_type === 'mobile' && $user_data == null) {
                $otp_response = [
                    'status' => true,
                    'is_user_exist' => false
                ];
                return comman_custom_response($otp_response);
            }
            if ($request->login_type === 'mobile' && $user_data != null) {
                $otp_response = [
                    'status' => true,
                    'is_user_exist' => true
                ];
                return comman_custom_response($otp_response);
            }

            $password = !empty($input['accessToken']) ? $input['accessToken'] : $input['email'];

            $input['user_type']  = "user";
            $input['display_name'] = $input['first_name'] . " " . $input['last_name'];
            $input['password'] = Hash::make($password);
            $input['user_type'] = isset($input['user_type']) ? $input['user_type'] : 'user';
            $user = User::create($input);

            $user->assignRole($input['user_type']);

            $user_data = User::where('id', $user->id)->first();
            $message = trans('messages.save_form', ['form' => $input['user_type']]);
        }

        $user_data['api_token'] = $user_data->createToken('auth_token')->plainTextToken;
        if ($user_data->login_type !== null && $user_data->login_type !== 'mobile') {

            $user_data['profile_image'] = $user_data->social_image ? $user_data->social_image : getSingleMedia($user_data, 'profile_image', null);
        } else {

            $user_data['profile_image'] = $user_data->profile_image ? $user_data->profile_image : getSingleMedia($user_data, 'profile_image', null);
        }
        $response = [
            'status' => true,
            'message' => $message,
            'data' => $user_data
        ];
        return comman_custom_response($response);
    }

    public function userStatusUpdate(Request $request)
    {
        $user_id =  $request->id;
        $user = User::where('id', $user_id)->first();

        if ($user == "") {
            $message = __('messages.user_not_found');
            return comman_message_response($message, 400);
        }
        $user->status = $request->status;
        $user->save();

        $message = __('messages.update_form', ['form' => __('messages.status')]);
        $response = [
            'data' => new UserResource($user),
            'message' => $message
        ];
        return comman_custom_response($response);
    }
    public function contactUs(Request $request)
    {
        try {
            \Mail::send(
                'contactus.contact_email',
                array(
                    'first_name' => $request->get('first_name'),
                    'last_name' => $request->get('last_name'),
                    'email' => $request->get('email'),
                    'subject' => $request->get('subject'),
                    'phone_no' => $request->get('phone_no'),
                    'user_message' => $request->get('user_message'),
                ),
                function ($message) use ($request) {
                    $message->from($request->email);
                    $message->to(env('MAIL_FROM_ADDRESS'));
                }
            );
            $messagedata = __('messages.contact_us_greetings');
            return comman_message_response($messagedata);
        } catch (\Throwable $th) {
            $messagedata = __('messages.something_wrong');
            return comman_message_response($messagedata);
        }
    }
    public function handymanAvailable(Request $request)
    {
        $user_id =  $request->id;
        $user = User::where('id', $user_id)->first();

        if ($user == "") {
            $message = __('messages.user_not_found');
            return comman_message_response($message, 400);
        }
        $user->is_available = $request->is_available;
        $user->save();

        $message = __('messages.update_form', ['form' => __('messages.status')]);
        $response = [
            'data' => new UserResource($user),
            'message' => $message
        ];
        return comman_custom_response($response);
    }
    public function handymanReviewsList(Request $request)
    {
        $id = $request->handyman_id;
        $handyman_rating_data = HandymanRating::where('handyman_id', $id);

        $per_page = config('constant.PER_PAGE_LIMIT');

        if ($request->has('per_page') && !empty($request->per_page)) {
            if (is_numeric($request->per_page)) {
                $per_page = $request->per_page;
            }
            if ($request->per_page === 'all') {
                $per_page = $handyman_rating_data->count();
            }
        }

        $handyman_rating_data = $handyman_rating_data->orderBy('created_at', 'desc')->paginate($per_page);

        $items = HandymanRatingResource::collection($handyman_rating_data);
        $response = [
            'pagination' => [
                'total_items' => $items->total(),
                'per_page' => $items->perPage(),
                'currentPage' => $items->currentPage(),
                'totalPages' => $items->lastPage(),
                'from' => $items->firstItem(),
                'to' => $items->lastItem(),
                'next_page' => $items->nextPageUrl(),
                'previous_page' => $items->previousPageUrl(),
            ],
            'data' => $items,
        ];
        return comman_custom_response($response);
    }
    public function deleteUserAccount(Request $request)
    {
        $user_id = \Auth::user()->id;
        $user = User::where('id', $user_id)->first();
        if ($user == null) {
            $message = __('messages.user_not_found');
            __('messages.msg_fail_to_delete', ['item' => __('messages.user')]);
            return comman_message_response($message, 400);
        }
        $user->booking()->forceDelete();
        $user->payment()->forceDelete();
        $user->forceDelete();
        $message = __('messages.msg_deleted', ['name' => __('messages.user')]);
        return comman_message_response($message, 200);
    }
    public function deleteAccount(Request $request)
    {
        $user_id = \Auth::user()->id;
        $user = User::where('id', $user_id)->first();
        if ($user == null) {
            $message = __('messages.user_not_found');
            __('messages.msg_fail_to_delete', ['item' => __('messages.user')]);
            return comman_message_response($message, 400);
        }
        if ($user->user_type == 'provider') {
            if ($user->providerPendingBooking()->count() == 0) {
                $user->providerService()->forceDelete();
                $user->providerPendingBooking()->forceDelete();
                $provider_handyman = User::where('provider_id', $user_id)->get();
                if (count($provider_handyman) > 0) {
                    foreach ($provider_handyman as $key => $value) {
                        $value->provider_id = NULL;
                        $value->update();
                    }
                }
                $user->forceDelete();
            } else {
                $message = __('messages.pending_booking');
                return comman_message_response($message, 402);
            }
        } else {
            if ($user->handymanPendingBooking()->count() == 0) {
                $user->handymanPendingBooking()->forceDelete();
                $user->forceDelete();
            } else {
                $message = __('messages.pending_booking');
                return comman_message_response($message, 402);
            }
        }
        $message = __('messages.msg_deleted', ['name' => __('messages.user')]);
        return comman_message_response($message, 200);
    }
    public function addUser(UserRequest $request)
    {
        $input = $request->all();

        $password = $input['password'];
        $input['display_name'] = $input['first_name'] . " " . $input['last_name'];
        $input['user_type'] = isset($input['user_type']) ? $input['user_type'] : 'user';
        $input['password'] = Hash::make($password);

        if ($input['user_type'] === 'provider') {
        }
        $user = User::create($input);
        $user->assignRole($input['user_type']);
        $input['api_token'] = $user->createToken('auth_token')->plainTextToken;

        unset($input['password']);
        $message = trans('messages.save_form', ['form' => $input['user_type']]);
        $user->api_token = $user->createToken('auth_token')->plainTextToken;
        $response = [
            'message' => $message,
            'data' => $user
        ];
        return comman_custom_response($response);
    }
    public function editUser(UserRequest $request)
    {
        if ($request->has('id') && !empty($request->id)) {
            $user = User::where('id', $request->id)->first();
        }
        if ($user == null) {
            return comman_message_response(__('messages.no_record_found'), 400);
        }

        $user->fill($request->all())->update();

        if (isset($request->profile_image) && $request->profile_image != null) {
            $user->clearMediaCollection('profile_image');
            $user->addMediaFromRequest('profile_image')->toMediaCollection('profile_image');
        }

        $user_data = User::find($user->id);

        $message = __('messages.updated');
        $user_data['profile_image'] = getSingleMedia($user_data, 'profile_image', null);
        $user_data['user_role'] = $user->getRoleNames();
        unset($user_data['roles']);
        unset($user_data['media']);
        $response = [
            'data' => $user_data,
            'message' => $message
        ];
        return comman_custom_response($response);
    }
    public function userWalletBalance(Request $request)
    {
        $user = Auth::user();
        $amount = 0;
        $wallet = Wallet::where('user_id', $user->id)->first();
        if ($wallet !== null) {
            $amount = $wallet->amount;
        }
        $response = [
            'balance' => $amount,
        ];
        return comman_custom_response($response);
    }


    // user email verify
    public function verify(Request $request)
    {
        $email = $request->email;
        $user = User::where('email', $email)->first();
        if ($user === null) {
            $message = 'User not registered. Please check your email or register.';
            $response = [
                'message' => $message,
            ];
            return comman_custom_response($response);
        }
        if ($user->is_email_verified == 0) {
            $verificationLink = route('verify', ['id' => $user->id]);
            $response_data = Mail::to($user->email)->send(new VerificationEmail($verificationLink));
            $message = 'Email Verification link has been sent to your email. Please Check your inbox';
            $response = [
                'message' => $message,
                'is_email_verified' => $user->is_email_verified,
            ];
            return comman_custom_response($response);
        } else {
            $message = 'Email already verify!!!';
            $response = [
                'message' => $message,
                'is_email_verified' => $user->is_email_verified,
            ];


            return comman_custom_response($response);
        }
    }
    public function checkUsername(Request $request)
    {
        $field = $request->input('field');
        $value = $request->input('value');

        if ($field == 'phone_number') {

            $field = 'contact_number';
        }

        $allowedFields = ['username', 'email', 'contact_number', 'phone_number'];



        if (!in_array($field, $allowedFields)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid field.'], 400);
        }


        $exists = User::where($field, $value)->exists();

        if ($exists) {
            return response()->json(['status' => 'error']);
        } else {
            return response()->json(['status' => 'success']);
        }
    }
    public function SwitchLang(Request $request)
    {
        $locale = $request->input('locale');
        App::setLocale($locale);
        session()->put('locale', $locale);
        \Artisan::call('cache:clear');
        if (auth()->check()) {
            $user = auth()->user();
            $user->language_option = $locale;
            $user->save();
        }
        return response()->json([
            'status' => true,
            'message' => __('messages.Language_preference_updated'),
            'locale' => $locale,
        ], 200);
    }
}
