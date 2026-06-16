<?php

namespace App\Http\Requests\Dashboard\Project;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\Project\ProjectStatus;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $locales = array_keys(config('laravellocalization.supportedLocales'));

        $rules = [
            'status' => 'required|in:active,inactive,published,draft',
            'company_id' => 'nullable|exists:companies,id',
            'project_types' => 'nullable|array',
            'project_types.*' => 'exists:project_types,id',
            'modules' => 'nullable|array',
            'modules.*' => 'exists:modules,id',
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
            $attributes["name.{$locale}"] = trans('dashboard/projects.name') . ' (' . strtoupper($locale) . ')';
            $attributes["description.{$locale}"] = trans('dashboard/projects.description') . ' (' . strtoupper($locale) . ')';
        }

        $attributes['status'] = trans('dashboard/projects.status');
        $attributes['project_types'] = trans('dashboard/projects.project_types');
        $attributes['modules'] = trans('dashboard/projects.modules');

        return $attributes;
    }

    public function messages(): array
    {
        return [
            'name.*.required' => trans('dashboard/projects.validation.name_required'),
            'status.required' => trans('dashboard/projects.validation.status_required'),
            'status.in' => trans('dashboard/projects.validation.status_in'),
        ];
    }
}