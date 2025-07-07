<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterRequest extends FormRequest
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
            'limit' => 'nullable|integer|between:1,500',
            'page' => 'nullable|integer|min:1',
            'dateFrom' => 'nullable|date|date_format:Y-m-d',
            'dateTo' => 'nullable|date|date_format:Y-m-d|after_or_equal:dateFrom'
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'dateFrom' => $this->dateFrom ?: null,
            'dateTo' => $this->dateTo ?: null,
        ]);
    }
}
