<?php

namespace App\Http\Controllers;

use App\Models\ServiceZone;
use App\Models\User;
use App\Models\Service;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;
use App\Models\ProviderAddressMapping;
use App\Models\ServiceZoneMapping;
use App\Models\ProviderZoneMapping;

class ServiceZoneController extends Controller
{
    public function index(Request $request)
    {
        $zones = ServiceZone::all();

        $zoneStats = $zones->map(function ($zone) {
            $providerIds = $this->getProviderIdsInZone($zone);

            $categoryIds = Service::whereIn('provider_id', $providerIds)
                ->pluck('category_id')
                ->filter()
                ->unique()
                ->toArray();

            return [
                'zone' => $zone,
                'providers' => count($providerIds),
                'categories' => count($categoryIds),
            ];
        });
        $auth_user = auth()->user();
        $pageTitle = trans('messages.list_form_title', ['form' => trans('messages.servicezone')]);
        $filter = [
            'status' => $request->status,
        ];

        return view('servicezone.index', compact('zoneStats', 'pageTitle', 'filter', 'auth_user'));
    }

    public function index_data(DataTables $datatable, Request $request)
    {
        try {
            $query = ServiceZone::query();

            $query->orderBy('created_at', 'DESC');

            $filter = $request->filter;
            if (isset($filter) && isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }

            if (auth()->check() && auth()->user()->hasAnyRole(['admin'])) {
                $query->withTrashed();
            }
            $primary_locale = app()->getLocale() ?? 'en';

            return $datatable->eloquent($query)
                ->addColumn('check', function ($row) {
                    return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-' . $row->id . '"  name="datatable_ids[]" value="' . $row->id . '" data-type="servicezone" onclick="dataTableRowCheck(' . $row->id . ',this)">';
                })
                ->editColumn('name', function ($row) {
                    if (auth()->user()->can('service zone edit')) {
                        return '<a class="btn-link btn-link-hover" href="' . route('servicezone.create', ['id' => $row->id]) . '">' . e($row->name) . '</a>';
                    }
                    return e($row->name);
                })
                ->addColumn('providers', function ($row) {
                    try {

                        $providerQuery = ProviderZoneMapping::where('zone_id', $row->id)
                            ->whereHas('provider', function ($query) {
                                $query->where('status', 1);
                            });

                        $providerIds = $providerQuery->pluck('provider_id')->toArray();
                        $providerCount = count($providerIds);

                        if ($providerCount > 0) {

                            return '<a class="btn-link btn-link-hover" data-bs-toggle="tooltip" title="View Provider" href="' . route('provider.index') . '?zone_id=' . $row->id . '">' . $providerCount . '</a>';
                        } else {

                            return $providerCount;
                        }
                    } catch (\Exception $e) {
                        Log::error('Error getting providers for zone: ' . $row->id, [
                            'error' => $e->getMessage()
                        ]);
                        return 0;
                    }
                })
                ->addColumn('service_count', function ($row) {

                    $servicecount = ServiceZoneMapping::where('zone_id', $row->id)
                        ->whereHas('service', function ($query) {
                            $query->where('status', 1)
                                ->where('service_request_status', 'approve');
                        })
                        ->count();

                    if ($servicecount > 0) {
                        return '<a class="btn-link btn-link-hover" href="' . route('service.index') . '?zone_id=' . $row->id . '">' . $servicecount . '</a>';
                    } else {

                        return $servicecount;
                    }
                })
                ->editColumn('status', function ($row) {
                    $disabled = $row->trashed() ? 'disabled' : '';
                    return '<div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                            <div class="custom-switch-inner">
                                <input type="checkbox" class="custom-control-input change_status" data-type="servicezone_status" ' . ($row->status ? "checked" : "") . ' ' . $disabled . ' value="' . $row->id . '" id="status-' . $row->id . '" data-id="' . $row->id . '">
                                <label class="custom-control-label" for="status-' . $row->id . '" data-on-label="" data-off-label=""></label>
                            </div>
                        </div>';
                })
                ->addColumn('action', function ($row) {
                    return view('servicezone.action', ['data' => $row])->render();
                })
                ->rawColumns(['check', 'name', 'status', 'action', 'providers', 'service_count'])
                ->toJson();
        } catch (\Exception $e) {
            Log::error('Error in service zone index_data: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching data'], 500);
        }
    }

    protected function getProviderIdsInZone($zone)
    {
        try {
            $polygon = is_string($zone->coordinates)
                ? json_decode($zone->coordinates, true)
                : $zone->coordinates;

            if (!$polygon || !is_array($polygon)) {
                return [];
            }

            $mappings = ProviderAddressMapping::with('providers')
                ->whereNotNull('address')
                ->get();

            $providerIds = [];

            foreach ($mappings as $mapping) {
                $location = null;

                if ($mapping->latitude && $mapping->longitude) {
                    $location = [
                        'lat' => $mapping->latitude,
                        'lng' => $mapping->longitude,
                    ];
                } else {
                    $location = $this->getLatLngFromAddress($mapping->address);
                    if ($location) {
                        $mapping->latitude = $location['lat'];
                        $mapping->longitude = $location['lng'];
                        $mapping->save();
                    }
                }

                if ($location && $this->pointInPolygon($location, $polygon)) {
                    $providerIds[] = $mapping->provider_id;
                }
            }

            return $providerIds;
        } catch (\Exception $e) {
            Log::error('Error in getProviderIdsInZone: ' . $e->getMessage());
            return [];
        }
    }

    protected function getLatLngFromAddress($address)
    {
        if (!$address) return null;

        try {
            $apiKey = config('services.google_maps.key');
            if (!$apiKey) {
                Log::error('Google Maps API key not configured');
                return null;
            }

            $response = Http::timeout(5)->get("https://maps.googleapis.com/maps/api/geocode/json", [
                'address' => $address,
                'key' => $apiKey,
            ]);

            $data = $response->json();

            if (!empty($data['results'][0]['geometry']['location'])) {
                return [
                    'lat' => $data['results'][0]['geometry']['location']['lat'],
                    'lng' => $data['results'][0]['geometry']['location']['lng'],
                ];
            }
        } catch (\Exception $e) {
            Log::error('Error geocoding address: ' . $address, [
                'error' => $e->getMessage()
            ]);
        }

        return null;
    }

    public function create(Request $request)
    {
        // dd('hello');
        if (!auth()->user()->can('service zone add')) {
            return redirect()->back()->withErrors(trans('messages.demo_permission_denied'));
        }

        $id = $request->id;
        $auth_user = authSession();
        $servicezone = ServiceZone::find($id);

        // Handle coordinates for existing service zone
        if ($servicezone) {
            // Ensure coordinates are properly decoded
            if (is_string($servicezone->coordinates)) {
                $servicezone->coordinates = json_decode($servicezone->coordinates, true);
            }
            $pageTitle = trans('messages.update_form_title', ['form' => trans('messages.servicezone')]);
        } else {
            $pageTitle = trans('messages.add_button_form', ['form' => trans('messages.servicezone')]);
            $servicezone = new ServiceZone;
            $servicezone->coordinates = [];
        }

        // Debug coordinates
        \Log::info('Service Zone Coordinates:', [
            'zone_id' => $id,
            'coordinates' => $servicezone->coordinates,
            'coordinates_type' => gettype($servicezone->coordinates)
        ]);

        return view('servicezone.create', compact('pageTitle', 'servicezone', 'auth_user'));
    }

    public function store(Request $request)
    {

        if (!auth()->user()->can('service zone add')) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => trans('messages.demo_permission_denied')
                ], 403);
            }
            return redirect()->back()->withErrors(trans('messages.demo_permission_denied'));
        }

        $request->validate([
            'name' => 'required|string|unique:service_zones,name,' . ($request->id ?? 'NULL') . ',id',
            'coordinates' => ['required', 'json', function ($attribute, $value, $fail) {
                $decoded = json_decode($value, true);

                if (!is_array($decoded) || count($decoded) < 3) {
                    $fail('Please draw a valid zone with at least 3 points.');
                }
            }],
        ]);

        try {
            $data = $request->all();

            // Ensure coordinates are properly formatted as JSON string
            $coordinates = is_array($data['coordinates']) ? json_encode($data['coordinates']) : $data['coordinates'];

            $result = ServiceZone::updateOrCreate(
                ['id' => $data['id'] ?? null],
                [
                    'name' => $data['name'],
                    'coordinates' => $coordinates,
                    'status' => $data['status'] ?? 1,
                ]
            );

            $message = trans('messages.update_form', ['form' => trans('messages.servicezone')]);
            if ($result->wasRecentlyCreated) {
                $message = trans('messages.save_form', ['form' => trans('messages.servicezone')]);
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            return redirect()->route('servicezone.index')->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Error storing service zone: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Zone Name already exists'
                ], 500);
            }

            return redirect()->back()->withErrors(['error' => 'An error occurred while saving the service zone']);
        }
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        try {
            $zone = ServiceZone::withTrashed()->findOrFail($id);

            if ($zone->trashed()) {
                $zone->forceDelete();
                $message = __('messages.permanent_deleted');
            } else {
                $zone->delete();
                $message = __('messages.deleted');
            }

            return response()->json([
                'status' => true,
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting service zone: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while deleting the service zone',
            ], 500);
        }
    }

    public function action(Request $request)
    {
        try {
            $zone = ServiceZone::withTrashed()->findOrFail($request->id);

            if ($request->type == 'restore') {
                $zone->restore();
                // $message = __('messages.restored');
                $message = __('messages.msg_restored', ['name' => __('messages.servicezone')]);
            } elseif ($request->type == 'status') {
                $zone->status = !$zone->status;
                $zone->save();
                $message = __('messages.status_updated');
            } elseif ($request->type == 'forcedelete') {
                $zone->forceDelete();
                $message = __('messages.msg_forcedelete', ['name' => __('messages.servicezone')]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid action type',
                ], 400);
            }

            return response()->json([
                'status' => true,
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in service zone action: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while processing the action',
            ], 500);
        }
    }

    public function bulk_action(Request $request)
    {
        try {
            $ids = explode(',', $request->rowIds);
            $actionType = $request->action_type;
            $message = 'Bulk Action Updated';

            switch ($actionType) {
                case 'change-status':
                    ServiceZone::whereIn('id', $ids)->update(['status' => $request->status]);
                    $message = 'Bulk Service Zone Status Updated';
                    break;

                case 'delete':
                    ServiceZone::whereIn('id', $ids)->delete();
                    $message = 'Bulk Service Zone Deleted';
                    break;

                case 'restore':
                    ServiceZone::whereIn('id', $ids)->restore();
                    $message = 'Bulk Service Zone Restored';
                    break;

                case 'permanently-delete':
                    ServiceZone::whereIn('id', $ids)->forceDelete();
                    $message = 'Bulk Service Zone Permanently Deleted';
                    break;

                default:
                    return response()->json(['status' => false, 'message' => 'Action Invalid']);
            }

            return response()->json(['status' => true, 'message' => $message]);
        } catch (\Exception $e) {
            Log::error('Error in bulk action: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while processing the bulk action',
            ], 500);
        }
    }

    protected function pointInPolygon($point, $polygon)
    {
        $lat = $point['lat'];
        $lng = $point['lng'];

        $inside = false;
        $numPoints = count($polygon);
        $j = $numPoints - 1;

        for ($i = 0; $i < $numPoints; $i++) {
            $xi = $polygon[$i]['lat'];
            $yi = $polygon[$i]['lng'];
            $xj = $polygon[$j]['lat'];
            $yj = $polygon[$j]['lng'];

            $intersect = (($yi > $lng) != ($yj > $lng)) &&
                ($lat < ($xj - $xi) * ($lng - $yi) / (($yj - $yi) ?: 0.0000001) + $xi);

            if ($intersect) {
                $inside = !$inside;
            }
            $j = $i;
        }

        return $inside;
    }
}
