<?php

use \Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Google\Client as Google_Client;

function authSession($force = false)
{
    $session = new \App\Models\User;
    if ($force) {
        $user = \Auth::user()->getRoleNames();
        \Session::put('auth_user', $user);
        $session = \Session::get('auth_user');
        return $session;
    }
    if (\Session::has('auth_user')) {
        $session = \Session::get('auth_user');
    } else {
        $user = \Auth::user();
        \Session::put('auth_user', $user);
        $session = \Session::get('auth_user');
    }
    return $session;
}

function comman_message_response($message, $status_code = 200)
{
    return response()->json(['message' => $message], $status_code);
}

function comman_custom_response($response, $status_code = 200)
{
    return response()->json($response, $status_code);
}

function checkMenuRoleAndPermission($menu)
{
    if (\Auth::check()) {
        if ($menu->data('role') == null && auth()->user()->hasRole('admin')) {
            return true;
        }

        if ($menu->data('permission') == null && $menu->data('role') == null) {
            return true;
        }

        if ($menu->data('role') != null) {
            if (is_array($menu->data('role'))) {
                if (auth()->user()->hasAnyRole($menu->data('role'))) {
                    return true;
                }
            }
            if (auth()->user()->hasAnyRole($menu->data('role'))) {
                return true;
            }
        }

        if ($menu->data('permission') != null) {
            if (is_array($menu->data('permission'))) {
                if (auth()->user()->hasAnyPermission($menu->data('permission'))) {
                    return true;
                }
            }
            if (auth()->user()->can($menu->data('permission'))) {
                return true;
            }
        }
    }

    return false;
}

function checkRolePermission($role, $permission)
{
    try {
        if ($role->hasPermissionTo($permission)) {
            return true;
        }
        return false;
    } catch (Exception $e) {
        return false;
    }
}

function demoUserPermission()
{
    if (\Auth::user()->hasAnyRole(['demo_admin'])) {
        return false; // Changed from true to false to allow demo admin to perform actions
    } else {
        return false;
    }
}

function getSingleMedia($model, $collection = 'profile_image', $skip = true)
{
    if (!\Auth::check() && $skip) {
        return asset('images/user/user.png');
    }
    $media = null;
    if ($model !== null) {
        $media = $model->getFirstMedia($collection);
    }

    if (getFileExistsCheck($media)) {
        return $media->getFullUrl();
    } else {

        switch ($collection) {
            case 'image_icon':
                $media = asset('images/user/user.png');
                break;
            case 'profile_image':
                $media = asset('images/user/user.png');
                break;
            case 'service_attachment':
                $media = asset('images/default.png');
                break;
            case 'banner_attachment':
                $media = asset('images/default.png');
                break;
            case 'site_logo':
                $media = asset('images/logo.png');
                break;
            case 'site_favicon':
                $media = asset('images/favicon.png');
                break;
            case 'app_image':
                $media = asset('images/frontend/mb-serv-1.png');
                break;
            case 'app_image_full':
                $media = asset('images/frontend/mb-serv-full.png');
                break;
            case 'footer_logo':
                $media = asset('landing-images/logo/logo.png');
                break;
            case 'logo':
                $media = asset('images/logo.png');
                break;
            case 'favicon':
                $media = asset('images/favicon.png');
                break;
            case 'loader':
                $media = asset('images/loader.gif');
                break;
            case 'helpdesk_attachment':
                $media = asset('images/default.png');
                break;
            case 'helpdesk_activity_attachment':
                $media = asset('images/default.png');
                break;
            default:
                $media = asset('images/default.png');
                break;
        }
        return $media;
    }
}

function getFileExistsCheck($media)
{
    $mediaCondition = false;

    if ($media) {
        if ($media->disk == 'public') {
            $mediaCondition = file_exists($media->getPath());
        } else {
            $mediaCondition = \Storage::disk($media->disk)->exists($media->getPath());
        }
    }

    return $mediaCondition;
}

function storeMediaFile($model, $file, $name)
{
    if ($file) {
        if (!in_array($name, ['service_attachment', 'package_attachment', 'blog_attachment', 'section5_attachment', 'helpdesk_attachment', 'helpdesk_activity_attachment', 'banner_attachment'])) {
            $model->clearMediaCollection($name);
        }
        if (is_array($file)) {
            foreach ($file as $key => $value) {
                $model->addMedia($value)->toMediaCollection($name);
            }
        } else {
            $model->addMedia($file)->toMediaCollection($name);
        }
    }

    return true;
}

function storeAttachments($request, $attachmentPrefix, $data)
{

    $file = [];

    if ($request->is('api/*')) {
        if ($request->has('attachment_count')) {
            for ($i = 0; $i < $request->attachment_count; $i++) {
                $attachment = "{$attachmentPrefix}_{$i}";
                if ($request->$attachment != null) {
                    $file[] = $request->$attachment;
                }
            }
            storeMediaFile($data, $file, $attachmentPrefix);
        }
    } else {

        if ($request->hasFile($attachmentPrefix)) {

            storeMediaFile($data, $request->file($attachmentPrefix), $attachmentPrefix);
        }
    }
}

function getAttachments($attchments)
{
    $files = [];
    if (count($attchments) > 0) {
        foreach ($attchments as $attchment) {
            if (getFileExistsCheck($attchment)) {
                array_push($files, $attchment->getFullUrl());
            }
        }
    }

    return $files;
}

function getAttachmentArray($attchments)
{
    $files = [];
    if (count($attchments) > 0) {
        foreach ($attchments as $attchment) {
            if (getFileExistsCheck($attchment)) {
                $file = [
                    'id' => $attchment->id,
                    'url' => $attchment->getFullUrl()
                ];
                array_push($files, $file);
            }
        }
    }

    return $files;
}

function getMediaFileExit($model, $collection = 'profile_image')
{
    if ($model == null) {
        return asset('images/user/user.png');;
    }

    $media = $model->getFirstMedia($collection);

    return getFileExistsCheck($media);
}

function saveBookingActivity($data)
{
    $admin = \App\Models\AppSetting::first();
    date_default_timezone_set($admin->time_zone ?? 'UTC');
    $data['datetime'] = date('Y-m-d H:i:s');
    $role = auth()->user()->user_type;
    switch ($data['activity_type']) {
        case "add_booking":

            $customer_name = $data['booking']->customer->display_name;

            $data['activity_message'] = __('messages.booking_added', ['name' => $customer_name]);
            $data['activity_type'] = __('messages.add_booking');
            $activity_data = [
                'service_id' => $data['booking']->service_id,
                'service_name' => isset($data['booking']->service) ? $data['booking']->service->name : '',
                'customer_id' => $data['booking']->customer_id,
                'customer_name' => isset($data['booking']->customer) ? $data['booking']->customer->display_name : '',
                'provider_id' => $data['booking']->provider_id,
                'provider_name' => isset($data['booking']->provider) ? $data['booking']->provider->display_name : '',
            ];
            $sendTo = ['admin', 'provider', 'demo_admin'];
            break;
        case "assigned_booking":
            $assigned_handyman = handymanNames($data['booking']->handymanAdded);
            $data['activity_message'] = __('messages.booking_assigned', ['name' => $assigned_handyman]);
            $data['activity_type'] = __('messages.assigned_booking');

            $activity_data = [
                'handyman_id' => $data['booking']->handymanAdded->pluck('handyman_id'),
                'handyman_name' => $data['booking']->handymanAdded,
            ];
            $sendTo = ['handyman', 'user', 'admin', 'demo_admin'];
            break;

        case "transfer_booking":
            $assigned_handyman = handymanNames($data['booking']->handymanAdded);

            $data['activity_type'] = __('messages.transfer_booking');
            $data['activity_message'] = __('messages.booking_transfer', ['name' => $assigned_handyman]);
            $activity_data = [
                'handyman_id' => $data['booking']->handymanAdded->pluck('handyman_id'),
                'handyman_name' => $data['booking']->handymanAdded,
            ];
            $sendTo = ['handyman'];
            break;

        case "update_booking_status":

            $status = \App\Models\BookingStatus::bookingStatus($data['booking']->status);
            $old_status = \App\Models\BookingStatus::bookingStatus($data['booking']->old_status);
            $data['activity_type'] = __('messages.update_booking_status');
            $data['activity_message'] = __('messages.booking_status_update', ['from' => $old_status, 'to' => $status]);
            $activity_data = [
                'reason' => $data['booking']->reason,
                'status' => $data['booking']->status,
                'status_label' => $status,
                'old_status' => $data['booking']->old_status,
                'old_status_label' => $old_status,
            ];

            $sendTo = removeArrayValue(['admin', 'provider', 'handyman', 'user', 'demo_admin'], $role);
            break;
        case "cancel_booking":
            $status = \App\Models\BookingStatus::bookingStatus($data['booking']->status);
            $old_status = \App\Models\BookingStatus::bookingStatus($data['booking']->old_status);
            $data['activity_type'] = __('messages.cancel_booking');

            $data['activity_message'] = __('messages.cancel_booking_message', ['name' => $role]);


            $activity_data = [
                'reason' => $data['booking']->reason,
                'status' => $data['booking']->status,
                'status_label' => \App\Models\BookingStatus::bookingStatus($data['booking']->status),
            ];
            $sendTo = removeArrayValue(['admin', 'provider', 'handyman', 'user', 'demo_admin'], $role);
            break;
        case "payment_message_status":
            $data['activity_type'] = __('messages.payment_message_status');

            $data['activity_message'] = __('messages.payment_message', ['status' => $data['payment_status']]);

            $activity_data = [
                'activity_type' => $data['activity_type'],
                'payment_status' => $data['payment_status'],
                'booking_id' => $data['booking_id'],
            ];
            $sendTo = ['user'];
            break;

        default:
            $activity_data = [];
            break;
    }
    $data['activity_data'] = json_encode($activity_data);
    \App\Models\BookingActivity::create($data);
    $notification_data = [
        'id'   => $data['booking']->id,
        'type' => $data['activity_type'],
        'subject' => $data['activity_type'],
        'message' => $data['activity_message'],
        "ios_badgeType" => "Increase",
        "ios_badgeCount" => 1,
        "notification-type" => 'booking'
    ];
    foreach ($sendTo as $to) {
        switch ($to) {
            case 'admin':
                $user = \App\Models\User::getUserByKeyValue('user_type', 'admin');
                break;

            case 'demo_admin':
                $user = \App\Models\User::getUserByKeyValue('user_type', 'demo_admin');
                break;

            case 'provider':
                $user = \App\Models\User::getUserByKeyValue('id', $data['booking']->provider_id);
                break;

            case 'handyman':
                $handymans = $data['booking']->handymanAdded->pluck('handyman_id');
                foreach ($handymans as $id) {
                    $user = \App\Models\User::getUserByKeyValue('id', $id);
                    if ($user->user_type != 'provider') {
                        sendNotification('provider', $user, $notification_data);
                    }
                }
                break;

            case 'user':
                $user = \App\Models\User::getUserByKeyValue('id', $data['booking']->customer_id);
                break;
        }
        if ($to != 'handyman') {
            sendNotification($to, $user, $notification_data);
        }
    }
}

function formatOffset($offset)
{
    $hours = $offset / 3600;
    $remainder = $offset % 3600;
    $sign = $hours > 0 ? '+' : '-';
    $hour = (int) abs($hours);
    $minutes = (int) abs($remainder / 60);

    if ($hour == 0 and $minutes == 0) {
        $sign = ' ';
    }
    return 'GMT' . $sign . str_pad($hour, 2, '0', STR_PAD_LEFT)
        . ':' . str_pad($minutes, 2, '0');
}

function settingSession($type = 'get')
{
    if (\Session::get('setting_data') == '') {
        $type = 'set';
    }
    switch ($type) {
        case "set":
            $settings = \App\Models\AppSetting::first();
            \Session::put('setting_data', $settings);
            break;
        default:
            break;
    }
    return \Session::get('setting_data');
}

function imageSession($type = 'get')
{
    if (\Session::get('images_data') == '') {
        $type = 'set';
    }
    switch ($type) {
        case "set":
            $settings = \App\Models\Setting::where('type', 'theme-setup')->where('key', 'theme-setup')->first();
            \Session::put('images_data', $settings);
            break;
        default:
            break;
    }
    return \Session::get('images_data');
}

function sitesetupSession($type = 'get')
{
    if (\Session::get('setup_data') == '') {
        $type = 'set';
    }
    switch ($type) {
        case "set":
            $sitesetup = App\Models\Setting::where('type', 'site-setup')->where('key', 'site-setup')->first();
            $settings = $sitesetup ? json_decode($sitesetup->value) : null;
            if (!empty($settings)) {
                \Session::put('setup_data', $settings);
            }

            break;
        default:
            break;
    }
    return \Session::get('setup_data');
}

function envChanges($type, $value)
{
    $path = base_path('.env');

    $checkType = $type . '="';
    if (strpos($value, ' ') || strpos(file_get_contents($path), $checkType) || preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $value)) {
        $value = '"' . $value . '"';
    }

    $value = str_replace('\\', '\\\\', $value);

    if (file_exists($path)) {
        $typeValue = env($type);

        if (strpos(env($type), ' ') || strpos(file_get_contents($path), $checkType)) {
            $typeValue = '"' . env($type) . '"';
        }

        file_put_contents($path, str_replace(
            $type . '=' . $typeValue,
            $type . '=' . $value,
            file_get_contents($path)
        ));

        $onesignal = collect(config('constant.ONESIGNAL'))->keys();

        $checkArray = \Arr::collapse([$onesignal, ['DEFAULT_LANGUAGE']]);


        if (in_array($type, $checkArray)) {
            if (env($type) === null) {
                file_put_contents($path, "\n" . $type . '=' . $value, FILE_APPEND);
            }
        }
    }
}

function getPriceFormat($price)
{
    $price = (float)$price;

    $setting = App\Models\Setting::getValueByKey('site-setup', 'site-setup');
    // $sitesetup = App\Models\Setting::where('type','site-setup')->where('key', 'site-setup')->first();
    // $sitesetupdata = $sitesetup ? json_decode($sitesetup->value) : null;
    $currencyId = $setting ? $setting->default_currency : "231";
    $currency_position = $setting ? $setting->currency_position : "left";
    $afterdecimalpoint = $setting ? $setting->digitafter_decimal_point : "2";
    $country = App\Models\Country::find($currencyId);

    $symbol = '$';
    if (!empty($country)) {
        $symbol = $country->symbol;
    }

    $position = 'left';
    if (!empty($currency_position)) {
        $position = $currency_position;
    }

    if ($position == 'left') {
        $price = $symbol . "" . number_format((float)$price, $afterdecimalpoint, '.', '');
    } else {
        $price = number_format((float)$price, $afterdecimalpoint, '.', '') . "" . $symbol;
    }

    return $price;
}

function currency_data()
{

    $setting = App\Models\Setting::getValueByKey('site-setup', 'site-setup');
    // $sitesetup = App\Models\Setting::where('type','site-setup')->where('key', 'site-setup')->first();
    // $sitesetupdata = $sitesetup ? json_decode($sitesetup->value) : null;
    $currencyId = $setting ? $setting->default_currency : "231";
    $currency_position = $setting ? $setting->currency_position : "left";
    $country = App\Models\Country::find($currencyId);

    $symbol = '$';
    if (!empty($country)) {
        $symbol = $country->symbol;
    }
    $position = 'left';
    if (!empty($currency_position)) {
        $position = $currency_position;
    }

    $data = [
        'currency_symbol' => $symbol,
        'currency_position' => $position,
    ];

    return  $data;
}

function payment_status()
{

    return [
        'pending' => __('messages.pending'),
        'paid' => __('messages.paid'),
        'failed' => __('messages.failed'),
        'refunded' => __('messages.refunded')
    ];
}

function timeZoneList()
{
    $list = \DateTimeZone::listAbbreviations();
    $idents = \DateTimeZone::listIdentifiers();

    $data = $offset = $added = array();
    foreach ($list as $abbr => $info) {
        foreach ($info as $zone) {
            if (!empty($zone['timezone_id']) and !in_array($zone['timezone_id'], $added) and in_array($zone['timezone_id'], $idents)) {

                $z = new \DateTimeZone($zone['timezone_id']);
                $c = new \DateTime(null, $z);
                $zone['time'] = $c->format('H:i a');
                $offset[] = $zone['offset'] = $z->getOffset($c);
                $data[] = $zone;
                $added[] = $zone['timezone_id'];
            }
        }
    }

    array_multisort($offset, SORT_ASC, $data);
    $options = array();
    foreach ($data as $key => $row) {

        $options[$row['timezone_id']] = $row['time'] . ' - ' . formatOffset($row['offset'])  . ' ' . $row['timezone_id'];
    }
    $options['America/Sao_Paulo'] = '3:00 pm -  GMT-03:00 America/Sao_Paulo';
    return $options;
}

function dateFormatList()
{
    return [
        'Y-m-d' => date('Y-m-d'),
        'm-d-Y' => date('m-d-Y'),
        'd-m-Y' => date('d-m-Y'),
        'd/m/Y' => date('d/m/Y'),
        'm/d/Y' => date('m/d/Y'),
        'Y/m/d' => date('Y/m/d'),
        'Y.m.d' => date('Y.m.d'),
        'd.m.Y' => date('d.m.Y'),
        'm.d.Y' => date('m.d.Y'),
        'jS M Y' => date('jS M Y'),
        'M jS Y' => date('M jS Y'),
        'D, M d, Y' => date('D, M d, Y'),
        'D, d M, Y' => date('D, d M, Y'),
        'D, M jS Y' => date('D, M jS Y'),
        'D, jS M Y' => date('D, jS M Y'),
        'F j, Y' => date('F j, Y'),
        'd F, Y' => date('d F, Y'),
        'jS F, Y' => date('jS F, Y'),
        'l jS F Y' => date('l jS F Y'),
        'l, F j, Y' => date('l, F j, Y'),

    ];
}

function getTimeInFormat($format)
{
    $now = new DateTime();
    $hours = $now->format('H');
    $minutes = $now->format('i');
    $seconds = $now->format('s');
    $milliseconds = $now->format('v');
    $totalSecondsSinceMidnight = ($hours * 3600) + ($minutes * 60) + $seconds;

    switch ($format) {
        case "H:i":
            return "$hours:$minutes";
        case "H:i:s":
            return "$hours:$minutes:$seconds";
        case "g:i A":
            $ampm = $hours >= 12 ? 'PM' : 'AM';
            $formattedHours = $hours % 12 || 12;
            return "$formattedHours:$minutes $ampm";
        case "H:i:s T":
            return "$hours:$minutes:$seconds UTC";
        case "H:i:s.v":
            return "$hours:$minutes:$seconds.$milliseconds";
        case "U":
            return $now->getTimestamp();
        case "u":
            return $milliseconds * 1000;
        case "G.i":
            return $hours + $minutes / 60;
        case "@BMT":
            $swatchBeat = floor($totalSecondsSinceMidnight / 86.4);
            return "@{$swatchBeat}BMT";
        default:
            return "Invalid format";
    }
}

function timeFormatList()
{
    $timeFormats = [
        "H:i",
        "H:i:s",
        "g:i A",
        "H:i:s T",
        "H:i:s.v",
        "U",
        "u",
        "G.i",
        "@BMT"
    ];

    return array_map(function ($format) {
        return ['format' => $format, 'time' => getTimeInFormat($format)];
    }, $timeFormats);
}

function dateAgoFormate($date, $type2 = '')
{
    if ($date == null || $date == '0000-00-00 00:00:00') {
        return '-';
    }

    $diff_time1 = \Carbon\Carbon::createFromTimeStamp(strtotime($date))->diffForHumans();
    $datetime = new \DateTime($date);
    $la_time = new \DateTimeZone(\Auth::check() ? \Auth::user()->time_zone ?? 'UTC' : 'UTC');
    $datetime->setTimezone($la_time);
    $diff_date = $datetime->format('Y-m-d H:i:s');

    $diff_time = \Carbon\Carbon::parse($diff_date)->isoFormat('LLL');

    if ($type2 != '') {
        return $diff_time;
    }

    return $diff_time1 . ' on ' . $diff_time;
}

function timeAgoFormate($date)
{
    if ($date == null) {
        return '-';
    }

    $diff_time = \Carbon\Carbon::createFromTimeStamp(strtotime($date))->diffForHumans();

    return $diff_time;
}

function duration($start = '', $end = '', $type = '')
{
    $start = \Carbon\Carbon::parse($start);
    $end = \Carbon\Carbon::parse($end);

    if ($type) {
        $diff_in_minutes = $start->diffInMinutes($end);
        return $diff_in_minutes;
    } else {
        $diff = $start->diff($end);
        return $diff->format('%H:%I');
    }
}

function removeArrayValue($array = [], $find = [])
{
    foreach (array_keys($array, $find) as $key) {
        unset($array[$key]);
    }

    return array_values($array);
}

function handymanNames($collection)
{
    return $collection->mapWithKeys(function ($item) {
        return [$item->handyman_id => optional($item->handyman)->display_name];
    })->values()->implode(',');
}

function languagesArray($ids = [])
{
    $language = [
        ['title' => 'Abkhaz', 'id' => 'ab'],
        ['title' => 'Afar', 'id' => 'aa'],
        ['title' => 'Afrikaans', 'id' => 'af'],
        ['title' => 'Akan', 'id' => 'ak'],
        ['title' => 'Albanian', 'id' => 'sq'],
        ['title' => 'Amharic', 'id' => 'am'],
        ['title' => 'Arabic', 'id' => 'ar'],
        ['title' => 'Aragonese', 'id' => 'an'],
        ['title' => 'Armenian', 'id' => 'hy'],
        ['title' => 'Assamese', 'id' => 'as'],
        ['title' => 'Avaric', 'id' => 'av'],
        ['title' => 'Avestan', 'id' => 'ae'],
        ['title' => 'Aymara', 'id' => 'ay'],
        ['title' => 'Azerbaijani', 'id' => 'az'],
        ['title' => 'Bambara', 'id' => 'bm'],
        ['title' => 'Bashkir', 'id' => 'ba'],
        ['title' => 'Basque', 'id' => 'eu'],
        ['title' => 'Belarusian', 'id' => 'be'],
        ['title' => 'Bengali', 'id' => 'bn'],
        ['title' => 'Bihari', 'id' => 'bh'],
        ['title' => 'Bislama', 'id' => 'bi'],
        ['title' => 'Bosnian', 'id' => 'bs'],
        ['title' => 'Breton', 'id' => 'br'],
        ['title' => 'Bulgarian', 'id' => 'bg'],
        ['title' => 'Burmese', 'id' => 'my'],
        ['title' => 'Catalan; Valencian', 'id' => 'ca'],
        ['title' => 'Chamorro', 'id' => 'ch'],
        ['title' => 'Chechen', 'id' => 'ce'],
        ['title' => 'Chichewa; Chewa; Nyanja', 'id' => 'ny'],
        ['title' => 'Chinese', 'id' => 'zh'],
        ['title' => 'Chuvash', 'id' => 'cv'],
        ['title' => 'Cornish', 'id' => 'kw'],
        ['title' => 'Corsican', 'id' => 'co'],
        ['title' => 'Cree', 'id' => 'cr'],
        ['title' => 'Croatian', 'id' => 'hr'],
        ['title' => 'Czech', 'id' => 'cs'],
        ['title' => 'Danish', 'id' => 'da'],
        ['title' => 'Divehi; Dhivehi; Maldivian;', 'id' => 'dv'],
        ['title' => 'Dutch', 'id' => 'nl'],
        ['title' => 'English', 'id' => 'en'],
        ['title' => 'Esperanto', 'id' => 'eo'],
        ['title' => 'Estonian', 'id' => 'et'],
        ['title' => 'Ewe', 'id' => 'ee'],
        ['title' => 'Faroese', 'id' => 'fo'],
        ['title' => 'Fijian', 'id' => 'fj'],
        ['title' => 'Finnish', 'id' => 'fi'],
        ['title' => 'French', 'id' => 'fr'],
        ['title' => 'Fula; Fulah; Pulaar; Pular', 'id' => 'ff'],
        ['title' => 'Galician', 'id' => 'gl'],
        ['title' => 'Georgian', 'id' => 'ka'],
        ['title' => 'German', 'id' => 'de'],
        ['title' => 'Greek, Modern', 'id' => 'el'],
        ['title' => 'Guaraní', 'id' => 'gn'],
        ['title' => 'Gujarati', 'id' => 'gu'],
        ['title' => 'Haitian; Haitian Creole', 'id' => 'ht'],
        ['title' => 'Hausa', 'id' => 'ha'],
        ['title' => 'Hebrew (modern)', 'id' => 'he'],
        ['title' => 'Herero', 'id' => 'hz'],
        ['title' => 'Hindi', 'id' => 'hi'],
        ['title' => 'Hiri Motu', 'id' => 'ho'],
        ['title' => 'Hungarian', 'id' => 'hu'],
        ['title' => 'Interlingua', 'id' => 'ia'],
        ['title' => 'Indonesian', 'id' => 'id'],
        ['title' => 'Interlingue', 'id' => 'ie'],
        ['title' => 'Irish', 'id' => 'ga'],
        ['title' => 'Igbo', 'id' => 'ig'],
        ['title' => 'Inupiaq', 'id' => 'ik'],
        ['title' => 'Ido', 'id' => 'io'],
        ['title' => 'Icelandic', 'id' => 'is'],
        ['title' => 'Italian', 'id' => 'it'],
        ['title' => 'Inuktitut', 'id' => 'iu'],
        ['title' => 'Japanese', 'id' => 'ja'],
        ['title' => 'Javanese', 'id' => 'jv'],
        ['title' => 'Kalaallisut, Greenlandic', 'id' => 'kl'],
        ['title' => 'Kannada', 'id' => 'kn'],
        ['title' => 'Kanuri', 'id' => 'kr'],
        ['title' => 'Kashmiri', 'id' => 'ks'],
        ['title' => 'Kazakh', 'id' => 'kk'],
        ['title' => 'Khmer', 'id' => 'km'],
        ['title' => 'Kikuyu, Gikuyu', 'id' => 'ki'],
        ['title' => 'Kinyarwanda', 'id' => 'rw'],
        ['title' => 'Kirghiz, Kyrgyz', 'id' => 'ky'],
        ['title' => 'Komi', 'id' => 'kv'],
        ['title' => 'Kongo', 'id' => 'kg'],
        ['title' => 'Korean', 'id' => 'ko'],
        ['title' => 'Kurdish', 'id' => 'ku'],
        ['title' => 'Kwanyama, Kuanyama', 'id' => 'kj'],
        ['title' => 'Latin', 'id' => 'la'],
        ['title' => 'Luxembourgish, Letzeburgesch', 'id' => 'lb'],
        ['title' => 'Luganda', 'id' => 'lg'],
        ['title' => 'Limburgish, Limburgan, Limburger', 'id' => 'li'],
        ['title' => 'Lingala', 'id' => 'ln'],
        ['title' => 'Lao', 'id' => 'lo'],
        ['title' => 'Lithuanian', 'id' => 'lt'],
        ['title' => 'Luba-Katanga', 'id' => 'lu'],
        ['title' => 'Latvian', 'id' => 'lv'],
        ['title' => 'Manx', 'id' => 'gv'],
        ['title' => 'Macedonian', 'id' => 'mk'],
        ['title' => 'Malagasy', 'id' => 'mg'],
        ['title' => 'Malay', 'id' => 'ms'],
        ['title' => 'Malayalam', 'id' => 'ml'],
        ['title' => 'Maltese', 'id' => 'mt'],
        ['title' => 'Māori', 'id' => 'mi'],
        ['title' => 'Marathi (Marāṭhī)', 'id' => 'mr'],
        ['title' => 'Marshallese', 'id' => 'mh'],
        ['title' => 'Mongolian', 'id' => 'mn'],
        ['title' => 'Nauru', 'id' => 'na'],
        ['title' => 'Navajo, Navaho', 'id' => 'nv'],
        ['title' => 'Norwegian Bokmål', 'id' => 'nb'],
        ['title' => 'North Ndebele', 'id' => 'nd'],
        ['title' => 'Nepali', 'id' => 'ne'],
        ['title' => 'Ndonga', 'id' => 'ng'],
        ['title' => 'Norwegian Nynorsk', 'id' => 'nn'],
        ['title' => 'Norwegian', 'id' => 'no'],
        ['title' => 'Nuosu', 'id' => 'ii'],
        ['title' => 'South Ndebele', 'id' => 'nr'],
        ['title' => 'Occitan', 'id' => 'oc'],
        ['title' => 'Ojibwe, Ojibwa', 'id' => 'oj'],
        ['title' => 'Oromo', 'id' => 'om'],
        ['title' => 'Oriya', 'id' => 'or'],
        ['title' => 'Ossetian, Ossetic', 'id' => 'os'],
        ['title' => 'Panjabi, Punjabi', 'id' => 'pa'],
        ['title' => 'Pāli', 'id' => 'pi'],
        ['title' => 'Persian', 'id' => 'fa'],
        ['title' => 'Polish', 'id' => 'pl'],
        ['title' => 'Pashto, Pushto', 'id' => 'ps'],
        ['title' => 'Portuguese', 'id' => 'pt'],
        ['title' => 'Quechua', 'id' => 'qu'],
        ['title' => 'Romansh', 'id' => 'rm'],
        ['title' => 'Kirundi', 'id' => 'rn'],
        ['title' => 'Romanian, Moldavian, Moldovan', 'id' => 'ro'],
        ['title' => 'Russian', 'id' => 'ru'],
        ['title' => 'Sanskrit (Saṁskṛta)', 'id' => 'sa'],
        ['title' => 'Sardinian', 'id' => 'sc'],
        ['title' => 'Sindhi', 'id' => 'sd'],
        ['title' => 'Northern Sami', 'id' => 'se'],
        ['title' => 'Samoan', 'id' => 'sm'],
        ['title' => 'Sango', 'id' => 'sg'],
        ['title' => 'Serbian', 'id' => 'sr'],
        ['title' => 'Scottish Gaelic; Gaelic', 'id' => 'gd'],
        ['title' => 'Shona', 'id' => 'sn'],
        ['title' => 'Sinhala, Sinhalese', 'id' => 'si'],
        ['title' => 'Slovak', 'id' => 'sk'],
        ['title' => 'Slovene', 'id' => 'sl'],
        ['title' => 'Somali', 'id' => 'so'],
        ['title' => 'Southern Sotho', 'id' => 'st'],
        ['title' => 'Spanish; Castilian', 'id' => 'es'],
        ['title' => 'Sundanese', 'id' => 'su'],
        ['title' => 'Swahili', 'id' => 'sw'],
        ['title' => 'Swati', 'id' => 'ss'],
        ['title' => 'Swedish', 'id' => 'sv'],
        ['title' => 'Tamil', 'id' => 'ta'],
        ['title' => 'Telugu', 'id' => 'te'],
        ['title' => 'Tajik', 'id' => 'tg'],
        ['title' => 'Thai', 'id' => 'th'],
        ['title' => 'Tigrinya', 'id' => 'ti'],
        ['title' => 'Tibetan Standard, Tibetan, Central', 'id' => 'bo'],
        ['title' => 'Turkmen', 'id' => 'tk'],
        ['title' => 'Tagalog', 'id' => 'tl'],
        ['title' => 'Tswana', 'id' => 'tn'],
        ['title' => 'Tonga (Tonga Islands)', 'id' => 'to'],
        ['title' => 'Turkish', 'id' => 'tr'],
        ['title' => 'Tsonga', 'id' => 'ts'],
        ['title' => 'Tatar', 'id' => 'tt'],
        ['title' => 'Twi', 'id' => 'tw'],
        ['title' => 'Tahitian', 'id' => 'ty'],
        ['title' => 'Uighur, Uyghur', 'id' => 'ug'],
        ['title' => 'Ukrainian', 'id' => 'uk'],
        ['title' => 'Urdu', 'id' => 'ur'],
        ['title' => 'Uzbek', 'id' => 'uz'],
        ['title' => 'Venda', 'id' => 've'],
        ['title' => 'Vietnamese', 'id' => 'vi'],
        ['title' => 'Volapük', 'id' => 'vo'],
        ['title' => 'Walloon', 'id' => 'wa'],
        ['title' => 'Welsh', 'id' => 'cy'],
        ['title' => 'Wolof', 'id' => 'wo'],
        ['title' => 'Western Frisian', 'id' => 'fy'],
        ['title' => 'Xhosa', 'id' => 'xh'],
        ['title' => 'Yiddish', 'id' => 'yi'],
        ['title' => 'Yoruba', 'id' => 'yo'],
        ['title' => 'Zhuang, Chuang', 'id' => 'za']
    ];
    if (!empty($ids)) {
        $language = collect($language)->whereIn('id', $ids)->values();
    }
    return $language;
}

function flattenToMultiDimensional(array $array, $delimiter = '.')
{
    $result = [];
    foreach ($array as $notations => $value) {
        // extract keys
        $keys = explode($delimiter, $notations);
        // reverse keys for assignments
        $keys = array_reverse($keys);

        // set initial value
        $lastVal = $value;
        foreach ($keys as $key) {
            // wrap value with key over each iteration
            $lastVal = [
                $key => $lastVal
            ];
        }

        // merge result
        $result = array_merge_recursive($result, $lastVal);
    }

    return $result;
}

// function createLangFile($lang=''){
//     $langDir = resource_path().'/lang/';
//     $enDir = $langDir.'en';
//     $currentLang = $langDir . $lang;
//     if(!File::exists($currentLang)){
//        File::makeDirectory($currentLang);
//        File::copyDirectory($enDir,$currentLang);
//     }
// }
function createLangFile($languages = [])
{
    $langDir = resource_path('lang/');
    $enDir = $langDir . 'en';
    foreach ($languages as $lang) {
        $currentLangDir = $langDir . $lang;
        if (!File::exists($currentLangDir)) {
            File::makeDirectory($currentLangDir, 0755, true);
            File::copyDirectory($enDir, $currentLangDir);
        }
    }
}
function deleteLangFile($selectedLanguages)
{
    $langDir = resource_path('lang/');
    $allDirs = File::directories($langDir);

    foreach ($allDirs as $dir) {
        $dirName = basename($dir);
        if (!in_array($dirName, $selectedLanguages)) {
            File::deleteDirectory($dir);
        }
    }
}

// function convertToHoursMins($time, $format = '%02d:%02d') {
//     if ($time < 1) {
//         return sprintf($format, 0, 0);
//     }
//     $hours = floor($time / 60);
//     $minutes = ($time % 60);
//     return sprintf($format, $hours, $minutes);
// }

function convertToHoursMins($time, $format = '%02d:%02d:%02d')
{
    if ($time < 1) {
        return sprintf($format, 0, 0, 0);
    }

    $hours = floor($time / 3600); // Total hours
    $minutes = floor(($time % 3600) / 60); // Remaining minutes after hours
    $seconds = $time % 60; // Remaining seconds after minutes

    return sprintf($format, $hours, $minutes, $seconds);
}

function getSettingKeyValue($key = "", $radius_type = "")
{
    $setting_data = \App\Models\Setting::where('key', $key)->first();
    $radious_distance = $setting_data ? json_decode($setting_data->value) : null;
    $radious = $radious_distance->radious;
    $distance_type = $radious_distance->distance_type;

    if ($radious_distance != null) {
        switch ($radius_type) {
            case 'distance_type':
                return $distance_type;
            case 'radious':
                return $radious;
            default:
                return getDefaultSetting($radius_type);
        }
    } else {

        switch ($radius_type) {
            case 'distance_type':
                return 'km';
                break;
            case 'radious':
                return 50;
                break;
            default:
                break;
        }
    }
}

function countUnitvalue($unit)
{
    switch ($unit) {
        case 'mile':
            return 3956;
            break;
        default:
            return 6371;
            break;
    }
}

function imageExtention($media)
{
    $extention = null;
    if ($media != null) {
        $path_info = pathinfo($media);
        $extention = $path_info['extension'];
    }
    return $extention;
}

function verify_provider_document($provider_id)
{
    $documents = \App\Models\Documents::where('is_required', 1)->where('status', 1)->withCount([
        'providerDocument',
        'providerDocument as is_verified_document' => function ($query) use ($provider_id) {
            $query->where('is_verified', 1)->where('provider_id', $provider_id);
        }
    ])
        ->get();

    $is_verified = $documents->where('is_verified_document', 1);

    if (count($documents) == count($is_verified)) {
        return true;
    } else {
        return false;
    }
}

function calculate_commission($total_amount = 0, $provider_commission = 0, $commission_type = 'percent', $type = '', $totalEarning = 0, $count = 0)
{
    if ($total_amount === 0) {
        return [
            'value' => '-',
            'number_format' => 0
        ];
    }
    switch ($type) {
        case 'provider':
            $earning = ($provider_commission * $count);
            if ($commission_type === 'percent') {
                $earning =  ($total_amount) * $provider_commission / 100;
            }
            $final_amount = $earning - $totalEarning;

            if (abs($final_amount) < 1) { // treat values less than 0.0001 as 0
                $final_amount = 0;
            }


            break;
        default:
            $earning = $total_amount - $provider_commission * $count;
            if ($commission_type === 'percent') {
                $earning = ($total_amount) * (100 - $provider_commission) / 100;
            }
            $final_amount = $earning;
            break;
    }
    return [
        'value' => getPriceFormat($final_amount),
        'number_format' => $final_amount
    ];
}

function get_provider_commission($bookings)
{
    $all_booking_total = $bookings->map(function ($booking) {
        return $booking->total_amount;
    })->toArray();

    $all_booking_tax = $bookings->map(function ($booking) {
        return $booking->getTaxesValue();
    })->toArray();

    $total = array_reduce($all_booking_total, function ($value1, $value2) {
        return $value1 + $value2;
    }, 0);

    $tax = array_reduce($all_booking_tax, function ($tax1, $tax2) {
        return $tax1 + $tax2;
    }, 0);

    $total_amount = $total;

    return [
        'total_amount' => $total_amount,
        'tax' => $tax,
        'total' => $total,
        'all_booking_tax' => $all_booking_tax,
        'all_booking_total' => $all_booking_total,
    ];
}

function get_handyman_provider_commission($handyman_id)
{
    $hadnymantype_id = !empty($handyman_id) ? $handyman_id : 1;
    $get_commission = \App\Models\HandymanType::withTrashed()->where('id', $hadnymantype_id)->first();
    if ($get_commission) {
        $commission_value = $get_commission->commission;
        $commission_type = $get_commission->type;

        $commission = getPriceFormat($commission_value);
        if ($commission_type === 'percent') {
            $commission = $commission_value . '%';
        }

        return $commission;
    }
    return '-';
}

function adminEarning()
{
    // Get commission earnings grouped by month
    $commissionData = \App\Models\CommissionEarning::selectRaw('sum(commission_amount) as total, DATE_FORMAT(updated_at, "%m") as month')
        ->whereYear('updated_at', date('Y'))
        ->whereIn('commission_status', ['paid', 'unpaid'])
        ->groupBy('month')
        ->get()
        ->keyBy('month')
        ->toArray();

    // Get cancellation charges grouped by month
    $cancellationData = \App\Models\Booking::selectRaw('sum(cancellation_charge_amount) as total, DATE_FORMAT(updated_at, "%m") as month')
        ->whereYear('updated_at', date('Y'))
        ->where('status', 'cancelled')
        ->groupBy('month')
        ->get()
        ->keyBy('month')
        ->toArray();

    $total_subscription_amout_data = \App\Models\SubscriptionTransaction::selectRaw('sum(amount) as total, DATE_FORMAT(updated_at, "%m") as month')
        ->whereYear('updated_at', date('Y'))
        ->where('payment_status', 'paid')
        ->groupBy('month')
        ->get()
        ->keyBy('month')
        ->toArray();

    // Get promotional banner earnings grouped by month
    $promotionalBannerData = \App\Models\PromotionalBanner::selectRaw('sum(total_amount) as total, DATE_FORMAT(start_date, "%m") as month')
        ->whereYear('start_date', date('Y'))
        ->where('payment_status', 'paid') // Assuming only paid banners count in earnings
        ->groupBy('month')
        ->get()
        ->keyBy('month')
        ->toArray();

    // Prepare data for the graph
    $data['revenueData'] = [];
    $data['revenueLabelData'] = [];

    for ($i = 1; $i <= 12; $i++) {
        $month = str_pad($i, 2, '0', STR_PAD_LEFT); // Format month as two digits
        $commission = $commissionData[$month]['total'] ?? 0;
        $cancellation = $cancellationData[$month]['total'] ?? 0;
        $total_subscription_amout = $total_subscription_amout_data[$month]['total'] ?? 0;
        $promotional = $promotionalBannerData[$month]['total'] ?? 0;

        // Add all earnings including promotional banners
        $data['revenueData'][] = $commission + $cancellation + $total_subscription_amout + $promotional;
        $data['revenueLabelData'][] = $month;
    }

    return $data['revenueData'];
}



function getTimeZone()
{
    $timezone = \App\Models\AppSetting::first();
    return $timezone->time_zone ?? 'UTC';
}

function get_plan_expiration_date($plan_start_date = '', $plan_type = '', $left_days = 0, $plan_duration = 1)
{
    $start_at = new \Carbon\Carbon($plan_start_date);
    $end_date = '';

    if ($plan_type === 'weekly') {
        $getdays = App\Models\Plans::where('identifier', 'free')->first();
        $getdays = $getdays->trial_period;
        $days = $left_days + $getdays;
        $end_date =  $start_at->addDays((int)$days);
    }
    if ($plan_type === 'monthly') {
        $end_date = $start_at->addMonths((int)$plan_duration)->addDays((int)$left_days);
    }
    if ($plan_type === 'yearly') {
        $end_date =  $start_at->addYears((int)$plan_duration)->addDays((int)$left_days);
    }
    return $end_date->format('Y-m-d H:i:s');
}

function get_user_active_plan($user_id)
{
    $get_provider_plan  =  \App\Models\ProviderSubscription::where('user_id', $user_id)->where('status', config('constant.SUBSCRIPTION_STATUS.ACTIVE'))->first();
    $activeplan = null;
    if (!empty($get_provider_plan)) {
        $activeplan = new App\Http\Resources\API\ProviderSubscribeResource($get_provider_plan);
    }
    return $activeplan;
}

function is_subscribed_user($user_id)
{
    $user_subscribed = \App\Models\ProviderSubscription::where('user_id', $user_id)->where('status', config('constant.SUBSCRIPTION_STATUS.ACTIVE'))->first();
    $value = 0;
    if ($user_subscribed) {
        $value = 1;
    }
    return $value;
}

function check_days_left_plan($old_plan, $new_plan)
{
    $previous_plan_start = $old_plan->start_at;
    $previous_plan_end = new \Carbon\Carbon($old_plan->end_at);
    $new_plan_start = new \Carbon\Carbon(date('Y-m-d H:i:s'));
    $left_days = $previous_plan_end->diffInDays($new_plan_start);
    return $left_days;
}

function user_last_plan($user_id)
{
    $user_subscribed = \App\Models\ProviderSubscription::where('user_id', $user_id)
        ->where('status', config('constant.SUBSCRIPTION_STATUS.INACTIVE'))->orderBy('id', 'desc')->first();
    $inactivePlan = null;
    if (!empty($user_subscribed)) {
        $inactivePlan = new App\Http\Resources\API\ProviderSubscribeResource($user_subscribed);
    }
    return $inactivePlan;
}

function is_any_plan_active($user_id)
{
    $user_subscribed = \App\Models\ProviderSubscription::where('user_id', $user_id)->where('status', config('constant.SUBSCRIPTION_STATUS.ACTIVE'))->first();
    $value = 0;
    if ($user_subscribed) {
        $value = 1;
    }
    return $value;
}

function default_earning_type()
{
    $gettype = \App\Models\Setting::where('type', 'earning-setting')->where('key', 'earning-setting')->first();
    if ($gettype !== null) {
        $earningtype = $gettype->value ?? 'commission';
    } else {
        $earningtype = 'commission';
    }
    return $earningtype;
}

function get_provider_plan_limit($provider_id, $type)
{
    $limit_array = array();

    if (is_any_plan_active($provider_id) == 1) {
        $exceed = '';
        $get_current_plan = get_user_active_plan($provider_id);
        if ($get_current_plan->plan_type === 'limited') {
            $get_plan_limit = json_decode($get_current_plan->plan_limitation, true);
            $plan_start_date =  date('Y-m-d', strtotime($get_current_plan->start_at));

            if ($type === 'service') {
                $limit_array = $get_plan_limit['service'];
                $provider_service_count = \App\Models\Service::where('provider_id', $provider_id)->whereDate('created_at', '>=', $plan_start_date)->count();
                if ($limit_array['is_checked'] == 'on' && $limit_array['limit'] != null) {
                    if ($provider_service_count >= $limit_array['limit']) {
                        $exceed = 1; // 1 for exceed limit;
                    }
                } elseif ($limit_array['is_checked'] === 'on' && $limit_array['limit'] == null) {
                    $exceed = 0;
                }
            }
            if ($type === 'featured_service') {
                $limit_array = $get_plan_limit['featured_service'];
                $provider_featured_service_count = \App\Models\Service::where('provider_id', $provider_id)->where('is_featured', 1)->whereDate('created_at', '>=', $plan_start_date)->count();
                if ($limit_array['is_checked'] == 'on' && $limit_array['limit'] != null) {
                    if ($provider_featured_service_count >= $limit_array['limit']) {
                        $exceed = 1; // 1 for exceed limit;
                    }
                } elseif ($limit_array['is_checked'] === 'on' && $limit_array['limit'] == null) {
                    $exceed = 0;
                }
            }
            if ($type === 'handyman') {
                $limit_array = $get_plan_limit['handyman'];
                $handyman_count = \App\Models\User::where('provider_id', $provider_id)->whereDate('created_at', '>=', $plan_start_date)->count();
                if ($limit_array['is_checked'] == 'on' && $limit_array['limit'] != null) {
                    if ($handyman_count >= (int)$limit_array['limit']) {
                        $exceed = 1; // 1 for exceed limit;
                    }
                } elseif ($limit_array['is_checked'] === 'on' && $limit_array['limit'] == null) {
                    $exceed = 0;
                }
            }
        } else {
            return;
        }
    } else {
        return;
    }
    return $exceed;
}

function sendNotification($type, $user, $data)
{

    $othersetting = \App\Models\Setting::where('type', 'OTHER_SETTING')->first();

    $decodedata = $othersetting ? json_decode($othersetting['value']) : null;
    $firebase_notification = $decodedata->firebase_notification;

    if ($firebase_notification == 1) {

        $projectID = isset($decodedata->project_id) ? $decodedata->project_id : null;

        $apiUrl = 'https://fcm.googleapis.com/v1/projects/' . $projectID . '/messages:send';
        $access_token = getAccessToken();
        $headers = [
            'Authorization: Bearer ' . $access_token,
            'Content-Type: application/json',
        ];

        $heading   = '#' . $data['id'] . ' ' . str_replace("_", " ", $data['subject']);
        $content   = $data['message'];


        $firebase_data = [
            'topic' => 'user_' . $user->id,
            'collapse_key' => 'type_a',
            'notification' => [
                'body' =>   $content,
                'title' => $heading,
            ],
            'data' => [
                'type' => $data['type'],
                'id' => $data['id']
            ],
        ];

        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($firebase_data));

        $response = curl_exec($ch);

        curl_close($ch);
    }
    $childData = array(
        "id" => $data['id'],
        "type" => $data['type'],
        "subject" => $data['subject'],
        "message" => $data['message'],
        'notification-type' => $data['notification-type']
    );

    $notification = \App\Models\Notification::create(
        array(
            'id' => Illuminate\Support\Str::random(32),
            'type' => $data['type'],
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $user->id,
            'data' => json_encode($childData)
        )
    );
}

// function getServiceTimeSlot($provider_id){
//     $sitesetup = App\Models\Setting::where('type','site-setup')->where('key', 'site-setup')->first();
//     $admin = json_decode($sitesetup->value);
//     date_default_timezone_set($admin->time_zone ?? 'UTC');

//     $current_time = \Carbon\Carbon::now();
//     $time = $current_time->toTimeString();
//     $current_day = strtolower(date('D'));

//     $days = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];

//     $handyman_count = \App\Models\User::where('provider_id', $provider_id)->where('is_available', 1)->count() + 1;

//     $providerSlots = \App\Models\ProviderSlotMapping::where('provider_id', $provider_id)
//         ->whereIn('days', $days)
//         ->orderBy('start_at', 'asc')
//         ->get();

//     $bookings = \App\Models\Booking::where('provider_id', $provider_id)->get();
//     $booking_count = count($bookings);

//     $slotsArray = [];

//     foreach ($days as $value) {
//         $slot = $providerSlots->where('days', $value);

//         if ($current_day === $value) {
//             $slot = $slot->where('start_at', '>', $time);
//         }

//         $filteredSlots = $slot->pluck('start_at')->toArray();

//         if ($handyman_count == $booking_count) {
//             $filteredSlots = array_diff($filteredSlots, $bookings->pluck('start_at')->toArray());
//         }

//         $obj = [
//             "day" => $value,
//             "slot" => $filteredSlots,
//         ];

//         array_push($slotsArray, $obj);
//     }

//     return $slotsArray;
// }
function getServiceTimeSlot($provider_id)
{

    $sitesetup = App\Models\Setting::where('type', 'site-setup')->where('key', 'site-setup')->first();
    $admin = json_decode($sitesetup->value);
    date_default_timezone_set($admin->time_zone ?? 'UTC');

    $current_time = \Carbon\Carbon::now();
    $time = $current_time->toTimeString();
    $current_day = strtolower(date('D'));

    $days = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];

    $handyman_count = \App\Models\User::where('provider_id', $provider_id)->where('is_available', 1)->count() + 1;

    $providerSlots = \App\Models\ProviderSlotMapping::where('provider_id', $provider_id)
        ->whereIn('days', $days)
        ->orderBy('start_at', 'asc')
        ->get();
    $slotsArray = [];

    $bookings = \App\Models\Booking::where('provider_id', $provider_id)
        ->where('date', '>', \Carbon\Carbon::today())
        ->where('status', '!=', 'cancelled')
        ->get(['date', 'booking_slot', 'id']);
    $groupedBySlot = $bookings->groupBy('booking_slot');

    foreach ($days as $value) {
        $bookingsOnDaySlot = 0;
        $results = [];
        $slot = $providerSlots->where('days', $value);
        if ($current_day === $value) {
            $slot = $slot->where('start_at', '>', $time);
        }

        $filteredSlots = $slot->pluck('start_at')->toArray();
        foreach ($groupedBySlot as $slot => $bookingsInSlot) {
            $groupedByDay = $bookingsInSlot->groupBy(function ($booking) {
                return \Carbon\Carbon::parse($booking->date)->format('D');
            });
            $dayCounts = [];
            $carbonDayFormat = ucfirst($value); {
                foreach ($groupedByDay as $day => $bookingsOnDay) {
                    if ($day == $carbonDayFormat) {
                        $bookingsOnDaySlot = $bookingsOnDay->count();
                        // $dayCounts[$day] = [
                        //     'count' => $bookingsOnDay->count(),
                        //     'day' => $value
                        // ];
                        // $results[$slot] = $dayCounts;
                        if ($handyman_count <= $bookingsOnDaySlot) {
                            $filteredSlots = array_diff($filteredSlots, (array) $slot);
                        }
                    }
                }
            }
        }
        $obj = [
            "day" => $value,
            "slot" => $filteredSlots,
        ];

        array_push($slotsArray, $obj);
    }
    return $slotsArray;
}

function bookingstatus($status)
{
    switch ($status) {
        case 'Pending':
            $html = '<span class="badge text-warning bg-warning-subtle ">' . $status . '</span>';

            break;

        case 'Accepted':
            $html = '<span class="badge text-success bg-success-subtle">' . $status . '</span>';

            break;


        case 'Ongoing':
            $html = '<span class="badge text-warning bg-warning-subtle">' . $status . '</span>';

            break;

        case 'In Progress':
            $html = '<span class="badge text-info bg-info-subtle">' . $status . '</span>';

            break;

        case 'Hold':
            $html = '<span class="badge text-dark bg-dark-subtle text-white">' . $status . '</span>';

            break;

        case 'Cancelled':
            $html = '<span class="badge text-dark bg-light border-dark">' . $status . '</span>';

            break;

        case 'Rejected':
            $html = '<span class="badge text-dark bg-light-subtle">' . $status . '</span>';

            break;

        case 'Completed':
            $html = '<span class="badge text-success bg-success-subtle">' . $status . '</span>';

            break;

        default:
            $html = '<span class="badge text-danger bg-danger-subtle">' . $status . '</span>';
            break;
    }
    return $html;
}

function total_cash_in_hand($user_id)
{
    $amount = 0;

    // Get the first role of the user
    $role = auth()->user()->getRoleNames()->first();
    $payment_history = App\Models\PaymentHistory::query();
    // Only proceed if the role is handyman or provider
    if (in_array($role, ['handyman', 'provider'])) {

        // Define role-specific actions and exclusion logic
        $validActions = $role === 'handyman'
            ? ['handyman_approved_cash', 'handyman_send_provider']
            : ['provider_approved_cash', 'provider_send_admin'];

        $excludeAction = $role === 'handyman'
            ? 'provider_approved_cash'
            : 'admin_approved_cash';
        // Base query for payment history
        $amount = $payment_history->where('receiver_id', $user_id)
            ->whereIn('action', $validActions)
            ->whereNotIn('booking_id', function ($subQuery) use ($excludeAction, $user_id) {
                $subQuery->select('booking_id')
                    ->from('payment_histories')
                    ->where('action', $excludeAction)
                    ->where('sender_id', $user_id);
            })
            ->sum('total_amount'); // Sum the valid total amounts
    }

    return $amount;
}

function admin_id()
{
    $user = \App\Models\User::getUserByKeyValue('user_type', 'admin');
    return $user->id;
}

function get_user_name($user_id)
{
    $name = '';
    $user = \App\Models\User::getUserByKeyValue('id', $user_id);
    if ($user !== null) {
        $name = $user->display_name;
    }
    return $name;
}

function set_admin_approved_cash($payment_id)
{
    $payment_status_check =  \App\Models\PaymentHistory::where('payment_id', $payment_id)
        ->where('action', 'provider_send_admin')->where('status', 'pending_by_admin')->first();
    if ($payment_status_check !== null) {
        $status = '<a class="btn-sm text-white btn-success "  href=' . route('cash.approve', $payment_id) . '><i class="fa fa-check"></i>Approve</a>';
    } else {
        $status = '-';
    }
    return $status;
}

function last_status($payment_id)
{
    $payment_status_check =  \App\Models\PaymentHistory::orderBy('id', 'desc')->where('payment_id', $payment_id)->first();
    if ($payment_status_check !== null) {
        $status = '<span class="text-center badge bg-success-subtle">' . str_replace('_', " ", ucfirst($payment_status_check->status)) . '</span>';
    } else {
        $status = '<span class="text-center d-block">-</span>';
    }
    return $status;
}

function providerpayout_rezopayX($data)
{

    $rezorpay_data = \App\Models\PaymentGateway::where('type', 'razorPayX')->first();


    if ($rezorpay_data) {

        $is_test = $rezorpay_data['is_test'];

        if ($is_test == 1) {

            $json_data = $rezorpay_data['value'];
        } else {

            $json_data = $rezorpay_data['live_value'];
        }
        $setting = App\Models\Setting::getValueByKey('site-setup', 'site-setup');
        // $sitesetup = App\Models\Setting::where('type','site-setup')->where('key', 'site-setup')->first();
        // $sitesetupdata = $sitesetup ? json_decode($sitesetup->value) : null;

        $currency_country_id = $setting ? $setting->default_currency : "231";

        $country_data = \App\Models\Country::where('id', $currency_country_id)->first();

        $currency = $country_data['currency_code'];

        $razopayX_credentials = json_decode($json_data, true);

        $url = $razopayX_credentials['razorx_url'];
        $key = $razopayX_credentials['razorx_key'];
        $secret = $razopayX_credentials['razorx_secret'];
        $RazorpayXaccount = $razopayX_credentials['razorx_account'];


        $provider_id = isset($data['provider_id']) ? $data['provider_id'] : (isset($data['user_id']) ? $data['user_id'] : null);
        $payout_amount = $data['amount'];

        $bank_id = $data['bank'];

        $providers_details = \App\Models\User::where('id', $provider_id)->first();

        $email = $providers_details['email'];
        $first_name = $providers_details['first_name'];
        $last_name = $providers_details['last_name'];
        $contact_number = $providers_details['contact_number'];
        $user_type = $providers_details['user_type'];

        $bank_details = \App\Models\Bank::where('id', $bank_id)->first();

        $bank_name = $bank_details['bank_name'];
        $account_number = $bank_details['account_no'];
        $ifsc = $bank_details['ifsc_no'];

        $payout_data = array(
            "account_number" => $RazorpayXaccount,
            "amount" => (int)$payout_amount * 100,
            "currency" => $currency,
            "mode" => "NEFT",
            "purpose" => "payout",
            "fund_account" => array(
                "account_type" => "bank_account",
                "bank_account" => array(
                    "name" => $first_name . $last_name,
                    "ifsc" => $ifsc,
                    "account_number" => $account_number
                ),
                "contact" => array(
                    "name" => $first_name . $last_name,
                    "email" =>  $email,
                    "contact" => $contact_number,
                    "type" => "vendor",
                )
            ),
            "queue_if_low_balance" => true,

        );

        // Convert data to JSON
        $json_data = json_encode($payout_data);
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode($key . ':' . $secret)
        ));

        $response = curl_exec($ch);

        return $response;
    } else {

        return $response = '';
    }
}

function providerpayout_stripe($data)
{

    //Stripe Payment

    $stripe_data = \App\Models\PaymentGateway::where('type', 'stripe')->first();

    if ($stripe_data->value != null) {

        $is_test = $stripe_data['is_test'];

        if ($is_test == 1) {

            $json_data = $stripe_data['value'];
        } else {

            $json_data = $stripe_data['live_value'];
        }

        $stripe_credentials = json_decode($json_data, true);

        $secret_key = $stripe_credentials['stripe_key'];

        $setting = App\Models\Setting::getValueByKey('site-setup', 'site-setup');
        // $sitesetup = App\Models\Setting::where('type','site-setup')->where('key', 'site-setup')->first();
        // $sitesetupdata = $sitesetup ? json_decode($sitesetup->value) : null;

        $currency_country_id = $setting ? $setting->default_currency : "231";

        $country_data = \App\Models\Country::where('id', $currency_country_id)->first();

        $country = $country_data['code'];

        $currency = strtolower($country_data['currency_code']);


        $provider_id = $data['provider_id'];
        $payout_amount = $data['amount'];
        $bank_id = $data['bank'];

        $bank_details = \App\Models\Bank::where('id', $bank_id)->first();

        $bank_name = $bank_details['bank_name'];
        $account_number = $bank_details['account_no'];
        $ifsc = $bank_details['ifsc_no'];
        $stripe_account = $bank_details['stripe_account'];

        if ($stripe_account == '') {

            $providers_details = \App\Models\User::where('id', $provider_id)->first();
            $email = $providers_details['email'];
            $first_name = $providers_details['first_name'];
            $last_name = $providers_details['last_name'];
            $contact_number = $providers_details['contact_number'];
            $user_type = $providers_details['user_type'];

            $current_datetime = time();

            $ip_address = file_get_contents('https://api.ipify.org');

            try {

                $stripe = new \Stripe\StripeClient($secret_key);

                $stripedata = $stripe->accounts->create(
                    [
                        'country' => $country,
                        'type' => 'custom',
                        'bank_account' => [
                            'account_number' => $account_number,
                            'country' => $country,
                            'account_holder_name' => $first_name . $last_name,
                            'routing_number' => $ifsc
                        ],

                        'capabilities' => [
                            'transfers' => [
                                'requested' => true
                            ]
                        ],
                        'business_type' => 'individual',
                        'country' => $country,
                        'email' => $email,
                        'individual' => [
                            'first_name' => $first_name,
                            'last_name' => $last_name
                        ],
                        'business_profile' => [
                            'name' => $first_name . $last_name,
                            'url' => 'demo.com'
                        ],
                        'tos_acceptance' => [
                            'date' => $current_datetime,
                            'ip' => $ip_address
                        ]
                    ]
                );

                $stripe_account = $stripedata['id'];

                \App\Models\Bank::where('id', $bank_id)->update(['stripe_account' => $stripe_account]);
            } catch (Stripe\Exception\ApiErrorException $e) {

                $error = $e->getError();

                if ($error == '') {

                    return $response = '';
                } else {

                    $error['status'] = 400;

                    return $error;
                }
            }
        }

        $data = [

            'secret_key' => $secret_key,
            'amount' => $payout_amount,
            'currency' => $currency,
            'stripe_account' => $stripe_account
        ];



        $bank_transfer = create_stripe_transfer($data);

        return $bank_transfer;
    } else {

        return $response = '';
    }
}

function create_stripe_transfer($data)
{
    try {


        \Stripe\Stripe::setApiKey($data['secret_key']);

        $transfer = \Stripe\Transfer::create([
            "amount" => $data['amount'] * 100,
            "currency" =>  $data['currency'],
            "destination" => $data['stripe_account'],
        ]);

        $payout = create_bank_tranfer($data);

        return $payout;
    } catch (Stripe\Exception\ApiErrorException $e) {


        $error = $e->getError();

        $error['status'] = 400;

        if ($error == '') {

            return $response = '';
        } else {

            $error['status'] = 400;
            return $error;
        }
    }
}

function create_bank_tranfer($data)
{

    try {

        \Stripe\Stripe::setApiKey($data['secret_key']);

        $payout = \Stripe\Payout::create([
            'amount' => $data['amount'] * 100,
            'currency' => $data['currency'],
        ], [
            'stripe_account' => $data['stripe_account'],

        ]);

        return $payout;
    } catch (Stripe\Exception\ApiErrorException $e) {


        $error = $e->getError();


        if ($error == '') {

            return $response = '';
        } else {

            $error['status'] = 400;
            return $error;
        }
    }
}

function calculateReadingTime($content, $wpm = 100)
{
    $wordCount = str_word_count(strip_tags($content));

    $readingTime = intval($wordCount / $wpm);

    return $readingTime;
}

function formatCurrency($number, $noOfDecimal, $currencyPosition, $currencySymbol)
{

    $formattedNumber = number_format($number, $noOfDecimal, '.', '');
    $parts = explode('.', $formattedNumber);
    $integerPart = $parts[0];
    $decimalPart = isset($parts[1]) ? $parts[1] : '';

    $currencyString = '';

    if ($currencyPosition == 'left') {
        $currencyString .= $currencySymbol;

        $currencyString .= $integerPart;

        if ($noOfDecimal > 0) {
            $currencyString .= '.' . $decimalPart;
        }
    }

    if ($currencyPosition == 'right') {

        if ($noOfDecimal > 0) {
            $currencyString .= $integerPart . '.' . $decimalPart;
        }

        $currencyString .= $currencySymbol;
    }

    return $currencyString;
}

function  getPaymentMethodkey($type)
{

    $pyament_gateway = App\Models\PaymentGateway::query();

    $payment_geteway_value = null;

    switch ($type) {

        case 'stripe':

            $pyament_gateway_data = $pyament_gateway->where('type', $type)->first();

            if ($pyament_gateway_data) {

                if ($pyament_gateway_data->is_test == 1) {

                    $payment_geteway_value = json_decode($pyament_gateway_data->value, true);
                } else {

                    $payment_geteway_value = json_decode($pyament_gateway_data->live_value, true);
                }
            }

            break;
    }

    return $payment_geteway_value;
}

function getstripepayments($data)
{
    $baseURL = env('APP_URL');

    $stripe_key_data = getPaymentMethodkey($data['payment_type']);

    $stripe_secret = $stripe_key_data['stripe_key'];

    $booking = App\Models\Booking::where('id', $data['booking_id'])->with('service')->first();

    try {
        $stripe = new \Stripe\StripeClient($stripe_secret);
        $checkout_session = $stripe->checkout->sessions->create([

            'success_url' => $baseURL . '/save-stripe-payment/' . $data['booking_id'] . '?type=' . $data['type'],
            'payment_method_types' => ['card'],
            'billing_address_collection' => 'required',
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => $data['currency_code'],
                        'product_data' => [
                            'name' => $booking->service->name,
                        ],
                        'unit_amount' => $data['total_amount'] * 100,
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
        ]);
    } catch (\Exception $e) {
        $message = $e->getMessage();

        $checkout_session = [
            'message' => $message,
            'status' => false,
        ];
    }

    return $checkout_session;
}

function getstripePaymnetId($stripe_session_id, $payment_type)
{
    $stripe_key_data = getPaymentMethodkey($payment_type);

    $stripe_secret = $stripe_key_data['stripe_key'];

    $stripe = new \Stripe\StripeClient($stripe_secret);
    $session_object = $stripe->checkout->sessions->retrieve($stripe_session_id, []);

    return $session_object;
}

function default_user_name()
{
    return __('messages.unknown_user');
}


function addWalletAmount($data)
{

    $baseURL = env('APP_URL');

    // Retrieve the Stripe secret key
    $stripe_key_data = getPaymentMethodkey($data['payment_type']);
    $stripe_secret = $stripe_key_data['stripe_key'];

    // Retrieve wallet details
    $wallet = App\Models\Wallet::where('user_id', $data['customer_id'])->first();

    try {
        // Create the Stripe checkout session
        $stripe = new \Stripe\StripeClient($stripe_secret);
        $checkout_session = $stripe->checkout->sessions->create([
            'success_url' => $baseURL . '/save-wallet-stripe-payment/' . $data['customer_id'] . '?amount=' . $data['amount'], // Use the route name
            'payment_method_types' => ['card'],
            'billing_address_collection' => 'required',
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => $data['currency_code'],
                        'unit_amount' => $data['amount'] * 100, // Amount in cents
                        'product_data' => [
                            'name' => 'Wallet Top-Up', // Change this if needed
                        ],
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
        ]);
    } catch (\Exception $e) {
        // Handle exceptions
        $message = $e->getMessage();
        $checkout_session = [
            'message' => $message,
            'status' => false,
        ];
    }

    return $checkout_session;
}

function fcm($fields)
{
    $otherSetting = \App\Models\Setting::where('type', 'OTHER_SETTING')->first();
    $other = json_decode($otherSetting->value);
    $projectID = $other->project_id;
    $access_token = getAccessToken();

    $headers = [
        'Authorization: Bearer ' . $access_token,
        'Content-Type: application/json',
    ];
    $ch = curl_init('https://fcm.googleapis.com/v1/projects/' . $projectID . '/messages:send');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

    $response = curl_exec($ch);

    Log::info($response);
    curl_close($ch);
}

function getAccessToken()
{
    $directory = storage_path('app/data');
    $credentialsFiles = File::glob($directory . '/*.json');


    if (!empty($credentialsFiles)) {

        try {
            $client = new \Google\Client();
            $client->setAuthConfig($credentialsFiles[0]);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

            $token = $client->fetchAccessTokenWithAssertion();

            return $token['access_token'];
        } catch (\Exception $e) {

            return null;
        }
    } else {

        return null;
    }
}


function countrySymbol()
{
    $setting = App\Models\Setting::getValueByKey('site-setup', 'site-setup');
    // $sitesetup = App\Models\Setting::where('type','site-setup')->where('key', 'site-setup')->first();
    // $sitesetupdata = $sitesetup ? json_decode($sitesetup->value) : null;

    $currencyId = $setting ? $setting->default_currency : "231";
    $country = \App\Models\Country::find($currencyId);
    $symbol = '$';
    if (!empty($country)) {
        $symbol = $country->symbol;
    }
    return $symbol;
}
function provider_total_calculate($total_amount = 0, $provider_commission = 0, $commission_type = 'percent', $type = '', $totalEarning = 0, $count = 0)
{
    if ($total_amount === 0) {
        return [
            'value' => '-',
            'number_format' => 0
        ];
    }
    switch ($type) {
        case 'provider':
            // dump($provider_commission * $count);
            $earning =   ($total_amount) - ($provider_commission * $count);
            if ($commission_type === 'percent') {
                $earning = ($total_amount) * (100 - $provider_commission) / 100;
            }
            //   dump($earning);
            $final_amount = $earning;
            break;
    }
    return [
        'value' => getPriceFormat($final_amount),
        'number_format' => $final_amount
    ];
}

if (!function_exists('isActive')) {

    function isActive($route, $className = 'active')
    {
        $currentRoute = Route::currentRouteName();

        if (is_array($route)) {
            return in_array($currentRoute, $route) ? $className : '';
        }

        return $currentRoute == $route ? $className : '';
    }
}

function dbConnectionStatus(): bool
{
    try {
        DB::connection()->getPdo();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
function formatString($input)
{
    // Replace underscores with spaces, capitalize each word, and remove spaces
    return ucfirst(str_replace('_', ' ', $input));
}

if (!function_exists('getFooterSettings')) {
    function getFooterSettings()
    {
        $settings = \App\Models\Setting::whereIn('type', ['general-setting', 'social-media', 'site-setup'])
            ->whereIn('key', ['general-setting', 'social-media', 'site-setup'])
            ->get()
            ->keyBy('type');

        $generalSetting = $settings->has('general-setting') ? json_decode($settings['general-setting']->value) : null;
        $socialMedia = $settings->has('social-media') ? json_decode($settings['social-media']->value) : null;
        $appSetting = $settings->has('site-setup') ? json_decode($settings['site-setup']->value) : null;

        $copyrightText = $appSetting ? $appSetting->site_copyright : null;
        $position = strpos($copyrightText, 'by');
        $firstPart = $position !== false ? substr($copyrightText, 0, $position + 2) : $copyrightText;
        $secondPart = $position !== false ? substr($copyrightText, $position + 2) : '';
        $footerSection = App\Models\FrontendSetting::where('key', 'footer-setting')->first();
        $sectionData = $footerSection ? json_decode($footerSection->value, true) : null;
        $categories = [];
        $primary_locale = session()->get('locale', 'en');
        if (isset($sectionData['category_id']) && is_array($sectionData['category_id'])) {
            $categories = \App\Models\Category::whereIn('id', $sectionData['category_id'])
                ->where('status', 1)
                ->get();
            $categories->transform(function ($category) use ($primary_locale) {
                $category->name = $category->translations
                    ->firstWhere('locale', $primary_locale)?->value
                    ?? $category->translations->firstWhere('locale', 'en')?->value
                    ?? $category->name;

                return $category;
            });
        }
        if (isset($sectionData['service_id']) && is_array($sectionData['service_id'])) {
            $services = \App\Models\Service::whereIn('id', $sectionData['service_id'])
                ->where('status', 1)
                ->with('media');
            // Eager load the media relationship

            if (session()->has('user_lat') && session()->has('user_lng')) {
                $lat = $request->latitude ?? session('user_lat');
                $lng = $request->longitude ?? session('user_lng');
            }


            if (isset($lat) && !empty($lat) && isset($lng) && !empty($lng)) {



                $serviceZone = App\Models\ServiceZone::all();

                if (count($serviceZone) > 0) {

                    try {
                        $zoneTrait = new class {
                            use \App\Traits\ZoneTrait;
                        };
                        $matchingZoneIds = $zoneTrait->getMatchingZonesByLatLng($lat, $lng);



                        $services->whereHas('serviceZoneMapping', function ($services) use ($matchingZoneIds) {
                            $services->whereIn('zone_id', $matchingZoneIds);
                        });
                    } catch (\Exception $e) {
                        $services = $services;
                    }
                } else {

                    $get_distance = getSettingKeyValue('site-setup', 'radious') ?? 50;
                    $get_unit = getSettingKeyValue('site-setup', 'distance_type') ?? 'km';

                    $locations = $services->locationService($lat, $lng, $get_distance, $get_unit);
                    $service_in_location =  App\Models\ProviderServiceAddressMapping::whereIn('provider_address_id', $locations)->get()->pluck('service_id');
                    $services->with('providerServiceAddress')->whereIn('id', $service_in_location);
                }
            }

            $services = $services->get();


            $services->transform(function ($service) use ($primary_locale) {
                $service->name = $service->translations
                    ->firstWhere('locale', $primary_locale)?->value
                    ?? $service->translations->firstWhere('locale', 'en')?->value
                    ?? $service->name;

                return $service;
            });
        }
        return [
            'generalSetting' => $generalSetting,
            'socialMedia' => $socialMedia,
            'appSetting' => $appSetting,
            'copyright' => [
                'first_part' => $firstPart,
                'second_part' => $secondPart,
            ],
            'sectionData' => $sectionData,
            'categories' => $categories,
            'services' => $services,
        ];
    }

    function isProviderBannerEnabled(): bool
    {
        $setting = \App\Models\Setting::where('type', 'provider-banner')
            ->where('key', 'provider-banner')
            ->first();

        if (!$setting) {
            return false;
        }

        $settings = json_decode($setting->value, true);

        return isset($settings['promotion_enable']) && $settings['promotion_enable'] === 1;
    }
}


function getSettingValue($property)
{
    $setting = \App\Models\Setting::where('key', 'OTHER_SETTING')->first();
    if ($setting) {
        $data = json_decode($setting->value, true);
        return isset($data[$property]) ? $data[$property] : null;
    }
    return null;
}

/**
 * Get meta tags for the current page (category, subcategory, service, or fallback)
 * @return array
 */
function getMetaTagsForPage()
{
    $route = request()->route();
    $routeName = $route ? $route->getName() : null;
    $meta = [
        'meta_title' => config('app.name', 'Laravel'),
        'meta_description' => '',
        'meta_keywords' => '',
        'og_image' => '',
    ];
    $locale = app()->getLocale();

    $globalSeoSetting = \App\Models\SeoSetting::first();
    // dd($globalSeoSetting);
    $global = [
        'meta_title' => '',
        'meta_description' => '',
        'meta_keywords' => '',
        'og_image' => '',
    ];

    if ($globalSeoSetting && $globalSeoSetting->meta_keywords) {
        if (is_array($globalSeoSetting->meta_keywords ?? null)) {
            $metaKeywordsValue = implode(',', $globalSeoSetting->meta_keywords);
        } elseif (!empty($globalSeoSetting->meta_keywords)) {
            $metaKeywordsValue = $globalSeoSetting->meta_keywords;
        } else {
            $metaKeywordsValue = '';
        }
        $global['meta_title'] = $globalSeoSetting->meta_title ?? '';
        $global['meta_description'] = $globalSeoSetting->meta_description ?? '';
        $global['meta_keywords'] = $metaKeywordsValue;
        $global['og_image'] = $globalSeoSetting->getFirstMediaUrl('seo_image') ?? '';

    }


    $module = [
        'meta_title' => '',
        'meta_description' => '',
        'meta_keywords' => '',
        'og_image' => '',
    ];

    $moduleType = null;
    // dd($routeName);
    if ($routeName) {
        if (strpos($routeName, 'category.') === 0 || in_array($routeName, ['category.detail', 'category.index', 'category.list', 'category.data'])) {
            $moduleType = 'category';
        } elseif (strpos($routeName, 'subcategory.') === 0 || in_array($routeName, ['subcategory.detail', 'subcategory.index', 'subcategory.list', 'subcategory.data'])) {
            $moduleType = 'subcategory';
        } elseif (strpos($routeName, 'service.') === 0 || in_array($routeName, ['service.detail', 'service.view', 'service.list', 'service.data'])) {
            $moduleType = 'service';
        }
    }
    // dd($moduleType);
    switch ($moduleType) {
        case 'category':
            $category = null;
            $id = request()->route('id');
            if ($id) {
                $category = \App\Models\Category::find($id);
            } elseif (isset($GLOBALS['category'])) {
                $category = $GLOBALS['category'];
            }
            if ($category && $category->seo_enabled == 1) {
                $module['meta_title'] = $category->translate('meta_title', $locale) ?: $category->name;
                $module['meta_description'] = $category->translate('meta_description', $locale) ?: $category->description;
                if (is_array($category->meta_keywords)) {
                    $module['meta_keywords'] = implode(',', $category->meta_keywords);
                } elseif (is_string($category->meta_keywords)) {
                    $decoded = json_decode($category->meta_keywords, true);
                    $module['meta_keywords'] = is_array($decoded) ? implode(',', $decoded) : $category->meta_keywords;
                } else {
                    $module['meta_keywords'] = '';
                }
                $module['og_image'] = $category->getFirstMediaUrl('seo_image') ?: '';
            }
            break;
        case 'subcategory':
            $subcategory = null;
            $id = request()->route('id');
            if ($id) {
                $subcategory = \App\Models\SubCategory::find($id);
            } elseif (isset($GLOBALS['subcategory'])) {
                $subcategory = $GLOBALS['subcategory'];
            }
            if ($subcategory && $subcategory->seo_enabled == 1) {
                $module['meta_title'] = $subcategory->translate('meta_title', $locale) ?: $subcategory->name;
                $module['meta_description'] = $subcategory->translate('meta_description', $locale) ?: $subcategory->description;
                // $module['meta_keywords'] = is_array($subcategory->meta_keywords) ? implode(',', $subcategory->meta_keywords) : $subcategory->meta_keywords;
                if (is_array($subcategory->meta_keywords)) {
                    $module['meta_keywords'] = implode(',', $subcategory->meta_keywords);
                } elseif (is_string($subcategory->meta_keywords)) {
                    $decoded = json_decode($subcategory->meta_keywords, true);
                    $module['meta_keywords'] = is_array($decoded) ? implode(',', $decoded) : $subcategory->meta_keywords;
                } else {
                    $module['meta_keywords'] = '';
                }
                $module['og_image'] = $subcategory->getFirstMediaUrl('seo_image') ?: '';
            }
            break;
        case 'service':
            $service = null;
            $id = request()->route('id');
            if ($id) {
                $service = \App\Models\Service::find($id);
            } elseif (isset($GLOBALS['service'])) {
                $service = $GLOBALS['service'];
            }
            if ($service && $service->seo_enabled == 1) {
                $module['meta_title'] = $service->translate('meta_title', $locale) ?: $service->name;
                $module['meta_description'] = $service->translate('meta_description', $locale) ?: $service->description;
                // $module['meta_keywords'] = is_array($service->meta_keywords) ? implode(',', $service->meta_keywords) : $service->meta_keywords;
                if (is_array($service->meta_keywords)) {
                    $module['meta_keywords'] = implode(',', $service->meta_keywords);
                } elseif (is_string($service->meta_keywords)) {
                    $decoded = json_decode($service->meta_keywords, true);
                    $module['meta_keywords'] = is_array($decoded) ? implode(',', $decoded) : $service->meta_keywords;
                } else {
                    $module['meta_keywords'] = '';
                }
                $module['og_image'] = $service->getFirstMediaUrl('seo_image') ?: '';
            }
            break;
    }

    $meta['meta_title'] = isFilled($module['meta_title']) ? $module['meta_title'] : (isFilled($global['meta_title']) ? $global['meta_title'] : $meta['meta_title']);
    $meta['meta_description'] = isFilled($module['meta_description']) ? $module['meta_description'] : (isFilled($global['meta_description']) ? $global['meta_description'] : $meta['meta_description']);
    $meta['meta_keywords'] = isFilled($module['meta_keywords']) ? $module['meta_keywords'] : (isFilled($global['meta_keywords']) ? $global['meta_keywords'] : $meta['meta_keywords']);
    $meta['og_image'] = isFilled($module['og_image']) ? $module['og_image'] : (isFilled($global['og_image']) ? $global['og_image'] : $meta['og_image']);
    // dd($meta);
    return $meta;
}

function isFilled($value) {
    return isset($value) && trim($value) !== '';
}
