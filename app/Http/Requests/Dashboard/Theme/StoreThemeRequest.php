<?php

namespace App\Http\Requests\Dashboard\Theme;

use Illuminate\Foundation\Http\FormRequest;

class StoreThemeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $themeId = $this->route('theme')?->id;

        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:themes,code,' . $themeId,
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'paid_type' => 'required|in:free,paid',
            'price' => 'required_if:paid_type,paid|nullable|numeric|min:0',
            'company_id' => 'nullable|exists:companies,id',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => trans('dashboard/themes.name'),
            'code' => trans('dashboard/themes.code'),
            'description' => trans('dashboard/themes.description'),
            'is_active' => trans('dashboard/themes.is_active'),
            'is_default' => trans('dashboard/themes.is_default'),
            'paid_type' => trans('dashboard/themes.paid_type'),
            'price' => trans('dashboard/themes.price'),
            'company_id' => trans('dashboard/themes.company'),
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => trans('dashboard/themes.validation.name_required'),
            'code.required' => trans('dashboard/themes.validation.code_required'),
            'code.unique' => trans('dashboard/themes.validation.code_unique'),
            'paid_type.required' => trans('dashboard/themes.validation.paid_type_required'),
            'paid_type.in' => trans('dashboard/themes.validation.paid_type_in'),
            'price.required_if' => trans('dashboard/themes.validation.price_required_if_paid'),
            'price.numeric' => trans('dashboard/themes.validation.price_numeric'),
            'price.min' => trans('dashboard/themes.validation.price_min'),
        ];
    }
}