<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\User;
use App\Models\Booking;
use App\Models\ProviderPayout as ProviderPayoutModel;
use App\Models\CommissionEarning;

class ProviderPayout extends FormRequest
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
        $method = request()->method();

         $provider_id = request()->provider_id;

        $payment_method = request()->payment_method;
        $amount = request()->amount;

        switch ($method) {
            case 'POST':
 
               if($payment_method=='bank'){

                return [
                    'payment_method' => 'required',
                    'provider_id' => 'required',
                    'bank'=>'required',
                    'amount' => 'required|numeric|min:1|max:'.$amount,
                  ];

                }else{

                    return [
                        'payment_method' => 'required',
                        'provider_id' => 'required',
                        'amount' => 'required|numeric|min:1|max:'.$amount,
                    ];



                }
                
                break;
            case 'DELETE':
                return [];
                break;
            default:
                return [];
                break;
        }
        return [
            //
        ];
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
