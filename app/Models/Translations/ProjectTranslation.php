<?php

declare(strict_types=1);

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Model;

class ProjectTranslation extends Model
{
    protected $table = 'project_translations';
    public $timestamps = false;
    protected $fillable = ['name', 'description'];
}