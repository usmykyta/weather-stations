<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class WeatherFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'lat' => [
                'required', 'string',
            ],
            'lng' => [
                'required', 'string'
            ],
            'units' => [
                'sometimes', 'in:standard,metric,imperial'
            ],
            'lang' => [
                'sometimes', 'in:en,ua,uk'
            ]
        ];
    }
}
