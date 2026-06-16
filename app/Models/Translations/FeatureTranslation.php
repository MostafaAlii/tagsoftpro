<?php
declare(strict_types=1);
namespace App\Models\Translations;
use Illuminate\Database\Eloquent\Model;
class FeatureTranslation extends Model {
    protected $table = 'feature_translations';
    public $timestamps = false;
    protected $fillable = ['name', 'key'];
}