<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class MainSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array {
        $projectTypeId = get_user_data()?->company?->project_type_id;
        return [
            'company_name' => ['required', 'string', 'max:255'],
            'phone'        => ['nullable', 'string', 'max:20'],
            'email'        => ['nullable', 'email', 'max:255'],
            'system_status'=> ['required'],
            'logo'         => ['nullable', 'image', 'max:2048'],
            'favicon'      => ['nullable', 'image', 'max:2048'],

            // ✅ الثيم اختياري - لو المستخدم مغيرش يفضل زي ما هو
            'theme_id' => [
                'nullable',
                Rule::exists('project_type_theme', 'theme_id')
                    ->where('project_type_id', $projectTypeId)
                    ->where('is_active', true),
            ],
        ];
    }
}
