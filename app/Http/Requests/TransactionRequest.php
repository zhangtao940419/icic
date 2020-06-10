<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
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
        return [
            'base_coin_id' => 'required',
            'exchange_coin_id' => 'required',
            'vol' => 'required',
            'price_float' => 'required',
            'float_type' => 'required',
            'min_price' => 'required',
            'max_price' => 'required',
            'current_price' => 'required',
            'day_vol' => 'required'
        ];
    }
}
