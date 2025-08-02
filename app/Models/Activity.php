<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use InvalidArgumentException;

class Activity extends Model
{
    use HasFactory;

    public const MAX_DEPTH = 3; // Максимальная глубина вложенности

    protected $fillable = ['name', 'parent_id'];
        
    protected static function booted()
    {
        static::saving(function (Activity $activity) {
            if ($activity->parent_id && $activity->getDepth() > self::MAX_DEPTH) {
                throw new InvalidArgumentException(
                    "Максимальная глубина вложенности видов деятельности - " . self::MAX_DEPTH . " уровня"
                );
            }
        });
    }

    /**
     * Получить все родительские категории
     */
    public function getAncestors(): \Illuminate\Support\Collection
    {
        $ancestors = collect();
        $current = $this->parent;

        while ($current) {
            $ancestors->push($current);
            $current = $current->parent;
        }

        return $ancestors;
    }

    /**
     * Получить все дочерние категории (включая вложенные)
     */
    public function getDescendants(): \Illuminate\Support\Collection
    {
        $descendants = collect();

        foreach ($this->children as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($child->getDescendants());
        }

        return $descendants;
    }

    /**
     * Получить текущую глубину вложенности
     */
    public function getDepth(): int
    {
        $depth = 0;
        $current = $this;

        while ($current->parent) {
            $depth++;
            $current = $current->parent;

            if ($depth > self::MAX_DEPTH) {
                return $depth;
            }
        }

        return $depth;
    }

    /**
     * Проверить, является ли категория корневой
     */
    public function isRoot(): bool
    {
        return $this->parent_id === null;
    }

    public static function roots()
    {
        return static::whereNull('parent_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Activity::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Activity::class, 'parent_id');
    }

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class);
    }
}
