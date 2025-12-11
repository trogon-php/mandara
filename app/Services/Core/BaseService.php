<?php

namespace App\Services\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Services\Core\FileUploadService;
use Illuminate\Support\Str;

abstract class BaseService
{
    protected $model;

    public function __construct()
    {
        if (isset($this->modelClass)) {
            $this->model = new $this->modelClass();
        }
    }

    // Common CRUD
    public function getAll(): Collection
    {
        return $this->model->sorted()->get();
    }

    public function paginate(int $perPage = 10, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->model->sorted()->paginate($perPage, $columns);
    }

    public function find(int $id, array $relations = []): ?Model
    {
        return $this->model->with($relations)->find($id);
    }

    public function store(array $data): Model
    {
        $this->processFileUploads($data); // no record yet
        return $this->model->create($data);
    }

    public function update(int $id, array $data): ?Model
    {
        $record = $this->model->find($id);
        if (!$record) {
            return null;
        }
        // handling removed files
        $fileFields = method_exists($record, 'getFileFields') ? $record->getFileFields() : [];
        // dd($fileFields);
        foreach ($fileFields as $field => $config) {
            // dd($field);
            $removedField = $field . '_removed';

            if (isset($data[$removedField]) && !empty($data[$removedField])) {
                $removedFiles = explode(',', $data[$removedField]);
                $currentFiles = $record->{$field} ?? [];
                $updatedFiles = array_filter($currentFiles, function($file) use ($removedFiles) {
                    return !in_array($file, $removedFiles);
                });
                $record->{$field} = array_values($updatedFiles);
                // dd($removedFiles, $currentFiles, $updatedFiles, $record->{$field});
                foreach ($removedFiles as $removedFile) {
                    if (!empty($removedFile)) {
                        FileUploadService::delete($removedFile);
                    }
                }
            }
            unset($data[$removedField]);
        }
        // dd($record,$data);
        $this->processFileUploads($data, $record);
        $record->update($data);

        return $record;
    }

    public function delete(int $id): bool
    {
        $record = $this->model->find($id);
        if (!$record) {
            return false;
        }

        $this->deleteAttachedFiles($record);
        return $record->delete();
    }

    public function bulkDelete(array $ids): int
    {
        $records = $this->model->whereIn('id', $ids)->get();

        foreach ($records as $record) {
            if ($record) {
                $this->deleteAttachedFiles($record);
            }
        }
        return $this->model->whereIn('id', $ids)->delete();
    }

    // Sorting
    public function sortUpdate(array $order, string $column = 'sort_order'): bool
    {
        foreach ($order as $position => $id) {
            $record = $this->model->find($id);
            if ($record) {
                $record->update([$column => $position + 1]);
            }
        }
        return true;
    }

    // Clone an item with comprehensive logic
    public function clone(Model $model, array $overrides = []): ?Model
    {
        try {
            $clone = $model->replicate();

            // Apply overrides first
            foreach ($overrides as $key => $value) {
                $clone->{$key} = $value;
            }

            // Handle timestamps
            if ($model->timestamps) {
                $clone->created_at = now();
                $clone->updated_at = now();
            }

            // Handle file fields (defined in model::$fileFields)
            foreach ($model->getFileFields() as $field => $config) {
                $value = $model->{$field};

                if (empty($value)) {
                    continue;
                }

                if (!empty($config['single']) && $config['single'] === true) {
                    // Single file
                    $clone->{$field} = FileUploadService::copy($value);
                } else {
                    // Multi-size JSON object (not array of files)
                    $files = is_string($value) ? json_decode($value, true) : $value;
                    
                    if (is_array($files)) {
                        // Check if this is a multi-size object (has string keys like 'original', 'thumb')
                        $isMultiSize = !empty($files) && !is_numeric(array_keys($files)[0]);
                        
                        if ($isMultiSize) {
                            // Multi-size object: {"original": "path1", "thumb": "path2"}
                            $copiedSizes = [];
                            foreach ($files as $size => $filePath) {
                                $copied = FileUploadService::copy($filePath);
                                if ($copied !== null) {
                                    $copiedSizes[$size] = $copied;
                                }
                            }
                            $clone->{$field} = $copiedSizes; // Store as object, not array
                        } else {
                            // Array of separate files: ["path1", "path2", "path3"]
                            $cloned = [];
                            foreach ($files as $file) {
                                if (is_array($file)) {
                                    // Each item is a multi-size object
                                    $copiedSizes = [];
                                    foreach ($file as $size => $filePath) {
                                        $copied = FileUploadService::copy($filePath);
                                        if ($copied !== null) {
                                            $copiedSizes[$size] = $copied;
                                        }
                                    }
                                    if (!empty($copiedSizes)) {
                                        $cloned[] = $copiedSizes;
                                    }
                                } else {
                                    // Single file path
                                    $copied = FileUploadService::copy($file);
                                    if ($copied !== null) {
                                        $cloned[] = $copied;
                                    }
                                }
                            }
                            $clone->{$field} = $cloned;
                        }
                    }
                }
            }

            // Handle audit fields - let the model events handle these automatically
            // The BaseModel's creating event will set created_by and updated_by
            
            $uniqueFields = property_exists($model, 'uniqueCloneFields')
            ? $model->uniqueCloneFields
            : ['slug', 'title', 'name', 'email'];

            foreach ($uniqueFields as $field) {
                if (!isset($overrides[$field]) && !empty($clone->{$field})) {
                    $value = $clone->{$field};

                    // Make unique suffix
                    $suffix = '-copy-' . Str::random(6);

                    if ($field === 'email') {
                        // Special handling for emails
                        $parts = explode('@', $value, 2);
                        $clone->email = $parts[0] . $suffix . '@' . ($parts[1] ?? 'example.com');
                    } elseif ($field === 'slug') {
                        $clone->slug = $value . $suffix;
                    } else {
                        $clone->{$field} = $value . ' (Copy)';
                    }
                }
            }

            // Save the clone
            $clone->save();

            return $clone;
        } catch (\Exception $e) {
            \Log::error('Failed to clone model: ' . $e->getMessage(), [
                'model_class' => get_class($model),
                'model_id' => $model->id,
                'overrides' => $overrides
            ]);
            
            return null;
        }
    }

    // Get id and title array
    public function getIdTitle(string $titleColumn = 'title'): array
    {
        return $this->model->pluck($titleColumn, 'id')->toArray();
    }

    // File handling
    protected function processFileUploads(array &$data, ?Model $record = null): void
    {
        // Use actual record if available, otherwise just the repository's model
        $model = $record ?? $this->model;

        $fileFields = method_exists($model, 'getFileFields') ? $model->getFileFields() : [];

        foreach ($fileFields as $field => $config) {
            if (isset($data[$field])) {
                $isMultiple = !($config['single'] ?? true);
                
                // Handle multiple files (array of UploadedFile)
                if ($isMultiple && is_array($data[$field])) {
                    $uploadedFiles = [];
                    foreach ($data[$field] as $file) {
                        if ($file instanceof \Illuminate\Http\UploadedFile) {
                            $uploadedFiles[] = $file;
                        }
                    }
                    
                    if (!empty($uploadedFiles)) {
                        // For multiple files, merge with existing instead of replacing
                        $existingFiles = [];
                        if ($record && $record->{$field}) {
                            $existingFiles = is_array($record->{$field}) ? $record->{$field} : [$record->{$field}];
                        }
                        
                        // Upload new files
                        $newFiles = FileUploadService::upload(
                            $uploadedFiles,
                            $config['folder'] ?? 'uploads',
                            $config['preset'] ?? null
                        );
                        
                        // Merge existing with new files
                        if (is_array($newFiles)) {
                            $data[$field] = array_merge($existingFiles, $newFiles);
                        } else {
                            $data[$field] = array_merge($existingFiles, [$newFiles]);
                        }
                    }
                }
                // Handle single file (either single size or multiple sizes)
                elseif ($data[$field] instanceof \Illuminate\Http\UploadedFile) {
                    // Delete old file if replacing
                    if ($record && $record->{$field}) {
                        FileUploadService::delete($record->{$field});
                    }

                    $data[$field] = FileUploadService::upload(
                        $data[$field],
                        $config['folder'] ?? 'uploads',
                        $config['preset'] ?? null
                    );
                }
            }
        }
    }

    protected function deleteAttachedFiles(Model $record): void
    {
        $fileFields = method_exists($record, 'getFileFields') ? $record->getFileFields() : [];

        foreach ($fileFields as $field => $config) {
            if ($record->{$field}) {
                FileUploadService::delete($record->{$field});
            }
        }
    }

    /**
     * Get filtered data with advanced search and filtering
     */
    public function getFilteredData(array $params = [])
    {
        $query = $this->model->newQuery();

        // Apply search
        if (!empty($params['search'])) {
            $this->applySearch($query, $params['search']);
        }

        // Apply filters
        if (!empty($params['filters'])) {
            $this->applyFilters($query, $params['filters']);
        }

        // Apply sorting
        $this->applySorting($query, $params['sort_by'] ?? null, $params['sort_dir'] ?? 'asc');

        // Apply pagination if requested
        if (isset($params['paginate']) && $params['paginate']) {
            return $query->paginate($params['per_page'] ?? 15);
        }

        return $query->get();
    }

    /**
     * Apply search across multiple fields
     */
    protected function applySearch($query, string $searchTerm)
    {
        $searchFields = $this->getDefaultSearchFields();

        $query->where(function ($q) use ($searchTerm, $searchFields) {
            foreach ($searchFields as $field) {
                if (strpos($field, '.') !== false) {
                    // Handle relationship columns
                    $parts = explode('.', $field);
                    $q->orWhereHas($parts[0], function ($subQ) use ($parts, $searchTerm) {
                        $subQ->where($parts[1], 'like', "%{$searchTerm}%");
                    });
                } else {
                    $q->orWhere($field, 'like', "%{$searchTerm}%");
                }
            }
        });
    }

    /**
     * Apply filters based on configuration
     */
    protected function applyFilters($query, array $filters)
    {
        $filterConfig = $this->getFilterConfig();

        foreach ($filters as $key => $value) {
            if ($value === null || $value === '' || !isset($filterConfig[$key])) continue;

            $config = $filterConfig[$key];
            $this->applyFilter($query, $key, $value, $config);
        }
    }

    /**
     * Apply individual filter based on configuration
     */
    protected function applyFilter($query, string $field, $value, array $config)
    {
        $type = $config['type'] ?? 'exact';

        switch ($type) {
            case 'exact':
                $query->where($field, $value);
                break;
            case 'like':
                $query->where($field, 'like', "%{$value}%");
                break;
            case 'date_range':
                if (isset($value['from'])) {
                    $query->whereDate($field, '>=', $value['from']);
                }
                if (isset($value['to'])) {
                    $query->whereDate($field, '<=', $value['to']);
                }
                break;
            case 'in':
                $query->whereIn($field, (array)$value);
                break;
            case 'between':
                if (isset($value['min'])) {
                    $query->where($field, '>=', $value['min']);
                }
                if (isset($value['max'])) {
                    $query->where($field, '<=', $value['max']);
                }
                break;
            case 'relationship':
                $relation = $config['relation'] ?? $field;
                $relationField = $config['relation_field'] ?? 'id';
                $query->whereHas($relation, function ($q) use ($relationField, $value) {
                    $q->where($relationField, $value);
                });
                break;
            case 'category_hierarchy':
                $this->applyCategoryHierarchyFilter($query, $field, $value, $config);
                break;
        }
    }

    /**
     * Apply category hierarchy filter (includes selected category and all descendants)
     */
    protected function applyCategoryHierarchyFilter($query, string $field, $value, array $config)
    {
        $modelClass = $config['model_class'] ?? 'App\\Models\\Category';
        $category = $modelClass::find($value);
        
        if (!$category) {
            // If category not found, fall back to exact match
            $query->where($field, $value);
            return;
        }

        // Get all descendant IDs
        $descendantIds = $category->descendants()->pluck('id')->toArray();
        
        // Include the selected category itself
        $allCategoryIds = array_merge([$value], $descendantIds);
        
        // Apply the filter
        $query->whereIn($field, $allCategoryIds);
    }

    /**
     * Apply sorting
     */
    protected function applySorting($query, ?string $sortBy = null, string $sortDir = 'asc')
    {
        if ($sortBy) {
            $query->orderBy($sortBy, $sortDir);
        } else {
            // Default sorting
            $defaultSort = $this->getDefaultSorting();
            $query->orderBy($defaultSort['field'], $defaultSort['direction']);
        }
    }

    /**
     * Get search configuration for UI
     */
    public function getSearchConfig(): array
    {
        return [
            'search_fields' => $this->getSearchFieldsConfig(),
            'default_search_fields' => $this->getDefaultSearchFields(),
        ];
    }

    // Abstract methods that each service must implement (public)
    abstract public function getFilterConfig(): array;
    abstract public function getSearchFieldsConfig(): array;
    abstract public function getDefaultSearchFields(): array;
    abstract public function getDefaultSorting(): array;

    // Generic paginated data for DataTables
     
    public function getPaginatedData($start = 0, $length = 10, $search = '', array $orderColumns = [], $orderColumn = 0, $orderDir = 'desc', array $filters = [])
    {
        $query = $this->model->newQuery();

        // Apply search
        if (!empty($search)) {
            $query->where(function ($q) use ($search, $orderColumns) {
                foreach ($orderColumns as $column) {
                    if (strpos($column, '.') !== false) {
                        // Handle relationship columns
                        $q->orWhereHas(explode('.', $column)[0], function ($subQ) use ($column, $search) {
                            $subQ->where(explode('.', $column)[1], 'like', "%{$search}%");
                        });
                    } else {
                        $q->orWhere($column, 'like', "%{$search}%");
                    }
                }
            });
        }

        // Apply filters
        foreach ($filters as $key => $value) {
            if (!empty($value)) {
                $query->where($key, $value);
            }
        }

        // Get total count before filtering
        $total = $this->model->count();

        // Get filtered count
        $filtered = $query->count();

        // Apply ordering
        $orderBy = $orderColumns[$orderColumn] ?? 'updated_at';
        $query->orderBy($orderBy, $orderDir);

        // Apply pagination
        $data = $query->skip($start)->take($length)->get();

        return [
            'total' => $total,
            'filtered' => $filtered,
            'data' => $data
        ];
    }

    /**
     * Search for a query across specified columns
     */
    public function search(string $query, array $columns = [], int $limit = 10, array $select = ['*']): Collection
    {
        $q = $this->model->query();

        // If service defines default search fields, use them
        if (empty($columns) && method_exists($this, 'getDefaultSearchFields')) {
            $columns = $this->getDefaultSearchFields();
        }

        $q->where(function ($sub) use ($query, $columns) {
            foreach ($columns as $column) {
                $sub->orWhere($column, 'LIKE', "%{$query}%");
            }
        });

        return $q->limit($limit)->get($select);
    }
}
