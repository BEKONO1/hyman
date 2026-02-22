<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class HandymanTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request()->id;
        $createdBy = Auth::id();
        return [
            'name' => [
                'required',
                Rule::unique('handyman_types')  // Replace with your actual table name
                    ->where(function ($query) use ($createdBy) {
                        return $query->where('created_by', $createdBy);  // Ensure name is unique for the authenticated user
                    })
                    ->ignore($id),  // Ignore the current record's ID when updating
            ],
            'commission'        => 'required',
            'status'            => 'required',
        ];
    }
    public function messages()
    {
        return [];
    }
    protected function failedValidation(Validator $validator)
    {
        if ( request()->is('api*')){
            $data = [
                'status' => 'false',
                'message' => $validator->errors()->first(),
                'all_message' =>  $validator->errors()
            ];

            throw new HttpResponseException(response()->json($data,422));
        }

        throw new HttpResponseException(redirect()->back()->withInput()->with('errors', $validator->errors()));
    }
}
