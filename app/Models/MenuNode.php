<?php

namespace App\Models;
use Illuminate\Database\Eloquent\{Builder,Collection,Model};
use Illuminate\Database\Eloquent\Relations\{BelongsTo,HasMany};
use Illuminate\Support\Facades\Route;

class MenuNode extends Model {
    protected $fillable = ['menu_id', 'menu_item_id', 'parent_id', 'sort_order', 'created_by', 'updated_by'];
    protected $appends = ['resolved_url'];

    /*
    |--------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------
    */

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'updated_by');
    }

    /*
    |--------------------------------------------------------------------
    | Link resolving
    |--------------------------------------------------------------------
    */

    public function getResolvedUrlAttribute(): ?string
    {
        $item = $this->menuItem;

        if (! $item || $item->type !== 'link') {
            return null;
        }

        if ($item->link_type === 'url') {
            return $item->url;
        }

        if ($item->link_type !== 'route' || ! $item->route_name) {
            return null;
        }

        $prefix = $this->menu?->route_prefix;

        $fullRouteName = $prefix && ! str_starts_with($item->route_name, "{$prefix}.")
            ? "{$prefix}.{$item->route_name}"
            : $item->route_name;

        return Route::has($fullRouteName)
            ? route($fullRouteName, $item->route_params ?? [])
            : null;
    }

    /*
    |--------------------------------------------------------------------
    | Tree building
    |--------------------------------------------------------------------
    */

    /**
     * بيبني شجرة منيو معينة كاملة بـ query واحد بس.
     * لازم تعمل عليها cache في الـ Service layer (مفتاح الكاش يشمل
     * menuKey + locale + isOwner + projectTypeId + companyId).
     */
    public static function tree(
        string $menuKey,
        string $locale,
        bool $isOwner = false,
        ?int $projectTypeId = null,
        ?int $companyId = null
    ): Collection {
        $query = self::query()
            ->whereHas('menu', fn(Builder $q) => $q->where('key', $menuKey)->active())
            ->whereHas('menuItem', function (Builder $q) use ($isOwner, $projectTypeId, $companyId) {
                $q->active()->visibleTo($isOwner)->forCompany($companyId);

                if ($projectTypeId !== null) {
                    $q->forProjectType($projectTypeId);
                }
            })
            ->with([
                'menu',
                'menuItem.translations' => fn($q) => $q->where('locale', $locale),
            ])
            ->orderBy('sort_order');

        $flat = $query->get();

        return self::nest($flat);
    }

    protected static function nest(Collection $nodes, ?int $parentId = null): Collection
    {
        return $nodes
            ->where('parent_id', $parentId)
            ->map(function (self $node) use ($nodes) {
                $node->setRelation('children', self::nest($nodes, $node->id));

                return $node;
            })
            ->values();
    }
}