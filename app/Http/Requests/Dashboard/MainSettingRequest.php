<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class MainSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = get_user_data();
        if ($user && $user->type === \App\Enums\Admin\AdminType::OWNER && is_null($user->company_id)) {
            return [
                'after_minutes_calculate_delay' => ['nullable', 'integer', 'min:1'],
                'after_minutes_calculate_early_departure' => ['nullable', 'integer', 'min:1'],
                'after_minutes_calculate_quarter_day' => ['nullable', 'integer', 'min:1'],
                'after_time_half_daycut' => ['nullable', 'integer', 'min:1'],
                'after_time_full_daycut' => ['nullable', 'integer', 'min:1'],

                'mounthly_vacation_balance' => ['nullable', 'integer', 'min:1', 'max:30'],
                'after_days_begin_vacation' => ['nullable', 'integer', 'min:1'],
                'first_balance_begin_vacation' => ['nullable', 'integer', 'min:1', 'max:30'],

                'sanction_value_first_absence' => ['nullable', 'integer', 'min:1'],
                'sanction_value_second_absence' => ['nullable', 'integer', 'min:1'],
                'sanction_value_third_absence' => ['nullable', 'integer', 'min:1'],
                'sanction_value_fourth_absence' => ['nullable', 'integer', 'min:1'],
            ];
        }
        return [
            'after_minutes_calculate_delay' => ['required', 'integer', 'min:1'],
            'after_minutes_calculate_early_departure' => ['required', 'integer', 'min:1'],
            'after_minutes_calculate_quarter_day' => ['required', 'integer', 'min:1'],
            'after_time_half_daycut' => ['required', 'integer', 'min:1'],
            'after_time_full_daycut' => ['required', 'integer', 'min:1'],

            'mounthly_vacation_balance' => ['required', 'integer', 'min:1', 'max:30'],
            'after_days_begin_vacation' => ['required', 'integer', 'min:1'],
            'first_balance_begin_vacation' => ['required', 'integer', 'min:1', 'max:30'],

            'sanction_value_first_absence' => ['required', 'integer', 'min:1'],
            'sanction_value_second_absence' => ['required', 'integer', 'min:1'],
            'sanction_value_third_absence' => ['required', 'integer', 'min:1'],
            'sanction_value_fourth_absence' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            '*.required' => 'هذا الحقل مطلوب',
            '*.integer'  => 'القيمة يجب أن تكون رقم صحيح',
            '*.min'      => 'القيمة يجب أن تكون أكبر من صفر',
            'mounthly_vacation_balance.max' => 'الرصيد الشهرى لا يمكن أن يزيد عن 30 يوم',
            'first_balance_begin_vacation.max' => 'الرصيد الاجازات الاولى لا يمكن أن يزيد عن 30 يوم',
        ];
    }
}
