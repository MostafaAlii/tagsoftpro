<?php

namespace Modules\Zone\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreZoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $zoneId = $this->route('zone')?->id;

        return [
            'key' => ['nullable', 'string', 'max:255', Rule::unique('zones')->ignore($zoneId)],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'locales' => ['required', 'array'],
            'locales.*.name' => ['required', 'string', 'max:255'],
            'locales.*.description' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'key.unique' => trans('zone::zones.validation.key_unique'),
            'status.required' => trans('zone::zones.validation.status_required'),
            'locales.required' => trans('zone::zones.validation.locales_required'),
            'locales.*.name.required' => trans('zone::zones.validation.name_required'),
        ];
    }
}