<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Booking;
use App\Models\User;
use Yajra\DataTables\DataTables;
use App\Models\PackageServiceMapping;
use App\Traits\TranslationTrait;
class UserServiceListController extends Controller
{
    use TranslationTrait;

    public function index_data(DataTables $datatable,Request $request)
    {

        $query = Service::query()->with(['translations', 'category.translations']);


        $filter = $request->filter;

        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }
        }
        if(auth()->user()->hasAnyRole(['admin','demo_admin'])){
            $query = $query->where('service_type','user_post_service')->withTrashed();
        }
        $primary_locale = app()->getLocale() ?? 'en';
        return $datatable->eloquent($query)
            ->addColumn('check', function ($row) {

                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-'.$row->id.'" data-type="service"  name="datatable_ids[]" value="'.$row->id.'" onclick="dataTableRowCheck('.$row->id.',this)">';
            })
            ->editColumn('name', function($query) use($primary_locale){
                $name = $this->getTranslation($query->translations, $primary_locale, 'name', $query->name) ?? $query->name;

                return $name ?? '-';
               
            })
            ->filterColumn('name',function($query,$keyword) use($primary_locale){
                if ($primary_locale !== 'en') {
                    $query->where(function ($query) use ($keyword, $primary_locale) {
                        $query->whereHas('translations', function($query) use ($keyword, $primary_locale) {
                                // Search in the translations table based on the primary_locale
                                $query->where('locale', $primary_locale)
                                      ->where('value', 'LIKE', '%'.$keyword.'%');
                            })
                            ->orWhere('name', 'LIKE', '%'.$keyword.'%'); // Fallback to 'name' field if no translation is found
                    });
                } else {
                    $query->where('name', 'LIKE', '%'.$keyword.'%');
                }
               
            })
            ->editColumn('category_id' , function ($query) use($primary_locale){
                $catname =  $this->getTranslation(optional($query->category)->translations, $primary_locale, 'name', optional($query->category)->name) ?? optional($query->category)->name;
                return $catname ?? '-';
                //return ($service->category_id != null && isset($service->category)) ? $service->category->name : '-';
            })
            ->filterColumn('category_id',function($query,$keyword) use($primary_locale){
                $query->whereHas('category', function ($q) use ($keyword, $primary_locale) {
                    // Check if the locale is not 'en'
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
            ->orderColumn('category_id', function ($query, $order) {
                $query->join('categories', 'categories.id', '=', 'services.category_id')
                      ->orderBy('categories.name', $order);
            })

            ->editColumn('provider_id' , function ($query){
                return view('service.service', compact('query'));
            })
            ->filterColumn('provider_id',function($query,$keyword){
                $query->whereHas('providers',function ($q) use($keyword){
                    $q->where('display_name','like','%'.$keyword.'%');
                });
            })
            ->orderColumn('provider_id', function ($query, $order) {
                $query->select('services.*')
                      ->join('users as providers', 'providers.id', '=', 'services.provider_id')
                      ->orderBy('providers.display_name', $order);   
            })
            ->editColumn('price' , function ($service){
                return getPriceFormat($service->price).'-'.ucFirst($service->type);
            })

            ->editColumn('discount' , function ($service){
                return $service->discount ? $service->discount .'%' : '-';
            })
            ->addColumn('action', function ($data) {
                return view('service.user_service_action', compact('data'));
            })
            ->editColumn('status' , function ($query){
                $disabled = $query->trashed() ? 'disabled': '';
                return '<div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                    <div class="custom-switch-inner">
                        <input type="checkbox" class="custom-control-input  change_status" data-type="user_service_status" '.($query->status ? "checked" : "").'  '.$disabled.' value="'.$query->id.'" id="'.$query->id.'" data-id="'.$query->id.'">
                        <label class="custom-control-label" for="'.$query->id.'" data-on-label="" data-off-label=""></label>
                    </div>
                </div>';
            })
            ->rawColumns(['check','action','status','is_featured'])
            ->toJson();
    }
}
