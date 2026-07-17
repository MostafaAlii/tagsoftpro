<?php

declare(strict_types=1);

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Model;

class PermissionGroupTranslation extends Model {
    protected $table = 'permission_group_translations';
    public $timestamps = false;
    protected $fillable = ['locale', 'name', 'description'];
}