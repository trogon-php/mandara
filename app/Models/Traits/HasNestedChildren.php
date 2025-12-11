<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Collection;

trait HasNestedChildren
{
    /**
     * Immediate parent relationship
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Immediate children relationship (with sorting if available)
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->sorted();
    }

    /**
     * Immediate children relationship (without sorting)
     */
    public function allChildren(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * Get all descendants recursively
     */
    public function descendants(): Collection
    {
        $descendants = new Collection();

        foreach ($this->children as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($child->descendants());
        }

        return $descendants;
    }

    /**
     * Get all ancestors recursively
     */
    public function ancestors(): Collection
    {
        $ancestors = new Collection();
        $parent = $this->parent;

        while ($parent) {
            $ancestors->prepend($parent);
            $parent = $parent->parent;
        }

        return $ancestors;
    }

    /**
     * Get the root (top-level parent)
     */
    public function root()
    {
        $root = $this;
        while ($root->parent) {
            $root = $root->parent;
        }
        return $root;
    }

    /**
     * Depth level in the tree (0 for root)
     */
    public function getDepthAttribute(): int
    {
        return $this->ancestors()->count();
    }

    /**
     * Breadcrumb path string
     */
    public function getBreadcrumbAttribute(): string
    {
        $ancestors = $this->ancestors();
        $breadcrumb = $ancestors->pluck('title')->toArray();
        $breadcrumb[] = $this->title;

        return implode(' > ', $breadcrumb);
    }

    /**
     * Scope: root categories (no parent)
     */
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope: leaf categories (no children)
     */
    public function scopeLeaves($query)
    {
        return $query->whereDoesntHave('children');
    }

    /**
     * Scope: categories that have children
     */
    public function scopeWithChildren($query)
    {
        return $query->whereHas('children');
    }

    /**
     * Recursive hierarchical tree
     */
    public static function getTree($parentId = null): Collection
    {
        $items = static::where('parent_id', $parentId)
            ->sorted()
            ->with('children')
            ->get();

        foreach ($items as $item) {
            $item->children = static::getTree($item->id);
        }

        return $items;
    }

    /**
     * Flat list with indentation for dropdowns
     */
    public static function getFlatList($parentId = null, $level = 0): Collection
    {
        $items = static::where('parent_id', $parentId)
            ->sorted()
            ->get();

        $result = new Collection();

        foreach ($items as $item) {
            $item->indented_title = str_repeat('— ', $level) . $item->title;
            $result->push($item);
            $result = $result->merge(static::getFlatList($item->id, $level + 1));
        }

        return $result;
    }

    /**
     * Prevent moving a node under its own descendant
     */
    public function canMoveToParent($newParentId): bool
    {
        if ($newParentId === null || $newParentId === $this->id) {
            return true;
        }

        $descendants = $this->descendants();
        return !$descendants->contains('id', $newParentId);
    }
}
