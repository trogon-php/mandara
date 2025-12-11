<?php

namespace App\Services\Users;

use App\Services\Core\BaseService;
use App\Models\UserMeta;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UserMetaService extends BaseService
{
    protected string $modelClass = UserMeta::class;

    /**
     * Get validation rules for user meta fields based on config
     */
    public function getValidationRules(): array
    {
        $userMetaConfig = config('user_meta', []);
        $rules = [];

        foreach ($userMetaConfig as $field => $config) {
            if (!$config['enabled']) {
                continue;
            }

            $fieldRules = $this->buildFieldRules($field, $config);
            if (!empty($fieldRules)) {
                $rules[$field] = $fieldRules;
            }
        }

        return $rules;
    }

    /**
     * Build validation rules for a specific field
     */
    protected function buildFieldRules(string $field, array $config): array
    {
        $rules = [];

        // Add required rule if needed
        if ($config['required']) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }

        // Add type-specific rules
        switch ($config['type']) {
            case 'text':
                $rules[] = 'string';
                $rules[] = 'max:255';
                break;

            case 'textarea':
                $rules[] = 'string';
                $rules[] = 'max:1000';
                break;

            case 'number':
                $rules[] = 'numeric';
                break;

            case 'date':
                $rules[] = 'date';
                $rules[] = 'before:today';
                break;

            case 'select':
                $rules[] = 'string';
                if (isset($config['options'])) {
                    // Handle both array of values and key-value pairs
                    if ($this->is_associative_array($config['options'])) {
                        // Key-value pairs: validate against keys
                        $rules[] = Rule::in(array_keys($config['options']));
                    } else {
                        // Simple array: validate against values
                        $rules[] = Rule::in($config['options']);
                    }
                }
                break;

            case 'relation':
                $rules[] = 'integer';
                $rules[] = 'exists:' . $this->getTableName($config['model']) . ',id';
                break;
        }

        return $rules;
    }

    /**
     * Get table name from model class
     */
    protected function getTableName(string $modelClass): string
    {
        $model = new $modelClass;
        return $model->getTable();
    }

    /**
     * Validate user meta data
     */
    public function validate(array $data): array
    {
        $rules = $this->getValidationRules();
        
        $validator = Validator::make($data, $rules);
        
        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors()->toArray()
            ];
        }

        return [
            'success' => true,
            'data' => $validator->validated()
        ];
    }

    /**
     * Get enabled user meta fields configuration
     */
    public function getEnabledFields(): array
    {
        $userMetaConfig = config('user_meta', []);
        
        return array_filter($userMetaConfig, function ($config) {
            return $config['enabled'] ?? false;
        });
    }

    /**
     * Get field options for frontend
     */
    public function getFieldOptions(): array
    {
        $enabledFields = $this->getEnabledFields();
        $options = [];

        foreach ($enabledFields as $field => $config) {
            $options[$field] = [
                'type' => $config['type'],
                'required' => $config['required'] ?? false,
                'label' => ucwords(str_replace('_', ' ', $field)),
            ];

            // Add specific options based on field type
            switch ($config['type']) {
                case 'select':
                    $options[$field]['options'] = $config['options'] ?? [];
                    break;

                case 'relation':
                    $options[$field]['model'] = $config['model'];
                    if (isset($config['depends_on'])) {
                        $options[$field]['depends_on'] = $config['depends_on'];
                    }
                    break;
            }
        }

        return $options;
    }

    /**
     * Get display value for a meta field
     */
    public function getDisplayValue(string $field, $value): string
    {
        $userMetaConfig = config('user_meta', []);
        
        if (!isset($userMetaConfig[$field])) {
            return $value;
        }
        
        $config = $userMetaConfig[$field];
        
        if ($config['type'] === 'select' && isset($config['options'])) {
            // Handle key-value pairs
            if ($this->is_associative_array($config['options'])) {
                return $config['options'][$value] ?? $value;
            }
        }
        
        return $value;
    }

    /**
     * Get all display values for user meta
     */
    public function getDisplayValues(array $metaData): array
    {
        $displayValues = [];
        
        foreach ($metaData as $key => $value) {
            $displayValues[$key] = $this->getDisplayValue($key, $value);
        }
        
        return $displayValues;
    }

    /**
     * Store user meta data
     */
    public function storeUserMeta(int $userId, array $metaData, ?int $createdBy = null): bool
    {
        try {
            DB::beginTransaction();

            foreach ($metaData as $key => $value) {
                // Check if meta already exists
                $existingMeta = UserMeta::where('user_id', $userId)
                    ->where('meta_key', $key)
                    ->first();

                if ($existingMeta) {
                    // Update existing meta
                    $existingMeta->update([
                        'meta_value' => $value,
                        'updated_by' => $createdBy,
                    ]);
                } else {
                    // Create new meta
                    UserMeta::create([
                        'user_id' => $userId,
                        'meta_key' => $key,
                        'meta_value' => $value,
                        'created_by' => $createdBy,
                    ]);
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Get user meta data
     */
    public function getUserMeta(int $userId): array
    {
        $metaRecords = UserMeta::where('user_id', $userId)->get();
        
        $metaData = [];
        foreach ($metaRecords as $record) {
            $metaData[$record->meta_key] = $record->meta_value;
        }

        return $metaData;
    }

    /**
     * Get specific user meta value
     */
    public function getUserMetaValue(int $userId, string $key, $default = null)
    {
        $meta = UserMeta::where('user_id', $userId)
            ->where('meta_key', $key)
            ->first();

        return $meta ? $meta->meta_value : $default;
    }

    /**
     * Update specific user meta value
     */
    public function updateUserMetaValue(int $userId, string $key, $value, ?int $updatedBy = null): bool
    {
        try {
            $meta = UserMeta::where('user_id', $userId)
                ->where('meta_key', $key)
                ->first();

            if ($meta) {
                $meta->update([
                    'meta_value' => $value,
                    'updated_by' => $updatedBy,
                ]);
            } else {
                UserMeta::create([
                    'user_id' => $userId,
                    'meta_key' => $key,
                    'meta_value' => $value,
                    'created_by' => $updatedBy,
                ]);
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Delete user meta
     */
    public function deleteUserMeta(int $userId, ?string $key): bool
    {
        try {
            $query = UserMeta::where('user_id', $userId);
            
            if ($key) {
                $query->where('meta_key', $key);
            }
            
            $query->delete();
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if array is associative
     */
    private function is_associative_array(array $array): bool
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }

    // Required abstract methods from BaseService
    public function getFilterConfig(): array
    {
        return [
            'user_id' => [
                'type' => 'exact',
                'label' => 'User ID',
                'col' => 3,
            ],
            'meta_key' => [
                'type' => 'like',
                'label' => 'Meta Key',
                'col' => 3,
            ],
        ];
    }

    public function getSearchFieldsConfig(): array
    {
        return [
            'meta_key' => 'Meta Key',
            'meta_value' => 'Meta Value',
        ];
    }

    public function getDefaultSearchFields(): array
    {
        return ['meta_key', 'meta_value'];
    }

    public function getDefaultSorting(): array
    {
        return ['field' => 'created_at', 'direction' => 'desc'];
    }
}
