<?php
namespace Modules\Provider\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProviderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $providerId = $this->route('provider')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('providers')->ignore($providerId)],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => [$providerId ? 'nullable' : 'required', 'string', 'min:8'],
            'status' => ['required', Rule::in(['active', 'inactive', 'pending', 'suspended'])],
            'date' => ['nullable', 'date'],
            'address' => ['nullable', 'string', 'max:500'],
            'website' => ['nullable', 'string', 'max:255', 'url'],
            'zone_id' => ['nullable', 'exists:zones,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => trans('provider::providers.validation.name_required'),
            'email.required' => trans('provider::providers.validation.email_required'),
            'email.unique' => trans('provider::providers.validation.email_unique'),
            'password.required' => trans('provider::providers.validation.password_required'),
            'password.min' => trans('provider::providers.validation.password_min'),
            'website.url' => trans('provider::providers.validation.website_url'),
            'zone_id.exists' => trans('provider::providers.validation.zone_exists'),
        ];
    }
}