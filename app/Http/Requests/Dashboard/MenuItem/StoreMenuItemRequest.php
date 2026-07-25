<?php
namespace App\Http\Requests\Dashboard\MenuItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class StoreMenuItemRequest extends FormRequest {
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        $rules = [
            'type' => ['required', Rule::in(['link', 'dropdown', 'header', 'divider'])],
            'icon' => 'nullable|string|max:100',
            'link_type' => ['nullable', Rule::in(['route', 'url', 'none'])],
            'route_name' => 'nullable|string|max:255',
            'route_params' => 'nullable|array',
            'url' => 'nullable|string|max:255',
            'target' => ['nullable', Rule::in(['_self', '_blank'])],
            'is_owner_only' => 'nullable|boolean',
            'permission_name' => 'nullable|string|max:255',
            'badge_text' => 'nullable|string|max:100',
            'badge_color' => 'nullable|string|max:50',
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'visible_from' => 'nullable|date',
            'visible_until' => 'nullable|date|after:visible_from',
            'company_id' => 'nullable|exists:companies,id',
        ];
        foreach ($this->input('locales', []) as $locale => $data) {
            $rules["locales.{$locale}.title"] = 'required|string|max:255';
            $rules["locales.{$locale}.description"] = 'nullable|string';
        }
        return $rules;
    }

    public function messages(): array {
        $messages = [
            'type.required' => trans('dashboard/menu_items.validation.type_required'),
            'type.in' => trans('dashboard/menu_items.validation.type_in'),
            'status.required' => trans('dashboard/menu_items.validation.status_required'),
            'status.in' => trans('dashboard/menu_items.validation.status_in'),
            'visible_until.after' => trans('dashboard/menu_items.validation.visible_until_after'),
        ];
        foreach ($this->input('locales', []) as $locale => $data) {
            $messages["locales.{$locale}.title.required"] = trans('dashboard/menu_items.validation.title_required') . " ($locale)";
            $messages["locales.{$locale}.title.max"] = trans('dashboard/menu_items.validation.title_max') . " ($locale)";
        }
        return $messages;
    }
}