<?php

namespace App\Http\Requests\Dashboard\Permission\PermissionGroup;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePermissionGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'icon' => 'nullable|string|max:100',
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'company_id' => 'nullable|exists:companies,id',
        ];

        // ─── قواعد الترجمات ──────────────────────────────────
        foreach ($this->input('locales', []) as $locale => $data) {
            $rules["locales.{$locale}.name"] = 'required|string|max:255';
            $rules["locales.{$locale}.description"] = 'nullable|string';
        }

        return $rules;
    }

    public function messages(): array
    {
        $messages = [
            'status.required' => trans('dashboard/permission_groups.validation.status_required'),
            'status.in' => trans('dashboard/permission_groups.validation.status_in'),
        ];

        foreach ($this->input('locales', []) as $locale => $data) {
            $messages["locales.{$locale}.name.required"] = trans('dashboard/permission_groups.validation.name_required') . " ($locale)";
            $messages["locales.{$locale}.name.max"] = trans('dashboard/permission_groups.validation.name_max') . " ($locale)";
        }

        return $messages;
    }
}