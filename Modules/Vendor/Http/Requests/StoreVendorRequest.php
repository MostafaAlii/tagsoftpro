<?php

namespace Modules\Vendor\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class StoreVendorRequest extends FormRequest
{
    public function rules(): array
    {
        $vendorId = $this->route('vendor')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('vendors')->ignore($vendorId)],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => [$vendorId ? 'nullable' : 'required', 'string', 'min:8'],
            'status' => ['required', Rule::in(['active', 'inactive', 'pending', 'suspended'])],
            'type' => ['nullable', 'string', 'max:255'],
            'date' => ['nullable', 'date'],
            'company_id' => ['nullable', 'exists:companies,id'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function messages(): array
    {
        return [
            'name.required' => trans('validation.required', ['attribute' => trans('vendor::vendors.name')]),
            'email.required' => trans('validation.required', ['attribute' => trans('vendor::vendors.email')]),
            'email.unique' => trans('validation.unique', ['attribute' => trans('vendor::vendors.email')]),
            'password.required' => trans('validation.required', ['attribute' => trans('vendor::vendors.password')]),
            'password.min' => trans('validation.min.string', ['attribute' => trans('vendor::vendors.password'), 'min' => 8]),
        ];
    }
}