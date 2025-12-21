<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApartmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if($this->isMethod('post')){
        return [
          'site'=>'required|string',
          'type'=>'required|in:home,villa,warehouse',
          'area'=>'required|integer',
          'number_of_room'=>'required|integer|min:1|max:10',
          'city'=>'required|string',
          'price'=>'required|integer',
          'image'=>'required|image|mimes:png,jpg,jpeg|max:20048',
          'description'=>'required|string'
        ];
    }
        else
        {
            return [

            ];
        }
    }
}
