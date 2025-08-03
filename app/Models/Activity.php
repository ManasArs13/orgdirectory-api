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
     * Получить все дочерние категории (включая вложенные)
     */
    public function getDescendantsAndSelf()
    {
        return $this->newQuery()
            ->where(function ($query) {
                $query->where('id', $this->id)
                    ->orWhere('parent_id', $this->id)
                    ->orWhereIn('parent_id', function ($q) {
                        $q->select('id')
                            ->from('activities')
                            ->where('parent_id', $this->id);
                    });
            })
            ->get();
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
