<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use App\Enums\Admin\AdminType;
class CompanyScope implements Scope {
    public function apply(Builder $builder, Model $model): void {
        $user = get_user_data();
        if (!$user) {
            return;
        }
        if ($user->type === AdminType::OWNER && is_null($user->company_id)) {
            return;
        }
        $builder->where($model->getTable() . '.company_id', $user->company_id);
    }
}