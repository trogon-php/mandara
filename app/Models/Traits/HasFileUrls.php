<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

trait HasFileUrls
{
    protected static $dynamicAccessorData = [];
    public static function bootHasFileUrls()
    {
        static::retrieved(fn($model) => $model->initFileUrlAccessors());
        static::creating(fn($model) => $model->initFileUrlAccessors());
    }

    protected function initFileUrlAccessors(): void
    {
        // Use public getter method instead of reflection for better performance
        if (! method_exists($this, 'getFileFields')) {
            return;
        }

        $fileFields = $this->getFileFields();

        if (! $fileFields) {
            return;
        }

        foreach ($fileFields as $field => $options) {
            $type    = $options['type'] ?? $options['preset'] ?? null;
            $isJson  = $options['json'] ?? false;   // multi-size JSON
            $isArray = $options['array'] ?? false; // gallery array

            $accessor = 'get' . ucfirst(Str::camel($field)) . 'UrlAttribute';
            $urlField = $field . '_url';
            
            // Check if the field exists in fillable, casts, or attributes
            $fieldExists = in_array($field, $this->getFillable()) || 
                          array_key_exists($field, $this->getCasts()) || 
                          array_key_exists($field, $this->getAttributes()) ||
                          $this->hasAttribute($field);
            
            if ($fieldExists) {
                
                if (! method_exists($this, $accessor)) {
                    // Store the accessor closure for later use in __call
                    $accessorData = [
                        'field' => $field,
                        'type' => $type,
                        'isJson' => $isJson,
                        'isArray' => $isArray
                    ];
                    
                    // Use a static property to store accessor data per class
                    if (! isset(static::$dynamicAccessorData)) {
                        static::$dynamicAccessorData = [];
                    }
                    static::$dynamicAccessorData[$accessor] = $accessorData;
                }

                if (! in_array($urlField, $this->appends ?? [])) {
                    $this->appends = $this->appends ?? [];
                    $this->appends[] = $urlField;
                }
            }
        }
    }

    /**
     * Handle dynamic accessor calls
     */
    public function __call($method, $parameters)
    {
        // Ensure accessors are initialized (handles cached models where 'retrieved' event didn't fire)
        $this->initFileUrlAccessors();
        // Check if this is a dynamic accessor we created
        if (isset(static::$dynamicAccessorData[$method])) {
            $data = static::$dynamicAccessorData[$method];
            $value = $this->{$data['field']};
            
            if (! $value) return null;
            
            // Multi-size JSON {thumb, original}
            if ($data['isJson'] && is_array($value)) {
                $urls = [];
                foreach ($value as $key => $path) {
                    // If path is already a URL, use it as-is
                    if (filter_var($path, FILTER_VALIDATE_URL)) {
                        $urls[$key] = $path;
                    } else {
                        $urls[$key] = file_url($path, $data['type']);
                    }
                }
                return $urls;
            }

            // Gallery array (either simple paths or multi-size JSON)
            if ($data['isArray'] && is_array($value)) {
                $urls = [];
                foreach ($value as $item) {
                    if (is_array($item)) {
                        $itemUrls = [];
                        foreach ($item as $key => $path) {
                            // If path is already a URL, use it as-is
                            if (filter_var($path, FILTER_VALIDATE_URL)) {
                                $itemUrls[$key] = $path;
                            } else {
                                $itemUrls[$key] = file_url($path, $data['type']);
                            }
                        }
                        $urls[] = $itemUrls;
                    } else {
                        // If item is already a URL, use it as-is
                        if (filter_var($item, FILTER_VALIDATE_URL)) {
                            $urls[] = $item;
                        } else {
                            $urls[] = file_url($item, $data['type']);
                        }
                    }
                }
                return $urls;
            }

            return file_url($value, $data['type']);
        }

        // Fall back to parent __call
        return parent::__call($method, $parameters);
    }

    /**
     * Handle dynamic attribute access
     */
    public function __get($key)
    {
        // Check if this is a URL attribute we created
        if (str_ends_with($key, '_url')) {
            // Ensure accessors are initialized
            $this->initFileUrlAccessors();
            
            $baseField = str_replace('_url', '', $key);
            $accessor = 'get' . ucfirst(Str::camel($baseField)) . 'UrlAttribute';
            
            if (isset(static::$dynamicAccessorData[$accessor])) {
                return $this->__call($accessor, []);
            }
            
            // Fallback: try to call the accessor directly if it exists
            if (method_exists($this, $accessor)) {
                return $this->$accessor();
            }
            
            // Final fallback: manually generate URL if field exists
            if (method_exists($this, 'getFileFields')) {
                $fileFields = $this->getFileFields();
                if (isset($fileFields[$baseField])) {
                    return $this->generateUrlForField($baseField, $fileFields[$baseField]);
                }
            }
        }

        // Fall back to parent __get
        return parent::__get($key);
    }
    
    /**
     * Manually generate URL for a field
     */
    protected function generateUrlForField($field, $options)
    {
        $value = $this->{$field};
        
        if (!$value) {
            return null;
        }
        
        $type = $options['type'] ?? $options['preset'] ?? null;
        $isJson = $options['json'] ?? false;
        
        if ($isJson && is_array($value)) {
            $urls = [];
            foreach ($value as $key => $path) {
                if (filter_var($path, FILTER_VALIDATE_URL)) {
                    $urls[$key] = $path;
                } else {
                    $urls[$key] = file_url($path, $type);
                }
            }
            return $urls;
        }
        
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        
        return file_url($value, $type);
    }
}
