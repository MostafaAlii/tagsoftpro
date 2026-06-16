<?php

declare(strict_types=1);

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Model;

class DepartmentTranslation extends Model
{
    protected $table = 'department_translations';
    public $timestamps = false;
    protected $fillable = ['name', 'description'];
}