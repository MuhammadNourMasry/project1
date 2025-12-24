<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterApartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'governorate' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'min_price' => 'nullable|integer|min:0',
            'max_price' => 'nullable|integer|min:0',
            'number_of_room' => 'nullable|integer|min:1|max:10',
            'exact_rooms' => 'nullable|integer|min:1|max:10', 
            'type' => 'nullable|in:home,villa,warehouse',
            'min_area' => 'nullable|integer|min:0',
            'max_area' => 'nullable|integer|min:0',
            'min_rating' => 'nullable|integer|min:1|max:5',
            'rating' => 'nullable|integer|min:1|max:5', 
            'exact_rating' => 'nullable|integer|min:1|max:5', 
            'search' => 'nullable|string|max:255',
            'sort_by' => 'nullable|in:price_asc,price_desc,newest,oldest,rating_high,rating_low,area_large,area_small,rooms_asc,rooms_desc',
            'limit' => 'nullable|integer|min:1|max:100',
            'is_favorite' => 'nullable|in:true,false',
            'user_id' => 'nullable|integer|exists:users,id'
        ];
    }
    
    public function messages(): array
    {
        return [
            'number_of_room.min' => 'عدد الغرف يجب أن يكون 1 على الأقل',
            'number_of_room.max' => 'عدد الغرف يجب أن يكون 10 على الأكثر',
            'rooms.min' => 'عدد الغرف يجب أن يكون 1 على الأقل',
            'rooms.max' => 'عدد الغرف يجب أن يكون 10 على الأكثر',
            'exact_rooms.min' => 'عدد الغرف يجب أن يكون 1 على الأقل',
            'exact_rooms.max' => 'عدد الغرف يجب أن يكون 10 على الأكثر',
            'type.in' => 'النوع يجب أن يكون: home, villa, warehouse',
            'sort_by.in' => 'ترتيب غير صحيح'
        ];
    }
}