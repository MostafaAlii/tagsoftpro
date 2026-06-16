<?php

namespace App\Http\Requests\Dashboard\Department;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $locales = array_keys(config('laravellocalization.supportedLocales'));

        $rules = [
            'status' => 'boolean',
            'company_id' => 'nullable|exists:companies,id',
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
            $attributes["name.{$locale}"] = trans('dashboard/departments.name') . ' (' . strtoupper($locale) . ')';
            $attributes["description.{$locale}"] = trans('dashboard/departments.description') . ' (' . strtoupper($locale) . ')';
        }

        $attributes['status'] = trans('dashboard/departments.status');

        return $attributes;
    }

    public function messages(): array
    {
        return [
            'name.*.required' => trans('dashboard/departments.validation.name_required'),
            'name.*.string' => trans('dashboard/departments.validation.name_string'),
            'name.*.max' => trans('dashboard/departments.validation.name_max'),
        ];
    }
}