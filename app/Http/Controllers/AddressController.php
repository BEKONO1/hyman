<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\API\AddressResource;

class AddressController extends Controller
{
    // Insert or update address for authenticated user
    public function storeOrUpdate(Request $request)
    {

        $data = $request->all();
        $validator = \Validator::make($data,[
            'address' => 'required|string',
            'lat' => 'required|numeric',
            'long' => 'required|numeric',
            'status' => 'sometimes|integer',
            'id' => 'sometimes|integer|exists:addresses,id'
        ]);

        if($validator->fails()) {
            $message = $validator->errors()->first();
            return comman_custom_response(['status' => false, 'message' => $message],422);
        }

        $userId = auth()->id();
        $data['user_id'] = $userId;

        $address = Address::updateOrCreate(
            ['id' => $request->id, 'user_id' => $userId],
            [
                'address' => $request->address,
                'lat' => $request->lat,
                'long' => $request->long,
                'status' => $request->status ?? 1,
            ]
        );
        
        $message = $request->has('id') ? __('messages.update_form', ['form' => __('messages.address')]) : __('messages.save_form', ['form' => __('messages.address')]);
        
        if ($request->is('api/*')) {
            return comman_custom_response(['status' => true, 'data' => new AddressResource($address),'message' => $message],200);
            // return response()->json([
            //     'success' => true,
            //     'data' => new AddressResource($address),
            //     'message' => $message
            // ]);
        }
        return redirect()->back()->withSuccess($message);
    }

    // List all addresses for authenticated user
    public function index(Request $request)
    {
        $userId = auth()->id();
        $addresses = Address::where('user_id', $userId);

        $per_page = config('constant.PER_PAGE_LIMIT', 15);
        if ($request->has('per_page') && !empty($request->per_page)) {
            if (is_numeric($request->per_page)) {
                $per_page = $request->per_page;
            }
            if ($request->per_page === 'all') {
                $per_page = $addresses->count();
            }
        }
        $addresses = $addresses->orderBy('created_at', 'desc')->paginate($per_page);
        $items = \App\Http\Resources\API\AddressResource::collection($addresses);
        if ($request->is('api/*')) {
            $response = [
                'status' => true,
                'message' => __('messages.address_list_success'),
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
        return view('address.index', compact('addresses'));
    }
    public function show(Request $request, $id)
    {
        $userId = Auth::id();
        $address = Address::where('id', $id)->where('user_id', $userId)->first();
        if (!$address) {
            $message = __('messages.record_not_found');
            if ($request->is('api/*')) {
                return comman_message_response($message, 404);
            }
            return redirect()->back()->withErrors($message);
        }
        if ($request->is('api/*')) {
            $message = __('messages.record_found', ['record' => __('messages.address')]);
            return comman_custom_response(['status' => true,'message' => $message, 'data' => new AddressResource($address)]);
        }
        return view('address.show', compact('address'));
    }

    // Delete address by id for authenticated user
    public function destroy(Request $request, $id)
    {
        $userId = Auth::id();
        $address = Address::where('id', $id)->where('user_id', $userId)->first();
        if (!$address) {
            $message = __('messages.record_not_found');
            if ($request->is('api/*')) {
                return comman_message_response($message, 404);
            }
            return redirect()->back()->withErrors($message);
        }
        $address->delete();
        $message = __('messages.delete_form', ['form' => __('messages.address')]);
        if ($request->is('api/*')) {
            return comman_message_response($message);
        }
        return redirect()->back()->withSuccess($message);
    }
} 