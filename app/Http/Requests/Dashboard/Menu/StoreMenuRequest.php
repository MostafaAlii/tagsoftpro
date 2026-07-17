<?php

namespace App\Http\Requests\Dashboard\Menu;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'key' => 'required|string|max:255|regex:/^[a-zA-Z0-9_]+$/|unique:menus,key',
            'icon' => 'nullable|string|max:100',
            'route_prefix' => 'nullable|string|max:255',
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'company_id' => 'nullable|exists:companies,id',
        ];

        foreach ($this->input('locales', []) as $locale => $data) {
            $rules["locales.{$locale}.name"] = 'required|string|max:255';
            $rules["locales.{$locale}.description"] = 'nullable|string';
        }

        return $rules;
    }

    public function messages(): array
    {
        $messages = [
            'key.required' => trans('dashboard/menus.validation.key_required'),
            'key.unique' => trans('dashboard/menus.validation.key_unique'),
            'key.regex' => trans('dashboard/menus.validation.key_regex'),
            'status.required' => trans('dashboard/menus.validation.status_required'),
            'status.in' => trans('dashboard/menus.validation.status_in'),
        ];

        foreach ($this->input('locales', []) as $locale => $data) {
            $messages["locales.{$locale}.name.required"] = trans('dashboard/menus.validation.name_required') . " ($locale)";
            $messages["locales.{$locale}.name.max"] = trans('dashboard/menus.validation.name_max') . " ($locale)";
        }

        return $messages;
    }
}