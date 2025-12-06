<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\BookingService;
use Illuminate\Support\Facades\Auth;

class CheckBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'apartment_id' => 'required|exists:apartments,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (BookingService::hasUserBookingForSamePeriod(
                Auth::id(),
                $this->apartment_id,
                $this->check_in,
                $this->check_out
            )) {
                $validator->errors()->add(
                    'apartment_id', 
                    'You already have a booking for this apartment during this period'
                );
            }
            
            if (!BookingService::isApartmentAvailable(
                $this->apartment_id,
                $this->check_in,
                $this->check_out,
                Auth::id()
            )) {
                $validator->errors()->add(
                    'check_in', 
                    'The apartment is not available for these dates'
                );
            }
        });
    }
}