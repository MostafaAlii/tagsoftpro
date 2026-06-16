<?php

namespace App\Http\Requests\Dashboard\Module;

use Illuminate\Foundation\Http\FormRequest;

class StoreModuleRequest extends FormRequest
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
            'project_types' => 'nullable|array',
            'project_types.*' => 'exists:project_types,id',
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
            $attributes["name.{$locale}"] = trans('dashboard/modules.name') . ' (' . strtoupper($locale) . ')';
            $attributes["description.{$locale}"] = trans('dashboard/modules.description') . ' (' . strtoupper($locale) . ')';
        }

        $attributes['status'] = trans('dashboard/modules.status');
        $attributes['project_type_id'] = trans('dashboard/modules.project_type');
        return $attributes;
    }

    public function messages(): array
    {
        return [
            'name.*.required' => trans('dashboard/modules.validation.name_required'),
            'name.*.string' => trans('dashboard/modules.validation.name_string'),
            'name.*.max' => trans('dashboard/modules.validation.name_max'),
            'project_type_id.exists' => trans('dashboard/modules.validation.project_type_exists'),
        ];
    }
}