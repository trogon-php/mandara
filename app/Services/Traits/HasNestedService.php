<?php

namespace App\Services\Traits;

use Illuminate\Support\Collection;

trait HasNestedService
{
    /**
     * Get root items (no parent)
     */
    public function getRootItems(): Collection
    {
        return $this->model->whereNull('parent_id')->sorted()->get();
    }

    /**
     * Get leaf items (no children)
     */
    public function getLeafItems(): Collection
    {
        return $this->model->whereDoesntHave('children')->sorted()->get();
    }

    /**
     * Get items that have children
     */
    public function getItemsWithChildren(): Collection
    {
        return $this->model->whereHas('children')->sorted()->get();
    }

    /**
     * Get hierarchical tree structure
     */
    public function getTree($parentId = null): Collection
    {
        $query = $this->model->where('parent_id', $parentId);
        
        // Load children recursively
        $items = $query->with(['children' => function ($query) {
            $query->sorted();
        }])->sorted()->get();
        
        return $items;
    }

    /**
     * Get descendants of a record
     */
    public function getDescendants(int $id): Collection
    {
        $item = $this->model->find($id);
        if (!$item) {
            return collect();
        }
        
        return $item->descendants()->sorted()->get();
    }

    /**
     * Get ancestors of a record
     */
    public function getAncestors(int $id): Collection
    {
        $item = $this->model->find($id);
        if (!$item) {
            return collect();
        }
        
        return $item->ancestors()->sorted()->get();
    }

    /**
     * Get root (top-most parent) of a record
     */
    public function getRootItem(int $id)
    {
        $item = $this->model->find($id);
        if (!$item) {
            return null;
        }
        
        return $item->root;
    }

    /**
     * Check if item can move under a new parent
     */
    public function canMoveItemToParent(int $id, ?int $newParentId): bool
    {
        $item = $this->model->find($id);
        if (!$item) {
            return false;
        }
        
        // Can't move to itself
        if ($newParentId === $id) {
            return false;
        }
        
        // If moving to null (root), it's always allowed
        if ($newParentId === null) {
            return true;
        }
        
        // Check if new parent exists
        $newParent = $this->model->find($newParentId);
        if (!$newParent) {
            return false;
        }
        
        // Check if new parent is not a descendant of the item being moved
        $descendants = $item->descendants()->pluck('id')->toArray();
        return !in_array($newParentId, $descendants);
    }

    /**
     * Get flat list with indentation (for dropdowns)
     */
    public function getFlatList($parentId = null, $level = 0): Collection
    {
        $items = $this->model->where('parent_id', $parentId)->sorted()->get();
        $result = collect();
        
        foreach ($items as $item) {
            // Add indentation prefix
            $indent = str_repeat('— ', $level);
            $item->indented_title = $indent . $item->title;
            
            $result->push($item);
            
            // Recursively get children
            $children = $this->getFlatList($item->id, $level + 1);
            $result = $result->merge($children);
        }
        
        return $result;
    }

    /**
     * Get all items in a flat structure with parent-child relationships
     */
    public function getFlatStructure(): Collection
    {
        return $this->model->with('parent')->sorted()->get();
    }

    /**
     * Get items by parent ID
     */
    public function getItemsByParent(?int $parentId): Collection
    {
        return $this->model->where('parent_id', $parentId)->sorted()->get();
    }

    /**
     * Get the depth level of an item
     */
    public function getItemDepth(int $id): int
    {
        $item = $this->model->find($id);
        if (!$item) {
            return 0;
        }
        
        return $item->depth;
    }

    /**
     * Get items at a specific depth level
     */
    public function getItemsAtDepth(int $depth): Collection
    {
        return $this->model->where('depth', $depth)->sorted()->get();
    }

    /**
     * Get the full path to an item (breadcrumb style)
     */
    public function getItemPath(int $id): Collection
    {
        $item = $this->model->find($id);
        if (!$item) {
            return collect();
        }
        
        $path = collect();
        $current = $item;
        
        while ($current) {
            $path->prepend($current);
            $current = $current->parent;
        }
        
        return $path;
    }

    /**
     * Get items that are siblings of a given item
     */
    public function getSiblings(int $id): Collection
    {
        $item = $this->model->find($id);
        if (!$item) {
            return collect();
        }
        
        return $this->model->where('parent_id', $item->parent_id)
            ->where('id', '!=', $id)
            ->sorted()
            ->get();
    }

    /**
     * Get the next sibling of an item
     */
    public function getNextSibling(int $id)
    {
        $item = $this->model->find($id);
        if (!$item) {
            return null;
        }
        
        return $this->model->where('parent_id', $item->parent_id)
            ->where('sort_order', '>', $item->sort_order)
            ->sorted()
            ->first();
    }

    /**
     * Get the previous sibling of an item
     */
    public function getPreviousSibling(int $id)
    {
        $item = $this->model->find($id);
        if (!$item) {
            return null;
        }
        
        return $this->model->where('parent_id', $item->parent_id)
            ->where('sort_order', '<', $item->sort_order)
            ->sorted()
            ->first();
    }

    /**
     * Move an item to a new parent
     */
    public function moveItemToParent(int $id, ?int $newParentId): bool
    {
        if (!$this->canMoveItemToParent($id, $newParentId)) {
            return false;
        }
        
        $item = $this->model->find($id);
        if (!$item) {
            return false;
        }
        
        $item->parent_id = $newParentId;
        return $item->save();
    }

    /**
     * Get the total count of items in the tree
     */
    public function getTotalCount(): int
    {
        return $this->model->count();
    }

    /**
     * Get the count of items at root level
     */
    public function getRootCount(): int
    {
        return $this->model->whereNull('parent_id')->count();
    }

    /**
     * Get the count of leaf items
     */
    public function getLeafCount(): int
    {
        return $this->model->whereDoesntHave('children')->count();
    }
}
