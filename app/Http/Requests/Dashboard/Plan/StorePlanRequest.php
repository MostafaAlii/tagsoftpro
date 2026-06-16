<?php

namespace App\Http\Requests\Dashboard\Plan;

use Illuminate\Foundation\Http\FormRequest;

class StorePlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $locales = array_keys(config('laravellocalization.supportedLocales'));

        $rules = [
            'price'         => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly',
            'status'        => 'boolean',
            'company_id'    => 'nullable|exists:companies,id',
        ];

        foreach ($locales as $locale) {
            $rules["name.{$locale}"] = 'required|string|max:255';
            $rules["description.{$locale}"] = 'nullable|string|max:1000';
        }

        return $rules;
    }

    public function attributes(): array
    {
        $locales = array_keys(config('laravellocalization.supportedLocales'));
        $attributes = [];

        foreach ($locales as $locale) {
            $attributes["name.{$locale}"] = trans('dashboard/plans.name') . ' (' . strtoupper($locale) . ')';
            $attributes["description.{$locale}"] = trans('dashboard/plans.description') . ' (' . strtoupper($locale) . ')';
        }

        $attributes['price']         = trans('dashboard/plans.price');
        $attributes['billing_cycle'] = trans('dashboard/plans.billing_cycle');
        $attributes['status']        = trans('dashboard/plans.status');

        return $attributes;
    }

    public function messages(): array
    {
        return [
            'price.required' => trans('dashboard/plans.validation.price_required'),
            'price.numeric'  => trans('dashboard/plans.validation.price_numeric'),
            'price.min'      => trans('dashboard/plans.validation.price_min'),
            'billing_cycle.required' => trans('dashboard/plans.validation.billing_cycle_required'),
            'billing_cycle.in'       => trans('dashboard/plans.validation.billing_cycle_in'),
        ];
    }
}