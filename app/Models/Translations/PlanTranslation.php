<?php

declare(strict_types=1);

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Model;

class PlanTranslation extends Model
{
    protected $table = 'plan_translations';
    public $timestamps = false;
    protected $fillable = ['name', 'description'];
}