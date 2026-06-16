<?php

declare(strict_types=1);

namespace App\Models\Translations;

use Illuminate\Database\Eloquent\Model;

class ProjectTypeTranslation extends Model
{
    protected $table = 'project_type_translations';
    public $timestamps = false;
    protected $fillable = ['name', 'description'];
}